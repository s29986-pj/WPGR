<div class="form-container">
    <h2>Rejestracja</h2>

    <?php
    if (isset($error) && !empty($error)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="<?php echo BASE_PATH ?>/register" method="POST">
        <div class="form-group">
            <label for="username">Nazwa użytkownika:</label>
            <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">Hasło:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirm">Potwierdź hasło:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        <div class="form-group">
            <button type="submit">Zarejestruj się</button>
        </div>
    </form>
    <p>Masz już konto? <a href="<?php echo BASE_PATH ?>/login">Zaloguj się</a></p>
</div>
