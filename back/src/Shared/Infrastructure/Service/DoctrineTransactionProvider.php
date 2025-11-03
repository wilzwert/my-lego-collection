<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Service\TransactionProvider;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Throwable;

readonly class DoctrineTransactionProvider implements TransactionProvider
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function transactional(callable $callback): mixed
    {
        $this->entityManager->beginTransaction();

        try {
            $result = $callback();
            $this->entityManager->flush();
            $this->entityManager->commit();
            return $result;
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            // TODO log the transaction error
            echo "Rollback done...\n";
            // then rethrow the exception as is, because it may (should ?) be a domain Exception with meaning
            // throw new TransactionProviderException($e);
            throw $e;
        }
    }
}
