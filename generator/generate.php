<?php

$templatePath = $argv[1];
$destination = $argv[2];

if (!is_dir($destination)) {
	throw new Exception("$destination must be a directory");
}

require_once __DIR__ . '/File.php';
require_once __DIR__ . '/Scope.php';
require_once __DIR__ . '/Freebase.php';
require_once __DIR__ . '/PageCollection.php';
require_once __DIR__ . '/Interpolation.php';
require_once __DIR__ . '/Parsers/FreebaseLookup.php';
require_once __DIR__ . '/Parsers/ForLoop.php';
require_once __DIR__ . '/Parsers/Page.php';
require_once __DIR__ . '/Parsers/PageLine.php';
require_once __DIR__ . '/Parsers/AbortPage.php';

$file 			= new File;
$scope 			= new Scope;
$freebase 		= new Freebase;
$pages 			= new PageCollection;
$interpolation 	= new Interpolation($scope);

$parsers = array(
	new FreebaseLookup($file, $scope, $freebase, $interpolation),
	new ForLoop($file, $scope, $interpolation),
	new Page($pages, $interpolation),
	new AbortPage($pages, $interpolation),
	new PageLine($pages, $interpolation),
);

$file->open($templatePath);

try {
	while (!$file->hasEnded()) {
		$line = $file->getLine();

		foreach ($parsers as $parser) {
			if ($parser->canHandle($line)) {
				$parser->handle($line);
				break;
			}
		}
	}
} catch (\Exception $e) {
	throw new Exception("Error while parsing $templatePath:" . $file->getLineNo() . "; " . $e->getMessage(), 0, $e);
}

$pages = $pages->getPages();

foreach ($pages as $page) {
	$path = rtrim($destination, '/') . '/' . $page['url'];
	file_put_contents($path, <<<CONTENT
---
title: {$page['title']}
layout: post
---

{$page['content']}
CONTENT
	);
}