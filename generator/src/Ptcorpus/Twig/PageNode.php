<?php

namespace Ptcorpus\Twig;

class PageNode extends \Twig_Node
{
	public function __construct(
		\Twig_Node_Expression $title,
		\Twig_Node_Expression $keywords,
		\Twig_Node_Expression $description,
		\Twig_NodeInterface $body,
		$lineno
	)
	{
		parent::__construct(array(
			'title' => $title,
			'keywords' => $keywords,
			'description' => $description,
			'body' => $body,
		), array(), $lineno);
	}

	public function compile(\Twig_Compiler $compiler)
	{
		$compiler
			->write('$pageData = array(')
			->write('"title" => ')
            ->subcompile($this->getNode('title'))
            ->write(', "keywords" => ')
            ->subcompile($this->getNode('keywords'))
            ->write(', "description" => ')
            ->subcompile($this->getNode('description'))
            ->write(', "url" => ' . get_class($this) . '::generateUrl(')
            ->subcompile($this->getNode('title'))
            ->raw("));\n")
			->write('echo json_encode($pageData), "---------------"')
            ->raw(";\n")
            ->subcompile($this->getNode('body'))
			->write('echo "---------------"')
            ->raw(";\n");
	}

	public static function generateUrl($title)
	{
		$title = strtolower($title);
		$url = preg_replace('/\W/', '-', $title);
		$url = preg_replace('/-+/', '-', $url);
		$url = $url . ".html";
		return $url;
	}
}