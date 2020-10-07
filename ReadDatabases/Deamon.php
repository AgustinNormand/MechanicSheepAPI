<?php




$cp = new Comparator();
$cp->setCheckpoint('../Databases/sermae2.dbf');
$cp->checkDiferences('../Databases/sermae2.dbf', 150);
?>