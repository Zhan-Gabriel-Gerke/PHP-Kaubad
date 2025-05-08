<?php
$kasutaja = "zhan";
$parool = "Zxc1234";
$andmebaas = "web";
$serverinimi = "localhost";

$yhendus = new mysqli($serverinimi, $kasutaja, $parool, $andmebaas);
$yhendus->set_charset("utf8");