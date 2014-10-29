<?php

namespace Ptcorpus;

class ParserFactory
{
	public function __construct(Interpolation $interpolation, Freebase $freebase)
	{
		$this->interpolation = $interpolation;
		$this->freebase = $freebase;
	}

	public function create($file, $scope, $pages)
	{
		$interpolation = clone $this->interpolation;
		$interpolation->setScope($scope);

		return array(
			new Parsers\ForLoop($file, $scope, $interpolation),
			new Parsers\Page($pages, $interpolation),
			new Parsers\AbortPage($pages, $interpolation),
			new Parsers\FreebaseLookup($file, $scope, $this->freebase, $interpolation),
			new Parsers\PageLine($pages, $interpolation),
		);
	}
}