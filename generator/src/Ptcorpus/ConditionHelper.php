<?php

namespace Ptcorpus;

class ConditionHelper {

	public static function evaluate($expression) {
		if (!preg_match('/(\S+)\s*(\=|\>|\<)\s*(\S+)/', $expression, $matches)) {
			throw new Exception("Cannot parse condition: $expression");
		}

		if (is_numeric($matches[1])) {
			$matches[1] = $matches[1] + 0;
		}
		if (is_numeric($matches[3])) {
			$matches[3] = $matches[3] + 0;
		}

		$result = null;
		switch ($matches[2]) {
			case '=':
				$result = ($matches[1] === $matches[3]);
				break;
			case '<':
				$result = ($matches[1] < $matches[3]);
				break;
			case '>':
				$result = ($matches[1] > $matches[3]);
				break;
		}

		return $result;
	}
}