<?php
namespace Application\Factory;

use Zend\Log\Exception\InvalidArgumentException;
use Zend\Log\Logger;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Logger.
 */
class LoggerServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // Configure the logger
        $config = $serviceLocator->get('Config');
        $options = isset($config['log']) ? $config['log'] : array();
        $logger = new Logger();
        if (is_array($options)) {
            if (isset($options['writers']) && is_array($options['writers'])) {
                foreach ($options['writers'] as $writer) {

                    if (!isset($writer['name'])) {
                        throw new InvalidArgumentException('Options must contain a name for the writer');
                    }

                    $priority = (isset($writer['priority'])) ? $writer['priority'] : null;
                    $writerOptions = (isset($writer['options'])) ? $writer['options'] : null;

                    if ($serviceLocator->has($writer['name'])) {
                        $writer = $serviceLocator->get($writer['name']);
                        $logger->addWriter($writer, $priority, $writerOptions);
                    } else {
                        $logger->addWriter($writer['name'], $priority, $writerOptions);
                    }
                }
            }

            if (isset($options['exceptionhandler']) && $options['exceptionhandler'] === true) {
                Logger::registerExceptionHandler($logger);
            }

            if (isset($options['errorhandler']) && $options['errorhandler'] === true) {
                Logger::registerErrorHandler($logger);
            }

        }
        return $logger;
    }
}
