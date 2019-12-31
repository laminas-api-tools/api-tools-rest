<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Rest\Factory;

use Laminas\ApiTools\Rest\Factory\RestControllerFactory;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

class RestControllerFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->services    = $services    = new ServiceManager();
        $this->controllers = $controllers = new ControllerManager($this->services);
        $this->factory     = $factory     = new RestControllerFactory();

        $controllers->addAbstractFactory($factory);
        $controllers->setServiceLocator($services);

        $services->setService('Laminas\ServiceManager\ServiceLocatorInterface', $services);
        $services->setService('Config', $this->getConfig());
        $services->setService('ControllerLoader', $controllers);
        $services->setFactory('ControllerPluginManager', 'Laminas\Mvc\Service\ControllerPluginManagerFactory');
        $services->setInvokableClass('EventManager', 'Laminas\EventManager\EventManager');
        $services->setInvokableClass('SharedEventManager', 'Laminas\EventManager\SharedEventManager');
        $services->setShared('EventManager', false);
    }

    public function getConfig()
    {
        return [
            'api-tools-rest' => [
                'ApiController' => [
                    'listener'   => 'LaminasTest\ApiTools\Rest\Factory\TestAsset\Listener',
                    'route_name' => 'api',
                ],
            ],
        ];
    }

    public function testWillInstantiateListenerIfServiceNotFoundButClassExists()
    {
        $this->assertTrue($this->controllers->has('ApiController'));
        $controller = $this->controllers->get('ApiController');
        $this->assertInstanceOf('Laminas\ApiTools\Rest\RestController', $controller);
    }

    public function testWillInstantiateAlternateRestControllerWhenSpecified()
    {
        $config = $this->services->get('Config');
        $config['api-tools-rest']['ApiController']['controller_class'] = 'LaminasTest\ApiTools\Rest\Factory\TestAsset\CustomController';
        $this->services->setAllowOverride(true);
        $this->services->setService('Config', $config);
        $controller = $this->controllers->get('ApiController');
        $this->assertInstanceOf('LaminasTest\ApiTools\Rest\Factory\TestAsset\CustomController', $controller);
    }

    public function testDefaultControllerEventManagerIdentifiersAreAsExpected()
    {
        $controller = $this->controllers->get('ApiController');
        $events = $controller->getEventManager();

        $identifiers = $events->getIdentifiers();

        $this->assertContains('Laminas\ApiTools\Rest\RestController', $identifiers);
        $this->assertContains('ApiController', $identifiers);
    }

    public function testControllerEventManagerIdentifiersAreAsSpecified()
    {
        $config = $this->services->get('Config');
        $config['api-tools-rest']['ApiController']['identifier'] = 'LaminasTest\ApiTools\Rest\Factory\TestAsset\ExtraControllerListener';
        $this->services->setAllowOverride(true);
        $this->services->setService('Config', $config);

        $controller = $this->controllers->get('ApiController');
        $events = $controller->getEventManager();

        $identifiers = $events->getIdentifiers();

        $this->assertContains('Laminas\ApiTools\Rest\RestController', $identifiers);
        $this->assertContains('LaminasTest\ApiTools\Rest\Factory\TestAsset\ExtraControllerListener', $identifiers);
    }

    public function testDefaultResourceEventManagerIdentifiersAreAsExpected()
    {
        $controller = $this->controllers->get('ApiController');
        $resource = $controller->getResource();
        $events = $resource->getEventManager();

        $expected = [
            'LaminasTest\ApiTools\Rest\Factory\TestAsset\Listener',
            'Laminas\ApiTools\Rest\Resource',
            'Laminas\ApiTools\Rest\ResourceInterface',
        ];
        $identifiers = $events->getIdentifiers();

        $this->assertEquals(array_values($expected), array_values($identifiers));
    }

    public function testResourceEventManagerIdentifiersAreAsSpecifiedString()
    {
        $config = $this->services->get('Config');
        $config['api-tools-rest']['ApiController']['resource_identifiers'] =
            'LaminasTest\ApiTools\Rest\Factory\TestAsset\ExtraResourceListener';
        $this->services->setAllowOverride(true);
        $this->services->setService('Config', $config);

        $controller = $this->controllers->get('ApiController');
        $resource = $controller->getResource();
        $events = $resource->getEventManager();

        $expected = [
            'LaminasTest\ApiTools\Rest\Factory\TestAsset\Listener',
            'LaminasTest\ApiTools\Rest\Factory\TestAsset\ExtraResourceListener',
            'Laminas\ApiTools\Rest\Resource',
            'Laminas\ApiTools\Rest\ResourceInterface',
        ];
        $identifiers = $events->getIdentifiers();

        $this->assertEquals(array_values($expected), array_values($identifiers));
    }

    public function testResourceEventManagerIdentifiersAreAsSpecifiedArray()
    {
        $config = $this->services->get('Config');
        $config['api-tools-rest']['ApiController']['resource_identifiers'] = [
            'LaminasTest\ApiTools\Rest\Factory\TestAsset\ExtraResourceListener1',
            'LaminasTest\ApiTools\Rest\Factory\TestAsset\ExtraResourceListener2',
        ];
        $this->services->setAllowOverride(true);
        $this->services->setService('Config', $config);

        $controller = $this->controllers->get('ApiController');
        $resource = $controller->getResource();
        $events = $resource->getEventManager();

        $expected = [
            'LaminasTest\ApiTools\Rest\Factory\TestAsset\Listener',
            'LaminasTest\ApiTools\Rest\Factory\TestAsset\ExtraResourceListener1',
            'LaminasTest\ApiTools\Rest\Factory\TestAsset\ExtraResourceListener2',
            'Laminas\ApiTools\Rest\Resource',
            'Laminas\ApiTools\Rest\ResourceInterface',
        ];
        $identifiers = $events->getIdentifiers();

        $this->assertEquals(array_values($expected), array_values($identifiers));
    }
}
