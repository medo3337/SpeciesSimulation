<?php
/*
 * Habitat class
 * @author: Mohamed Abowarda
 */

class Habitat
{
	public $name;

	/*
	 * Monthly available food and water
	 */
	public $monthlyFood;
	public $monthlyWater;

	/*
	 * Current available food
	 */
	public $currentFood;
	public $currentWater;

	/*
	 * Temperature for all seasons
	 */
	public $temperature;

	/*
	 * Current living animals
	 */
	public $animals;

	public function __construct( $name, $monthlyFood, $monthlyWater, $temperature )
	{
		$this->name = $name;
		$this->monthlyFood = $monthlyFood;
		$this->monthlyWater = $monthlyWater;
		$this->temperature = $temperature;
	}

	public function simulate($currentMonth)
	{
		// To convert month to season id
		$monthSeason = array(6  => 0, 7  => 0, 8  => 0,    // Summer
							 3  => 1, 4  => 1, 5  => 1,    // Spring
							 9  => 2, 10 => 3, 11 => 3,    // Fall
							 12 => 4, 1  => 4, 2  => 5);   // Winter

		// For each animal
		foreach ( $this->animals as $animal )
		{
			$animal->simulate($currentMonth, $monthSeason[($currentMonth % 12) + 1]);
		}
	}
}

?>