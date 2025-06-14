<div class="admin-panel-container">
    <h1>Zarządzaj Postami</h1>
    <p>Poniżej znajduje się lista wszystkich postów na blogu. Możesz je edytować lub trwale usunąć.</p>

    <?php if (!empty($posts)): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tytuł</th>
                    <th>Autor</th>
                    <th>Data utworzenia</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post['id']); ?></td>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo htmlspecialchars($post['username']); ?></td>
                        <td><?php echo htmlspecialchars(date('d.m.Y H:i', strtotime($post['created_at']))); ?></td>
                        <td class="actions-cell">
                            <a href="<?php echo BASE_PATH; ?>/posts/<?php echo htmlspecialchars($post['id']); ?>/edit" class="button edit-button">Edytuj</a>
                            <form action="<?php echo BASE_PATH; ?>/posts/<?php echo htmlspecialchars($post['id']); ?>/delete" method="POST">
                                <button type="submit" class="button delete-button" onclick="return confirm('Czy na pewno chcesz usunąć ten post?');">Usuń</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?php echo BASE_PATH; ?>/admin/manage-posts?page=<?php echo $i; ?>" class="<?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p>Brak postów do wyświetlenia.</p>
    <?php endif; ?>
    <p class="admin-back-link"><a href="<?php echo BASE_PATH; ?>/admin">&larr; Wróć do panelu admina</a></p>
</div>
