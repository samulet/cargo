<?php
namespace Application\Navigation\Service;

use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Exception;
class ListNavigationFactory extends DefaultNavigationFactory
{
    /**
     * @{inheritdoc}
     */
    protected function getName()
    {
        return 'list';
    }

    protected function getPages(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->pages) {
            $nameMenu=$this->getName();
            $addListModel = $serviceLocator->get('AddList\Model\AddListModel');
            $list=$addListModel->getListName();

            $match = $serviceLocator->get('application')
                ->getMvcEvent()
                ->getRouteMatch();

            $paramId=$match->getParam('id');

            $auth = $serviceLocator->get('zfcuser_auth_service');
            $currentOrg = $auth->getIdentity()->getCurrentOrg();
            $authorize = $serviceLocator->get('BjyAuthorize\Provider\Identity\ProviderInterface');
            $roles = $authorize->getIdentityRoles();
            $listDataName=$addListModel->getOneList($paramId);
            if(!empty($listDataName['listId'])) {
                $listId=$listDataName['listId'];
            } else {
                $listId=null;
            }


            $pages=array();
            foreach($list as $lName =>$l) {
                array_push($pages,array(
                        'label' => $l['fieldRusName'],
                        'route' => 'addList',
                        'action' => 'my-fields',
                        'params' => array('id' => $lName),
                        'resource'   => 'route/addList',)
                );
                if( ($paramId==$lName) || ($listId==$lName) ) {
                    if(array_search("admin",$roles,true)) {
                        $listFields=$addListModel->getListAdmin($lName)['field'];
                    } else {
                        $listFields=$addListModel->getList($lName,$currentOrg)['field'];
                    }
                    foreach($listFields as $fi) {
                    $fi=$fi['it'];

                    //die(var_dump($fi));
                        array_push($pages,array(
                                'label' => '- '.$fi['value'],
                                'route' => 'addList',
                                'action' => 'edit',
                                'params' => array('id' => $fi['uuid']),
                                'resource'   => 'route/addList',)
                        );
                    }
                }
                if(!empty($l['child'])) {
                    foreach($l['child'] as $lChildName =>$lChild) {
                        array_push($pages,array(
                                'label' => '-- '.$lChild['fieldRusName'],
                                'route' => 'addList',
                                'action' => 'my-fields',
                                'params' => array('id' => $lChildName),
                                'resource'   => 'route/addList',)
                        );
                    }
                }
            }
            $configuration['navigation'][$nameMenu]=$pages;
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
}
