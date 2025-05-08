<?php
require("abifunktsioonid.php");
$sorttulp="nimetus";
$otsisona="";
if(isSet($_REQUEST["sort"])){
    $sorttulp=$_REQUEST["sort"];
}
if(isSet($_REQUEST["otsisona"])){
    $otsisona=$_REQUEST["otsisona"];
}
$kaubad=kysiKaupadeAndmed($sorttulp, $otsisona);
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Kaupade leht</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
<h1>Kaubad | Kaubagrupid</h1>
<form action="kaubasortimine.php">
    Otsi: <input type="text" name="otsisona" />
    <table>
        <tr>
            <th><a href="kaubasortimine.php?sort=nimetus">Nimetus</a></th>
            <th><a href="kaubasortimine.php?sort=grupinimi">Kaubagrupp</a></th>
            <th><a href="kaubasortimine.php?sort=hind">Hind</a></th>
        </tr>
        <?php foreach($kaubad as $kaup): ?>
            <tr>
                <td><?=$kaup->nimetus ?></td>
                <td><?=$kaup->grupinimi ?></td>
                <td><?=$kaup->hind ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</form>
</body>
</html>