<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Rest\Exception;

use Laminas\ApiTools\ApiProblem\Exception\DomainException;

/**
 * Throw this exception from a "patch" resource listener in order to indicate
 * an inability to patch an item and automatically report it.
 */
class PatchException extends DomainException
{
}
