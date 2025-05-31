<div class="single-post-container">
    <?php if (!empty($post)): ?>
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p class="post-meta">Dodano przez: <?php echo htmlspecialchars($post['username']); ?> | <?php echo htmlspecialchars(date('d.m.Y H:i', strtotime($post['created_at']))); ?></p>
        <?php if ($post['created_at'] !== $post['updated_at']): ?>
            <p class="post-meta">Ostatnia aktualizacja: <?php echo htmlspecialchars(date('d.m.Y H:i', strtotime($post['updated_at']))); ?></p>
        <?php endif; ?>

        <div class="post-full-content">
            <?php echo nl2br(htmlspecialchars($post['content']));?>
        </div>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $post['user_id']): ?>
            <div class="post-actions-single">
                <a href="<?php echo BASE_PATH; ?>/posts/<?php echo htmlspecialchars($post['id']); ?>/edit" class="button edit-button">Edytuj</a>
                <form action="<?php echo BASE_PATH; ?>/posts/<?php echo htmlspecialchars($post['id']); ?>/delete" method="POST" style="display:inline-block;">
                    <button type="submit" class="button delete-button" onclick="return confirm('Czy na pewno chcesz usunąć ten post?');">Usuń</button>
                </form>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p>Post nie został znaleziony.</p>
    <?php endif; ?>

    <p><a href="<?php echo BASE_PATH; ?>/">&larr; Powrót do listy postów</a></p>
</div>
