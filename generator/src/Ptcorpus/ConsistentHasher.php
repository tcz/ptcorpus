<?php

namespace Ptcorpus;

class ConsistentHasher
{
	private $items = array();
	private $itemIndexes = array();

	public function addItem($item)
	{
		if (!isset($this->itemIndexes[$item])) {
			$this->itemIndexes[$item] = 0;
		}

		$this->itemIndexes[$item]++;
		$key = $this->hash($item.$this->itemIndexes[$item]);

		$this->items[$key] = $item;
	}

	public function getAssociated($item, $count)
	{
		$uniqueItems 		= array_unique($this->items);
		$count 				= min(count($uniqueItems), $count);
		$reached 			= false;
		$associatedItems 	= array();
		$itemHash 			= $this->hash($item);

		ksort($this->items);
		reset($this->items);
		while (count($associatedItems) < $count) {
			$currentKey = key($this->items);

			if ($reached || $currentKey >= $itemHash) {
				$reached = true;

				if (!in_array($this->items[$currentKey], $associatedItems))
				{
					$associatedItems[] = $this->items[$currentKey];
				}
			}

			next($this->items);

			if (false === current($this->items)) {
				$reached = true;
				reset($this->items);
			}
		}

		return $associatedItems;
	}

	private function hash($item)
	{
		return md5($item);
	}
}
