<?php
include('SRVconf.php');
session_start();

if (!empty($_POST['login']) && !empty($_POST['pass'])) {
    $login = htmlspecialchars(trim($_POST['login']));
    $pass = trim($_POST['pass']);

    global $yhendus;
    $paring = $yhendus->prepare("SELECT kasutaja, parool, onadmin FROM kasutaja WHERE kasutaja=?");
    $paring->bind_param('s', $login);
    $paring->execute();
    $paring->bind_result($kasutaja, $parool, $onadmin);
    $paring->fetch();

    if ($kasutaja && password_verify($pass, $parool)) {
        $_SESSION['kasutaja'] = $kasutaja;
        $_SESSION['admin'] = ($onadmin == 1);
        $paring->close();
        $yhendus->close();
        header('Location: adminPanel.php');
        exit();
    } else {
        echo "Kasutajanimi või parool on vale.";
    }
    $paring->close();
    $yhendus->close();
}
?>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>
<body>
<h1>Login</h1>
<form action="" method="post">
    <table>
        <tr>
            <td><label for="login">Login:</label></td>
            <td><input type="text" id="login" name="login" required></td>
        </tr>
        <tr>
            <td><label for="password">Password:</label></td>
            <td><input type="password" id="password" name="pass" required></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" value="Logi sisse"></td>
        </tr>
    </table>
</form>
</body>
<?php include 'footer.php'; ?>