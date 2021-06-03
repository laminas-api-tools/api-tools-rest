<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Rest\TestAsset;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;

class CollectionIntegrationListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /** @var iterable */
    public $collection;

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach('fetchAll', [$this, 'onFetchAll']);
    }

    public function setCollection(iterable $collection): void
    {
        $this->collection = $collection;
    }

    /** @param array|object $e */
    public function onFetchAll($e): iterable
    {
        return $this->collection;
    }
}
