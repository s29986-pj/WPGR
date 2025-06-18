<div class="posts-list-container">
    <h1>Najnowsze wpisy</h1>

    <?php if (isset($_SESSION['user_id']) && in_array($_SESSION['user_role'], ['author', 'admin'])): ?>
        <p class="add-post-link"><a href="<?php echo BASE_PATH; ?>/posts/create">Dodaj nowy post</a></p>
    <?php endif; ?>

    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-item">
                <h2><a href="<?php echo BASE_PATH; ?>/posts/<?php echo htmlspecialchars($post['id']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                <p class="post-meta">Dodano przez: <?php echo htmlspecialchars($post['username']); ?> | <?php echo htmlspecialchars(date('d.m.Y H:i', strtotime($post['created_at']))); ?></p>
                <div class="post-content-excerpt">
                    <?php 
                        echo nl2br(htmlspecialchars(strip_tags($post['content']))); 
                    ?>
                </div>
                <a href="<?php echo BASE_PATH; ?>/posts/<?php echo htmlspecialchars($post['id']); ?>" class="read-more-link">Czytaj więcej &rarr;</a>
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
