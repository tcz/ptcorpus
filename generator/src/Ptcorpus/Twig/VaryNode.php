<?php

namespace Ptcorpus\Twig;

class VaryNode extends \Twig_Node
{
	public function __construct(
		\Twig_Node_Expression $seed,
		array $items,
		$lineno
	)
	{
		$nodes = array(
			'seed' => $seed,
		);

		$i = 0;
		foreach ($items as $item) {
			$nodes['item'.$i] = $item['item'];
			$nodes['weight'.$i] = $item['weight'];
			$i++;
		}

		parent::__construct($nodes, array(
			'item_count' => count($items),
		), $lineno);
	}

	public function compile(\Twig_Compiler $compiler)
	{
		$compiler
			->write('$variator = new Ptcorpus\Twig\Variator')
            ->raw(";\n")
			->write('$variator->setSeed(')
            ->subcompile($this->getNode('seed'))
            ->raw(");\n")
        ;

        $itemCount = $this->getAttribute('item_count');

        for ($i=0; $i < $itemCount; $i++) {

			$compiler
				->write("ob_start();\n")
           		->subcompile($this->getNode('item'.$i))
           		->raw(";\n")
           		->write('$tmp = ob_get_clean()')
           		->raw(";\n")
				->write('$variator->addItem($tmp')
           	;

            $weight = $this->getNode('weight'.$i);
            if ($weight) {
            	$compiler
            		->raw(', ')
            		->subcompile($weight)
            	;
            }

            $compiler->raw(");\n");
        }

        $compiler
			->write('echo $variator->vary()')
            ->raw(";\n")
        ;
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