<?php
declare(strict_types=1);


use App\Domain\Repository\NotifierInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotifier implements NotifierInterface
{
    private MailerInterface $mailer;
    private string $senderEmail;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->senderEmail = 'noreply@example.com';
    }

    /**
     * @param string $recipientAddress
     * @param string $subject
     * @param string $body
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function notify(string $recipientAddress, string $subject, string $body): bool
    {
        try {
            $email = (new Email())
                ->from($this->senderEmail)
                ->to($recipientAddress)
                ->subject($subject)
                ->text($body);

            $this->mailer->send($email);

            return true;
        }catch (Exception $exception){
            return false;
        }

    }
}