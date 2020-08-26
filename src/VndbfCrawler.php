<?php

namespace Dz0x44\Mi;


use Goutte\Client;

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

		$year = 2019;
		if ($data = $data['dataTable']['rows'] ?? false){
			$response = [];
			$pre_loop_month = null;
			foreach ($data as $row){
                if ($pre_loop_month == 'Dec' && strpos($row['c'][0]['v'], $pre_loop_month) === false) {
			        $year++;
                }
                $key = $row['c'][0]['v'] . '-' . $year;
                $response[$key] = $row['c'][1]['v'];
                $pre_loop_month = explode("-", $row['c'][0]['v'])[1];
			}

			return $response;
		}

		return false;
	}
}
