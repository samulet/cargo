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
            $vhm = $serviceLocator->get('viewhelpermanager');
            $url = $vhm->get('url');
            $pages=array();
            foreach($list as $lName =>$l) {
                array_push($pages,array(
                        'label' => $l['fieldRusName'],
                        'route' => 'addList',
                        'action' => 'my-fields',
                        'params' => array('id' => $lName),
                        'resource'   => 'route/addList',)
                );
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
