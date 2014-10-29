<?php

namespace Ptcorpus;

class File {
	private $handle;
	private $lineNo;
	private $path;

	public function open($path) {
		if (!is_file($path)) {
			echo "File $file not found.";
			exit(-1);
		}
		$this->handle = fopen($path, 'r');
		$this->lineNo = 0;
	}

	public function hasEnded() {
		return feof($this->handle);
	}

	public function getLine() {
		$this->lineNo++;
		return trim(fgets($this->handle));
	}

	public function getLineNo() {
		return $this->lineNo;
	}

	public function getPosition() {
		return ftell($this->handle);
	}

	public function toPosition($position) {
		return fseek($this->handle, $position);
	}

	public function getPath() {
		return $this->path;
	}
}