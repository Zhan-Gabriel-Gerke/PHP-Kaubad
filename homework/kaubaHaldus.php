<?php
require("abifunktsioonid.php");

if(isset($_REQUEST["maakonnalisamine"]) && !empty(trim($_REQUEST["maakonnanimi"]))){
    lisaMaakond($_REQUEST["maakonnanimi"], $_REQUEST["maakonnakeskus"]);
    header("Location: kaubaHaldus.php");
    exit();
}

if(isset($_REQUEST["temperatuuriLisamine"]) && !empty($_REQUEST["temperatuur"])){
    lisaTemperatuur($_REQUEST["temperatuur"], $_REQUEST["kuupaev_kellaaeg"], $_REQUEST["maakonna_id"]);
    header("Location: kaubaHaldus.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ilmahaldus</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Температурные данные</h1>

<form action="kaubaHaldus.php">
    <h2>Добавить уезд</h2>
    Название уезда: <input type="text" name="maakonnanimi" />
    Центр: <input type="text" name="maakonnakeskus" />
    <input type="submit" name="maakonnalisamine" value="Добавить уезд" />
</form>

<form action="kaubaHaldus.php">
    <h2>Добавить температуру</h2>
    Температура: <input type="text" name="temperatuur" />
    Дата и время: <input type="datetime-local" name="kuupaev_kellaaeg" />
    Уезд:
    <?php echo looRippMenyy("SELECT id, maakonnanimi FROM maakonnad", "maakonna_id"); ?>
    <input type="submit" name="temperatuuriLisamine" value="Добавить" />
</form>

<a href="kaubasortimine.php">Посмотреть таблицу температур</a>
</body>
</html>
