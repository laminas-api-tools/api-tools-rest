<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Rest\Factory;

use Laminas\ApiTools\Rest\Listener\OptionsListener;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class OptionsListenerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services 
     * @return OptionsListener
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $config = array();
        if ($services->has('Config')) {
            $allConfig = $services->get('Config');
            if (array_key_exists('api-tools-rest', $allConfig)
                && is_array($allConfig['api-tools-rest'])
            ) {
                $config = $allConfig['api-tools-rest'];
            }
        }
        return new OptionsListener($config);
    }
}
