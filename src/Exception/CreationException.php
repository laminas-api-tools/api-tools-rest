<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Rest\Exception;

use Laminas\ApiTools\ApiProblem\Exception\DomainException;

/**
 * Throw this exception from a "create" resource listener in order to indicate
 * an inability to create an item and automatically report it.
 */
class CreationException extends DomainException
{
}
