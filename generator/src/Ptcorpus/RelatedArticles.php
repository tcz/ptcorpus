<?php

namespace Ptcorpus;

class RelatedArticles
{
	private $pagesWithKeywords = array();
	private $pageParser;

	public function __construct(PageParser $pageParser)
	{
		$this->pageParser = $pageParser;
	}

	public function clear()
	{
		$this->pagesWithKeywords = array();
	}

	public function addPage($path)
	{
		$this->pagesWithKeywords[$path] = $this->pageParser->getKeywords($path);
	}

	public function getRelatedArticles($mainPath, $numberOfArticles)
	{
		if (!isset($this->pagesWithKeywords[$mainPath])) {
			throw new \LogicException("$mainPath has to be added first.");
		}

		$hasher = new ConsistentHasher;

		foreach ($this->pagesWithKeywords as $path => $keywords) {
			$commonKeywords = array_intersect($keywords, $this->pagesWithKeywords[$mainPath]);
			for ($i=0; $i < count($commonKeywords); $i++) {
				$hasher->addItem($path);
			}
		}

		$html = "<ul class='related'>";

		$associatedPaths = $hasher->getAssociated($mainPath, $numberOfArticles);
		$counter = 0;

		foreach ($associatedPaths as $associatedPath) {
			$html .= "<li>";
			$html .= "<a href='" .
				htmlspecialchars($this->pageParser->getUrl($associatedPath)) .
				"' title='".
				htmlspecialchars($this->pageParser->getTitle($associatedPath)) .
				"'>";
			$html .= "<strong>";
			$html .= htmlspecialchars($this->pageParser->getTitle($associatedPath));
			$html .= "</strong> ";
			$html .= htmlspecialchars($this->pageParser->getDescription($associatedPath));
			$html .= "</a>";
			$html .= "</li>";

			$counter++;

			if ($counter % 3 === 0) {
				$html .= "</ul><ul class='related'>";
			}
		}

		$html .= "</ul>";

		return $html;
	}


}