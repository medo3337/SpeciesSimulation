<?php

class SpeciesSimulation
{
	/*
	 * List of all the habitats
	 */
	public $habitats;

	/*
	 * Number of months to run the simulation
	 */
	public $months;

	/*
	 * Number of months to run the simulation
	 */
	private $stats;

	public function __construct( $configFile = null )
	{
		if ( $configFile != null )
		{
			$this->load($configFile);
		}
	}

	public function load($configFile)
	{
		$yaml = yaml_parse_file($configFile);
		$this->months = $yaml['years'] * 12;

		// Load species
		foreach ($yaml['species'] as $animalInfo)
		{
			// Create habitats for species
			foreach ($yaml['habitats'] as $habitatInfo)
			{
				$habitat = new Habitat($habitatInfo['name'], $habitatInfo['monthly_food'], $habitatInfo['monthly_water'],
									  [$habitatInfo['average_temperature']['summer'], $habitatInfo['average_temperature']['spring'],
									   $habitatInfo['average_temperature']['fall'], $habitatInfo['average_temperature']['winter']]);
				$this->habitats[] = $habitat;
	
				// Male
				$animal = new Animal($animalInfo['name'], 'male', $animalInfo['attributes']['monthly_food_consumption'], $animalInfo['attributes']['monthly_water_consumption'],
								 	 [$animalInfo['attributes']['minimum_temperature'], $animalInfo['attributes']['maximum_temperature']], $animalInfo['attributes']['life_span'],
								 	  $habitat);
				$habitat->animals[] = $animal;

				// Female
				$animal = new Animal($animalInfo['name'], 'female', $animalInfo['attributes']['monthly_food_consumption'], $animalInfo['attributes']['monthly_water_consumption'],
								 	 [$animalInfo['attributes']['minimum_temperature'], $animalInfo['attributes']['maximum_temperature']], $animalInfo['attributes']['life_span'],
								 	  $habitat);
				$habitat->animals[] = $animal;
			}
		}
	}

	public function simulate()
	{
		// For each month
		for ( $month = 1; $month < $this->months; $month++ )
		{
			// For each habitate
			foreach ( $this->habitats as $habitat )
			{
				// Simulate
				$habitat->simulate($month);

				// Statistics
				$this->collectStatistics($habitat);
			}
		}

		// Get the average population
		foreach ( $this->stats as $key => $animalStats )
		{
			foreach ( $this->stats[$key] as $animalName => $value )
			{
				$this->stats[$key][$animalName]['avg_population'] = number_format($this->stats[$key][$animalName]['total_population'] / $this->months, 2);
				// This is not needed for stats
				//unset($this->stats[$key][$animalName]['total_population']);
			}
		}
	}

	public function collectStatistics($habitat)
	{
		foreach ( $habitat->animals as $animal )
		{
			if ( !isset($this->stats[$animal->name][$habitat->name]['max_population']) )
			{
				$this->stats[$animal->name][$habitat->name]['max_population'] = 0;
			}

			if ( !isset($this->stats[$animal->name][$habitat->name]['total_population']) )
			{
				$this->stats[$animal->name][$habitat->name]['total_population'] = 0;
			}
			
			// Max population
			$this->stats[$animal->name][$habitat->name]['max_population'] = max(count($habitat->animals), $this->stats[$animal->name][$habitat->name]['max_population']);

			// Average population
			$this->stats[$animal->name][$habitat->name]['total_population'] += count($habitat->animals);
		}
	}

	public function output()
	{
		header('Content-Type: text');
		echo 'Simulation ran for ' . floor($this->months / 12) . " years\n";
		echo yaml_emit($this->stats);
	}
}

?>