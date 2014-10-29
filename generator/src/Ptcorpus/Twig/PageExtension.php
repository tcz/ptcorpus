<?php

namespace Ptcorpus\Twig;

class PageExtension extends \Twig_Extension
{
    public function getTokenParsers()
    {
        return array(
            new PageTokenParser,
        );
    }

    public function getName()
    {
        return 'page_extension';
    }
}