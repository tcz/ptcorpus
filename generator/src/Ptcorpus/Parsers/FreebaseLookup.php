<?php

namespace Ptcorpus\Parsers;

use Ptcorpus\File;
use Ptcorpus\Scope;
use Ptcorpus\Interpolation;
use Ptcorpus\Freebase;

class FreebaseLookup implements Parser {

	public function __construct(File $file, Scope $scope, Freebase $freebase, Interpolation $interpolation) {
		$this->file = $file;
		$this->scope = $scope;
		$this->freebase = $freebase;
		$this->interpolation = $interpolation;
	}

	public function canHandle($line) {
		return false !== strpos($line, "=");
	}

	public function handle($line) {
		$name = trim(substr($line, 0, strpos($line, "=")));
		$json = trim(substr($line, strpos($line, "=") + 1));

		while (null === ($query = json_decode($json, true)) && !$this->file->hasEnded()) {
			$json .= $this->file->getLine();
		}

		if (null === $query) {
			throw new \Exception("Could not decode json: $json");
		}

		$query = $this->interpolateQuery($query);
		$value = $this->freebase->query($query);

		$this->scope->push($name, $value);
	}

	private function interpolateQuery($query) {
		foreach ($query as $key => $value) {
			if (is_array($value)) {
				$query[$key] = $this->interpolateQuery($value);
				continue;
			}

			$query[$key] = $this->interpolation->apply($query[$key]);
		}

		return $query;
	}
}