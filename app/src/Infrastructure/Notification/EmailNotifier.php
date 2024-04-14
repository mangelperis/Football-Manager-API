<?php
declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Repository\NotifierInterface;
use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class EmailNotifier implements NotifierInterface
{
    private MailerInterface $mailer;
    private ValidatorInterface $validator;
    private string $defaultSenderEmail;

    public function __construct(MailerInterface $mailer, ValidatorInterface $validator)
    {
        $this->mailer = $mailer;
        $this->validator = $validator;
        $this->defaultSenderEmail = 'noreply@footballmanager.com';
    }

    /**
     * @param string $recipientAddress
     * @param string $senderAddress
     * @param string $subject
     * @param string $body
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function notify(string $recipientAddress, string $senderAddress, string $subject, string $body): bool
    {
        try {
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = 'Invalid email address';

            $errors = $this->validator->validate($senderAddress, $emailConstraint);

            if (count($errors) === 0) {
                $from = $senderAddress;
            } else {
                $from = $this->defaultSenderEmail;
            }

            $email = (new Email())
                ->from($from)
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