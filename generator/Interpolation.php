<?php

class Interpolation {
	const PREFIX = ':::';

	public function __construct(Scope $scope) {
		$this->scope = $scope;
	}

	public function apply($string) {
		$regex = '/' . preg_quote(self::PREFIX, '/') . '([a-zA-Z0-9\/\.\-\_\(\)\,]+)/';
		if (!preg_match($regex, $string, $matches)) {
			return $string;
		}

		$scope = $this->scope;
		$functions = $this->functions;

		return preg_replace_callback($regex, function($matches) use ($scope, $functions) {
			if (false !== strpos($matches[1], '(')) {
				$function = substr($matches[1], 0, strpos($matches[1], '('));

				if (!isset($functions[$function])) {
					throw new \Exception("Function $function not found.");
				}

				$function = $functions[$function];

				$variables = substr($matches[1], strpos($matches[1], '(') + 1, -1);
				$variables = explode(",", $variables);
				$variables = array_map(function($variable) use ($scope) {
					return $scope->lookup($variable);
				}, $variables);

				return call_user_func_array(array($function, 'call'), $variables);
			}
			return $scope->lookup($matches[1]);
		}, $string);
	}

	public function registerFunction(UserFunction $function) {
		$this->functions[$function->getName()] = $function;
	}
}