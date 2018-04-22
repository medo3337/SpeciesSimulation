<?php
/*
 * Species simulation class, the class will handle loading and simulating species in several habitats
 * @author: Mohamed Abowarda <Medo3337@hotmail.com>
 *
 */
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

	public function __construct($configFile = null)
	{
		if ( $configFile != null )
		{
			$this->load($configFile);
		}
	}

    /**
     * Load habitats and species from YAML configuration file
     * @param  string  $configFile
     *
     * @return void
     */
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
				$habitat = new Habitat($habitatInfo);
				$this->habitats[] = $habitat;
	
				// Male
				$animal = new Animal($animalInfo['name'], $animalInfo['attributes'], 'male', $habitat);
				$habitat->animals[] = $animal;

				// Female
				$animal = new Animal($animalInfo['name'], $animalInfo['attributes'], 'female', $habitat);
				$habitat->animals[] = $animal;
			}
		}
	}

    /**
     * Simulate species in habitats
     *
     * @return void
     */
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
				// Average population
				$this->stats[$key][$animalName]['avg_population'] = number_format($this->stats[$key][$animalName]['total_population'] / $this->months, 2);
				// Mortality rate
				if ( $this->stats[$key][$animalName]['total_death'] > 0 )
				{
					$this->stats[$key][$animalName]['mortality_rate'] = number_format(($this->stats[$key][$animalName]['total_death'] / $this->stats[$key][$animalName]['total_born']) * 100, 2);
				} else {
					$this->stats[$key][$animalName]['mortality_rate'] = 0;
				}
				// This is not needed for stats
				unset($this->stats[$key][$animalName]['total_population']);
			}
		}

	}

    /**
     * Collect statistics needed during the simulation process
     * @param  Habitate  $habitat
     *
     * @return void
     */
	public function collectStatistics(Habitat $habitat)
	{
		foreach ( $habitat->animals as $animal )
		{
			if ( !isset($this->stats[$animal->name][$habitat->name]['max_population']) )
			{
				// Initialize to zero
				$this->stats[$animal->name][$habitat->name]['max_population'] 	= 0;
				$this->stats[$animal->name][$habitat->name]['total_population'] = 0;
				$this->stats[$animal->name][$habitat->name]['total_born'] 		= 0;
				$this->stats[$animal->name][$habitat->name]['total_death'] 		= 0;
			}

			// Max population
			$this->stats[$animal->name][$habitat->name]['max_population'] = max(count($habitat->animals), $this->stats[$animal->name][$habitat->name]['max_population']);

			// Total population
			$this->stats[$animal->name][$habitat->name]['total_population']++;

			// Total born/death
			$this->stats[$animal->name][$habitat->name]['total_born']  = $habitat->totalBorn;
			$this->stats[$animal->name][$habitat->name]['total_death'] = $habitat->totalDeath;
		}
	}

    /**
     * Output the statistics
     *
     * @return void
     */
	public function output()
	{
		header('Content-Type: text');
		echo 'Simulation ran for ' . floor($this->months / 12) . " years\n";
		echo yaml_emit($this->stats);
	}
}

?>