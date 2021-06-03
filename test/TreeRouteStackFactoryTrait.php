<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Rest;

use Laminas\Mvc\Router\Http\TreeRouteStack as V2TreeRouteStack;
use Laminas\Router\Http\TreeRouteStack;

use function class_exists;

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
