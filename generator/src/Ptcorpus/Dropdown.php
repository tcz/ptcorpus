<?php

namespace Ptcorpus;

class Dropdown
{
	private $pages = array();
	private $patterns = array();
	private $pageParser;

	public function __construct(PageParser $pageParser)
	{
		$this->pageParser = $pageParser;
	}

	public function clear()
	{
		$this->pages = array();
		$this->patterns = array();
	}

	public function addPage($path)
	{
		$this->pages[] = $path;
	}

	public function addPattern($pattern)
	{
		$this->patterns[] = $pattern;
	}

	public function getDropdowns()
	{
		$dropdowns = "";

		foreach ($this->patterns as $pattern) {
			$values = array();

			foreach ($this->pages as $path) {
				$title = $this->pageParser->getTitle($path);

				if (!preg_match($pattern, $title, $matches, PREG_OFFSET_CAPTURE)) {
					continue;
				}

				$template = "";
				$lastTemplateOffset = 0;

				foreach ($matches as $index => $match) {
					if ($index === 0) {
						continue;
					}
					if (!isset($values[$index])) {
						$values[$index] = array();
					}
					$values[$index][$match[0]] = true;
					$template .= substr($title, $lastTemplateOffset, $match[1] - $lastTemplateOffset) . "$" . $index . "$";
					$lastTemplateOffset = $match[1] + strlen($match[0]);
				}
				$template .= substr($title, $lastTemplateOffset);
			}

			if (empty($values)) {
				echo "Pattern $pattern didn't match any pages.\n";
				continue;
			}

			$dropdowns .= $this->generateSingleDropdown($template, $values);
		}

		return $dropdowns;
	}

	private function generateSingleDropdown($template, array $values)
	{
		foreach ($values as $index => $items) {
			ksort($items);
			$html = "<select class='value-selector'>";

			foreach ($items as $item => $void) {
				$html .= "<option>" . htmlspecialchars($item) . "</option>";
			}

			$html .= "</select>";

			$template = str_replace("$$index$", $html, $template);
		}

		return "<p class='dropdown'>$template</p>";
	}
}