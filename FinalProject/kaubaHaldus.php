<?php
session_start();
require("SRVconf.php");
require("abifunktsioonid.php");

if (!isset($_SESSION['kasutaja'])) {
    header('Location: login2.php');
    exit();
}

function isAdmin() {
    return isset($_SESSION['admin']) && $_SESSION['admin'];
}

// Функция для получения одной брони по ID
function kysiBroneeringId($id) {
    global $yhendus;
    $paring = $yhendus->prepare("SELECT broneering_id, kliendi_nimi, laud_id, kuupaev, kellaaeg, inimiste_arv FROM broneering WHERE broneering_id = ?");
    $paring->bind_param('i', $id);
    $paring->execute();
    $tulemus = $paring->get_result();
    $rida = $tulemus->fetch_assoc();
    $paring->close();
    return $rida;
}

// Функция для обновления брони
function muudaBroneering($id, $kliendi_nimi, $laud_id, $kuupaev, $kellaaeg, $inimiste_arv) {
    global $yhendus;
    $paring = $yhendus->prepare("UPDATE broneering SET kliendi_nimi=?, laud_id=?, kuupaev=?, kellaaeg=?, inimiste_arv=? WHERE broneering_id=?");
    $paring->bind_param('sissii', $kliendi_nimi, $laud_id, $kuupaev, $kellaaeg, $inimiste_arv, $id);
    $paring->execute();
    $paring->close();
}

// Добавление новой резервации
if (isset($_POST["broneeri"])) {
    lisaBroneering(
        trim($_POST["kliendi_nimi"]),
        $_POST["laud_id"],
        $_POST["kuupaev"],
        $_POST["kellaaeg"],
        $_POST["inimiste_arv"]
    );
    header("Location: kaubaHaldus.php");
    exit();
}

// Сохранение изменений брони
if (isset($_POST['salvesta_muudatused']) && isset($_POST['broneering_id'])) {
    muudaBroneering(
        $_POST['broneering_id'],
        trim($_POST["kliendi_nimi"]),
        $_POST["laud_id"],
        $_POST["kuupaev"],
        $_POST["kellaaeg"],
        $_POST["inimiste_arv"]
    );
    header("Location: kaubaHaldus.php");
    exit();
}

// Удаление резервации — только для админа
if (isset($_GET["kustutusid"]) && isAdmin()) {
    kustutaBroneering($_GET["kustutusid"]);
    header("Location: kaubaHaldus.php");
    exit();
}

// Получаем все брони
$broneeringud = kysiBroneeringud();

// Если есть параметр muudaid — получаем одну бронь для редактирования
$muudetavBroneering = null;
if (isset($_GET['muudaid'])) {
    $muudetavBroneering = kysiBroneeringId($_GET['muudaid']);
}
?>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>
<body>
<?php if (isset($_SESSION['kasutaja'])): ?>
    <p>Tere, <?= htmlspecialchars($_SESSION['kasutaja']) ?>!</p>
    <h1>Restorani broneeringute haldus</h1>

    <?php if ($muudetavBroneering): ?>
        <form action="kaubaHaldus.php" method="post" style="margin-bottom: 30px;">
            <h2>Muuda broneeringut (ID: <?= $muudetavBroneering['broneering_id'] ?>)</h2>
            <input type="hidden" name="broneering_id" value="<?= $muudetavBroneering['broneering_id'] ?>" />

            <label>Kliendi nimi:</label>
            <input type="text" name="kliendi_nimi" value="<?= htmlspecialchars($muudetavBroneering['kliendi_nimi']) ?>" required />

            <label>Kuupäev:</label>
            <input type="date" name="kuupaev" value="<?= $muudetavBroneering['kuupaev'] ?>" required />

            <label>Kellaaeg:</label>
            <input type="time" name="kellaaeg" value="<?= $muudetavBroneering['kellaaeg'] ?>" required />

            <label>Inimeste arv:</label>
            <input type="number" name="inimiste_arv" min="1" value="<?= $muudetavBroneering['inimiste_arv'] ?>" required />

            <label>Vali laud (istekohtade arv):</label>
            <?= looLaudRippMenyy("SELECT laud_id, istekohtade_arv FROM laud", "laud_id", $muudetavBroneering['laud_id']) ?>

            <input type="submit" name="salvesta_muudatused" value="Salvesta muudatused" />
            <a href="kaubaHaldus.php" style="margin-left:15px;">Katkesta</a>
        </form>
    <?php else: ?>
        <form action="kaubaHaldus.php" method="post" style="margin-bottom: 30px;">
            <h2>Uue broneeringu lisamine</h2>
            <label>Kliendi nimi:</label>
            <input type="text" name="kliendi_nimi" required />

            <label>Kuupäev:</label>
            <input type="date" name="kuupaev" required />

            <label>Kellaaeg:</label>
            <input type="time" name="kellaaeg" required />

            <label>Inimeste arv:</label>
            <input type="number" name="inimiste_arv" min="1" required />

            <label>Vali laud (istekohtade arv):</label>
            <?= looLaudRippMenyy("SELECT laud_id, istekohtade_arv FROM laud", "laud_id") ?>

            <input type="submit" name="broneeri" value="Broneeri laud" />
        </form>
    <?php endif; ?>

    <h2>Broneeringute tabel</h2>
    <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: center;">
        <tr>
            <th>ID</th>
            <th>Kliendi nimi</th>
            <th>Kuupäev</th>
            <th>Kellaaeg</th>
            <th>Inimeste arv</th>
            <th>Laua number</th> <!-- изменено -->
            <th>Toimingud</th>
        </tr>
        <?php foreach ($broneeringud as $broneering): ?>
            <tr>
                <td><?= $broneering['broneering_id'] ?></td>
                <td><?= htmlspecialchars($broneering['kliendi_nimi']) ?></td>
                <td><?= $broneering['kuupaev'] ?></td>
                <td><?= $broneering['kellaaeg'] ?></td>
                <td><?= $broneering['inimiste_arv'] ?></td>
                <td><?= $broneering['laud_id'] ?></td> <!-- изменено -->
                <td>
                    <a href="kaubaHaldus.php?muudaid=<?= $broneering['broneering_id'] ?>">Muuda</a> |
                    <?php if (isAdmin()): ?>
                        <a href="kaubaHaldus.php?kustutusid=<?= $broneering['broneering_id'] ?>" onclick="return confirm('Kas soovid kustutada?')">Kustuta</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php endif; ?>
</body>
<?php include 'footer.php'; ?>
</html>
