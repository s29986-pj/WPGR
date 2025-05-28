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
