<?php
/*
 * Habitat class
 * @author: Mohamed Abowarda <Medo3337@hotmail.com>
 *
 */

class Habitat
{
	public $name;

	/*
	 * Store current temperature
	 */
	public $currentTemperature;

	/*
	 * Current living animals
	 */
	public $animals;

	/*
	 * Current food and water
	 */
	public $currentFood;
	public $currentWater;

	/*
	 * Total number of born/death
	 */
	public $totalBorn  = 0;
	public $totalDeath = 0;

	public function __construct($habitatInfo)
	{
		foreach ( $habitatInfo as $key => $value )
		{
			$this->{$key} = $value;
		}
		// Convert associative array to indexed array
		$this->average_temperature = array_values($this->average_temperature);
	}

	public function simulate($currentMonth)
	{
		if ( count($this->animals) < 1 )
		{
			// No animals exists on this habitat
			return false;
		}

		// To convert month to season id
		$monthSeason = array(6  => 0, 7  => 0, 8  => 0,    // Summer = 0
							 3  => 1, 4  => 1, 5  => 1,    // Spring = 1
							 9  => 2, 10 => 2, 11 => 2,    // Fall   = 2
							 12 => 3, 1  => 3, 2  => 3);   // Winter = 3

		// Season id
		$currentSeasonId = $monthSeason[($currentMonth % 12) + 1];

		// Monthly food supply
		$this->currentFood  = $this->monthly_food;
		$this->currentWater = $this->monthly_water;

		// Change temperature for this month based on the current season
		// 0.5% chance of having up to a 15 degree fluctuation
		if ( rand(1, 200) <= 5 )
		{
			$this->currentTemperature = $this->average_temperature[$currentSeasonId] + rand(-15, 15);
		} else {
			// The temperature for the month should fluctuate randomly above/below the average by up to 5 degrees
			$this->currentTemperature = $this->average_temperature[$currentSeasonId] + rand(-5, 5);
		}

		// For each animal
		foreach ( $this->animals as $animal )
		{
			$animal->simulate($currentMonth, $currentSeasonId);
		}
		return true;
	}
}

?>