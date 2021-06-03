<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Rest\TestAsset;

use Laminas\ApiTools\Rest\AbstractResourceListener;
use PHPUnit\Framework\TestCase;

use function func_get_args;

class TestResourceListener extends AbstractResourceListener
{
    /** @var TestCase */
    public $testCase;

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    /** @param array $data */
    public function create($data)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    /**
     * @param int|string $id
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    /** @param array $data */
    public function replaceList($data)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    /**
     * @param int|string $id
     * @param array $data
     */
    public function patch($id, $data)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    /** @param array $data */
    public function patchList($data)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    /** @param int|string $id */
    public function delete($id)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    /** @param array $data */
    public function deleteList($data)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    /** @param int|string $id */
    public function fetch($id)
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = func_get_args();
    }

    /** @param array $params */
    public function fetchAll($params = [])
    {
        $this->testCase->methodInvokedInListener = __METHOD__;
        $this->testCase->paramsPassedToListener  = $params;
    }
}
