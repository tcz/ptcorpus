<?php

namespace Ptcorpus\Twig;

class GrammarExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('orconcat', array($this, 'orConcat')),
            new \Twig_SimpleFunction('andconcat', array($this, 'andConcat')),
            new \Twig_SimpleFunction('singular', array($this, 'singular')),
            new \Twig_SimpleFunction('lower', array($this, 'lower')),
            new \Twig_SimpleFunction('nicedate', array($this, 'niceDate')),
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

    public function andConcat($items)
    {
        if (count($items) == 1) {
            return $items[0];
        }

        if (count($items) == 2) {
            return $items[0] . ' and ' . $items[1];
        }

        $lastTwo = array_slice($items, -2, 2);
        $rest = array_slice($items, 0, -2);

        return implode(", ", $rest) . ', ' . implode(" and ", $lastTwo);
    }

    public function singular($text)
    {
        if (preg_match('/^\s*[aeiou]/i', $text)) {
            return "an " . $text;
        }
        return "a " . $text;
    }

    public function lower($text)
    {
        return strtolower($text);
    }

    public function niceDate($text)
    {
        $time = strtotime($text);

        return date('F j, Y', $time);
    }

    public function getName()
    {
        return 'grammar_extension';
    }
}