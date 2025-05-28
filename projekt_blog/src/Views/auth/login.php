<div class="form-container">
    <h2>Logowanie</h2>

    <?php
    $status_message = $_GET['status'] ?? '';
    if (!empty($status_message)): ?>
        <p class="success-message"><?php echo htmlspecialchars($status_message); ?></p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="<?php echo BASE_PATH  ?>/login" method="POST">
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">Hasło:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="remember_me">
                <input type="checkbox" id="remember_me" name="remember_me"> Zapamiętaj mnie
            </label>
        </div>
        <div class="form-group">
            <button type="submit">Zaloguj się</button>
        </div>
    </form>
    <p><a href="<?php echo BASE_PATH  ?>/forgot-password">Nie pamiętasz hasła?</a></p>
    <p>Nie masz konta? <a href="<?php echo BASE_PATH  ?>/register">Zarejestruj się</a></p>
</div>
