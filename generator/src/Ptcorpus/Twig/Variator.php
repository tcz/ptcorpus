<?php

namespace Ptcorpus\Twig;

class Variator
{
	private $seed;
	private $items = array();

	public function setSeed($seed)
	{
		$this->seed = $seed;
	}

	public function clearItems()
	{
		$this->items = array();
	}

	public function addItem($item, $weight = 1.0)
	{
		$this->items[(string) $item] = $weight * 100;
	}

	public function vary()
	{
		$seedHash = $this->getSeedHash();
		$weightSum = array_sum($this->items);
		$modHash = $seedHash % $weightSum;

		//var_dump($seedHash, $weightSum, $modHash);

		foreach ($this->items as $item => $weight)
		{
			$modHash = $modHash - $weight;
			if ($modHash < 0)
			{
				return $item;
			}
		}

		throw new \LogicException("Couldn't select item.");
	}

	private function getSeedHash()
	{
		$json = json_encode($this->seed);
		$hash = sha1($json, true);
		$integers = unpack('N*', $hash);

		return end($integers);
	}
}
