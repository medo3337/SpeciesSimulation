<?php

class SpeciesSimulation
{
	public $habitats;

	public function simulate($months)
	{
		// For each month
		for ( $month = 1; $month < $months; $month++ )
		{
			// For each habitate
			foreach ( $habitats as $habitat )
			{
				// Simulate
				$habitat->simulate();
			}

		}
	}

	public function output()
	{

	}
}

?>