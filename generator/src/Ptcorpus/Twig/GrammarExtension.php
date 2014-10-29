<?php

namespace Ptcorpus\Twig;

class GrammarExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('orconcat', array($this, 'orConcat')),
        );
    }

    public function orConcat($items)
    {
        if (count($items) == 1) {
            return $items[0];
        }

        if (count($items) == 2) {
            return $items[0] . ' or ' . $items[1];
        }

        $lastTwo = array_slice($items, -2, 2);
        $rest = array_slice($items, 0, -2);

        return implode(", ", $rest) . ', ' . implode(" or ", $lastTwo);
    }

    public function getName()
    {
        return 'grammar_extension';
    }
}