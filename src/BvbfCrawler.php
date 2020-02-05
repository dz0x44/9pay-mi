<?php

namespace Dz0x44\Mi;

class BvbfCrawler extends CrawlerBase {
	const URL = 'https://baovietfund.com.vn/san-pham/BVBF';

	public function crawl(){
		$crawler = $this->client->request('GET', self::URL);
		return $this->_extract_data($crawler->text());
	}

	private function _extract_data($content){
		if (preg_match('/var x = (.*?);/', $content, $matches)) {
			$data = json_decode($matches[1]);
			$response = [];

			foreach ($data as $row)
			if (sizeof($row) === 2){
				$response[$row[0]] = $row[1];
			}

			ksort($response);
			return $response;
		}

		return false;
	}
}