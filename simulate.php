<?php
error_reporting(E_ALL);
require('src/autoload.php');

$life = new SpeciesSimulation('config.yml');
$life->simulate();
$life->output();

?>