<?php
$kasutaja = "d133843_testuser";
$parool = "NL_.exCd79LEtfn";
$andmebaas = "d133843_phpbaas";
$serverinimi = "d133843.mysql.zonevs.eu";

$yhendus = new mysqli($serverinimi, $kasutaja, $parool, $andmebaas);
$yhendus->set_charset("utf8");