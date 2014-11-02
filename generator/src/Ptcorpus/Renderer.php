<?php

namespace Ptcorpus;

use Twig_Environment as Twig;

class Renderer
{
	public function __construct(Twig $twig)
	{
		$this->twig = $twig;
	}

	public function render($file)
	{
		$template = $this->twig->loadTemplate($file);
		$renderedPages = $template->render(array());

		$renderedPages = explode("---------------", $renderedPages);
		$pages = array();
		while ($renderedPages) {
			$metadata = array_shift($renderedPages);
			$metadata = trim($metadata);
			if (!$metadata) break;

			$content = array_shift($renderedPages);

			$metadata = (array) json_decode($metadata, true);
			$page = $metadata + array('content' => $content);

			$pages[] = $page;
		}

		return $pages;
	}
}