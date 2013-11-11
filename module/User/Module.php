<?php
namespace User;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();

        $zfcServiceEvents = $e->getApplication()->getServiceManager()->get('zfcuser_user_service')->getEventManager();
        $zfcServiceEvents->attach(
            'register',
            function ($e) {
                /* @var $user \User\Entity\User */
                $user = $e->getParam('user');
                $user->addRole('user');
            }
        );
        $scnServiceEvents = $e->getApplication()->getServiceManager()->get('ScnSocialAuth\Authentication\Adapter\HybridAuth')->getEventManager();
        $scnServiceEvents->attach(
            'register.post',
            function ($e) {
                /* @var $user \User\Entity\User */
                $user = $e->getParam('user');
                $user->addRole('user');
            }
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
