<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class MessageTransport implements TransportInterface
{
    private SerializerInterface $serializer;

    public function __construct()
    {
        $this->serializer = new PhpSerializer();
    }

    public function get(): iterable
    {
        $queue = $this->getAll();

        if (empty($queue)) {
            return [];
        }

        reset($queue);

        $envelope = $this->serializer->decode(['body' => current($queue)]);

        return [$envelope->with(new TransportMessageIdStamp(key($queue)))];
    }

    private function fileName(): string
    {
        return __DIR__ . '/../var/queue.dat';
    }

    private function getAll(): array
    {
        if (!file_exists($this->fileName())) {
            $this->save([]);
        }
        return unserialize(file_get_contents($this->fileName()));
    }

    private function save(array $queue): void
    {
        file_put_contents($this->fileName(), serialize($queue));
    }

    public function ack(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);

        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new \LogicException('No TransportMessageIdStamp found on the Envelope.');
        }

        $messages = $this->getAll();
        unset($messages[$stamp->getId()]);

        $this->save($messages);
    }

    public function reject(Envelope $envelope): void
    {
        $this->ack($envelope);
    }

    public function send(Envelope $envelope): Envelope
    {
        $encodedMessage = $this->serializer->encode($envelope);
        $id = bin2hex(random_bytes(4));

        $messages = $this->getAll();
        $messages[$id] = $encodedMessage['body'];

        $this->save($messages);

        return $envelope->with(new TransportMessageIdStamp($id));
    }
}
