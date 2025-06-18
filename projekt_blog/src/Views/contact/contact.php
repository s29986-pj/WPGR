<div class="form-container">
    <h2>Skontaktuj się z autorem: <?php echo htmlspecialchars($post['username']); ?></h2>
    <p>Twoja wiadomość dotyczy posta: "<?php echo htmlspecialchars($post['title']); ?>"</p>

    <?php if (isset($error)): ?><p class="error-message"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if (isset($success)): ?>
        <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
        <p><a href="<?php echo BASE_PATH . '/posts/' . $post['id']; ?>">&larr; Wróć do posta</a></p>
    <?php else: ?>
        <form action="<?php echo BASE_PATH . '/posts/' . $post['id'] . '/contact'; ?>" method="POST">
            <div class="form-group">
                <label for="name">Twoje imię:</label>
                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($post_data['name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="email">Twój e-mail:</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($post_data['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="message">Wiadomość:</label>
                <textarea id="message" name="message" required><?php echo htmlspecialchars($post_data['message'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit">Wyślij wiadomość</button>
            </div>
        </form>
    <?php endif; ?>
</div>
