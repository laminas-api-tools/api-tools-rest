<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Rest\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\Rest\Listener\OptionsListener;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class OptionsListenerFactory implements FactoryInterface
{
    /**
     * Create and return an OptionsListener instance.
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return OptionsListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new OptionsListener($this->getConfig($container));
    }

    /**
     * Create and return an OptionsListener instance (v2).
     *
     * Provided for backwards compatibility; proxies to __invoke().
     *
     * @param ServiceLocatorInterface $container
     * @return OptionsListener
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, OptionsListener::class);
    }

    /**
     * Retrieve api-tools-rest config from the container, if available.
     *
     * @param ContainerInterface $container
     * @return array
     */
    private function getConfig(ContainerInterface $container)
    {
        if (! $container->has('config')) {
            return [];
        }

        $config = $container->get('config');

        if (! array_key_exists('api-tools-rest', $config)
            || ! is_array($config['api-tools-rest'])
        ) {
            return [];
        }

        return $config['api-tools-rest'];
    }
}
