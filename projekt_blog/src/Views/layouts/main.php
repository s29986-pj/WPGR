<?php
// Mapowanie kodów statusu na wiadomości
$statusMessages = [
    // Statusy z AuthController
    'registered_email_sent' => 'Konto zostało utworzone. Sprawdź swoją skrzynkę e-mail, aby aktywować konto.',
    'registered_email_fail' => 'Konto zostało utworzone, ale nie udało się wysłać e-maila weryfikacyjnego. Spróbuj później.',
    'logged_in' => 'Logowanie zakończone sukcesem!',
    'logged_out' => 'Zostałeś wylogowany.',
    'activated' => 'Twoje konto zostało pomyślnie aktywowane! Możesz się teraz zalogować.',
    'not_logged_in_post' => 'Musisz być zalogowany, aby dodać post.',
    'not_logged_in_post_edit' => 'Musisz być zalogowany, aby edytować posty.',
    'invalid_reset_token' => 'Nieprawidłowy lub wygasły token resetowania hasła.',
    'reset_link_sent' => 'Link do resetowania hasła został wysłany na Twój adres e-mail.',
    'reset_link_fail' => 'Wystąpił błąd podczas wysyłki linku resetującego. Spróbuj później.',
    'reset_link_sent_if_exists' => 'Jeśli podany adres e-mail istnieje w naszej bazie, link do resetowania hasła został wysłany.',
    'password_reset_success' => 'Twoje hasło zostało pomyślnie zresetowane. Możesz się teraz zalogować.',
    'email_verification_error' => 'Wystąpił błąd podczas weryfikacji e-maila. Spróbuj ponownie lub skontaktuj się z administratorem.',

    // Statusy z PostController
    'added' => 'Post został dodany pomyślnie.',
    'updated' => 'Post został zaktualizowany pomyślnie.',
    'deleted' => 'Post został usunięty pomyślnie.',
    'delete_error' => 'Wystąpił błąd podczas usuwania posta.',

    
];

// Pobieranie kodu statusu z URL-a
$status_code = $_GET['status'] ?? '';
$display_status_message = '';

if (!empty($status_code) && isset($statusMessages[$status_code])) {
    $display_status_message = $statusMessages[$status_code];
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_PATH ?>/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-left">
                <a href="<?php echo BASE_PATH; ?>/">Strona główna</a>
            </div>
            <div class="nav-right">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span>Witaj, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a href="<?php echo BASE_PATH; ?>/logout">Wyloguj</a>
                <?php else: ?>
                    <a href="<?php echo BASE_PATH; ?>/login">Zaloguj</a>
                    <a href="<?php echo BASE_PATH; ?>/register">Zarejestruj</a>
                <?php endif; ?>
            </div>
        </nav>
    </header> 

    <main>
        <?php if (!empty($display_status_message)): ?>
            <p class="status-message success-message"><?php echo htmlspecialchars($display_status_message); ?></p>
        <?php endif; ?>

        <?php
        if (isset($pageContent)) {
            echo $pageContent;
        }
        ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Wszelkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
