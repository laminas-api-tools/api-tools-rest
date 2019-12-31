<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Rest;

use Laminas\Mvc\Router\Http\Segment as V2SegmentRoute;
use Laminas\Router\Http\Segment as SegmentRoute;

trait SegmentRouteFactoryTrait
{
    /**
     * Create and return a version-specific SegmentRoute instance.
     *
     * Passes all provided arguments to the constructor.
     *
     * @return SegmentRoute|V2SegmentRoute
     */
    public function createSegmentRoute(...$params)
    {
        $class = class_exists(V2SegmentRoute::class) ? V2SegmentRoute::class : SegmentRoute::class;
        return new $class(...$params);
    }
}
