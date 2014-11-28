<?php

namespace Ptcorpus;

use GuzzleHttp\Client as HttpClient;

class Freebase {
	//const API_KEY = 'AIzaSyBpe_wWFnUMBI5MtMyr6JvnEP4N6OQF4hg';
	const API_KEY = 'AIzaSyBOZpApMj6jcYUekBYsEw9T1iJGIpLkrm0';
	const BASE_URL = 'https://www.googleapis.com/freebase/v1/mqlread?lang=%2Flang%2Fen';
	const CACHE_DIR = '/tmp/fbcache/';

	private $client;

	public function __construct(HttpClient $client) {
		$this->client = $client;
	}

	public function query($query) {
		$query = json_encode($query);

		fwrite(STDERR, "Querying $query\n");

		if (!$response = $this->readFromCache($query)) {
			$url = self::BASE_URL .
				"&query=" . urlencode($query) .
				"&key=" . self::API_KEY;


			try {
				$responseObject = $this->client->get($url);
			} catch (\GuzzleHttp\Exception\RequestException $e) {
				if ($e->hasResponse()) {
					if (strpos($e->getResponse()->getBody(), "Query too difficult") !== false) {
						return array();
					}

					throw new \Exception("Invalid response: {$e->getResponse()}, queried $query");
				}
				throw new \Exception("Invalid response. Queried $query");
			}

			$status 		= $responseObject->getStatusCode();
			$response 		= $responseObject->getBody();
		}

		$data = json_decode($response, true);

		if (null === $data) {
			throw new \Exception("Invalid response: $response, queried $query");
		}

		if (array_key_exists("errors", $data) || !array_key_exists("result", $data)) {
			throw new \Exception("Error: $response");
		}

		$this->saveToCache($query, $response);

		return $data['result'];
	}

	private function readFromCache($key) {
		$normalizedKey = $this->normalizeKey($key);
		$path = $this->getCachePath($normalizedKey);

		if (is_file($path)) {
			return file_get_contents($path);
		}
	}

	private function saveToCache($key, $value) {
		$normalizedKey = $this->normalizeKey($key);
		$path = $this->getCachePath($normalizedKey);

		if (!is_dir(dirname($path))) {
			mkdir(dirname($path), 0777, true);
		}

		file_put_contents($path, $value);
	}

	private function getCachePath($normalizedKey) {
		return self::CACHE_DIR .
			substr($normalizedKey, 0, 1) . '/' .
			substr($normalizedKey, 1, 1) . '/' .
			substr($normalizedKey, 2, -1);
	}

	private function normalizeKey($key) {
		return md5($key);
	}
}