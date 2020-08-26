<?php

namespace Dz0x44\Mi;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class VfmCrawler extends CrawlerBase {
	const DOMAIN = 'https://vfm.com.vn/';
	const URL_DATA = self::DOMAIN . 'wp-admin/admin-ajax.php';

	private $crawler = null;
	private $date_from = null;
	private $date_to = null;
	private $fund_code = null;

	private function get_nav_list(){
		$params = [
			'category' => 'bao-cao-nav',
			'fund_code' => $this->fund_code,
			'action' => 'get_report_ajax',
			'limit' => 10000,
			'from' => $this->date_from,
			'to' => $this->date_to
		];

		$links = [];

		$this->crawler = $this->client->request('POST', self::URL_DATA, $params);
		$this->crawler->filter('a')->each(function ($node) use (&$links){
			$link = $node->attr('href');
			$title = explode(' ', $node->text());
			$date = array_pop($title);

			if (strpos($link, 'bao-cao-gia-tri-tai-san-rong-tuan') !== false){
				$links[$date] = $link;
			}
		});

		return $links;
	}

	private function download_file($file_url){
		$tmp_name = tempnam(sys_get_temp_dir(),"tmp_xls");
		$fileHandler = fopen($tmp_name, 'w');

		$data = file_get_contents($file_url);
		fwrite($fileHandler, $data);

		return $tmp_name;
	}

	private function read_xlsx($file_path){
		$reader = ReaderEntityFactory::createXLSXReader();
		$reader->open($file_path);

		$data = [];
		foreach ($reader->getSheetIterator() as $sheet) {
			if ($sheet->getIndex() === 1) {
				foreach ($sheet->getRowIterator() as $row) {
					$rowData = [];
					foreach ($row->getCells() as $col){
						$rowData[] = $col->getValue();
					}
					$data[] = $rowData;
				}
				break;
			}
		}
		$reader->close();

		return $data;
	}

	private function get_data($link){
		$this->crawler = $this->client->request('GET', $link);
		$file_link = $this->crawler->filter('.view-detail-report a')->attr('href');

		$file_path = $this->download_file($file_link);
		$data = $this->read_xlsx($file_path);

		return $data[2][3] ?? false;
	}

	public function crawl($fund_code, $date_from, $date_to){
		$this->fund_code = $fund_code;
		$this->date_from = $date_from;
		$this->date_to = $date_to;

		$nav_links = $this->get_nav_list();
		$response = [];

		foreach ($nav_links as $date => $link){
			$response[$date] = $this->get_data($link);
		}

		return $response;
	}
}