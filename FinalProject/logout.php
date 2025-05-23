<?php
session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login2.php');
    exit();
}
?>
