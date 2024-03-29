<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Rest;

use Laminas\Mvc\Router\Http\Segment as V2SegmentRoute;
use Laminas\Router\Http\Segment as SegmentRoute;

use function class_exists;

trait SegmentRouteFactoryTrait
{
    /**
     * Create and return a version-specific SegmentRoute instance.
     *
     * Passes all provided arguments to the constructor.
     *
     * @param mixed $params Constructor parameters for the SegmentRoute, in order.
     * @return SegmentRoute|V2SegmentRoute
     */
    public function createSegmentRoute(...$params)
    {
        $class = class_exists(V2SegmentRoute::class) ? V2SegmentRoute::class : SegmentRoute::class;
        return new $class(...$params);
    }
}
