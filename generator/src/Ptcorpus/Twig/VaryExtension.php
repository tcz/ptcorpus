<?php

namespace Ptcorpus\Twig;

class VaryExtension extends \Twig_Extension
{
    public function getTokenParsers()
    {
        return array(
            new VaryTokenParser,
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('vary', array($this, 'vary')),
        );
    }

    public function getName()
    {
        return 'vary_extension';
    }

    public function vary($seed, array $items) {
        $variator = new Variator;
        $variator->setSeed($seed);

        // No weight specified
        if (is_numeric(key($items))) {
            foreach ($items as $item) {
                $variator->addItem($item);
            }
        } else {
            foreach ($items as $item => $weight) {
                $variator->addItem($item, $weight);
            }
        }

        return $variator->vary();
    }
}