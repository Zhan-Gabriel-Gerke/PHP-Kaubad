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

// Добавление новой группы
if (isset($_POST["grupilisamine"]) && !empty(trim($_POST["uuegrupinimi"]))) {
    lisaGrupp($_POST["uuegrupinimi"]);
    header("Location: kaubaHaldus.php");
    exit();
}

// Добавление нового товара
if (isset($_POST["kaubalisamine"]) && !empty($_POST["nimetus"])) {
    lisaKaup($_POST["nimetus"], $_POST["kaubagrupi_id"], $_POST["hind"]);
    header("Location: kaubaHaldus.php");
    exit();
}

// Удаление товара — только для админа
if (isset($_GET["kustutusid"]) && isAdmin()) {
    kustutaKaup($_GET["kustutusid"]);
    header("Location: kaubaHaldus.php");
    exit();
}

// Изменение товара — только для админа
if (isset($_POST["muutmine"]) && isAdmin()) {
    muudaKaup($_POST["muudetudid"], $_POST["nimetus"], $_POST["kaubagrupi_id"], $_POST["hind"]);
    header("Location: kaubaHaldus.php");
    exit();
}

$kaubad = kysiKaupadeAndmed();

?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Kaupade leht</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php if (isset($_SESSION['kasutaja'])): ?>
    Tere, <?= htmlspecialchars($_SESSION['kasutaja']) ?>!
    <form action="logout.php" method="post">
        <input type="submit" value="Logi välja" name="logout">
    </form>

    <h1>Kaubad | Kaubagrupid</h1>

    <?php if (isAdmin()): ?>
        <!-- Форма добавления товара -->
        <form action="kaubaHaldus.php" method="post">
            <h2>Kauba lisamine</h2>
            <dl>
                <dt>Nimetus:</dt>
                <dd><input type="text" name="nimetus" required /></dd>
                <dt>Kaubagrupp:</dt>
                <dd>
                    <?php echo looRippMenyy("SELECT id, grupinimi FROM kaubagrupid", "kaubagrupi_id"); ?>
                </dd>
                <dt>Hind:</dt>
                <dd><input type="number" name="hind" step="0.01" required /></dd>
            </dl>
            <input type="submit" name="kaubalisamine" value="Lisa kaup" />
        </form>

        <!-- Форма добавления группы -->
        <form action="kaubaHaldus.php" method="post">
            <h2>Grupi lisamine</h2>
            <input type="text" name="uuegrupinimi" required />
            <input type="submit" name="grupilisamine" value="Lisa grupp" />
        </form>
    <?php endif; ?>

    <!-- Таблица товаров -->
    <h2>Kaupade loetelu</h2>
    <table>
        <tr>
            <th>Haldus</th>
            <th>Nimetus</th>
            <th>Kaubagrupp</th>
            <th>Hind</th>
        </tr>

        <?php foreach ($kaubad as $kaup): ?>
            <tr>
                <?php if (isset($_GET["muutmisid"]) && intval($_GET["muutmisid"]) == $kaup->id): ?>
                    <form action="kaubaHaldus.php" method="post">
                        <td>
                            <input type="submit" name="muutmine" value="Muuda" />
                            <a href="kaubaHaldus.php">Katkesta</a>
                            <input type="hidden" name="muudetudid" value="<?= $kaup->id ?>" />
                        </td>
                        <td>
                            <input type="text" name="nimetus" value="<?= htmlspecialchars($kaup->nimetus) ?>" required />
                        </td>
                        <td>
                            <?php
                            echo looRippMenyy(
                                "SELECT id, grupinimi FROM kaubagrupid",
                                "kaubagrupi_id",
                                $kaup->kaubagrupi_id ?? null
                            );
                            ?>
                        </td>
                        <td>
                            <input type="number" name="hind" value="<?= $kaup->hind ?>" step="0.01" required />
                        </td>
                    </form>
                <?php else: ?>
                    <?php if (isAdmin()): ?>
                        <td>
                            <a href="kaubaHaldus.php?kustutusid=<?= $kaup->id ?>"
                               onclick="return confirm('Kas ikka soovid kustutada?')">x</a>
                            <a href="kaubaHaldus.php?muutmisid=<?= $kaup->id ?>">m</a>
                        </td>
                    <?php else: ?>
                        <td></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($kaup->nimetus) ?></td>
                    <td><?= htmlspecialchars($kaup->grupinimi) ?></td>
                    <td><?= $kaup->hind ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>

    </table>

<?php endif; ?>
</body>
</html>
