<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Notifier;


use App\Infrastructure\Notification\EmailNotifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotifierTest extends TestCase
{
    private MailerInterface $mailer;
    private EmailNotifier $emailNotifier;

    protected function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->emailNotifier = new EmailNotifier($this->mailer);
    }


    /**
     * @throws TransportExceptionInterface
     */
    public function testNotify(): void
    {
        $recipientEmail = 'recipient@example.com';
        $subject = 'Subject';
        $body = 'Body';

        $email = (new Email())
            ->from('noreply@footballmanager.com')
            ->to($recipientEmail)
            ->subject($subject)
            ->text($body);

        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->equalTo($email));

        $result = $this->emailNotifier->notify($recipientEmail, $subject, $body);
        $this->assertTrue($result);
    }
}