<?php
namespace Application;

use Application\Service\ErrorHandling as ErrorHandlingService;
use Zend\Mvc\ApplicationInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use ZfcUser\Entity\UserInterface;
use Zend\View\HelperPluginManager;

class Module
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    public static $serviceManager;

    public function onBootstrap(MvcEvent $e)
    {
        self::$serviceManager = $e->getApplication()->getServiceManager();
        $this->registerShutdownFunction($e->getApplication());
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

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
                'topmenu_navigation' => 'Application\Navigation\Service\TopMenuNavigationFactory',
                'sidebar_navigation' => 'Application\Navigation\Service\SidebarNavigationFactory',
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                // This will overwrite the native navigation helper
                'navigation' => function (HelperPluginManager $pluginManager) {
                    /** @var \Zend\View\Helper\Navigation $navigation */
                    $navigation = $pluginManager->get('Zend\View\Helper\Navigation');

                    $serviceLocator = $pluginManager->getServiceLocator();
                    /* @var $authorize \BjyAuthorize\Service\Authorize */
                    $authorize = $serviceLocator->get('BjyAuthorize\Service\Authorize');

                    // Store ACL and role in the proxy helper:
                    $navigation->setAcl($authorize->getAcl())
                               ->setRole($authorize->getIdentity());

                    // Return the new navigation helper instance
                    return $navigation;
                }
            )
        );
    }

    public static function identityInfo()
    {
        if (!self::$serviceManager->has('zfcuser_auth_service')) {
            return null;
        }

        /** @var \Zend\Authentication\AuthenticationService $auth */
        $auth = self::$serviceManager->get('zfcuser_auth_service');
        if (!$auth->hasIdentity()) {
            return null;
        }

        $identity = $auth->getIdentity();
        if (!$identity instanceof UserInterface) {
            return null;
        }

        return array(
            'id' => $identity->getId(),
            'email' => $identity->getEmail(),
            'username' => $identity->getDisplayName()
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

                    if ('cli' !== php_sapi_name()) {
                        $errorPage = __DIR__ . '/../../public/500.html';
                        if (file_exists($errorPage)) {
                            readfile($errorPage);
                        }
                    }
                }
            }
        );
    }
}
