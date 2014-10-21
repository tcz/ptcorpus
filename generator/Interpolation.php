<?php

class Interpolation {
	const PREFIX = ':::';

	public function __construct(Scope $scope) {
		$this->scope = $scope;
	}

	public function apply($string) {
		$regex = '/' . preg_quote(self::PREFIX, '/') . '([a-zA-Z0-9\/\.\-\_]+)/';
		if (!preg_match($regex, $string, $matches)) {
			return $string;
		}

		$scope = $this->scope;

		return preg_replace_callback($regex, function($matches) use ($scope) {
			return $scope->lookup($matches[1]);
		}, $string);
	}
}