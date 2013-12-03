<?php
namespace User;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $scnServiceEvents = $serviceManager->get('ScnSocialAuth\Authentication\Adapter\HybridAuth')->getEventManager();

        $scnServiceEvents->attach('registerViaProvider', function ($e) {
            /* @var $user \User\Entity\User */
            $user = $e->getParam('user');
            $user->addRole('user');
            $user->setUUID();
        });
        $scnServiceEvents->attach('register.post', function ($e) use ($serviceManager) {
            /** @var \ZfcUser\Options\ModuleOptions $zfcUserModuleOptions */
            $zfcUserModuleOptions = $serviceManager->get('zfcuser_module_options');
            $zfcUserModuleOptions->setLoginRedirectRoute('greetings');
        });
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
