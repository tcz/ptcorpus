<?php

namespace Ptcorpus\UserFunction;

use Ptcorpus\Renderer;
use Ptcorpus\File;
use Ptcorpus\Scope;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Render implements UserFunction {

	const TEMPLATE_PATH = '/../../../../pages/';
	const TEMPATE_SUFFIX = '.page';

	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function getName() {
		return 'render';
	}

	public function call(array $variables) {
		$templateName = $variables['template'];
		unset($variables['template']);

		$templatePath = __DIR__ . self::TEMPLATE_PATH . $templateName . self::TEMPATE_SUFFIX;

		$file = new File;
		$file->open($templatePath);

		$scope = new Scope($variables);

		$renderer = $this->container->get('ptcorpus.renderer');

		$pageCollection = $renderer->render($file, $scope);
		$pages = $pageCollection->getPages();

		if (1 !== count($pages)) {
			throw new \LogicException("Rendering $templateName returned more than one pages.");
		}

		$page = reset($pages);
		return $page['content'];
	}
}