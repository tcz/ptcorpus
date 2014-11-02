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
            new \Twig_SimpleFunction('title', array($this, 'title')),
            new \Twig_SimpleFunction('nicedate', array($this, 'niceDate')),
            new \Twig_SimpleFunction('numbertostring', array($this, 'numberToString')),
            new \Twig_SimpleFunction('pluralize', array($this, 'pluralize')),
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

    public function title($text)
    {
        return ucfirst($text);
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

    public function numberToString($number)
    {
        $number = (int) $number;
        switch ($number) {
            case 0:
                return 'zero';
            case 1:
                return 'one';
            case 2:
                return 'two';
            case 3:
                return 'three';
            case 4:
                return 'four';
            case 5:
                return 'five';
            case 6:
                return 'six';
            case 7:
                return 'seven';
            case 8:
                return 'eight';
            case 9:
                return 'nine';
            case 10:
                return 'ten';
            case 11:
                return 'eleven';
            case 12:
                return 'twelve';
            default:
                return $number;
        }
    }

    public static function pluralize($word)
    {
        $plurals = array(
            '/(quiz)$/i'                => '\1zes',
            '/^(ox)$/i'                 => '\1en',
            '/([m|l])ouse$/i'           => '\1ice',
            '/(matr|vert|ind)ix|ex$/i'  => '\1ices',
            '/(x|ch|ss|sh)$/i'          => '\1es',
            '/([^aeiouy]|qu)ies$/i'     => '\1y',
            '/([^aeiouy]|qu)y$/i'       => '\1ies',
            '/(hive)$/i'                => '\1s',
            '/(?:([^f])fe|([lr])f)$/i'  => '\1\2ves',
            '/sis$/i'                   => 'ses',
            '/([ti])um$/i'              => '\1a',
            '/(buffal|tomat)o$/i'       => '\1oes',
            '/(bu)s$/i'                 => '\1ses',
            '/(alias|status)/i'         => '\1es',
            '/(octop|vir)us$/i'         => '\1i',
            '/(ax|test)is$/i'           => '\1es',
            '/s$/i'                     => 's',
            '/$/'                       => 's'
        );
        $uncountables = array(
            'equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep'
        );
        $irregulars = array(
            'person'  => 'people',
            'man'     => 'men',
            'child'   => 'children',
            'sex'     => 'sexes',
            'move'    => 'moves'
        );
        $lowerCasedWord = strtolower($word);
        foreach ($uncountables as $uncountable) {
            if(substr($lowerCasedWord, (-1 * strlen($uncountable))) == $uncountable) {
                return $word;
            }
        }
        foreach ($irregulars as $plural => $singular) {
            if (preg_match('/(' . $plural . ')$/i', $word, $arr)) {
                return preg_replace(
                    '/(' . $plural . ')$/i',
                    substr($arr[0], 0, 1) . substr($singular, 1),
                    $word
                );
            }
        }
        foreach ($plurals as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }
        if (preg_match('/s$/i', $word)) {
            return $word . 'es';
        }

        return $word . 's';
    }
}