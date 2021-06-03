<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Rest\Listener;

use Laminas\ApiTools\Rest\Listener\RestParametersListener;
use Laminas\ApiTools\Rest\Resource;
use Laminas\ApiTools\Rest\RestController;
use Laminas\EventManager\SharedEventManager;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Router\RouteMatch as V2RouteMatch;
use Laminas\Router\RouteMatch;
use Laminas\Stdlib\Parameters;
use LaminasTest\ApiTools\Rest\RouteMatchFactoryTrait;
use PHPUnit\Framework\TestCase;

use function method_exists;

class RestParametersListenerTest extends TestCase
{
    use RouteMatchFactoryTrait;

    /** @var Resource */
    private $resource;

    /** @var RestController */
    private $controller;

    /** @var RouteMatch|V2RouteMatch */
    private $matches;

    /** @var Parameters */
    private $query;

    /** @var Request */
    private $request;

    /** @var MvcEvent */
    private $event;

    /** @var RestParametersListener */
    private $listener;

    public function setUp()
    {
        $this->resource   = $resource   = new Resource();
        $this->controller = $controller = new RestController();
        $controller->setResource($resource);

        $this->matches = $matches    = $this->createRouteMatch([]);
        $this->query   = $query      = new Parameters();
        $this->request = $request    = new Request();
        $request->setQuery($query);

        $this->event = new MvcEvent();
        $this->event->setTarget($controller);
        $this->event->setRouteMatch($matches);
        $this->event->setRequest($request);

        $this->listener = new RestParametersListener();
    }

    public function testIgnoresNonRestControllers()
    {
        $controller = $this->createMock(AbstractRestfulController::class);
        $this->event->setTarget($controller);
        $this->listener->onDispatch($this->event);
        $this->assertNull($this->resource->getRouteMatch());
        $this->assertNull($this->resource->getQueryParams());
    }

    public function testInjectsRouteMatchOnDispatchOfRestController()
    {
        $this->listener->onDispatch($this->event);
        $this->assertSame($this->matches, $this->resource->getRouteMatch());
    }

    public function testInjectsQueryParamsOnDispatchOfRestController()
    {
        $this->listener->onDispatch($this->event);
        $this->assertSame($this->query, $this->resource->getQueryParams());
    }

    public function testAttachSharedAttachOneListenerOnEventDispatch()
    {
        $sharedEventManager = new SharedEventManager();
        $this->listener->attachShared($sharedEventManager);

        // Vary identifiers based on laminas-eventmanager version
        $identifiers = method_exists($sharedEventManager, 'getEvents')
            ? RestController::class
            : [RestController::class];
        $listeners   = $sharedEventManager->getListeners($identifiers, MvcEvent::EVENT_DISPATCH);

        $this->assertCount(1, $listeners);
    }

    public function testDetachSharedDetachAttachedListener()
    {
        $sharedEventManager = new SharedEventManager();
        $this->listener->attachShared($sharedEventManager);

        $this->listener->detachShared($sharedEventManager);

        // Vary identifiers based on laminas-eventmanager version
        $identifiers = method_exists($sharedEventManager, 'getEvents')
            ? RestController::class
            : [RestController::class];
        $listeners   = $sharedEventManager->getListeners($identifiers, MvcEvent::EVENT_DISPATCH);

        $this->assertCount(0, $listeners);
    }
}
