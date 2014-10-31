<?php

namespace Ptcorpus\Twig;

class KnownExtension extends \Twig_Extension
{
    public function getTests()
    {
        return array(
            new \Twig_SimpleTest('known', null, array('node_class' => 'Ptcorpus\Twig\KnownNode')),
        );
    }

    public function getName()
    {
        return 'known_extension';
    }
}