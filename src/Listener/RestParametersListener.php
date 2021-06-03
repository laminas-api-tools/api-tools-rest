<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Rest\Listener;

use Laminas\ApiTools\Rest\RestController;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\CallbackHandler;

use function method_exists;

class RestParametersListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /** @var CallbackHandler[] */
    protected $sharedListeners = [];

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
    }

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
