<?php

class Scope {
	private $scopeLayers = array(array());
	private $layerPointer = 0;

	public function push($name, $value) {
		$this->scopeLayers[$this->layerPointer][$name] = $value;
	}

	public function lookup($name) {
		$currentPointer = $this->layerPointer;

		$chain = explode(".", $name);
		$base = $chain[0];

		while ($currentPointer >= 0) {
			if (isset($this->scopeLayers[$currentPointer][$base])) {
				return $this->deepLookup($this->scopeLayers[$currentPointer], $chain);
			}

			$currentPointer--;
		}

		throw new \Exception("$name not found in scope");
	}

	public function addLayer() {
		$this->layerPointer++;
		$this->scopeLayers[$this->layerPointer] = array();
	}

	public function removeLayer() {
		$this->layerPointer--;
	}

	private function deepLookup($context, array $chain) {
		$currentKey = array_shift($chain);

		if (!array_key_exists($currentKey, $context)) {
			if ($currentKey === 'length') {
				return count($context);
			}

			throw new \Exception("Key $currentKey not found. " .
				"Existing keys: " . implode(", ", array_keys($context)));
		}

		if (empty($chain)) {
			return $context[$currentKey];
		}

		return $this->deepLookup($context[$currentKey], $chain);
	}
}