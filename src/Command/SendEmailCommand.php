<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendEmailCommand extends Command
{
    protected static $defaultName = 'app:send-email';
    protected static $defaultDescription = 'Add a short description for your command';
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = (new Email())
            ->from('sender@example.com')
            ->to('receiver@example.com')
            ->subject('test')
            ->text('message body')
        ;

        $email->getHeaders()->addTextHeader('X-Transport', 'alternative');

        $this->mailer->send($email);

        $io->success('Done.');

        return Command::SUCCESS;
    }
}
