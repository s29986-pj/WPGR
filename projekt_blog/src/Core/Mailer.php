<?php

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true); // true włącza wyjątki dla błędów

        // Ustawienia SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = 'sandbox.smtp.mailtrap.io';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Port = 2525;
        $this->mailer->Username = '460d5986daa041';
        $this->mailer->Password = 'e8c9a42577816d';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        // Ustawienia domyślne dla wysyłanych wiadomości
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->setFrom('s29986@pjwstk.edu.pl', 'Projekt_Blog'); // Adres nadawcy
    }

    // Wysyła wiadomość e-mail
    public function sendEmail(string $to, string $subject, string $body): bool
    {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Błąd wysyłki e-maila: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}
