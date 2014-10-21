<?php

class PageCollection {
	private $pages = array();
	private $currentPage;
	private $pageAborted = false;

	public function setPage($title) {
		if (!empty($this->currentPage)) {
			throw new \Exception("Cannot start page before closing previous.");
		}

		$this->currentPage = $title;
		$this->pages[$this->currentPage] = '';
		$this->pageAborted = false;
	}

	public function unsetPage() {
		$this->currentPage = null;
	}

	public function abortPage() {
		$this->pageAborted = true;
		unset($this->pages[$this->currentPage]);
	}

	public function appendLine($line) {
		if ($this->pageAborted) {
			return;
		}
		if (empty($this->currentPage)) {
			throw new \Exception("Tried to add line outside a page.");
		}

		$this->pages[$this->currentPage] .= $line . "\n";
	}

	public function getPages() {
		$result = array();
		foreach ($this->pages as $title => $content) {
			$page = array(
				'title' => $title,
				'content' => $content,
				'url' => $this->generateUrl($title),
			);
			$result[] = $page;
		}

		return $result;
	}

	private function generateUrl($title) {
		$title = strtolower($title);
		$url = preg_replace('/\W/', '-', $title);
		$url = preg_replace('/-+/', '-', $url);
		$url = $url . ".html";

		return $url;
	}
}