<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Rest\TestAsset;

/**
 * @subpackage UnitTest
 */
class ArraySerializable
{
    public function getHijinx()
    {
        return 'should not get this';
    }

    public function getArrayCopy()
    {
        return ['foo' => 'bar'];
    }
}
