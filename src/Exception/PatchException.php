<?php

namespace Laminas\ApiTools\Rest\Exception;

use Laminas\ApiTools\ApiProblem\Exception\DomainException;

/**
 * Throw this exception from a "patch" resource listener in order to indicate
 * an inability to patch an item and automatically report it.
 */
class PatchException extends DomainException
{
}
