<?php
namespace Application;

use Application\Service\ErrorHandling as ErrorHandlingService;
use Zend\Mvc\ApplicationInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $this->registerShutdownFunction($e->getApplication());
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager = $e->getApplication()->getEventManager();
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

        $eventManager->attach(
            'dispatch.error',
            function ($event) {
                $exception = $event->getResult()->exception;
                if ($exception) {
                    $sm = $event->getApplication()->getServiceManager();
                    $service = $sm->get('Application\Service\ErrorHandling');
                    $service->logException($exception);
                }
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

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Service\ErrorHandling' => function ($sm) {
                    $logger = $sm->get('Application\Logger');
                    $service = new ErrorHandlingService($logger);
                    return $service;
                },
            ),
        );
    }

    private function registerShutdownFunction(ApplicationInterface $application)
    {
        register_shutdown_function(
            function () use ($application) {
                /** @var \Zend\Log\Logger $logger */
                $logger = $application->getServiceManager()->get('Application\Logger');
                $error = error_get_last();
                if ($error) {
                    $logger->emerg($error['message'], $error);

                    $errorPage = __DIR__ . '/../../public/500.html';
                    if (file_exists($errorPage)) {
                        readfile($errorPage);
                    }
                }
            }
        );
    }
}
