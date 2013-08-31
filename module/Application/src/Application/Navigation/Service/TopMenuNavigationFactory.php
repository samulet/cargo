<?php
namespace Application\Navigation\Service;

use Zend\Navigation\Service\DefaultNavigationFactory;

class TopMenuNavigationFactory extends DefaultNavigationFactory
{
    /**
     * @{inheritdoc}
     */
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
