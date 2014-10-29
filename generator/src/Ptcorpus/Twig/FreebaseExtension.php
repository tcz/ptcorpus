<?php

namespace Ptcorpus\Twig;

use Ptcorpus\Freebase;

class FreebaseExtension extends \Twig_Extension
{
	public function __construct(Freebase $freebase)
	{
		$this->freebase = $freebase;
	}

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('freebase', array($this, 'freebaseFunction')),
        );
    }

    public function freebaseFunction($json)
    {
        $object = json_decode($json, true);
        if (!$object) {
        	throw new \RuntimeException("Cannot parse JSON: $json");
        }

        return $this->freebase->query($object);
    }

    public function getName()
    {
        return 'freebase_extension';
    }
}