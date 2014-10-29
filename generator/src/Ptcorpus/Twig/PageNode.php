<?php

namespace Ptcorpus\Twig;

class PageNode extends \Twig_Node
{
	public function __construct(\Twig_Node_Expression $title, \Twig_NodeInterface $body, $lineno)
	{
		parent::__construct(array(
			'title' => $title,
			'body' => $body,
		), array(), $lineno);
	}

	public function compile(\Twig_Compiler $compiler)
	{
		$compiler
			->write('echo ')
            ->subcompile($this->getNode('title'))
            ->raw(";\n")
            ->subcompile($this->getNode('body'));
	}
}