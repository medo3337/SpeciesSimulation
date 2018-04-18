<?php

class SpeciesSimulation
{
	public $habitats;

	public function __construct( $configFile )
	{

	}

	public function simulate($months)
	{
		// For each month
		for ( $month = 1; $month < $months; $month++ )
		{
			// For each habitate
			foreach ( $this->habitats as $habitat )
			{
				// Simulate
				$habitat->simulate($month);
			}

		}
	}

	public function output()
	{

	}
}

?>