<?php

require_once(__DIR__.'/UserFunction.php');

class Render implements UserFunction {
	public function getName() {
		return 'render';
	}

	public function call() {
		$person = func_get_arg(1);
		$template = func_get_arg(0);

		return $template . ': ' . $person['name'];
	}
}