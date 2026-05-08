<?php

namespace App\Exceptions;

use Exception;

class WalletLockedException extends Exception
{
    protected $code = 4003;

    public function __construct(string $message = 'Wallet is locked', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code ?: $this->code, $previous);
    }
}