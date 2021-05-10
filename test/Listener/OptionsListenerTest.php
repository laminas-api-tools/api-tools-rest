<?php

namespace LaminasTest\ApiTools\Rest\Listener;

use Laminas\ApiTools\Rest\Listener\OptionsListener;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\Test\EventListenerIntrospectionTrait;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\Request as StdlibRequest;
use LaminasTest\ApiTools\Rest\RouteMatchFactoryTrait;
use PHPUnit\Framework\TestCase;

class OptionsListenerTest extends TestCase
{
    use EventListenerIntrospectionTrait;
    use RouteMatchFactoryTrait;

    public function testListenerRegistersAtExpectedPriority()
    {
        $listener = new OptionsListener([]);
        $events   = new EventManager();
        $listener->attach($events);

        $this->assertListenerAtPriority(
            [$listener, 'onRoute'],
            -100,
            'route',
            $events
        );
    }

    public function seedListenerConfig()
    {
        return [
            'controller-without-config' => [],
            'controller-with-entity-config' => [
                'route_identifier_name' => 'entity_id',
                'entity_http_methods' => [
                    'GET',
                    'PATCH',
                    'DELETE',
                ],
            ],
            'controller-with-collection-config' => [
                'collection_http_methods' => [
                    'GET',
                    'POST',
                ],
            ],
            'controller-with-all-config' => [
                'route_identifier_name' => 'entity_id',
                'entity_http_methods' => [
                    'GET',
                    'PATCH',
                    'DELETE',
                ],
                'collection_http_methods' => [
                    'GET',
                    'POST',
                ],
            ],
            'controller-with-all-config-except-entity-id' => [
                'entity_http_methods' => [
                    'GET',
                    'PATCH',
                    'DELETE',
                ],
                'collection_http_methods' => [
                    'GET',
                    'POST',
                ],
            ],
        ];
    }

    public function validMethodsProvider()
    {
        return [
            'collection-get' => ['GET', [
                'controller' => 'controller-with-collection-config',
            ]],
            'collection-post' => ['POST', [
                'controller' => 'controller-with-collection-config',
            ]],
            'entity-get' => ['GET', [
                'controller' => 'controller-with-entity-config',
                'entity_id'  => 'foo',
            ]],
            'entity-patch' => ['PATCH', [
                'controller' => 'controller-with-entity-config',
                'entity_id'  => 'foo',
            ]],
            'entity-delete' => ['DELETE', [
                'controller' => 'controller-with-entity-config',
                'entity_id'  => 'foo',
            ]],
            'all-collection-get' => ['GET', [
                'controller' => 'controller-with-all-config',
            ]],
            'all-collection-post' => ['POST', [
                'controller' => 'controller-with-all-config',
            ]],
            'all-entity-get' => ['GET', [
                'controller' => 'controller-with-all-config',
                'entity_id'  => 'foo',
            ]],
            'all-entity-patch' => ['PATCH', [
                'controller' => 'controller-with-all-config',
                'entity_id'  => 'foo',
            ]],
            'all-entity-delete' => ['DELETE', [
                'controller' => 'controller-with-all-config',
                'entity_id'  => 'foo',
            ]],
            'all-except-collection-get' => ['GET', [
                'controller' => 'controller-with-all-config-except-entity-id',
            ]],
            'all-except-collection-post' => ['POST', [
                'controller' => 'controller-with-all-config-except-entity-id',
            ]],
        ];
    }

    public function invalidMethodsProvider()
    {
        return [
            'collection-patch' => ['PATCH', [
                'controller' => 'controller-with-collection-config',
            ], ['GET', 'POST']],
            'collection-put' => ['PUT', [
                'controller' => 'controller-with-collection-config',
            ], ['GET', 'POST']],
            'collection-delete' => ['DELETE', [
                'controller' => 'controller-with-collection-config',
            ], ['GET', 'POST']],
            'entity-post' => ['POST', [
                'controller' => 'controller-with-entity-config',
                'entity_id'  => 'foo',
            ], ['GET', 'PATCH', 'DELETE']],
            'entity-put' => ['PUT', [
                'controller' => 'controller-with-entity-config',
                'entity_id'  => 'foo',
            ], ['GET', 'PATCH', 'DELETE']],
            'all-collection-patch' => ['PATCH', [
                'controller' => 'controller-with-all-config',
            ], ['GET', 'POST']],
            'all-collection-put' => ['PUT', [
                'controller' => 'controller-with-all-config',
            ], ['GET', 'POST']],
            'all-collection-delete' => ['DELETE', [
                'controller' => 'controller-with-all-config',
            ], ['GET', 'POST']],
            'all-entity-post' => ['POST', [
                'controller' => 'controller-with-all-config',
                'entity_id'  => 'foo',
            ], ['GET', 'PATCH', 'DELETE']],
            'all-entity-put' => ['PUT', [
                'controller' => 'controller-with-all-config',
                'entity_id'  => 'foo',
            ], ['GET', 'PATCH', 'DELETE']],
            'except-collection-patch' => ['PATCH', [
                'controller' => 'controller-with-all-config-except-entity-id',
            ], ['GET', 'POST']],
            'except-collection-put' => ['PUT', [
                'controller' => 'controller-with-all-config-except-entity-id',
            ], ['GET', 'POST']],
            'except-collection-delete' => ['DELETE', [
                'controller' => 'controller-with-all-config-except-entity-id',
            ], ['GET', 'POST']],
        ];
    }

    /**
     * @dataProvider validMethodsProvider
     *
     * @param string $method
     * @param array $matchParams
     */
    public function testListenerReturnsNullWhenMethodIsAllowedForCurrentRequest($method, array $matchParams)
    {
        $listener = new OptionsListener($this->seedListenerConfig());
        $request  = new Request();
        $request->setMethod($method);
        $matches  = $this->createRouteMatch($matchParams);
        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);
        $mvcEvent->setRouteMatch($matches);
        $mvcEvent->setResponse(new Response());

        $this->assertNull($listener->onRoute($mvcEvent));
    }

    /**
     * @dataProvider invalidMethodsProvider
     *
     * @param string $method
     * @param array $matchParams
     */
    public function testListenerReturnsNullIfNotAnHttpRequest($method, array $matchParams)
    {
        $listener = new OptionsListener($this->seedListenerConfig());
        $request  = new StdlibRequest();
        $matches  = $this->createRouteMatch($matchParams);
        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);
        $mvcEvent->setRouteMatch($matches);
        $mvcEvent->setResponse(new Response());

        $this->assertNull($listener->onRoute($mvcEvent));
    }

    /**
     * @dataProvider invalidMethodsProvider
     *
     * @param string $method
     */
    public function testListenerReturnsNullIfNoRouteMatches($method)
    {
        $listener = new OptionsListener($this->seedListenerConfig());
        $request  = new Request();
        $request->setMethod($method);
        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);
        $mvcEvent->setResponse(new Response());

        $this->assertNull($listener->onRoute($mvcEvent));
    }

    public function testListenerReturnsNullIfNoMatchingControllerInRouteMatches()
    {
        $listener = new OptionsListener($this->seedListenerConfig());
        $request  = new Request();
        $request->setMethod('GET');
        $matches  = $this->createRouteMatch([]);
        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);
        $mvcEvent->setResponse(new Response());

        $this->assertNull($listener->onRoute($mvcEvent));
    }

    public function testListenerReturnsNullIfMatchingControllerInRouteMatchesButNoConfigForController()
    {
        $listener = new OptionsListener($this->seedListenerConfig());
        $request  = new Request('GET');
        $matches  = $this->createRouteMatch([
            'controller' => 'controller-without-config',
        ]);
        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);

        $this->assertNull($listener->onRoute($mvcEvent));
    }

    /**
     * @dataProvider invalidMethodsProvider
     *
     * @param string $method
     * @param array $matchParams
     * @param array $expectedAllow
     */
    public function testListenerReturns405ResponseWithAllowHeaderForInvalidRequestMethod(
        $method,
        array $matchParams,
        array $expectedAllow
    ) {
        $listener = new OptionsListener($this->seedListenerConfig());
        $request  = new Request();
        $request->setMethod($method);
        $matches  = $this->createRouteMatch($matchParams);
        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);
        $mvcEvent->setRouteMatch($matches);
        $mvcEvent->setResponse(new Response());

        $result = $listener->onRoute($mvcEvent);
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(405, $result->getStatusCode());
        $headers = $result->getHeaders();
        $this->assertTrue($headers->has('Allow'));
        $allow = $headers->get('Allow');
        $allow = $allow->getFieldValue();
        $allow = explode(',', $allow);
        array_walk($allow, function (&$value) {
            $value = trim($value);
        });
        sort($allow);
        sort($expectedAllow);
        $this->assertEquals($expectedAllow, $allow);
    }
}
