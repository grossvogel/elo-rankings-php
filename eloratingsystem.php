<?php
require 'elocompetitor.php';
/**
*	Implementation of an Elo rating system that assings a numerical rating to each competitor
*	based on their wins and losses against other rated competitors in the system
*	@see http://en.wikipedia.org/wiki/Elo_rating_system
*	@todo: it seems that the k-factor should maybe differ depending on the competitor. Newer and lower-ranked competitors should
*		be allowed higher mobility (higher k-factor) than the others. Maybe.
*/
class EloRatingSystem
{
	private $scoreFactor;	//	determines the size of the scoring scale: 400 means a 200 difference is a 75% win rate
	private $kFactor;		//	determines how drastically the scores are adjusted each time
	private $competitors = array ();

	//	these are used for computations under the hood
	private $results = array ();
	private $qScores = array ();
	private $expectations = array ();

	/**
	*	construct an EloRating system by giving its configuration options
	*/
	public function __construct ($scoreFactor = 400, $kFactor = 24)
	{
		$this->scoreFactor = $scoreFactor;
		$this->kFactor = $kFactor;
	}

	/**
	*	add a competitor whose score will be tracked
	*/
	public function addCompetitor (EloCompetitor $competitor)
	{
		$id = $competitor->getId ();
		$this->competitors [$id] = $competitor;
		$this->qScores[$id] = $this->getQScore ($competitor); 
		$this->results[$id] = 0;
		$this->expectations[$id] = 0;
	}

	/**
	*	record a result by supplying the ids of the teams in the proper order
	*/
	public function addResult ($winningId, $losingId, $tie = false)
	{
		if ($tie)
		{
			$this->results[$winningId] += 0.5;
			$this->results[$losingId] += 0.5;
		}
		else
		{
			$this->results[$winningId] += 1;
		}
		$denom = ($this->qScores[$winningId] + $this->qScores[$losingId]);
		$this->expectations[$winningId] += ($this->qScores[$winningId] / $denom);
		$this->expectations[$losingId] += ($this->qScores[$losingId] / $denom);
	}

	/**
	*	return the collection of competitors
	*/
	public function getCompetitors ()
	{
		return $this->competitors;
	}
	
	/**
	*	based on all of the results reported since the last update,
	*	adjust all of the competitor ratings
	*/
	public function updateRatings ()
	{
		foreach ($this->competitors as $id => $competitor)
		{
			$competitor->adjustRating ($this->kFactor * ($this->results[$id] - $this->expectations[$id]));
			
			//	get ready for the next round
			$this->qScores[$id] = $this->getQScore ($competitor);
			$this->results[$id] = 0;
			$this->expectations[$id] = 0;
		}
	}

	/**
	*	Get the Q score for the competitor
	*	Used to calculate the expected score for each competitor. 
	*	If a plays b, then Ea = Qa / (Qa + Qb)	
	*/
	private function getQScore (EloCompetitor $competitor)
	{
		return pow (10, $competitor->getRating () / $this->scoreFactor);
	}
}

