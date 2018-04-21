<?php

class Animal
{
	public $name;
	public $gender = 'male';

	/*
	 * Monthly food and water consumption
	 */
	public $monthlyFood;
	public $monthlyWater;

	/*
	 * Animal life span
	 */
	public $lifeSpan;

	/*
	 * Minimum and maximum temperature for the animal
	 */
	public $minTemperature;
	public $maxTemperature;

	/*
	 * The current animal age
	 */
	private $age;

	/*
	 * Breeding age
	 */
	private $procreation_age = 2;

	/*
	 * Values to determine how many month of pregnancy and the partner
	 */
	private $pregnancy;
	private $partner;

	/*
	 * Starving, thirsty or feeling extreme temperature
	 */
	private $starvation;
	private $thirst;
	private $extremeTemperature;

	private $thresholdStarvation = 3;
	private $thresholdThirst = 1;
	private $thresholdTemperature = 1;

	/*
	 * Habitat class to hold information about the habitat that the animal is on
	 */
	private $habitat;

	public function __construct($name, $gender, $monthlyFood, $monthlyWater, $temperature, $lifeSpan, $habitat)
	{
		$this->name = $name;
		$this->monthlyFood = $monthlyFood;
		$this->monthlyWater = $monthlyWater;
		$this->minTemperature = $temperature[0];
		$this->maxTemperature = $temperature[1];
		$this->lifeSpan = $lifeSpan;
		$this->habitat = $habitat;

		// Default values
		$this->age = 0;
		$this->starvation = 0;
		$this->thirsty = 0;
		$this->extremeTemperature = 0;
	}

	public function simulate($currentMonth, $currentSeason)
	{
		// Simulate one month
		// Update age (in years)
		$this->age = floor($currentMonth / 12);

		// Consume food/water
		$this->consumeFoodWater();

		$habitatTemp = $this->habitat->temperature[$currentSeason];
		if ( !($habitatTemp >= $this->minTemperature && $habitatTemp <= $this->maxTemperature) )
		{
			$this->extremeTemperature++;
		}

		// Determine if the animal will die this month
		$this->simulateDeath($currentSeason);

		// Animal breeding
		$this->breeding();
	}

	public function consumeFoodWater()
	{
		if ( $this->habitat->currentFood > 0 )
		{
			$this->habitat->currentFood -= $this->monthlyFood;
		} else {
			// Starving
			$this->starvation++;
		}

		if ( $this->habitat->currentWater > 0 )
		{
			$this->habitat->currentWater -= $this->monthlyWater;
		} else {
			// Thirsty
			$this->thirsty++;
		}
	}

	public function breeding()
	{
		if ( $this->gender != 'female' )
		{
			return false;
		}

		// If not pregnant
		if ( $this->pregnancy == 0 && $this->age == $this->breedingAge )
		{
			// Pick a male from the habitat
			$males = [];
			//var_dump($this->habitat->animals); exit;
			foreach ($this->habitat->animals as $animal)
			{
				if ( $animal->gender == 'male' )
				{
					$males[] = $animal;
				}
			}
			
			if ( count($males) > 0 )
			{
				// Random male partner
				$this->partner = $males[rand(0, count($males) - 1)];
				// Pregnancy started
				$this->pregnancy++;
			}
		}

		// If pregnant
		if ( $this->pregnancy > 0 )
		{
			$this->pregnancy++;
			if ( $this->pregnancy == 3 )
			{
				// Give birth
				$animal = new Animal($this->name, $this->monthlyFood, $this->monthlyWater, [$this->minTemperature, $this->maxTemperature], $this->lifeSpan, $this->habitat);
				$animal->gender = rand(0, 1) == 0 ? 'male': 'female';
				$this->habitat->animals[] = $animal;
				echo 'New birth<br>';
			}
		}
		return true;
	}

	public function simulateDeath($currentSeason)
	{
		// Due to aging
		if ( $this->age >= $this->lifeSpan )
		{
			$this->death('Age');
		}

		// Due to temperature
	 	if ( $this->extremeTemperature >= $this->thresholdTemperature )
	 	{
	 		$this->death('Temperature');
	 	}

		// Due to starvation
		if ( $this->starvation >= $this->thresholdStarvation )
		{
			$this->death('Starvation');
		}

		// Due to dehydration
		if ( $this->thirst >= $this->thresholdThirst )
		{
			$this->death('Dehydration');
		}
	}

	public function death( $cause )
	{
		$key = array_search($this, $this->habitat->animals);
		if ( $key !== false )
		{
			unset($this->habitat->animals[$key]);
		}
		//echo "Animal died from $cause<br>";
	}
}

?>