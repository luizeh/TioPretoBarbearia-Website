    <script src="../../assets/js/utils.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="../../assets/js/swal-theme.js"></script>
    <script src="../../assets/js/swal-modals.js"></script>
    <?php foreach ($pageScripts ?? [] as $ps): ?>
        <script src="../../assets/js/<?= htmlspecialchars($ps) ?>"></script>
    <?php endforeach; ?>
    </body>

    </html>