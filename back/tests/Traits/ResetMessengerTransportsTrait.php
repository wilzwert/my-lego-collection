<?php

namespace App\Tests\Traits;

/**
 * @author Wilhelm Zwertvaegher
 */
trait ResetMessengerTransportsTrait
{

    protected function resetMessengerTransports(): void
    {
        $container = self::getContainer();

        $transports = ['messenger.transport.async'];
        foreach ($transports as $transport) {
            $actualTransport = $container->get($transport);
            if (method_exists($actualTransport, 'reset')) {
                $actualTransport->reset();
            } elseif (method_exists($actualTransport, 'get')) {
                iterator_to_array($actualTransport->get());
            }
        }
    }
}
