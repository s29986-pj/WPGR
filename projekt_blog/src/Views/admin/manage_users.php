<div class="admin-panel-container">
    <h1>Zarządzaj Użytkownikami</h1>
    <p>Zmień role użytkowników lub usuń ich konta. Nie możesz edytować ani usunąć własnego konta z tej listy.</p>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th>E-mail</th>
                <th>Rola</th>
                <th>Aktywny</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <form action="<?php echo BASE_PATH; ?>/admin/process-user-action" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <input type="hidden" name="action" value="change_role">
                            <select name="role" onchange="this.form.submit()">
                                <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                                <option value="author" <?php echo ($user['role'] === 'author') ? 'selected' : ''; ?>>Author</option>
                                <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </form>
                    </td>
                    <td><?php echo $user['is_active'] ? 'Tak' : 'Nie'; ?></td>
                    <td class="actions-cell">
                        <form action="<?php echo BASE_PATH; ?>/admin/process-user-action" method="POST" class="form-delete">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <input type="hidden" name="action" value="delete_user">
                            <button type="submit" class="button delete-button">Usuń</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="admin-back-link"><a href="<?php echo BASE_PATH; ?>/admin">&larr; Wróć do panelu admina</a></p>
</div>
