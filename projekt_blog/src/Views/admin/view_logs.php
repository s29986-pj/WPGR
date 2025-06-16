<div class="admin-panel-container">
    <h1><?php echo htmlspecialchars($logTitle); ?></h1>
    <p>
        <?php echo htmlspecialchars($logDescription); ?> 
        <code><?php echo htmlspecialchars($logFileName); ?></code>.
    </p>

    <div class="log-container">
        <pre><?php echo htmlspecialchars($logContent); ?></pre>
    </div>
    
    <p class="admin-back-link"><a href="<?php echo BASE_PATH; ?>/admin">&larr; Wróć do panelu admina</a></p>
</div>
