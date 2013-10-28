<?php
namespace Application\Navigation\Service;

use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Exception;

class TopMenuNavigationFactory extends DefaultNavigationFactory
{
    /**
     * @{inheritdoc}
     */

    protected function getPages(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->pages) {
            $nameMenu = $this->getName();
            $comUserModel = $serviceLocator->get('Account\Model\CompanyUserModel');
            $comModel = $serviceLocator->get('Account\Model\CompanyModel');
            $accModel = $serviceLocator->get('Account\Model\AccountModel');

            $auth = $serviceLocator->get('zfcuser_auth_service');
            if ($auth->hasIdentity()) {
                $currentOrg = $auth->getIdentity()->getCurrentAcc();
                $currentCom = $auth->getIdentity()->getCurrentCom();
                if (!empty($currentCom)) {
                    $currentCom = $comModel->getCompany($currentCom);
                    $comName = $currentCom['property'] . ' ' . $currentCom['name'];
                } else {
                    $comName = '';
                }
                if (!empty($currentOrg)) {
                    $currentOrg = $accModel->getAccount($currentOrg);
                    $orgName = $currentOrg['name'];
                } else {
                    $orgName = '';
                }
                $userId = $auth->getIdentity()->getId();
                $acc = $comUserModel->getOrgWenUserConsist($userId);

                $accComArray = $comUserModel->addCompanyInOrgWhenConsist($acc, $userId);

                $pages = array();

                foreach ($accComArray as $accCom) {
                    foreach ($accCom['acc'] as $key => $value) {
                        array_push(
                            $pages,
                            array(
                                'label' => 'Аккаунт - ' . $value,
                                'route' => 'account',
                                'group' => 'right',
                                'params' => array('id' => $key, 'param' => 'acc'),
                                'action' => 'setAccAndCom',
                                'resource' => 'route/account',
                            )
                        );
                    }
                    foreach ($accCom['com'] as $key => $value) {
                        array_push(
                            $pages,
                            array(
                                'label' => '-- ' . $value,
                                'route' => 'account',
                                'group' => 'right',
                                'params' => array('id' => $key, 'param' => 'com'),
                                'action' => 'setAccAndCom',
                                'resource' => 'route/account',
                            )
                        );
                    }
                }
                array_push(
                    $pages,
                    array(
                        'label' => 'Создать аккаунт',
                        'route' => 'account',
                        'action' => 'addAccount',
                        'params' => array('id' => null),
                        'resource' => 'route/account',
                    )
                );
                $setAccArray = array(
                    'route' => 'dashboard',
                    'group' => 'right',
                    'resource' => 'route/dashboard',
                    'pages' => $pages
                );

                if (empty($comName) && empty($orgName)) {
                    $setAccArray['label'] = 'Выберите аккаунт/компанию';
                } else {
                    $setAccArray['label'] = $orgName . ' / ' . $comName;
                }
            } else {
                $setAccArray = null;
            }

            $configuration['navigation'][$nameMenu] = array(
                array(
                    'label' => 'Главная',
                    'route' => 'dashboard',
                    'group' => 'left',
                    'resource' => 'route/dashboard',
                ),
                array(
                    'label' => 'Админка',
                    'route' => 'zfcadmin',
                    'group' => 'left',
                    'resource' => 'route/zfcadmin',
                ),
                array(
                    'label' => 'Регистрация',
                    'route' => 'zfcuser/register',
                    'group' => 'right',
                    'resource' => 'route/zfcuser/register',
                ),
                $setAccArray
            ,
                array(
                    'label' => '##USERNAME##',
                    'route' => 'zfcuser',
                    'group' => 'right',
                    'resource' => 'route/zfcuser',
                    'pages' => array(
                        array(
                            'label' => 'Профиль',
                            'route' => 'zfcuser',
                            'group' => 'right',
                            'resource' => 'route/zfcuser',
                        ),
                        array(
                            'label' => 'Аккаунты',
                            'route' => 'account',
                            'group' => 'right',
                            'params' => array('id' => null),
                            'resource' => 'route/account',
                        ),
                        array(
                            'label' => '',
                            'type' => 'uri',
                            'uri' => '',
                            'class' => 'divider',
                            'resource' => 'route/zfcuser/logout',
                        ),
                        array(
                            'label' => 'Выйти',
                            'route' => 'zfcuser/logout',
                            'resource' => 'route/zfcuser/logout',
                        ),
                    ),
                ),
                array(
                    'label' => 'Войти',
                    'route' => 'zfcuser/login',
                    'group' => 'right',
                    'class' => 'js',
                    'data-event' => "click",
                    'data-handler' => "App.Login.dialog",
                    'data-content' => "#login-form",
                    'icon' => 'icon-off',
                    'resource' => 'route/zfcuser/login',
                ),
            );
            if (!isset($configuration['navigation'])) {
                throw new Exception\InvalidArgumentException('Could not find navigation configuration key');
            }
            if (!isset($configuration['navigation'][$nameMenu])) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Failed to find a navigation container by the name "%s"',
                    $this->getName()
                ));
            }

            $application = $serviceLocator->get('Application');
            $routeMatch = $application->getMvcEvent()->getRouteMatch();
            $router = $application->getMvcEvent()->getRouter();
            $pages = $this->getPagesFromConfig($configuration['navigation'][$nameMenu]);

            $this->pages = $this->injectComponents($pages, $routeMatch, $router);
        }

        return $this->pages;
    }

    protected function getName()
    {
        return 'top-menu';
    }


}
