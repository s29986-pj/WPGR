<div class="posts-list-container">
    <h1>Najnowsze wpisy</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p class="add-post-link"><a href="<?php echo BASE_PATH; ?>/posts/create">Dodaj nowy post</a></p>
    <?php endif; ?>

    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-item">
                <h2><a href="<?php echo BASE_PATH; ?>/posts/<?php echo htmlspecialchars($post['id']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                <p class="post-meta">Dodano przez: <?php echo htmlspecialchars($post['username']); ?> | <?php echo htmlspecialchars(date('d.m.Y H:i', strtotime($post['created_at']))); ?></p>
                <div class="post-content-excerpt">
                    <?php
                    // Wyświetla tylko fragment treści
                    $excerpt = substr(strip_tags($post['content']), 0, 100); // 100 znaków bez HTML
                    echo nl2br(htmlspecialchars($excerpt));
                    if (strlen(strip_tags($post['content'])) > 100) {
                        echo '... <a href="' . BASE_PATH . '/posts/' . htmlspecialchars($post['id']) . '">Czytaj więcej</a>';
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?php echo BASE_PATH; ?>/?page=<?php echo $i; ?>" class="<?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p>Brak postów do wyświetlenia.</p>
    <?php endif; ?>
</div>
