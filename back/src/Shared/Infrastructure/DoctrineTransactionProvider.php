<?php

namespace App\Shared\Infrastructure;

use App\Shared\Domain\TransactionProvider;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineTransactionProvider implements TransactionProvider
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function transactional(callable $callback): mixed
    {
        $this->entityManager->beginTransaction();

        try {
            $result = $callback();
            $this->entityManager->flush();
            $this->entityManager->commit();
            return $result;
        }
        catch (\Throwable $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
}
