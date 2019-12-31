<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Rest;

use Laminas\Mvc\Router\SimpleRouteStack as V2SimpleRouteStack;
use Laminas\Router\SimpleRouteStack;

trait SimpleRouteStackFactoryTrait
{
    /**
     * Create and return a version-specific SimpleRouteStack instance.
     *
     * @return SimpleRouteStack|V2SimpleRouteStack
     */
    public function createSimpleRouteStack()
    {
        $class = class_exists(V2SimpleRouteStack::class) ? V2SimpleRouteStack::class : SimpleRouteStack::class;
        return new $class();
    }
}
