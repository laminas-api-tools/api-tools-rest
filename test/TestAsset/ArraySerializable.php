<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Rest\TestAsset;

class ArraySerializable
{
    public function getHijinx(): string
    {
        return 'should not get this';
    }

    public function getArrayCopy(): array
    {
        return ['foo' => 'bar'];
    }
}
