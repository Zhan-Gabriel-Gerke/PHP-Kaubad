<?php
require("SRVconf.php");
session_start();

function isAdmin() {
    return isset($_SESSION['admin']) && $_SESSION['admin'];
}

if (isAdmin() && isset($_POST['lisa_toit'])) {
    $nimetus = $_POST['nimetus'] ?? '';
    $hind = $_POST['hind'] ?? '';
    $kategooria = $_POST['kategooria'] ?? '';

    if (!empty($nimetus) && !empty($hind) && !empty($kategooria)) {
        $paring = $yhendus->prepare("INSERT INTO menuu (nimetus, hind, kategooria) VALUES (?, ?, ?)");
        $paring->bind_param("sds", $nimetus, $hind, $kategooria);
        $paring->execute();
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $error = "Kõik väljad peavad olema täidetud!";
    }
}

if (isAdmin() && isset($_POST['kustuta_id'])) {
    $id = $_POST['kustuta_id'];
    $paring = $yhendus->prepare("DELETE FROM menuu WHERE menuu_id = ?");
    $paring->bind_param("i", $id);
    $paring->execute();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

if (isAdmin() && isset($_POST['muuda_id'])) {
    $id = $_POST['muuda_id'];
    $nimetus = $_POST['u_nimetus'] ?? '';
    $hind = $_POST['u_hind'] ?? '';
    $kategooria = $_POST['u_kategooria'] ?? '';

    if (!empty($nimetus) && !empty($hind) && !empty($kategooria)) {
        $paring = $yhendus->prepare("UPDATE menuu SET nimetus=?, hind=?, kategooria=? WHERE menuu_id=?");
        $paring->bind_param("sdsi", $nimetus, $hind, $kategooria, $id);
        $paring->execute();
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $error = "Kõik väljad peavad olema täidetud!";
    }
}

global $yhendus;
$paring = $yhendus->prepare("SELECT menuu_id, nimetus, hind, kategooria FROM menuu ORDER BY kategooria, nimetus");
$paring->execute();
$tulemus = $paring->get_result();

$edit_id = $_POST['edit_id'] ?? null;
?>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<h1>Restorani Menüü</h1>
<table>
    <tr>
        <th>Toidu nimetus</th>
        <th>Hind (€)</th>
        <th>Kategooria</th>
        <?php if (isAdmin()): ?>
            <th>Tegevused</th>
        <?php endif; ?>
    </tr>
    <?php while ($rida = $tulemus->fetch_object()): ?>
        <tr>
            <td><?= htmlspecialchars($rida->nimetus) ?></td>
            <td><?= number_format($rida->hind, 2, '.', '') ?></td>
            <td><?= htmlspecialchars($rida->kategooria) ?></td>
            <?php if (isAdmin()): ?>
                <td>
                    <!-- Удаление -->
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="kustuta_id" value="<?= $rida->menuu_id ?>">
                        <input type="submit" value="Kustuta" onclick="return confirm('Kas oled kindel?');">
                    </form>
                    <!-- Показ формы редактирования -->
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="edit_id" value="<?= $rida->menuu_id ?>">
                        <input type="submit" value="Muuda">
                    </form>
                </td>
            <?php endif; ?>
        </tr>

        <?php if (isAdmin() && $edit_id == $rida->menuu_id): ?>
            <!-- Форма для редактирования -->
            <tr>
                <td colspan="4">
                    <form method="post" class="edit-form">
                        <input type="hidden" name="muuda_id" value="<?= $rida->menuu_id ?>">

                        <label for="u_nimetus_<?= $rida->menuu_id ?>">Nimetus:</label>
                        <input type="text" id="u_nimetus_<?= $rida->menuu_id ?>" name="u_nimetus" value="<?= htmlspecialchars($rida->nimetus) ?>" required>

                        <label for="u_hind_<?= $rida->menuu_id ?>">Hind (€):</label>
                        <input type="number" step="0.01" min="0" id="u_hind_<?= $rida->menuu_id ?>" name="u_hind" value="<?= $rida->hind ?>" required>

                        <label for="u_kategooria_<?= $rida->menuu_id ?>">Kategooria:</label>
                        <input type="text" id="u_kategooria_<?= $rida->menuu_id ?>" name="u_kategooria" value="<?= htmlspecialchars($rida->kategooria) ?>" required>

                        <input type="submit" value="Salvesta">
                    </form>
                </td>
            </tr>
        <?php endif; ?>

    <?php endwhile; ?>
</table>

<?php if (isAdmin()): ?>
    <div class="admin-form">
        <h2>Lisa uus toit</h2>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
        <form method="post">
            <label for="nimetus">Toidu nimetus:</label>
            <input type="text" id="nimetus" name="nimetus" required>

            <label for="hind">Hind (€):</label>
            <input type="number" id="hind" name="hind" step="0.01" min="0" required>

            <label for="kategooria">Kategooria:</label>
            <input type="text" id="kategooria" name="kategooria" required>

            <div style="text-align:center;">
                <input type="submit" name="lisa_toit" value="Lisa menüüsse">
            </div>
        </form>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
