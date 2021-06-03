<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Rest\Factory\TestAsset;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;

class Listener implements ListenerAggregateInterface
{
    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
    }

    public function detach(EventManagerInterface $events)
    {
    }
}
