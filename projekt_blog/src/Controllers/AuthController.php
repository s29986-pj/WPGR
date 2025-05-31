<?php

namespace App\Controllers;

use App\Models\User;
use App\Core\Mailer;
use function App\Utils\view;

class AuthController
{
    private $userModel;
    private $mailer;

    public function __construct()
    {
        $this->userModel = new User();
        $this->mailer = new Mailer();
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            $error = null;
            $success = null;

            // Walidacja
            if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
                $error = "Wszystkie pola są wymagane.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Nieprawidłowy format e-maila.";
            } elseif ($password !== $password_confirm) {
                $error = "Hasła nie są zgodne.";
            } elseif ($this->userModel->findByUsername($username)) {
                $error = "Nazwa użytkownika jest już zajęta.";
            } elseif ($this->userModel->findByEmail($email)) {
                $error = "Użytkownik o tym adresie e-mail już istnieje.";
            }
            if ($error) {
                // Jeśli jest błąd, renderuje formularz ponownie, przekazując błąd
                view('auth/register', [
                    'pageTitle' => 'Rejestracja',
                    'error' => $error,
                    'username' => $username, // Przekazuje wprowadzone dane, żeby nie znikały z formularza
                    'email' => $email
                ]);
                return;
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Unikalny token do weryfikacji e-maila
            // bin2hex(random_bytes(32)) tworzy losowy ciąg 64 znaków
            $verificationToken = bin2hex(random_bytes(32));

            // Tworzy użytkownika w bazie danych
            $userId = $this->userModel->create($username, $email, $passwordHash, $verificationToken);

            if ($userId) {
                // Treść e-maila z linkiem aktywacyjnym
                $activationLink = "http://" . $_SERVER['HTTP_HOST'] . BASE_PATH . "/verify-email?token=" . $verificationToken;
                
                $subject = "Aktywuj swoje konto na " . APP_NAME;
                $body = "Cześć " . htmlspecialchars($username) . ",<br><br>"
                      . "Dziękujemy za rejestrację na naszym blogu. Aby aktywować swoje konto, kliknij w poniższy link:<br><br>"
                      . "<a href='" . htmlspecialchars($activationLink) . "'>" . htmlspecialchars($activationLink) . "</a><br><br>"
                      . "Jeśli nie rejestrowałeś się na naszym blogu, zignoruj tę wiadomość.<br><br>"
                      . "Pozdrawiamy,<br>Zespół " . APP_NAME;

                // Wysyła e-mail aktywacyjny
                if ($this->mailer->sendEmail($email, $subject, $body)) {
                    header("Location: " . BASE_PATH . "/login?status=registered_email_sent");
                    exit();
                } else {
                    header("Location: " . BASE_PATH . "/login?status=registered_email_fail");
                    exit();
                }
            } else {
                $error = "Wystąpił błąd podczas tworzenia konta. Spróbuj ponownie.";
            }
        }

        // Jeśli jest sukces lub błąd po wysyłce, renderuje formularz z komunikatem
        view('auth/register', [
            'pageTitle' => 'Rejestracja',
            'error' => $error,
            'success' => $success,
            'username' => $username ?? '',
            'email' => $email ?? ''
        ]);
    }



    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember_me = isset($_POST['remember_me']);

            $error = null;

            if (empty($email) || empty($password)) {
                $error = "Wszystkie pola są wymagane.";
            } else {
                $user = $this->userModel->findByEmail($email);

                if (!$user || !password_verify($password, $user['password_hash'])) {
                    $error = "Nieprawidłowy adres e-mail lub hasło.";
                } elseif (!$user['is_active']) {
                    $error = "Twoje konto nie zostało aktywowane. Sprawdź swoją skrzynkę e-mail.";
                }
            }

            if ($error) {
                // Jeśli jest błąd, renderuje formularz ponownie
                view('auth/login', [
                    'pageTitle' => 'Logowanie',
                    'error' => $error,
                    'email' => $email // Przekazuje e-mail, żeby nie znikał
                ]);
                return;
            }

            // Logowanie pomyślne - ustawia sesję
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];

            // Obsługa "Zapamiętaj mnie" (ciasteczka).
            if ($remember_me) {
                $selector = bin2hex(random_bytes(16));
                $validator = bin2hex(random_bytes(32));
                $token = $selector . $validator;

                // Ustawia ciasteczko z tokenem (na 30 dni)
                setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
            }

            // Przekierowuje na stronę główną
            header("Location: " . BASE_PATH . "/?status=logged_in");
            exit();
        }

        // Pierwsze wejście na stronę logowania
        view('auth/login', ['pageTitle' => 'Logowanie']);
    }



    public function logout()
    {
        // Zakończenie sesji.
        session_unset();
        session_destroy();
        setcookie('remember_me', '', time() - 3600, '/');

        // Przekierowuje na stronę główną.
        header("Location: " . BASE_PATH . "/login?status=logged_out");
        exit();
    }

    public function verifyEmail()
    {
        $token = $_GET['token'] ?? '';
        $error = null;
        $success = null;

        if (empty($token)) {
            $error = "Brak tokena weryfikacyjnego.";
        } else {
            $user = $this->userModel->findByVerificationToken($token);

            if (!$user) {
                $error = "Nieprawidłowy lub wygasły token weryfikacyjny.";
            } elseif ($this->userModel->activateUser($user['id'])) {
                $success = "Twoje konto zostało pomyślnie aktywowane! Możesz się teraz zalogować.";
                header("Location: " . BASE_PATH. "/login?status=activated");
                exit();
            } else {
                $error = "Wystąpił błąd podczas aktywacji konta. Spróbuj ponownie.";
            }
        }

        // Jeśli wystąpił błąd aktywacji), wyświetla widok z komunikatem
        view('auth/login', [ 
            'pageTitle' => 'Weryfikacja konta',
            'error' => $error,
            'success_message' => $success
        ]);
    }

     public function sendResetLink()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';

            $error = null;

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Wprowadź prawidłowy adres e-mail.";
            } else {
                $user = $this->userModel->findByEmail($email);

                if ($user) {
                    // Generuje token resetowania hasła
                    $resetToken = bin2hex(random_bytes(32));
                    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    if ($this->userModel->updateResetToken($user['id'], $resetToken, $expiry)) {
                        // Link do resetowanie hasła
                        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . BASE_PATH. "/reset-password?token=" . $resetToken;

                        $subject = "Resetowanie hasła do konta " . APP_NAME;
                        $body = "Cześć " . htmlspecialchars($user['username'] ?? $user['email']) . ",<br><br>"
                              . "Otrzymaliśmy prośbę o zresetowanie hasła do Twojego konta. "
                              . "Aby zresetować hasło, kliknij w poniższy link:<br><br>"
                              . "<a href='" . htmlspecialchars($resetLink) . "'>" . htmlspecialchars($resetLink) . "</a><br><br>"
                              . "Link będzie ważny przez 1 godzinę. Jeśli nie prosiłeś o zresetowanie hasła, zignoruj tę wiadomość.<br><br>"
                              . "Pozdrawiamy,<br>Zespół " . APP_NAME;

                        if ($this->mailer->sendEmail($email, $subject, $body)) {
                            header("Location: " . BASE_PATH . "/forgot-password?status=reset_link_sent");
                            exit();
                        } else {
                            header("Location: " . BASE_PATH . "/forgot-password?status=reset_link_fail");
                            exit();
                        }
                    } else {
                        $error = "Wystąpił błąd podczas generowania tokena resetującego. Spróbuj ponownie.";
                    }
                } else {
                    header("Location: " . BASE_PATH . "/forgot-password?status=reset_link_sent_if_exists");
                    exit();
                }
            }

            view('auth/forgot_password', [
                'pageTitle' => 'Resetowanie hasła',
                'error' => $error,
                'email' => $email
            ]);
            return;
        }

        // Pierwsze wejście na stronę
        view('auth/forgot_password', ['pageTitle' => 'Resetowanie hasła']);
    }

    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            $error = null;

            if (empty($token) || empty($password) || empty($password_confirm)) {
                $error = "Wszystkie pola są wymagane.";
            } elseif ($password !== $password_confirm) {
                $error = "Hasła nie są zgodne.";
            } else {
                $user = $this->userModel->findByResetToken($token);

                if (!$user) {
                    $error = "Nieprawidłowy lub wygasły token resetowania hasła.";
                } else {
                    // Haszowanie nowego hasła
                    $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);

                    // Aktualizacja hasła
                    if ($this->userModel->updatePassword($user['id'], $newPasswordHash)) {
                        $success = "Twoje hasło zostało pomyślnie zresetowane. Możesz się teraz zalogować.";
                        header("Location: " . BASE_PATH . "/login?status=password_reset_success");
                        exit();
                    } else {
                        $error = "Wystąpił błąd podczas resetowania hasła. Spróbuj ponownie.";
                    }
                }
            }

            // Jeśli błąd walidacji lub resetowania, wyświetla formularz z błędem
            view('auth/reset_password', [
                'pageTitle' => 'Ustaw nowe hasło',
                'error' => $error,
                'token' => $token
            ]);
            return;
        }
    }
}
