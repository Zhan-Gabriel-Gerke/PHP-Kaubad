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

// Добавление новой резервации
if (isset($_POST["broneeri"])) {
    lisaBroneering(
        trim($_POST["kliendi_nimi"]),
        $_POST["laud_id"],
        $_POST["kuupaev"],
        $_POST["kellaaeg"],
        $_POST["inimeste_arv"]
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

$broneeringud = kysiBroneeringud();
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Restorani broneeringud</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php if (isset($_SESSION['kasutaja'])): ?>
    <p>Tere, <?= htmlspecialchars($_SESSION['kasutaja']) ?>!</p>
    <form action="logout.php" method="post">
        <input type="submit" value="Logi välja" name="logout">
    </form>

    <h1>Restorani broneeringute haldus</h1>

    <form action="kaubaHaldus.php" method="post">
        <h2>Uue broneeringu lisamine</h2>
        <label>Kliendi nimi:</label>
        <input type="text" name="kliendi_nimi" required />

        <label>Kuupäev:</label>
        <input type="date" name="kuupaev" required />

        <label>Kellaaeg:</label>
        <input type="time" name="kellaaeg" required />

        <label>Inimeste arv:</label>
        <input type="number" name="inimeste_arv" min="1" required />

        <label>Vali laud (istekohtade arv):</label>
        <?= looLaudRippMenyy("SELECT laud_id, istekohtade_arv FROM laud", "laud_id") ?>

        <input type="submit" name="broneeri" value="Broneeri laud" />
    </form>

    <h2>Broneeringute tabel</h2>
    <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: center;">
        <tr>
            <th>ID</th>
            <th>Kliendi nimi</th>
            <th>Kuupäev</th>
            <th>Kellaaeg</th>
            <th>Inimeste arv</th>
            <th>Istekohtade arv</th>
            <th>Toimingud</th>
        </tr>
        <?php foreach ($broneeringud as $broneering): ?>
            <tr>
                <td><?= $broneering['broneering_id'] ?></td>
                <td><?= htmlspecialchars($broneering['kliendi_nimi']) ?></td>
                <td><?= $broneering['kuupaev'] ?></td>
                <td><?= $broneering['kellaaeg'] ?></td>
                <td><?= $broneering['inimiste_arv'] ?></td>
                <td><?= $broneering['istekohtade_arv'] ?></td>
                <td>
                    <a href="kaubaHaldus.php?kustutusid=<?= $broneering['broneering_id'] ?>" onclick="return confirm('Kas soovid kustutada?')">Kustuta</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</body>
</html>
