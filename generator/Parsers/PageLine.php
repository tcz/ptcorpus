<?php

require_once(__DIR__.'/Parser.php');

class PageLine implements Parser {

	public function __construct(PageCollection $pages, Interpolation $interpolation) {
		$this->pages = $pages;
		$this->interpolation = $interpolation;
	}

	public function canHandle($line) {
		return "" !== $line;
	}

	public function handle($line) {
		$line = $this->interpolation->apply($line);
		$this->pages->appendLine($line);
	}
}