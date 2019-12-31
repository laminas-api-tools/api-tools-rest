<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Rest;

use Laminas\Mvc\Router\Http\TreeRouteStack as V2TreeRouteStack;
use Laminas\Router\Http\TreeRouteStack;

trait TreeRouteStackFactoryTrait
{
    /**
     * Create and return a version-specific TreeRouteStack instance.
     *
     * @return TreeRouteStack|V2TreeRouteStack
     */
    public function createTreeRouteStack()
    {
        $class = class_exists(V2TreeRouteStack::class) ? V2TreeRouteStack::class : TreeRouteStack::class;
        return new $class();
    }
}
