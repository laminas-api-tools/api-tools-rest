<?php

namespace LaminasTest\ApiTools\Rest\TestAsset;

use JsonSerializable as JsonSerializableInterface;

/**
 * @subpackage UnitTest
 */
class JsonSerializable implements JsonSerializableInterface
{
    public function jsonSerialize()
    {
        return ['foo' => 'bar'];
    }
}
