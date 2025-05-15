<?php include('SRVconf.php'); ?>
<?php
session_start();
/*if (isset($_SESSION['tuvastamine'])) {
    header('Location: kaubaHaldus.php');
    exit();
}*/
global $yhendus;
//kontrollime kas väljad on täidetud
if (!empty($_POST['login']) && !empty($_POST['pass'])) {
    $login = htmlspecialchars(trim($_POST['login']));
    $pass = htmlspecialchars(trim($_POST['pass']));
    $sool = 'cool';
    $krypt = crypt($pass, $sool);

    $paring = $yhendus->prepare("SELECT kasutaja, parool, onadmin FROM kasutajad WHERE kasutaja=? AND parool=?");
    $paring->bind_param('ss', $login, $krypt);
    $paring->bind_result($kasutaja, $parool, $onadmin);
    $paring->execute();

    if ($paring->fetch() && $parool == $krypt) {
        $_SESSION['kasutaja'] = $login;
        //заменила if ($onadmin == 1) на то что ниже
        $_SESSION['admin'] = ($onadmin == 1);
        //if ($onadmin == 1) {
        //$_SESSION['admin'] = true;
        //}
        $paring->close();
        $yhendus->close();
        header('Location: kaubaHaldus.php');
        exit();
    } else {
        echo "kasutaja või parool on vale";
        $paring->close();
        $yhendus->close();
    }
}


?>
<link rel="stylesheet" href="style.css">
<h1>Login</h1>
<form action="" method="post">
    <table>
        <tr>
            <td>
                <label for="login">Login:</label>
            </td>
            <td>
                <input type="text" id="login" name="login">
            </td>
        </tr>
        <tr>
            <td>
                <label for="login">Password:</label>
            </td>
            <td>
                <input type="password" id="password" name="pass">
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" value="Logi sisse"</td>
        </tr>
    </table>
</form>
<h2>Registrerimine</h2>
<a href="singin.php">Registrerimine</a>