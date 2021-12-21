<?php

declare(strict_types=1);

namespace App;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class MailTransport extends AbstractTransport
{
    private string $name;
    private LoggerInterface $logger;

    public function __construct(string $name, LoggerInterface $logger)
    {
        parent::__construct(null, $logger);
        $this->name = $name;
        $this->logger = $logger;
    }

    protected function doSend(SentMessage $message): void
    {
        $this->logger->debug("transport: $this->name");

        // trigger some error for the second transport
        if ($this->name === 'second-mailer') {
            throw new Exception('oops');
        }

        $this->logger->debug($message->toString());
        $this->logger->debug('message sent');
    }

    public function __toString(): string
    {
        return 'boo';
    }
}
