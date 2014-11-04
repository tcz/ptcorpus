<?php

namespace Ptcorpus;

class StaffPicks
{
	private $pages = array();
	private $pageParser;

	const DAYS = 5;
	const PER_DAY = 3;

	public function __construct(PageParser $pageParser)
	{
		$this->pageParser = $pageParser;
	}

	public function clear()
	{
		$this->pages = array();
	}

	public function addPage($path)
	{
		$this->pages[] = $path;
	}

	public function getPicks()
	{
		$hasher = new ConsistentHasher;

		foreach ($this->pages as $path) {
			$hasher->addItem($path);
		}

		$html = "<ul class='picks'>";

		$pickedPaths = array();

		for ($i=0; $i < self::DAYS; $i++) {
			$pickedPaths = array_merge(
				$pickedPaths,
				array_values($hasher->getAssociated(date('Y-m-d',time()-86400*$i), self::PER_DAY))
			);
		}

		foreach ($pickedPaths as $pickedPath) {
			$html .= "<li>";
			$html .= "<a href='" .
				htmlspecialchars($this->pageParser->getUrl($pickedPath)) .
				"' title='".
				htmlspecialchars($this->pageParser->getTitle($pickedPath)) .
				"'>";
			$html .= "<h1>";
			$html .= htmlspecialchars($this->pageParser->getTitle($pickedPath));
			$html .= "</h1>";
			$html .= htmlspecialchars($this->pageParser->getDescription($pickedPath));
			$html .= "</a>";
			$html .= "</li>";
		}

		$html .= "</ul>";

		return $html;
	}


}