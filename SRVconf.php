<?php
$kasutaja = "zhan";
$parool = "Zxc1234";
$andmebaas = "web";
$serverinimi = "192.168.1.180";

$yhendus = new mysqli($serverinimi, $kasutaja, $parool, $andmebaas);
$yhendus->set_charset("utf8");