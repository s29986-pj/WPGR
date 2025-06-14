<?php

namespace App\Controllers;

use App\Core\Mailer;
use function App\Utils\view;

class ContactController
{
    // Wyświetla formularz kontaktowy
    public function showContactForm()
    {
        view('contact/index', ['pageTitle' => 'Kontakt']);
    }

    //  Przetwarza dane z formularza kontaktowego
    public function handleContactForm()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_PATH . '/contact');
            exit();
        }

        // Pobieranie danych z formularza
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';

        // Walidacja
        $error = null;
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $error = 'Wszystkie pola są wymagane.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Proszę podać poprawny adres e-mail.';
        }

        if ($error) {
            // Jeśli jest błąd, wyświetl formularz ponownie z błędem i wpisanymi danymi
            view('contact/index', [
                'pageTitle' => 'Kontakt', 
                'error' => $error, 
                'post_data' => $_POST
            ]);
            return;
        }

        // Zapis wiadomości do pliku
        $logFile = LOG_DIR . 'contact_messages.log';
        $logEntry = "[" . date('Y-m-d H:i:s') . "] From: $name <$email> | Subject: $subject\nMessage: $message\n---\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);


        // Wysłanie wiadomości e-mailem
        $mailer = new Mailer();
        $adminEmail = 's29986@pjwstk.edu.pl';
        $emailSubject = "Wiadomość z formularza kontaktowego: " . $subject;
        $emailBody = "Otrzymano nową wiadomość z formularza kontaktowego.<br><br>"
                   . "<b>Od:</b> " . htmlspecialchars($name) . " (" . htmlspecialchars($email) . ")<br>"
                   . "<b>Temat:</b> " . htmlspecialchars($subject) . "<br><br>"
                   . "<b>Wiadomość:</b><br>" . nl2br(htmlspecialchars($message));
        
        $emailSent = $mailer->sendEmail($adminEmail, $emailSubject, $emailBody);

        if ($emailSent) {
            $success = 'Dziękujemy za wiadomość! Odpowiemy najszybciej, jak to możliwe.';
            view('contact/index', ['pageTitle' => 'Kontakt', 'success' => $success]);
        } else {
            $error = 'Wystąpił błąd podczas wysyłania wiadomości. Prosimy spróbować ponownie później.';
            view('contact/index', ['pageTitle' => 'Kontakt', 'error' => $error, 'post_data' => $_POST]);
        }
    }
}