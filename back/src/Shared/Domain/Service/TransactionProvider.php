<?php

namespace App\Shared\Domain\Service;

interface TransactionProvider
{
    /**
     * Execute $callback in a transaction
     * Rollback is triggered if an exception occurs
     * @param callable(): mixed $callback
     * @return mixed
     * @throws TransactionProviderException
     */
    public function transactional(callable $callback): mixed;
}
