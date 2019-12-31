<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Rest\Listener;

use Laminas\ApiTools\Rest\RestController;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Mvc\MvcEvent;

class RestParametersListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var \Laminas\Stdlib\CallbackHandler[]
     */
    protected $sharedListeners = [];

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $listener = $events->attach(
            RestController::class,
            MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            100
        );

        if (! $listener) {
            $listener = [$this, 'onDispatch'];
        }

        $this->sharedListeners[] = $listener;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        $eventManagerVersion = method_exists($events, 'getEvents') ? 2 : 3;
        foreach ($this->sharedListeners as $index => $listener) {
            switch ($eventManagerVersion) {
                case 2:
                    if ($events->detach(RestController::class, $listener)) {
                        unset($this->sharedListeners[$index]);
                    }
                    break;
                case 3:
                    if ($events->detach($listener, RestController::class, MvcEvent::EVENT_DISPATCH)) {
                        unset($this->sharedListeners[$index]);
                    }
                    break;
            }
        }
    }

    /**
     * Listen to the dispatch event
     *
     * @param MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        $controller = $e->getTarget();
        if (! $controller instanceof RestController) {
            return;
        }

        $request  = $e->getRequest();
        $query    = $request->getQuery();
        $matches  = $e->getRouteMatch();
        $resource = $controller->getResource();
        $resource->setQueryParams($query);
        $resource->setRouteMatch($matches);
    }
}
