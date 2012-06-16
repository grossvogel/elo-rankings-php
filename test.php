<?php
/**
*	Run experiments to test the output of the elo rating system and tweak the parameters
*/
require 'eloratingsystem.php';

/**
*	A collection of test competitors, with 'skill' representing the true skill and 'rating' the starting elo rating	
*/
$competitors = array (
	array ('id'=>1, 'skill'=>1, 'rating' => 1000),
	array ('id'=>2, 'skill'=>2, 'rating' => 1000),
	array ('id'=>3, 'skill'=>3, 'rating' => 1000),
	array ('id'=>4, 'skill'=>4, 'rating' => 1000),
	array ('id'=>5, 'skill'=>6, 'rating' => 1000),
	array ('id'=>6, 'skill'=>12, 'rating' => 1000),
	array ('id'=>7, 'skill'=>25, 'rating' => 1000),
	array ('id'=>8, 'skill'=>50, 'rating' => 1000),
	array ('id'=>9, 'skill'=>100, 'rating' => 1000)
);

//	initialize the ranking system and add the competitors	
$elo = new EloRatingSystem (400,24);
foreach ($competitors as $competitor)
{
	$elo->addCompetitor (new EloCompetitor ($competitor['id'], $competitor['rating']));
}

/**
*	Run $rounds rounds of $roundSize matchups (random, no guarantee that everybody competes the same # of times) 
*	updating and printing rankings after each round
*/
$rounds = 100;
$roundSize = 10;
for ($i = 0; $i < $rounds; $i++)
{
	for ($j = 0; $j < $roundSize; $j++)
	{
		$match = array_rand ($competitors, 2);	
		$away = $competitors[$match[0]];
		$home = $competitors[$match[1]];
		if (rand (1, $away['skill'] + $home['skill']) <= $away['skill'])
		{
			$elo->addResult ($away['id'], $home['id']);
		}
		else
		{
			$elo->addResult ($home['id'], $away['id']);
		}
	}
	$elo->updateRatings ();

	$roundNum = $i+1;
	echo "\nResults after $roundNum rounds of $roundSize:\n";
	foreach ($elo->getCompetitors () as $competitor)
	{
		echo "$competitor \n";
	}
	echo "\n\n";
}

