<?php

namespace Ptcorpus\Parsers;

use Ptcorpus\PageCollection;
use Ptcorpus\Interpolation;
use Ptcorpus\ConditionHelper;

class AbortPage implements Parser {

	public function __construct(PageCollection $pages, Interpolation $interpolation) {
		$this->pages = $pages;
		$this->interpolation = $interpolation;
	}

	public function canHandle($line) {
		return preg_match('/^(AbortPage:\s)/i', $line);
	}

	public function handle($line) {
		preg_match('/^AbortPage:\s+(.+)/i', $line, $matches);
		$condition = $this->interpolation->apply($matches[1]);

		$result = ConditionHelper::evaluate($condition);

		if (true === $result) {
			$this->pages->abortPage();
		}
	}

	private function handleEnding($line) {
		$this->pages->unsetPage();
	}
}