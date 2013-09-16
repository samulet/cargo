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
            //FETCH data from table menu :
//die(var_dump($serviceLocator->get('navigation')));
            $nameMenu=$this->getName();

    /*     $fetchMenu = $serviceLocator->get('menu')->fetchAll();

            $configuration['navigation'][$nameMenu] = array();
            foreach($fetchMenu as $key=>$row)
            {
                $configuration['navigation'][$nameMenu][$row['name']] = array(
                    'label' => $row['label'],
                    'route' => $row['route'],
                );
            }
    */
            $configuration['navigation'][$nameMenu] = array(
                array(
                    'label' => 'Главная',
                    'route' => 'dashboard',
                    'group' => 'left',
                    'resource'   => 'route/dashboard',
                ),
                array(
                    'label' => 'Админка',
                    'route' => 'zfcadmin',
                    'group' => 'left',
                    'resource'   => 'route/zfcadmin',
                ),
                array(
                    'label' => 'Регистрация',
                    'route' => 'zfcuser/register',
                    'group' => 'right',
                    'resource'   => 'route/zfcuser/register',
                ),
                array(
                    'label' => '##USERNAME##',
                    'route' => 'zfcuser',
                    'group' => 'right',
                    'resource'   => 'route/zfcuser',
                    'pages' => array(
                        array(
                            'label' => 'Профиль',
                            'route' => 'zfcuser',
                            'group' => 'right',
                            'resource'   => 'route/zfcuser',
                        ),
                        array(
                            'label' => 'Аккаунты',
                            'route' => 'account',
                            'group' => 'right',
                            'params' => array('id' => null),
                            'resource'   => 'route/account',
                        ),
                        array(
                            'label' => '',
                            'type' => 'uri',
                            'uri' => '',
                            'class' => 'divider',
                            'resource'   => 'route/zfcuser/logout',
                        ),
                        array(
                            'label' => 'Выйти',
                            'route' => 'zfcuser/logout',
                            'resource'   => 'route/zfcuser/logout',
                        ),
                    ),
                ),

                array(
                    'label' => 'Выберите аккаунт',
                    'route' => 'dashboard',
                    'group' => 'right',
                    'resource'   => 'route/dashboard',
                    'pages' => array(
                        array(
                            'label' => 'Профиль',
                            'route' => 'dashboard',
                            'group' => 'right',
                            'resource'   => 'route/zfcuser',
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
                    'resource'   => 'route/zfcuser/login',
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
            $routeMatch  = $application->getMvcEvent()->getRouteMatch();
            $router      = $application->getMvcEvent()->getRouter();
            $pages       = $this->getPagesFromConfig($configuration['navigation'][$nameMenu]);

            $this->pages = $this->injectComponents($pages, $routeMatch, $router);
        }

        return $this->pages;
    }
    protected function getName()
    {
        /*
         $pages = array( "top-menu" =>
                    array(
                        'label' => 'Главная2',
                        'route' => 'dashboard',
                        'group' => 'left',
                        'resource'   => 'route/dashboard',
                    ),
                    );

// add two pages
                    $container = new \Zend\Navigation\Navigation($pages);

                    $navigation->setContainer($container);
         */
        return 'top-menu';
    }
}
