<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class MessageTransportFactory implements TransportFactoryInterface
{
    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        return new MessageTransport();
    }

    public function supports(string $dsn, array $options): bool
    {
        return 0 === strpos($dsn, 'file://');
    }
}
