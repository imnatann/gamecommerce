<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    protected $code = 4002;

    public function __construct(string $message = 'Insufficient wallet balance', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code ?: $this->code, $previous);
    }
}