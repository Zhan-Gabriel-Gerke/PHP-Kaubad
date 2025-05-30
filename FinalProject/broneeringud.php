<?php
session_start();
require("SRVconf.php");
require("abifunktsioonid.php");

// Добавление новой брони
if (isset($_POST["broneeringuLisamine"])) {
    lisaBroneering($_POST["kliendi_nimi"], $_POST["laud_id"], $_POST["kuupaev"], $_POST["kellaaeg"], $_POST["inimeste_arv"]);
    header("Location: broneeringud.php?success=1");
    exit();
}

$broneeringud = kysiBroneeringud();

// Получаем данные столов для выпадающего списка
$laudid = kysiLaudadeAndmed();
?>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="success-message">Broneering on edukalt lisatud!</div>
<?php endif; ?>

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
    <input type="number" name="inimeste_arv" id="inimeste_arv" min="1" required />

    <label>Vali laud:</label>
    <select name="laud_id" id="laud_id" required>
        <option value="">-- Vali laud --</option>
        <?php foreach ($laudid as $laud): ?>
            <option value="<?= $laud->laud_id ?>" data-max="<?= $laud->istekohtade_arv ?>">
                Laud #<?= $laud->laud_id ?> (<?= $laud->istekohtade_arv ?> kohta)
            </option>
        <?php endforeach; ?>
    </select>

    <input type="submit" name="broneeringuLisamine" value="Lisa broneering" />
</form>

<script>
    // JS для установки max в зависимости от выбранного стола
    document.getElementById('laud_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const maxSeats = selectedOption.getAttribute('data-max');
        const peopleInput = document.getElementById('inimeste_arv');

        if (maxSeats) {
            peopleInput.max = maxSeats;
            if (peopleInput.value > maxSeats) {
                peopleInput.value = maxSeats;
            }
        } else {
            peopleInput.removeAttribute('max');
        }
    });
</script>

<?php include 'footer.php'; ?>
