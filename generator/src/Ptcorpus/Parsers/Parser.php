<?php

namespace Ptcorpus\Parsers;

interface Parser {
	public function canHandle($line);
	public function handle($line);
}