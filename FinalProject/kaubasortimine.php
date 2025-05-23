<?php
require("abifunktsioonid.php");

$sorttulp = "kuupaev_kellaaeg";
$otsisona = "";

if (isset($_REQUEST["sort"])) {
    $sorttulp = $_REQUEST["sort"];
}
if (isset($_REQUEST["otsisona"])) {
    $otsisona = $_REQUEST["otsisona"];
}

$temperatuurid = kysiTemperatuuriAndmed($sorttulp, $otsisona);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ilmaandmed</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Температуры по уездам</h1>
<form action="kaubasortimine.php" method="get">
    Поиск: <input type="text" name="otsisona" value="<?= htmlspecialchars($otsisona) ?>" />
    <input type="submit" value="Искать" />
    <table border="1" cellpadding="5">
        <tr>
            <th><a href="?sort=kuupaev_kellaaeg">Дата и время</a></th>
            <th><a href="?sort=temperatuur">Температура</a></th>
            <th><a href="?sort=maakonnanimi">Уезд</a></th>
        </tr>
        <?php foreach ($temperatuurid as $temp): ?>
            <tr>
                <td><?= htmlspecialchars($temp->kuupaev_kellaaeg) ?></td>
                <td><?= htmlspecialchars($temp->temperatuur) ?> °C</td>
                <td><?= htmlspecialchars($temp->maakonnanimi) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</form>
</body>
</html>
