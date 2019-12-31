<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Rest\Factory;

use Laminas\ApiTools\Rest\Factory\RestControllerFactory;
use Laminas\ApiTools\Rest\Resource;
use Laminas\ApiTools\Rest\ResourceInterface;
use Laminas\ApiTools\Rest\RestController;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\SharedEventManager;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\Mvc\Service\ControllerPluginManagerFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class RestControllerFactoryTest extends TestCase
{
    /** @var ServiceManager */
    private $services;

    /** @var ControllerManager */
    private $controllers;

    /** @var RestControllerFactory */
    private $factory;

    public function setUp()
    {
        $this->services    = $services    = new ServiceManager();
        $this->controllers = $controllers = new ControllerManager($this->services);
        $this->factory     = $factory     = new RestControllerFactory();

        $controllers->addAbstractFactory($factory);

        $services->setService(ServiceLocatorInterface::class, $services);
        $services->setService('config', $this->getConfig());
        $services->setService('ControllerManager', $controllers);
        $services->setFactory('ControllerPluginManager', ControllerPluginManagerFactory::class);
        $services->setInvokableClass('EventManager', EventManager::class);
        $services->setInvokableClass('SharedEventManager', SharedEventManager::class);
        $services->setShared('EventManager', false);
    }

    public function getConfig()
    {
        return [
            'api-tools-rest' => [
                'ApiController' => [
                    'listener'   => TestAsset\Listener::class,
                    'route_name' => 'api',
                ],
            ],
        ];
    }

    public function testWillInstantiateListenerIfServiceNotFoundButClassExists()
    {
        $this->assertTrue($this->controllers->has('ApiController'));
        $controller = $this->controllers->get('ApiController');
        $this->assertInstanceOf(RestController::class, $controller);
    }

    public function testWillInstantiateAlternateRestControllerWhenSpecified()
    {
        $config = $this->services->get('config');
        $config['api-tools-rest']['ApiController']['controller_class'] = TestAsset\CustomController::class;
        $this->services->setAllowOverride(true);
        $this->services->setService('config', $config);
        $controller = $this->controllers->get('ApiController');
        $this->assertInstanceOf(TestAsset\CustomController::class, $controller);
    }

    public function testDefaultControllerEventManagerIdentifiersAreAsExpected()
    {
        $controller = $this->controllers->get('ApiController');
        $events = $controller->getEventManager();

        $identifiers = $events->getIdentifiers();

        $this->assertContains(RestController::class, $identifiers);
        $this->assertContains('ApiController', $identifiers);
    }

    public function testControllerEventManagerIdentifiersAreAsSpecified()
    {
        $config = $this->services->get('config');
        $config['api-tools-rest']['ApiController']['identifier'] = TestAsset\ExtraControllerListener::class;
        $this->services->setAllowOverride(true);
        $this->services->setService('config', $config);

        $controller = $this->controllers->get('ApiController');
        $events = $controller->getEventManager();

        $identifiers = $events->getIdentifiers();

        $this->assertContains(RestController::class, $identifiers);
        $this->assertContains(TestAsset\ExtraControllerListener::class, $identifiers);
    }

    public function testDefaultResourceEventManagerIdentifiersAreAsExpected()
    {
        $controller = $this->controllers->get('ApiController');
        $resource = $controller->getResource();
        $events = $resource->getEventManager();

        $expected = [
            TestAsset\Listener::class,
            Resource::class,
            ResourceInterface::class,
        ];
        $identifiers = $events->getIdentifiers();

        $this->assertEquals(array_values($expected), array_values($identifiers));
    }

    public function testResourceEventManagerIdentifiersAreAsSpecifiedString()
    {
        $config = $this->services->get('config');
        $config['api-tools-rest']['ApiController']['resource_identifiers'] = TestAsset\ExtraResourceListener::class;
        $this->services->setAllowOverride(true);
        $this->services->setService('config', $config);

        $controller = $this->controllers->get('ApiController');
        $resource = $controller->getResource();
        $events = $resource->getEventManager();

        $expected = [
            TestAsset\Listener::class,
            TestAsset\ExtraResourceListener::class,
            Resource::class,
            ResourceInterface::class,
        ];
        $identifiers = $events->getIdentifiers();

        $this->assertEquals(array_values($expected), array_values($identifiers));
    }

    public function testResourceEventManagerIdentifiersAreAsSpecifiedArray()
    {
        $config = $this->services->get('config');
        $config['api-tools-rest']['ApiController']['resource_identifiers'] = [
            TestAsset\ExtraResourceListener1::class,
            TestAsset\ExtraResourceListener2::class,
        ];
        $this->services->setAllowOverride(true);
        $this->services->setService('config', $config);

        $controller = $this->controllers->get('ApiController');
        $resource = $controller->getResource();
        $events = $resource->getEventManager();

        $expected = [
            TestAsset\Listener::class,
            TestAsset\ExtraResourceListener1::class,
            TestAsset\ExtraResourceListener2::class,
            Resource::class,
            ResourceInterface::class,
        ];
        $identifiers = $events->getIdentifiers();

        $this->assertEquals(array_values($expected), array_values($identifiers));
    }
}
