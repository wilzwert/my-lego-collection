<?php

namespace App\Shared\Infrastructure\Normalizer;

use App\Shared\Domain\Exception\ValidationException;
use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
abstract class ExceptionNormalizer implements NormalizerInterface
{
    use ClockAwareTrait;

    /**
     * @param \Throwable $throwable
     * @return array<string, array<string, string[]|int[]>>
     */
    abstract protected function normalizeErrors(\Throwable $throwable): array;

    abstract protected function getStatus(\Throwable $throwable): int;

    protected function getErrorCode(\Throwable $throwable): string
    {
        return 'internal-error';
    }

    protected function getMessage(\Throwable $throwable): string
    {
        return $throwable->getMessage();
    }

    /**
     * @throws \InvalidArgumentException
     */

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    final public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        return [
            'timestamp' => $this->now()->format(\DateTimeInterface::RFC3339_EXTENDED),
            'status' => $this->getStatus($data),
            'error' => $this->getErrorCode($data),
            'message' => $this->getMessage($data),
            'errors' => $this->normalizeErrors($data),
        ];
    }

}
