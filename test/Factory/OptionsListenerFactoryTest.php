<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Rest\Factory;

use Laminas\ApiTools\Rest\Factory\OptionsListenerFactory;
use Laminas\ApiTools\Rest\Listener\OptionsListener;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class OptionsListenerFactoryTest extends TestCase
{
    /** @var ServiceManager */
    private $services;

    /** @var OptionsListenerFactory */
    private $factory;

    public function setUp()
    {
        $this->services = new ServiceManager();
        $this->factory  = new OptionsListenerFactory();
    }

    public function seedConfigService()
    {
        return ['api-tools-rest' => [
            'some-controller' => [
                'listener'                => 'SomeListener',
                'route_name'              => 'api.rest.some',
                'route_identifer_name'    => 'some_id',
                'entity_class'            => 'SomeEntity',
                'entity_http_methods'     => ['GET', 'PATCH', 'DELETE'],
                'collection_name'         => 'some',
                'collection_http_methods' => ['GET', 'POST'],
            ],
        ]];
    }

    public function testFactoryCreatesOptionsListenerFromRestConfiguration()
    {
        $config = $this->seedConfigService();
        $this->services->setService('config', $config);

        $listener = $this->factory->createService($this->services);

        $this->assertInstanceOf(OptionsListener::class, $listener);

        $r = new ReflectionObject($listener);
        $p = $r->getProperty('config');
        $p->setAccessible(true);
        $instanceConfig = $p->getValue($listener);
        $this->assertEquals($config['api-tools-rest'], $instanceConfig);
    }
}
