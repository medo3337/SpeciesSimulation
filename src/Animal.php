<?php

class Animal
{
	private $name;

	/*
	 * Monthly food and water consumption
	 */
	private $monthlyFoodConsumption;
	private $monthlyWaterConsumption;

	/*
	 * Animal life span
	 */
	private $lifeSpan;

	/*
	 * Minimum and maximum temperature for the animal
	 */
	private $minTemperature;
	private $maxTemperature;

	/*
	 * Habitat class to hold information about the habitat that the animal is on
	 */
	private $habitat;

	public function __construct($habitat)
	{
		$this->habitat = $habitat;
	}

	function simulate()
	{
		// Each simulation will make one month changes

		echo 'animal simulation in progress';
	}
}

?>