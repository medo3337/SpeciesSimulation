<?php
error_reporting(E_ALL);
require('src/autoload.php');

// Habitat
$habitat = new Habitat('Land', 100, 50, [75, 45, 30, 65]);

// Animal
$animal = new Animal('Dog', 50, 50, [35, 100], $habitat);
$animal->simulate();

/*
$life = new SpeciesSimulation();
$life->simulate();
$life->output();
*/


?>