<?php

$file = realpath($argv[1]);
if (!is_file($file)) {
	echo "File $file not found.";
	exit(-1);
}

$handle = fopen($file, 'r');

while (!feof($handle)) {
	$title = trim(fgets($handle));
	$body = "";
	while (!feof($handle)) {
		$line = trim(fgets($handle));
		if ($line === "") {
			break;
		}
		$body .= $line . "\n";
	}

	echo "Title: $title\n\n";
	echo "Body: $body\n\n";
	break;
}