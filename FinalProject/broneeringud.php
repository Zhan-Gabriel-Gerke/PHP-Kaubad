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

// Добавление новой брони
if (isset($_POST["broneeringuLisamine"])) {
    lisaBroneering($_POST["kliendi_nimi"], $_POST["laud_id"], $_POST["kuupaev"], $_POST["kellaaeg"], $_POST["inimeste_arv"]);
    header("Location: broneeringud.php");
    exit();
}

$broneeringud = kysiBroneeringud();
?>

    <!DOCTYPE html>
    <html lang="et">
    <head>
        <meta charset="UTF-8">
        <title>Broneeringud</title>
        <link rel="stylesheet" href="style.css">
    </head>
<body>
    <p>Tere, <?= htmlspecialchars($_SESSION['kasutaja']) ?>!</p>
    <form action="logout.php" method="post">
        <input type="submit" value="Logi välja" name="logout">
    </form>

    <h1>Restorani broneeringud</h1>

    <form action="broneeringud.php" method="post">
        <h2>Uue broneeringu lisamine</h2>
        <label>Kliendi nimi:</label>
        <input type="text" name="kliendi_nimi" required />
        <label>Kuupäev:</label>
        <input type="date" name="kuupaev" required />
        <label>Kellaaeg:</label>
        <input type="time" name="kellaaeg" required />
        <label>Inimeste arv:</label>
        <input type="number" name="inimeste_arv" min="1" required />
        <label>Vali laud:</label>
<?= looRippMenyy("SELECT laud_id, CONCAT('Laud #', laud_id, ' (', istek
