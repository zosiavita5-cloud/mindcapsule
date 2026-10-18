
</main>

<footer class="site-footer">
    <p>made with care, for the days that feel a little heavy.</p>
    <p class="footer-brand">
        MIND CAPSULE &copy; <?= date('Y') ?>
    </p>
</footer>

<?php
$currentScript = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isAdminPage = strpos($currentScript, '/admin/') !== false;
$baseUrl = $isAdminPage ? '../' : '';
?>

<script src="<?= $baseUrl ?>assets/js/script.js"></script>
</body>
</html>
