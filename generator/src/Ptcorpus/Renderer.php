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
		$renderedPage = $template->render(array());

		return array(array(
			'content' => $renderedPage,
			'title' => 'test',
			'url' => 'test.html'
		));
	}
}