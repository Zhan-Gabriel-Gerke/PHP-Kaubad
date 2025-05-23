<?php
require("abifunktsioonid.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nimi = htmlspecialchars(trim($_POST["nimi"]));
    $laud_id = intval($_POST["laud_id"]);
    $kuupaev = $_POST["kuupaev"];
    $kellaaeg = $_POST["kellaaeg"];
    $inimeste_arv = intval($_POST["inimeste_arv"]);

    lisaBroneering($nimi, $laud_id, $kuupaev, $kellaaeg, $inimeste_arv);
    header("Location: broneeringud.php");
    exit();
}

$laudadeAndmed = kysiLaudadeAndmed();
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Uus broneering</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Lisa uus broneering</h1>
<form method="post">
    <label>Kliendi nimi: <input type="text" name="nimi" required></label><br><br>
    <label>Kuupäev: <input type="date" name="kuupaev" required></label><br><br>
    <label>Kellaaeg: <input type="time" name="kellaaeg" required></label><br><br>
    <label>Inimeste arv: <input type="number" name="inimeste_arv" min="1" required></label><br><br>

    <label>Vali laud:</label>
    <select name="laud_id" required>
        <?php foreach ($laudadeAndmed as $laud): ?>
            <option value="<?= $laud->laud_id ?>">Laud #<?= $laud->laud_id ?> - <?= $laud->istekohtade_arv ?> kohta (<?= htmlspecialchars($laud->asukoht) ?>)</option>
        <?php endforeach; ?>
    </select><br><br>

    <input type="submit" value="Broneeri laud">
</form>
</body>
</html>
