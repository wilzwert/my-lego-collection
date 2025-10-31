<?php

namespace App\Tests\Shared\Infrastructure\Service;

/**
 * @author Wilhelm Zwertvaegher
 */

use App\Shared\Infrastructure\Service\DoctrineTransactionProvider;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class DoctrineTransactionProviderTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private DoctrineTransactionProvider $provider;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->provider = new DoctrineTransactionProvider($this->entityManager);
    }

    #[Test]
    public function shouldExecuteCallbackWithinTransactionAndCommits(): void
    {
        $callback = fn () => 'ok';

        // On s’attend à ce que la transaction se déroule normalement
        $this->entityManager->expects($this->once())->method('beginTransaction');
        $this->entityManager->expects($this->once())->method('flush');
        $this->entityManager->expects($this->once())->method('commit');
        $this->entityManager->expects($this->never())->method('rollback');

        $result = $this->provider->transactional($callback);

        $this->assertSame('ok', $result);
    }

    #[Test]
    public function whenCallbackThrows_thenShouldRollbackAndRethrow(): void
    {
        $exception = new RuntimeException('error');
        $callback = fn () => throw $exception;

        $this->entityManager->expects($this->once())->method('beginTransaction');
        $this->entityManager->expects($this->never())->method('flush');
        $this->entityManager->expects($this->never())->method('commit');
        $this->entityManager->expects($this->once())->method('rollback');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('error');

        $this->provider->transactional($callback);
    }

    #[Test]
    public function shouldReturnCallbackResultUnchanged(): void
    {
        $data = ['user' => 'alice', 'id' => 42];
        $callback = fn () => $data;

        $this->entityManager->expects($this->once())->method('beginTransaction');
        $this->entityManager->expects($this->once())->method('flush');
        $this->entityManager->expects($this->once())->method('commit');

        $result = $this->provider->transactional($callback);

        $this->assertSame($data, $result);
    }
}
