<?php

namespace Ptcorpus;

class Freebase {
	const API_KEY = 'AIzaSyBpe_wWFnUMBI5MtMyr6JvnEP4N6OQF4hg';
	const BASE_URL = 'https://www.googleapis.com/freebase/v1/mqlread?lang=%2Flang%2Fen';
	const CACHE_DIR = '/tmp/freebasecache/';

	public function query($query) {
		$query = json_encode($query);

		if (!$response = $this->readFromCache($query)) {
			$url = self::BASE_URL .
				"&query=" . urlencode($query) .
				"&key=" . self::API_KEY;

			$response = file_get_contents($url);
		}

		$data = json_decode($response, true);

		if (null === $data) {
			throw new \Exception("Invalid response: $response");
		}

		if (array_key_exists("errors", $data) || !array_key_exists("result", $data)) {
			throw new \Exception("Error: $response");
		}

		$this->saveToCache($query, $response);

		return $data['result'];
	}

	private function readFromCache($key) {
		$normalizedKey = $this->normalizeKey($key);
		$path = self::CACHE_DIR . $normalizedKey;

		if (is_file($path)) {
			return file_get_contents($path);
		}
	}

	private function saveToCache($key, $value) {
		$normalizedKey = $this->normalizeKey($key);
		$path = self::CACHE_DIR . $normalizedKey;

		if (!is_dir(dirname($path))) {
			mkdir(dirname($path), 0777, true);
		}

		file_put_contents($path, $value);
	}

	private function normalizeKey($key) {
		return md5($key);
	}
}