<?php
/**
 * Admin Footer
 */

declare(strict_types=1);
?>
        </main>

        <footer class="admin-footer">
            <span>&copy; <?= date('Y') ?> <?= htmlspecialchars(product()['company']) ?> — Control Center · Authorized personnel only</span>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_PATH ?>/assets/js/app.js"></script>
</body>
</html>
