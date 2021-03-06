<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Rest\TestAsset;

use Laminas\ApiTools\Rest\AbstractResourceListener;

class TestResourceListener extends AbstractResourceListener
{
    public $testCase;

    public function __construct($testCase)
    {
        $this->testCase = $testCase;
    }

    public function create($data)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    public function update($id, $data)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    public function replaceList($data)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    public function patch($id, $data)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    public function patchList($data)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    public function delete($id)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    public function deleteList($data)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    public function fetch($id)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    public function fetchAll($params = [])
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = $params;
    }
}
