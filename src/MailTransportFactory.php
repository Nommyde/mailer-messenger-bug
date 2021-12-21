<?php

declare(strict_types=1);

namespace App;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportFactoryInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;

class MailTransportFactory implements TransportFactoryInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public function create(Dsn $dsn): TransportInterface
    {
        return new MailTransport($dsn->getHost(), $this->logger);
    }

    public function supports(Dsn $dsn): bool
    {
        return $dsn->getScheme() === 'my-mailer';
    }
}
