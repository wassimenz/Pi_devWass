<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($to, $subject, $body)
    {
        try {
            $email = (new Email())
                ->from('wassimhadji70@gmail.com')
                ->to($to)
                ->subject($subject)
                ->html($body);
    
            $this->mailer->send($email);
        } catch (\Exception $e) {
            // Log or print the exception message
            echo 'Error: ' . $e->getMessage();
        }
    }
}