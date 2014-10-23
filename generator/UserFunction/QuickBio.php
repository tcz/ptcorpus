<?php

require_once(__DIR__.'/UserFunction.php');

class QuickBio implements UserFunction {
	public function getName() {
		return 'quickbio';
	}

	public function call() {
		$person = func_get_arg(0);

		return $person['name'];
	}
}