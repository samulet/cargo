<?php
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $this->registerShutdownFunction($e->getApplication());
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        /** @var \Zend\Authentication\AuthenticationService $auth */
        $auth = $e->getApplication()->getServiceManager()->get('zfcuser_auth_service');
        if ($auth->hasIdentity()) {
            /** @var \RollbarNotifier $rollbar */
            $rollbar = $e->getApplication()->getServiceManager()->get('RollbarNotifier');
            $identity = $auth->getIdentity();
            $rollbar->person = array(
                'id' => $identity->getId(),
                'email' => $identity->getEmail(),
                'username' => $identity->getDisplayName()
            );
        }
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
    private function registerShutdownFunction(ApplicationInterface $application)
    {
        register_shutdown_function(function() use ($application) {
            /** @var \Zend\Log\Logger $logger */
            $logger = $application->getServiceManager()->get('Application\Logger');
            $error = error_get_last();
            if ($error) {
                $logger->emerg($error['message'], $error);
            }
        });
    }
}
