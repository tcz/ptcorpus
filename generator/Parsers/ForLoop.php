<?php

require_once(__DIR__.'/Parser.php');
require_once(__DIR__.'/../ConditionHelper.php');

class ForLoop implements Parser {

	private $forStack = array();

	public function __construct(File $file, Scope $scope, Interpolation $interpolation) {
		$this->file = $file;
		$this->scope = $scope;
		$this->interpolation = $interpolation;
	}

	public function canHandle($line) {
		return
			$this->isOpening($line) ||
			$this->isEnding($line) ||
			$this->isContinue($line);
	}

	private function isOpening($line) {
		return preg_match('/^For:\s*(.+)/i', $line);
	}

	private function isEnding($line) {
		return preg_match('/^:EndFor/i', $line);
	}

	private function isContinue($line) {
		return preg_match('/^Continue:\s*(.+)/i', $line);
	}

	public function handle($line) {
		if ($this->isOpening($line)) {
			$this->handleOpening($line);
		} elseif ($this->isEnding($line)) {
			$this->handleEnding($line);
		} elseif ($this->isContinue($line)) {
			$this->handleContinue($line);
		}
	}

	private function handleOpening($line) {
		preg_match('/^For:\s*(.+)/i', $line, $matches);

		$parts = preg_split("/\s+in\s+/i", $matches[1]);
		$loopedValue = $this->scope->lookup($parts[1]);

		if (!is_array($loopedValue)) {
			throw new \Exception("Cannot loop over a non-array item");
		}

		$loopedValue = array_values($loopedValue);
		$alias = $parts[0];

		if (count($loopedValue) === 0) {
			$this->skipToEndOfLoop();
			return;
		}

		$this->forStack[] = array(
			'loopedValue' => $loopedValue,
			'loopIndex' => 0,
			'alias' => $alias,
			'filePosition' => $this->file->getPosition(),
		);

		$this->scope->addLayer();
		$this->scope->push($alias, $loopedValue[0]);
	}

	private function handleEnding($line) {
		if (empty($this->forStack)) {
			throw new \Exception("Unmatched EndFor found");
		}

		$currentFor = &$this->forStack[count($this->forStack) - 1];
		$currentFor['loopIndex']++;

		if ($currentFor['loopIndex'] >= count($currentFor['loopedValue'])) {
			$this->scope->removeLayer();
			array_pop($this->forStack);
			return;
		}

		$this->scope->push($currentFor['alias'], $currentFor['loopedValue'][$currentFor['loopIndex']]);
		$this->file->toPosition($currentFor['filePosition']);
	}

	private function handleContinue($line) {
		preg_match('/^Continue:\s+(.+)/i', $line, $matches);
		$condition = $this->interpolation->apply($matches[1]);

		$result = ConditionHelper::evaluate($condition);

		if (true === $result) {
			$this->handleEnding($line);
		}
	}

	private function skipToEndOfLoop() {
		$loopsToMatch = 1;
		while (true) {
			if ($this->file->hasEnded()) {
				throw new \Exception("Unmatched For found");
			}

			$line = $this->file->getLine();

			if ($this->isOpening($line)) {
				$loopsToMatch++;
				continue;
			}

			if ($this->isEnding($line)) {
				$loopsToMatch--;
			}

			if ($loopsToMatch === 0) {
				return;
			}
		}
	}
}