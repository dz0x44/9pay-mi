<?php

namespace Dz0x44\Mi;


class VndbfCrawler extends CrawlerBase {
	const URL = 'https://ipaam.com.vn/quy-dau-tu/quy-mo-trai-phieu-vndbf/bao-cao-nav/';

	public function crawl(){
		$crawler = $this->client->request('GET', self::URL);
        $src = $crawler->filter('iframe')->first()->attr('src');
        $src = str_replace('widget=true', 'widget=false', $src);
        $crawler = $this->client->request('GET', trim($src));
		return $this->_extract_data($crawler->html());
	}

	private function _extract_data($content){
        if (preg_match('/chartJson(.*?)serializedChartProperties/', $content, $matches)) {
            return $this->decode($matches[1]);
        }

        return false;
	}

	private function decode($code){
		$code = str_replace("': '", '', $code);
		$code = str_replace("', '", '', $code);
		$code = str_replace('\x7b', '{', $code);
		$code = str_replace('\x7d', '}', $code);
		$code = str_replace('\x22', '"', $code);
		$code = str_replace('\x5b', "[", $code);
		$code = str_replace('\x5d', "]", $code);

		$data = json_decode($code, true);

		if ($data = $data['dataTable']['rows'] ?? false){
			$response = [];
			foreach ($data as $row){
				$response[$row['c'][0]['v']] = $row['c'][1]['v'];
			}

			return $response;
		}

		return false;
	}
}