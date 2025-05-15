<?php
session_start();
require('SRVconf.php');
require('abifunktsioonid.php');

if (!isset($_SESSION['kasutaja'])) {
    header('Location: login2.php');
    exit();
}
/*if (!isset($_SESSION['admin'])) {
    $_SESSION['admin'] = false;
}*/

// eluba !empty ja trim - tühiku lisamine
if(isSet($_REQUEST["grupilisamine"]) && !empty(trim($_REQUEST["uuegrupinimi"]))){
    if (grupinimiKontroll(trim($_REQUEST["uuegrupinimi"]))==0) {
        lisaGrupp($_REQUEST["uuegrupinimi"]);
        header("Location: kaubahaldus.php");
        exit();
    }
}
if(isSet($_REQUEST["kaubalisamine"]) && !empty(trim($_REQUEST["nimetus"]))){
    lisaKaup($_REQUEST["nimetus"], $_REQUEST["kaubagrupi_id"], $_REQUEST["hind"]);
    header("Location: kaubahaldus.php");
    exit();
}
if(isSet($_REQUEST["kustutusid"])){
    kustutaKaup($_REQUEST["kustutusid"]);
}
if(isSet($_REQUEST["muutmine"])){
    muudaKaup($_REQUEST["muudetudid"], $_REQUEST["nimetus"],
        $_REQUEST["kaubagrupi_id"], $_REQUEST["hind"]);
}

$kaubad=kysiKaupadeAndmed();

function isAdmin(){
    return isset($_SESSION['admin']) && $_SESSION['admin'];
}
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="stylesheet" href="kaubastyle.css">
</head>
<header>
    <h1>Kaupade leht</h1>
</header>
<body>
<p>Tere, <?=$_SESSION['kasutaja']?>!</p>

<form action="logout.php" method="post">
    <input type="submit" value="Logi välja" name="logout">
    <?php
    if (isset($_SESSION['kasutaja'])) {
    ?>
</form>
<?php
if (isAdmin()) {
?>
<h1>Kaubad / Kaubagrupid</h1>
<main>
    <form action="kaubahaldus.php">
        <h2>Kauba lisamine</h2>
        <dl>
            <dt><label for="nimetus">Nimetus:</label></dt>
            <dd><input type="text" id="nimetus" name="nimetus" /></dd>
            <dt>Kaubagrupp:</dt>
            <dd><?php
                echo looRippMenyy("SELECT id, grupinimi FROM kaubagrupid",
                    "kaubagrupi_id");
                ?>
            </dd>
            <dt><label for="hind">Hind:</label></dt>
            <dd><input type="text" id = "hind" name="hind" /></dd>
        </dl>
        <input type="submit" name="kaubalisamine" value="Lisa kaup" />
        <h2><label for="uuegrupinimi">Grupi lisamine:</label></h2>
        <input type="text" id = "uuegrupinimi" name="uuegrupinimi" />
        <input type="submit" name="grupilisamine" value="Lisa grupp" />
        <?php
        // grupnimi kontroll
        if(isSet($_REQUEST["uuegrupinimi"])){
            if(grupinimiKontroll(trim($_REQUEST["uuegrupinimi"]))==0){
                echo "Sisestatud grupinimi on olemas!";
            }
        }
        ?>
        <?php
        }
        ?>
    </form>
    <form action="kaubahaldus.php">
        <h2>Kaupade loetelu</h2>
        <table>
            <tr>
                <th>Haldus</th>
                <th>Nimetus</th>
                <th>Kaubagrupp</th>
                <th>Hind</th>
            </tr>
            <?php foreach($kaubad as $kaup): ?>
            <tr>
                <?php if(isSet($_REQUEST["muutmisid"]) &&
                intval($_REQUEST["muutmisid"])==$kaup->id): ?>
                    <td>
                        <input type="submit" name="muutmine" value="Muuda" />
                        <input type="submit" name="katkestus" value="Katkesta" />
                        <input type="hidden" name="muudetudid" value="<?=$kaup->id ?>" />
                    </td>
                    <td><input type="text" name="nimetus" value="<?=$kaup->nimetus ?>" /></td>
                    <td><?php
                        echo looRippMenyy("SELECT id, grupinimi FROM kaubagrupid",
                            "kaubagrupi_id", $kaup->id);
                        ?></td>
                    <td><input type="text" name="hind" value="<?=$kaup->hind ?>" /></td>
                <?php else: ?>
                <td>
                    <?php if (isAdmin()): ?>
                        <a href="kaubaHaldus.php?kustutusid=<?=$kaup->id ?>"
                           onclick="return confirm('Kas ikka soovid kustutada?')">Kustuta</a>
                    <?php endif; ?>
                    <a href="kaubaHaldus.php?muutmisid=<?=$kaup->id ?>">Muuda</a>
                </td>
                    <td><?=$kaup->nimetus ?></td>
                    <td><?=$kaup->grupinimi ?></td>
                    <td><?=$kaup->hind ?></td>
                <?php endif ?>
            </tr>
            <?php endforeach; ?>
        </table>
    </form>
</main>
<?php
}
?>
</body>
<footer>
    <div id="jalusekiht">
    </div>
</footer>
</html>