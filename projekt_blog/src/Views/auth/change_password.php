<div class="form-container">
    <h2>Zmień hasło</h2>

    <?php if (isset($error) && !empty($error)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="<?php echo BASE_PATH ?>/change-password" method="POST">
        <div class="form-group">
            <label for="current_password">Aktualne hasło:</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">Nowe hasło:</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="new_password_confirm">Potwierdź nowe hasło:</label>
            <input type="password" id="new_password_confirm" name="new_password_confirm" required>
        </div>
        <div class="form-group">
            <button type="submit">Zmień hasło</button>
        </div>
    </form>
    <p><a href="<?php echo BASE_PATH; ?>/">&larr; Wróć na stronę główną</a></p>
</div>
