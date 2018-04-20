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
	private $statistics;

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
				$animal = new Animal($animalInfo['name'], $animalInfo['attributes']['monthly_food_consumption'], $animalInfo['attributes']['monthly_water_consumption'],
								 	 [$animalInfo['attributes']['minimum_temperature'], $animalInfo['attributes']['maximum_temperature']], $animalInfo['attributes']['life_span'],
								 	  $habitat);
				$animal->gender = 'male';
				$habitat->animals[] = $animal;

				// Female
				$animal = new Animal($animalInfo['name'], $animalInfo['attributes']['monthly_food_consumption'], $animalInfo['attributes']['monthly_water_consumption'],
								 	 [$animalInfo['attributes']['minimum_temperature'], $animalInfo['attributes']['maximum_temperature']], $animalInfo['attributes']['life_span'],
								 	  $habitat);
				$animal->gender = 'female';
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

		echo '<pre>';
		print_r($this->statistics);
		echo '</pre>';
	}

	public function collectStatistics($habitat)
	{
		foreach ( $habitat->animals as $animal )
		{
			if ( !isset($this->statistics[$habitat->name][$animal->name]['max_population']) )
			{
				$this->statistics[$habitat->name][$animal->name]['max_population'] = 0;
			}
			$this->statistics[$habitat->name][$animal->name]['max_population'] = max(count($habitat->animals), $this->statistics[$habitat->name][$animal->name]['max_population']);
		}
	}

	public function output()
	{
		echo 'Simulation ran for ' . floor($this->months / 12) . ' years';

	}
}

?>