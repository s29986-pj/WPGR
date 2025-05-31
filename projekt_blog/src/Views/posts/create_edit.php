<?php
$title_value = htmlspecialchars($post['title'] ?? '');
$content_value = htmlspecialchars($post['content'] ?? '');
$form_action = BASE_PATH . ($isEditing ? '/posts/' . htmlspecialchars($post['id']) . '/edit' : '/posts/create');
?>

<div class="form-container">
    <h2><?php echo $isEditing ? 'Edytuj post' : 'Dodaj nowy post'; ?></h2>

    <?php
    if (isset($error) && !empty($error)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="<?php echo $form_action; ?>" method="POST">
        <div class="form-group">
            <label for="title">Tytuł:</label>
            <input type="text" id="title" name="title" value="<?php echo $title_value; ?>" required>
        </div>
        <div class="form-group">
            <label for="content">Treść:</label>
            <textarea id="content" name="content" required><?php echo $content_value; ?></textarea>
        </div>
        <div class="form-group">
            <button type="submit"><?php echo $isEditing ? 'Zapisz zmiany' : 'Dodaj post'; ?></button>
        </div>
    </form>
    <p><a href="<?php echo BASE_PATH; ?>/">&larr; Anuluj</a></p>
</div>
