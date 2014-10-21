<?php

require_once(__DIR__.'/Parser.php');

class Page implements Parser {

	public function __construct(PageCollection $pages, Interpolation $interpolation) {
		$this->pages = $pages;
		$this->interpolation = $interpolation;
	}

	public function canHandle($line) {
		return $this->isOpening($line) || $this->isEnding($line);
	}

	private function isOpening($line) {
		return preg_match('/^(Page:\s)/i', $line);
	}

	private function isEnding($line) {
		return preg_match('/^(:EndPage)/i', $line);
	}

	public function handle($line) {
		if ($this->isOpening($line)) {
			$this->handleOpening($line);
		} elseif ($this->isEnding($line)) {
			$this->handleEnding($line);
		}
	}

	private function handleOpening($line) {
		preg_match('/^Page:\s+(.+)/i', $line, $matches);
		$title = $this->interpolation->apply($matches[1]);
		$this->pages->setPage($title);
	}

	private function handleEnding($line) {
		$this->pages->unsetPage();
	}
}