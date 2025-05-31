<div class="form-container">
    <h2>Ustaw nowe hasło</h2>
    
    <?php
    if (isset($error) && !empty($error)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="<?php echo BASE_PATH ?>/reset-password" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">

        <div class="form-group">
            <label for="password">Nowe hasło:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirm">Potwierdź nowe hasło:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        <div class="form-group">
            <button type="submit">Zmień hasło</button>
        </div>
    </form>
</div>
