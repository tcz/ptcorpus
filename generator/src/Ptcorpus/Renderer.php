<?php

namespace Ptcorpus;

class Renderer
{
	public function __construct(ParserFactory $parserFactory)
	{
		$this->parserFactory 	= $parserFactory;
	}

	public function render(File $file, Scope $scope = null)
	{
		if (!$scope) {
			$scope = new Scope;
		}

		$pages 		= new PageCollection;
		$parsers 	= $this->parserFactory->create($file, $scope, $pages);

		try {
			while (!$file->hasEnded()) {
				$line = $file->getLine();

				foreach ($parsers as $parser) {
					if ($parser->canHandle($line)) {
						$parser->handle($line);
						break;
					}
				}
			}
		} catch (\Exception $e) {
			throw new \Exception("Error while parsing {$file->getPath()}:" . $file->getLineNo() . "; " . $e->getMessage(), 0, $e);
		}

		return $pages;
	}
}