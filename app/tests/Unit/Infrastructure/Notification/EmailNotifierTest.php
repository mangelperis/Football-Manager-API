<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Notifier;


use App\Infrastructure\Notification\EmailNotifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailNotifierTest extends TestCase
{
    private MailerInterface $mailer;
    private EmailNotifier $emailNotifier;
    private ValidatorInterface $validator;


    protected function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->emailNotifier = new EmailNotifier($this->mailer, $this->validator);
    }


    /**
     * @throws TransportExceptionInterface
     */
    public function testNotify(): void
    {
        $recipientEmail = 'recipient@example.com';
        $senderEmail = 'noreply@footballmanager.com';
        $subject = 'Subject';
        $body = 'Body';

        $email = (new Email())
            ->from($senderEmail)
            ->to($recipientEmail)
            ->subject($subject)
            ->text($body);

        $this->mailer->expects($this->once())
            ->method('send')
            ->with($email);

        $result = $this->emailNotifier->notify($recipientEmail,$senderEmail, $subject, $body);
        $this->assertTrue($result);
    }
}