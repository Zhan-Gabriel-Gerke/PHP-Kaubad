<?php
$parool = 'opilane';
$sool = 'cool';
$kryp = crypt($parool, $sool);
echo $kryp;