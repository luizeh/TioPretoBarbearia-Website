    <script src="../../assets/js/shared/csrf.js" defer></script>
    <script src="../../assets/js/shared/form-utils.js" defer></script>
    <script src="../../assets/js/admin/table-filters.js" defer></script>
    <script src="../../assets/js/admin/layout.js" defer></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.all.min.js" defer></script>
    <script src="../../assets/js/shared/swal-theme.js" defer></script>
    <script src="../../assets/js/admin/modal-handler.js" defer></script>
    <script src="../../assets/js/admin/image-upload.js" defer></script>
    <?php foreach ($pageScripts ?? [] as $ps): ?>
        <script src="../../assets/js/admin/<?= htmlspecialchars($ps) ?>" defer></script>
    <?php endforeach; ?>
    </body>

    </html>
