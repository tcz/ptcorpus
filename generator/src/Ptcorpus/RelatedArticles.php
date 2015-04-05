<?php

namespace Ptcorpus;

class RelatedArticles
{
	private $keywordsToPages = array();
	private $pageParser;

	public function __construct(PageParser $pageParser)
	{
		$this->pageParser = $pageParser;
	}

	public function clear()
	{
		$this->keywordsToPages = array();
	}

	public function addPage($path)
	{
		$keywords = $this->pageParser->getKeywords($path);

		foreach ($keywords as $keyword) {
			if (!isset($this->keywordsToPages[$keyword])) {
				$this->keywordsToPages[$keyword] = array();
			}

			$this->keywordsToPages[$keyword][] = $path;
		}
	}

	public function getRelatedArticles($mainPath, $numberOfArticles)
	{
		$keywords = $this->pageParser->getKeywords($mainPath);
		$relatedPaths = array();

		foreach ($keywords as $keyword) {
			if (isset($this->keywordsToPages[$keyword])) {
				$relatedPaths = array_merge($relatedPaths, $this->keywordsToPages[$keyword]);
			}
		}

		$hasher = new ConsistentHasher;
		// Duplicates are fine here.
		foreach ($relatedPaths as $path) {
			$hasher->addItem($path);
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