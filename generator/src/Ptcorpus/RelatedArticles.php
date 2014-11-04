<?php

namespace Ptcorpus;

class RelatedArticles
{
	private $pagesWithKeywords = array();

	public function addPage($path)
	{
		$this->pagesWithKeywords[$path] = $this->getKeywords($path);
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

		foreach ($associatedPaths as $associatedPath) {
			$headerValues = $this->getHeaderValues($associatedPath);
			$html .= "<li>";
			$html .= "<a href='/" .
				htmlspecialchars($this->getUrl($associatedPath)) .
				"' title='".
				htmlspecialchars($headerValues['title']) .
				"'>";
			$html .= "<strong>";
			$html .= htmlspecialchars($headerValues['title']);
			$html .= "</strong> ";
			$html .= htmlspecialchars($headerValues['description']);
			$html .= "</a>";
			$html .= "</li>";
		}

		$html .= "</ul>";

		return $html;
	}

	private function getKeywords($path)
	{
		$headerValues = $this->getHeaderValues($path);
		if (!isset($headerValues['keywords'])) {
			return array();
		}

		return preg_split('/\s*,\s*/', $headerValues['keywords']);
	}

	private function getHeaderValues($path)
	{
		$content = file_get_contents($path);
		if (!preg_match('/^---\n(.*?)\n---/s', $content, $matches))
		{
			return array();
		}

		$header = $matches[1];
		$lines = explode("\n", $header);
		$headerValues = array();

		foreach ($lines as $line) {
			$parts = preg_split("/\s*:\s*/", $line, 2);
			if (2 !== count($parts)) {
				continue;
			}
			$headerValues[$parts[0]] = $parts[1];
		}

		return $headerValues;
	}

	private function getUrl($path)
	{
		$basename = basename($path);
		return str_replace('1970-01-01-', '', $basename);
	}
}