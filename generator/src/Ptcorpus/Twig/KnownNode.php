<?php

namespace Ptcorpus\Twig;

class KnownNode extends \Twig_Node_Expression_Test
{
	public function __construct(\Twig_NodeInterface $node, $name, \Twig_NodeInterface $arguments = null, $lineno)
    {
        parent::__construct($node, $name, $arguments, $lineno);
        if ($node instanceof \Twig_Node_Expression_Name || $node instanceof \Twig_Node_Expression_GetAttr) {
            $this->changeIgnoreStrictCheck($node);
        } else {
            throw new \Twig_Error_Syntax('The "defined" test only works with simple variables', $this->getLine());
        }
    }

	protected function changeIgnoreStrictCheck(\Twig_Node $node)
    {
        $node->setAttribute('ignore_strict_check', true);
        if ($node->getNode('node') instanceof Twig_Node_Expression_GetAttr) {
            $this->changeIgnoreStrictCheck($node->getNode('node'));
        }
    }

	public function compile(\Twig_Compiler $compiler)
    {
        $compiler
        	->raw('(null !== ')
        	->subcompile($this->getNode('node'))
        	->raw(' && array() !== ')
        	->subcompile($this->getNode('node'))
        	->raw(')')
        ;
    }
}