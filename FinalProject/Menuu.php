<?php
require("SRVconf.php");
session_start();

// Получаем все позиции из меню
global $yhendus;
$paring = $yhendus->prepare("SELECT menuu_id, nimetus, hind, kategooria FROM menuu ORDER BY kategooria, nimetus");
$paring->execute();
$tulemus = $paring->get_result();
?>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>
<body>
<h1>Restorani Menüü</h1>
<table border="1" cellpadding="5">
    <tr>
        <th>Toidu nimetus</th>
        <th>Hind (€)</th>
        <th>Kategooria</th>
    </tr>
    <?php while ($rida = $tulemus->fetch_object()): ?>
        <tr>
            <td><?= htmlspecialchars($rida->nimetus) ?></td>
            <td><?= number_format($rida->hind, 2, '.', '') ?></td>
            <td><?= htmlspecialchars($rida->kategooria) ?></td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
<?php include 'footer.php'; ?>
</html>
