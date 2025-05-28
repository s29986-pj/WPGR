<h1>Witaj na blogu!</h1>
<?php if (isset($_SESSION['user_id'])): ?>
    <p>Cieszymy się, że znowu jesteś z nami, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
<?php endif; ?>
