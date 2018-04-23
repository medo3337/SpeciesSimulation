<?php
/*
 * Animal class, each instance represent one single animal
 * @author: Mohamed Abowarda <Medo3337@hotmail.com>
 *
 */
class Animal
{
	public $gender;

	/*
	 * The current animal age (in months)
	 */
	private $age;

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

	private $thresholdStarvation  = 3;
	private $thresholdThirst 	  = 1;
	private $thresholdTemperature = 1;

	/*
	 * Habitat class to hold information about the habitat that the animal is on
	 */
	private $habitat;

	public function __construct($name, $attributes, $gender, $habitat)
	{
		foreach ( $attributes as $key => $value )
		{
			$this->{$key} = $value;
		}

		$this->name = $name;
		$this->gender = $gender;
		$this->habitat = $habitat;

		// Life span (Convert years to months)
		$this->life_span *= 12;

		// Default values
		$this->age = 0;
		$this->starvation = 0;
		$this->thirst = 0;
		$this->extremeTemperature = 0;
		$this->pregnancy = 0;
		$this->partner = null;

		$habitat->totalBorn++;
	}

    /**
     * Simulate one month
     * @param  integer  $currentMonth
     * @param  integer  $currentSeason
     *
     * @return void
     */
	public function simulate($currentMonth, $currentSeason)
	{
		// Update age (in months)
		$this->age++;

		// Consume food/water
		$this->consumeFoodWater();

		if ( !($this->habitat->currentTemperature >= $this->minimum_temperature && $this->habitat->currentTemperature <= $this->maximum_temperature) )
		{
			$this->extremeTemperature++;
		} else {
			// No longer in extreme temperature
			$this->extremeTemperature = 0;
		}

		// Determine if the animal will die this month
		$this->simulateDeath($currentSeason);

		// Animal breeding
		$this->breeding();
	}

    /**
     * Consuming food and water from the habitat
     *
     * @return void
     */
	public function consumeFoodWater()
	{
		if ( $this->habitat->currentFood >= $this->monthly_food_consumption )
		{
			$this->habitat->currentFood -= $this->monthly_food_consumption;
			// No longer starving
			$this->thirst = 0;
		} else {
			// Starving
			$this->starvation++;
			$this->habitat->currentFood = 0;
		}

		if ( $this->habitat->currentWater >= $this->monthly_water_consumption )
		{
			$this->habitat->currentWater -= $this->monthly_water_consumption;
			// No longer thirsty
			$this->thirst = 0;
		} else {
			// Thirsty
			$this->thirst++;
			$this->habitat->currentWater = 0;
		}
	}

    /**
     * The breeding process will run for females, it will handle pregnancy and giving birth
     *
     * @return void
     */
	public function breeding()
	{
		if ( $this->gender != 'female' )
		{
			return false;
		}

		// If not pregnant
		if ( $this->pregnancy == 0 && $this->age > 0 && ($this->age * 12) % $this->procreation_age == 0 )
		{
			// Pick a male partner from the habitat
			$males = [];
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
			if ( $this->pregnancy >= $this->gestation_period )
			{
				// Give birth
				// Pick random gender 50:50
				$gender = rand(0, 1) == 0 ? 'male': 'female';
				$animal = new Animal($this->name, $this, $gender, $this->habitat);
				$animal->isChild = true;
				$this->habitat->animals[] = $animal;
				$this->pregnancy = 0;
			}
		}
		return true;
	}

    /**
     * Simulate animal death for various of reasons (aging, starvation, extreme temperature, etc...)
     *
     * @return void
     */
	public function simulateDeath($currentSeason)
	{
		// Due to aging
		if ( $this->age >= $this->life_span )
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

    /**
     * Animal death occurs
     *
     * @return void
     */
	public function death($cause)
	{		
		$key = array_search($this, $this->habitat->animals, true);
		if ( $key !== false )
		{
			unset($this->habitat->animals[$key]);
			$this->habitat->totalDeath++;
		}
	}
}

?>