<?php

namespace Ptcorpus;

use Ptcorpus\UserFunction\UserFunction;

class Interpolation {
	const PREFIX = ':::';

	public function setScope(Scope $scope) {
		$this->scope = $scope;
	}

	public function apply($string) {
		if (!$this->scope) {
			throw new \LogicException("No scope set");
		}

		$quotedPrefix = preg_quote(self::PREFIX, '/');
		$idenfitifer = "a-zA-Z0-9\/\.\-\_";
		$regex = <<<REGEX
			/
			$quotedPrefix
			( 							# capturing the whole expression
				[$idenfitifer]+			# function or variable name
				(						# optional argument list (for a function)
					\(
					([$idenfitifer\,\'\s]+)?
					\)
				)?
			)
			/x
REGEX;

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
				$variables = preg_split("/\s*,\s*/", $variables);

				$variables = array_map(function($variable) use ($scope) {
					$variable = trim($variable);
					// String literal
					if (preg_match("/^'(.*)'$/", $variable, $matches)) {
						return $matches[1];
					}
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