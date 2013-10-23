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

        $vhm = $serviceLocator->get('viewhelpermanager');
        $url = $vhm->get('url');

    }
}
