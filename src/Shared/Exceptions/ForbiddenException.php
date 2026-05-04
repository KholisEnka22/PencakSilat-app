<?php

declare(strict_types=1);

namespace Shared\Exceptions;

use Exception;

class ForbiddenException extends Exception
{
    public function __construct(string $message = 'Anda tidak memiliki akses.')
    {
        parent::__construct($message, 403);
    }
}
