<?php
$kasutaja = "zhan";
$parool = "Zxc1234";
$andmebaas = "webfinnal";
$serverinimi = "192.168.174.78";
$yhendus = new mysqli($serverinimi, $kasutaja, $parool, $andmebaas);
$yhendus->set_charset( "utf8");