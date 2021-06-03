<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Rest\Exception;

use Laminas\ApiTools\ApiProblem\Exception\DomainException;

/**
 * Throw this exception from a "update" resource listener in order to indicate
 * an inability to update an item and automatically report it.
 */
class UpdateException extends DomainException
{
}
