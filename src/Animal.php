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
	 * The current animal age
	 */
	private $age;

	/*
	 * Habitat class to hold information about the habitat that the animal is on
	 */
	private $habitat;

	public function __construct($habitat)
	{
		$this->habitat = $habitat;
	}

	public function simulate($currentMonth, $currentSeason)
	{
		// Simulation will make one month changes

		// Update age
		$this->age = $currentMonth / 12;

		// Determine if the animal will die this month
		$this->determineDeath($currentSeason);

		echo 'animal simulation in progress';
	}

	public function determineDeath($currentSeason)
	{
		// Due to aging
		if ( $this->age >= $this->lifeSpan )
		{
			$this->death('Age');
		}

		// Due to temperature
		// Get current temperature
		$temperature = $this->habitat->temperature[$currentSeason];
		if ( !($temperature[0] >= $this->minTemperature && $temperature[1] <= $this->maxTemperature) )
		{
			$this->death('Temperature');
		}

		// Due to starvation
		if ( $this->habitat->currentFood <= 0 )
		{
			$this->death('Starvation');
		}

		// Due to dehydration
		if ( $this->habitat->currentWater <= 0 )
		{
			$this->death('Dehydration');
		}

	}

	public function death( $cause )
	{
		//$habitat 
	}
}

?>