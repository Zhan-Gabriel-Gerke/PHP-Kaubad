<?php
$parool = 'teenindaja';
$sool = 'cool';
$kryp = crypt($parool, $sool);
echo $kryp;