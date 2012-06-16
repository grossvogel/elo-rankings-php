<?php
/**
*	a participant in an Elo Rating system
*/
class EloCompetitor
{
	private $id;
	private $rating;

	public function __construct ($id, $rating)
	{
		$this->id = $id;
		$this->rating = $rating;
	}

	public function getId ()
	{
		return $this->id;
	}

	public function getRating ()
	{
		return $this->rating;
	}

	public function adjustRating ($adjustment)
	{
		$this->rating += $adjustment;
	}

	public function __toString ()
	{
		return $this->id . ': ' . $this->rating;
	}
}
