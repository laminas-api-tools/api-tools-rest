<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Rest\Listener;

use Laminas\ApiTools\Rest\Listener\OptionsListener;
use Laminas\EventManager\EventManager;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Router\RouteMatch;
use Laminas\Stdlib\Request as StdlibRequest;
use PHPUnit_Framework_TestCase as TestCase;

class OptionsListenerTest extends TestCase
{
    public function testListenerRegistersAtExpectedPriority()
    {
        $listener = new OptionsListener(array());
        $events   = new EventManager();
        $listener->attach($events);
        $listeners = $events->getListeners('route');
        $this->assertEquals(1, count($listeners));
        foreach ($listeners as $test) {
            break;
        }
        $this->assertInstanceOf('Laminas\Stdlib\CallbackHandler', $test);
        $this->assertEquals(array($listener, 'onRoute'), $test->getCallback());
        $this->assertEquals(-100, $test->getMetadatum('priority'));
    }

    public function seedListenerConfig()
    {
        return array(
            'controller-without-config' => array(),
            'controller-with-entity-config' => array(
                'route_identifier_name' => 'entity_id',
                'entity_http_methods' => array(
                    'GET',
                    'PATCH',
                    'DELETE',
                ),
            ),
            'controller-with-collection-config' => array(
                'collection_http_methods' => array(
                    'GET',
                    'POST',
                ),
            ),
            'controller-with-all-config' => array(
                'route_identifier_name' => 'entity_id',
                'entity_http_methods' => array(
                    'GET',
                    'PATCH',
                    'DELETE',
                ),
                'collection_http_methods' => array(
                    'GET',
                    'POST',
                ),
            ),
            'controller-with-all-config-except-entity-id' => array(
                'entity_http_methods' => array(
                    'GET',
                    'PATCH',
                    'DELETE',
                ),
                'collection_http_methods' => array(
                    'GET',
                    'POST',
                ),
            ),
        );
    }

    public function validMethodsProvider()
    {
        return array(
            'collection-get' => array('GET', array(
                'controller' => 'controller-with-collection-config',
            )),
            'collection-post' => array('POST', array(
                'controller' => 'controller-with-collection-config',
            )),
            'entity-get' => array('GET', array(
                'controller' => 'controller-with-entity-config',
                'entity_id'  => 'foo',
            )),
            'entity-patch' => array('PATCH', array(
                'controller' => 'controller-with-entity-config',
                'entity_id'  => 'foo',
            )),
            'entity-delete' => array('DELETE', array(
                'controller' => 'controller-with-entity-config',
                'entity_id'  => 'foo',
            )),
            'all-collection-get' => array('GET', array(
                'controller' => 'controller-with-all-config',
            )),
            'all-collection-post' => array('POST', array(
                'controller' => 'controller-with-all-config',
            )),
            'all-entity-get' => array('GET', array(
                'controller' => 'controller-with-all-config',
                'entity_id'  => 'foo',
            )),
            'all-entity-patch' => array('PATCH', array(
                'controller' => 'controller-with-all-config',
                'entity_id'  => 'foo',
            )),
            'all-entity-delete' => array('DELETE', array(
                'controller' => 'controller-with-all-config',
                'entity_id'  => 'foo',
            )),
            'all-except-collection-get' => array('GET', array(
                'controller' => 'controller-with-all-config-except-entity-id',
            )),
            'all-except-collection-post' => array('POST', array(
                'controller' => 'controller-with-all-config-except-entity-id',
            )),
        );
    }

    public function invalidMethodsProvider()
    {
        return array(
            'collection-patch' => array('PATCH', array(
                'controller' => 'controller-with-collection-config',
            ), array('GET', 'POST')),
            'collection-put' => array('PUT', array(
                'controller' => 'controller-with-collection-config',
            ), array('GET', 'POST')),
            'collection-delete' => array('DELETE', array(
                'controller' => 'controller-with-collection-config',
            ), array('GET', 'POST')),
            'entity-post' => array('POST', array(
                'controller' => 'controller-with-entity-config',
                'entity_id'  => 'foo',
            ), array('GET', 'PATCH', 'DELETE')),
            'entity-put' => array('PUT', array(
                'controller' => 'controller-with-entity-config',
                'entity_id'  => 'foo',
            ), array('GET', 'PATCH', 'DELETE')),
            'all-collection-patch' => array('PATCH', array(
                'controller' => 'controller-with-all-config',
            ), array('GET', 'POST')),
            'all-collection-put' => array('PUT', array(
                'controller' => 'controller-with-all-config',
            ), array('GET', 'POST')),
            'all-collection-delete' => array('DELETE', array(
                'controller' => 'controller-with-all-config',
            ), array('GET', 'POST')),
            'all-entity-post' => array('POST', array(
                'controller' => 'controller-with-all-config',
                'entity_id'  => 'foo',
            ), array('GET', 'PATCH', 'DELETE')),
            'all-entity-put' => array('PUT', array(
                'controller' => 'controller-with-all-config',
                'entity_id'  => 'foo',
            ), array('GET', 'PATCH', 'DELETE')),
            'except-collection-patch' => array('PATCH', array(
                'controller' => 'controller-with-all-config-except-entity-id',
            ), array('GET', 'POST')),
            'except-collection-put' => array('PUT', array(
                'controller' => 'controller-with-all-config-except-entity-id',
            ), array('GET', 'POST')),
            'except-collection-delete' => array('DELETE', array(
                'controller' => 'controller-with-all-config-except-entity-id',
            ), array('GET', 'POST')),
        );
    }

    /**
     * @dataProvider validMethodsProvider
     */
    public function testListenerReturnsNullWhenMethodIsAllowedForCurrentRequest($method, $matchParams)
    {
        $listener = new OptionsListener($this->seedListenerConfig());
        $request  = new Request();
        $request->setMethod($method);
        $matches  = new RouteMatch($matchParams);
        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);
        $mvcEvent->setRouteMatch($matches);
        $mvcEvent->setResponse(new Response());

        $this->assertNull($listener->onRoute($mvcEvent));
    }

    /**
     * @dataProvider invalidMethodsProvider
     */
    public function testListenerReturnsNullIfNotAnHttpRequest($method, $matchParams)
    {
        $listener = new OptionsListener($this->seedListenerConfig());
        $request  = new StdlibRequest();
        $matches  = new RouteMatch($matchParams);
        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);
        $mvcEvent->setRouteMatch($matches);
        $mvcEvent->setResponse(new Response());

        $this->assertNull($listener->onRoute($mvcEvent));
    }

    /**
     * @dataProvider invalidMethodsProvider
     */
    public function testListenerReturnsNullIfNoRouteMatches($method, $matchParams, $expectedAllow)
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
        $matches  = new RouteMatch(array());
        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);
        $mvcEvent->setResponse(new Response());

        $this->assertNull($listener->onRoute($mvcEvent));
    }

    public function testListenerReturnsNullIfMatchingControllerInRouteMatchesButNoConfigForController()
    {
        $listener = new OptionsListener($this->seedListenerConfig());
        $request  = new Request('GET');
        $matches  = new RouteMatch(array(
            'controller' => 'controller-without-config',
        ));
        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);

        $this->assertNull($listener->onRoute($mvcEvent));
    }

    /**
     * @dataProvider invalidMethodsProvider
     */
    public function testListenerReturns405ResponseWithAllowHeaderForInvalidRequestMethod(
        $method,
        $matchParams,
        $expectedAllow
    ) {
        $listener = new OptionsListener($this->seedListenerConfig());
        $request  = new Request();
        $request->setMethod($method);
        $matches  = new RouteMatch($matchParams);
        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($request);
        $mvcEvent->setRouteMatch($matches);
        $mvcEvent->setResponse(new Response());

        $result = $listener->onRoute($mvcEvent);
        $this->assertInstanceOf('Laminas\Http\Response', $result);
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
