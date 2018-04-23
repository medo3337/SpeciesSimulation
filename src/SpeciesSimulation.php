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
			}
		}
	}

    /**
     * Output the statistics
     *
     * @return void
     */
	public function output()
	{
		$stats = [];
		foreach ($this->habitats as $habitat)
		{
			// $stats[$habitat->animalName][$habitat->name]['total_born']  = $habitat->totalBorn;
			// $stats[$habitat->animalName][$habitat->name]['total_death'] = $habitat->totalDeath;
			$stats[$habitat->animalName][$habitat->name]['avg_population'] = number_format($habitat->totalPopulation / $this->months, 2);
			$stats[$habitat->animalName][$habitat->name]['mortality_rate'] = number_format(($habitat->totalDeath / $habitat->totalBorn) * 100, 2);
			$stats[$habitat->animalName][$habitat->name]['max_population'] = $habitat->maxPopulation;
		}

		header('Content-Type: text');
		echo 'Simulation ran for ' . floor($this->months / 12) . " years\n";
		echo yaml_emit($stats);
	}
}

?>