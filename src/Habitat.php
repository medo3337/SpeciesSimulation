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

	public function simulate()
	{
		// For each animal
		foreach ( $animals as $animal )
		{
			$animal->simulate();
		}
	}
}

?>