<div class="form-container">
    <h2>Resetowanie hasła</h2>
    <p>Wprowadź swój adres e-mail, aby otrzymać link do zresetowania hasła.</p>

    <?php
    if (isset($error) && !empty($error)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="<?php echo BASE_PATH ?>/forgot-password" method="POST">
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <button type="submit">Wyślij link do resetowania</button>
        </div>
    </form>
    <p><a href="<?php echo BASE_PATH ?>/login">Wróć do logowania</a></p>
</div>
