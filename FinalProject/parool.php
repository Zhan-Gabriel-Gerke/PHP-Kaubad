<?php
$parool = 'user';
$sool = 'cool';
$kryp = crypt($parool, $sool);
echo $kryp;