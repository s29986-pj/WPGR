<div class="single-post-container">

    <?php if (!empty($post)): ?>
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>

        <p class="post-meta">
            Dodano przez: <?php echo htmlspecialchars($post['username']); ?>
            | <?php echo htmlspecialchars(date('d.m.Y H:i', strtotime($post['created_at']))); ?>
        </p>
        <?php if ($post['created_at'] !== $post['updated_at']): ?>
            <p class="post-meta">Ostatnia aktualizacja: <?php echo htmlspecialchars(date('d.m.Y H:i', strtotime($post['updated_at']))); ?></p>
        <?php endif; ?>

        <?php if (!empty($post['image_path'])): ?>
            <div class="post-image-container">
                <img src="<?php echo BASE_PATH . '/' . htmlspecialchars($post['image_path']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
            </div>
        <?php endif; ?>

        <div class="post-full-content">
            <?php echo nl2br(htmlspecialchars($post['content']));?>
        </div>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $post['user_id']): ?>
            <div class="post-actions-single">
                <a href="<?php echo BASE_PATH; ?>/posts/<?php echo htmlspecialchars($post['id']); ?>/edit" class="button edit-button">Edytuj</a>
                <form action="<?php echo BASE_PATH; ?>/posts/<?php echo htmlspecialchars($post['id']); ?>/delete" method="POST" class="form-delete">
                    <button type="submit" class="button delete-button">Usuń</button>
                </form>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p>Post nie został znaleziony.</p>
    <?php endif; ?>


    <div class="post-navigation">
        <div class="prev-post">
            <?php if ($previousPost): ?>
                <a href="<?php echo BASE_PATH; ?>/posts/<?php echo $previousPost['id']; ?>">
                    &larr; Poprzedni wpis
                </a>
            <?php endif; ?>
        </div>
        <div class="next-post">
                <?php if ($nextPost): ?>
                <a href="<?php echo BASE_PATH; ?>/posts/<?php echo $nextPost['id']; ?>">
                    Następny wpis &rarr;
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="contact-author-button-container">
        <a href="<?php echo BASE_PATH; ?>/posts/<?php echo $post['id']; ?>/contact" class="button edit-button">
            Skontaktuj się z autorem
        </a>
    </div>


    <hr class="post-divider">

    <div id="comments" class="comments-section">

        <h3>Komentarze (<?php echo count($comments); ?>)</h3>

        <div class="comment-form-container">
            <h4>Dodaj komentarz</h4>
            <form action="<?php echo BASE_PATH; ?>/posts/<?php echo htmlspecialchars($post['id']); ?>/comments" method="POST">
                <div class="form-group">
                    <label for="content">Treść komentarza:</label>
                    <textarea id="content" name="content" required></textarea>
                </div>
                <div class="form-group">
                    <button type="submit">Opublikuj komentarz</button>
                </div>
            </form>
        </div>

        <div class="comments-list">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <p><strong>
                            <?php echo htmlspecialchars($comment['author_name']); ?>
                        </strong></p>
                        <p class="comment-meta">
                            <?php echo htmlspecialchars(date('d.m.Y H:i', strtotime($comment['created_at']))); ?>
                        </p>
                        <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Brak komentarzy. Bądź pierwszy!</p>
            <?php endif; ?>
        </div>
    </div>

    <p><a href="<?php echo BASE_PATH; ?>/">&larr; Powrót do listy postów</a></p>
    
</div>
