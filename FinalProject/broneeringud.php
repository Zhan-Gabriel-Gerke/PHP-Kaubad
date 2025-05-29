<?php
session_start();
require("SRVconf.php");
require("abifunktsioonid.php");
// Добавление новой брони
if (isset($_POST["broneeringuLisamine"])) {
    lisaBroneering($_POST["kliendi_nimi"], $_POST["laud_id"], $_POST["kuupaev"], $_POST["kellaaeg"], $_POST["inimeste_arv"]);
    header("Location: broneeringud.php");
    exit();
}
$broneeringud = kysiBroneeringud();
?>
<!-- Страница создания брони для обычного юзера-->
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>
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
    <?= looRippMenyy("SELECT laud_id, CONCAT('Laud #', laud_id, ' (', istekohtade_arv, ' kohta)') as nimetus FROM laud", "laud_id") ?>
    <input type="submit" name="broneeringuLisamine" value="Lisa broneering" />
</form>
<?php include 'footer.php'; ?>