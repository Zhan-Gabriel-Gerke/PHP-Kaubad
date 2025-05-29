<?php
session_start();

require("SRVconf.php");
require("abifunktsioonid.php");

// проверяем авторизацию
if (!isset($_SESSION['kasutaja'])) {
    header('Location: login2.php');
    exit();
}
//проверяет админ или нет
function isAdmin() {
    return isset($_SESSION['admin']) && $_SESSION['admin'];
}

// функция для получения данных одной брони по ID
function kysiBroneeringId($id) {
    global $yhendus;
    $paring = $yhendus->prepare("
        SELECT broneering_id, kliendi_nimi, laud_id, kuupaev, kellaaeg, inimiste_arv
        FROM broneering
        WHERE broneering_id = ?
    ");
    $paring->bind_param('i', $id);
    $paring->execute();
    $tulemus = $paring->get_result();
    $rida = $tulemus->fetch_assoc();
    $paring->close();
    return $rida;
}

// функция для обновления брони по ID
function muudaBroneering($id, $kliendi_nimi, $laud_id, $kuupaev, $kellaaeg, $inimiste_arv) {
    global $yhendus;
    $paring = $yhendus->prepare("
        UPDATE broneering 
        SET kliendi_nimi=?, laud_id=?, kuupaev=?, kellaaeg=?, inimiste_arv=? 
        WHERE broneering_id=?
    ");
    // привязываем параметры в нужном порядке
    $paring->bind_param('sissii', $kliendi_nimi, $laud_id, $kuupaev, $kellaaeg, $inimiste_arv, $id);
    $paring->execute();
    $paring->close();
}

// обработка отправки формы для сохранения изменений брони
if (isset($_POST['salvesta_muudatused']) && isset($_POST['broneering_id'])) {
    muudaBroneering(
        $_POST['broneering_id'],
        trim($_POST["kliendi_nimi"]),
        $_POST["laud_id"],
        $_POST["kuupaev"],
        $_POST["kellaaeg"],
        $_POST["inimiste_arv"]
    );
    header("Location: adminPanel.php");
    exit();
}

// удаление только для адм работает
if (isset($_GET["kustutusid"]) && isAdmin()) {
    kustutaBroneering($_GET["kustutusid"]);
    header("Location: adminPanel.php");
    exit();
}

// Поиск бронирований по имени клиента или дате
$otsing = "";
$broneeringud = [];

if (isset($_GET['otsi'])) {
    $otsing = trim($_GET['otsi']);
    global $yhendus;
    $paring = $yhendus->prepare("
        SELECT * FROM broneering 
        WHERE kliendi_nimi LIKE CONCAT('%', ?, '%') 
        OR kuupaev = ?
        ORDER BY kuupaev DESC, kellaaeg ASC
    ");
    $paring->bind_param('ss', $otsing, $otsing);
    $paring->execute();
    $tulemus = $paring->get_result();
    while ($rida = $tulemus->fetch_assoc()) {
        $broneeringud[] = $rida;
    }
    $paring->close();
} else {
    $broneeringud = kysiBroneeringud(); // Получаем все брони
}

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

    <!-- форма поиска -->
    <form action="adminPanel.php" method="get" style="margin-bottom: 30px;">
        <h2>Otsi broneeringuid</h2>
        <input type="text" name="otsi" placeholder="Kliendi nimi või kuupäev (YYYY-MM-DD)" value="<?= htmlspecialchars($otsing) ?>" />
        <input type="submit" value="Otsi" />
        <a href="adminPanel.php" style="margin-left: 15px;">Näita kõiki</a>
    </form>
    <?php if ($muudetavBroneering): ?>
        <!-- форма редактирования брони -->
        <form action="adminPanel.php" method="post" style="margin-bottom: 30px;">
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
            <!-- создание выпадающего меню -->

            <input type="submit" name="salvesta_muudatused" value="Salvesta muudatused" />
            <a href="adminPanel.php" style="margin-left:15px;">Katkesta</a>
        </form>
    <?php endif; ?>

    <h2>Broneeringute tabel</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Kliendi nimi</th>
            <th>Kuupäev</th>
            <th>Kellaaeg</th>
            <th>Inimeste arv</th>
            <th>Laua number</th>
            <?php if (isAdmin()): ?>
                <th>Toimingud</th>
            <?php endif; ?>
        </tr>

        <?php if (count($broneeringud) === 0): ?>
            <tr><td colspan="7">Broneeringuid ei leitud</td></tr>
        <?php else: ?>
            <?php foreach ($broneeringud as $broneering): ?>
                <tr>
                    <td><?= $broneering['broneering_id'] ?></td>
                    <td><?= htmlspecialchars($broneering['kliendi_nimi']) ?></td>
                    <td><?= $broneering['kuupaev'] ?></td>
                    <td><?= $broneering['kellaaeg'] ?></td>
                    <td><?= $broneering['inimiste_arv'] ?></td>
                    <td><?= $broneering['laud_id'] ?></td>
                    <?php if (isAdmin()): ?>
                        <td>
                            <!-- кнопки на ред -->
                            <a href="adminPanel.php?muudaid=<?= $broneering['broneering_id'] ?>">Muuda</a>
                            |
                            <a href="adminPanel.php?kustutusid=<?= $broneering['broneering_id'] ?>" onclick="return confirm('Kas soovid kustutada?')">Kustuta</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

    </table>
<?php endif; ?>

</body>
<?php include 'footer.php'; ?>
</html>