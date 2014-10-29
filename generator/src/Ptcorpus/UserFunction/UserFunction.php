<?php

namespace Ptcorpus\UserFunction;

use Symfony\Component\DependencyInjection\ContainerInterface;

interface UserFunction
{
	public function getName();
	public function call(array $variables);
	public function setContainer(ContainerInterface $container);
}