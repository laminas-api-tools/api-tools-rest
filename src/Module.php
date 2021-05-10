<?php

namespace Laminas\ApiTools\Rest;

use Laminas\Loader\StandardAutoloader;
use Laminas\Mvc\MvcEvent;

/**
 * Laminas module
 */
class Module
{
    /**
     * Retrieve module configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Bootstrap listener
     *
     * Attaches a listener to the RestController dispatch event.
     *
     * @param  MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app      = $e->getTarget();
        $services = $app->getServiceManager();
        $events   = $app->getEventManager();

        $services->get('Laminas\ApiTools\Rest\OptionsListener')->attach($events);

        $sharedEvents = $events->getSharedManager();
        $services->get('Laminas\ApiTools\Rest\RestParametersListener')->attachShared($sharedEvents);
    }
}
