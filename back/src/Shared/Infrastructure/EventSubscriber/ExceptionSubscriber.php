<?php

namespace App\Shared\Infrastructure\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Handles exceptions to generate Json responses using custom normalizers
 * This allows more control on the error response format with no dependency to symfony internals
 *
 * @author Wilhelm Zwertvaegher
 *
 */
readonly class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function __construct(
        #[AutowireIterator('app.exception_normalizer')]
        private iterable $normalizers
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->getRequestFormat() !== 'json') {
            return;
        }
        $throwable = $event->getThrowable();
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->supportsNormalization($throwable)) {
                $data = $normalizer->normalize($throwable);
                $status = $data['status'] ?? ($throwable instanceof HttpExceptionInterface ? $throwable->getStatusCode() : 500);
                $event->setResponse(new JsonResponse($data, $status));
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onExceptionEvent',
        ];
    }
}
