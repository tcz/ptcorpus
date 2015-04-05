<?php

namespace Ptcorpus;

class PageParser
{
	private $headerCache = array();

	public function getKeywords($path)
	{
		$headerValues = $this->getHeaderValues($path);
		if (!isset($headerValues['keywords'])) {
			return array();
		}

		return preg_split('/\s*,\s*/', $headerValues['keywords'], -1, PREG_SPLIT_NO_EMPTY);
	}

	public function getTitle($path)
	{
		$headerValues = $this->getHeaderValues($path);
		if (!isset($headerValues['title'])) {
			return '';
		}

		return $headerValues['title'];
	}

	public function getDescription($path)
	{
		$headerValues = $this->getHeaderValues($path);
		if (!isset($headerValues['description'])) {
			return '';
		}

		return $headerValues['description'];
	}

	public function getUrl($path)
	{
		$basename = basename($path);
		return '/' . str_replace('1970-01-01-', '', $basename);
	}

	private function getHeaderValues($path)
	{
		if (isset($this->headerCache[$path])) {
			return $this->headerCache[$path];
		}

		$content = file_get_contents($path);
		if (!preg_match('/^---\n(.*?)\n---/s', $content, $matches)) {
			return $this->headerCache[$path] = array();
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

		return $this->headerCache[$path] = $headerValues;
	}
}