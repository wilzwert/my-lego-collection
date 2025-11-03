<?php

namespace App\Shared\Domain\Service;

use Exception;
use Throwable;

class TransactionProviderException extends Exception
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('An error occured while performing a transaction', 0, $previous);
    }
}
