<?php

namespace Dz0x44\Mi;

class FinhayCrawler extends CrawlerBase {
	const DOMAIN = 'https://app.finhay.com.vn/';
	const URL_LOGIN = self::DOMAIN . 'login/';
	const URL_HOME = self::DOMAIN . 'home';
	const URL_DATA = self::DOMAIN . 'navFund?fund_id=';

	const USERNAME = '0989315924';
	const PASSWORD = 'Vcc123**';

	private $crawler = null;
	private $ccqs = [
		'VFB' => 1,
		'TCBF' => 2,
		'VF1' => 3,
		'BCF' => 4,
		'SCA' => 5,
		'BVBF' => 6,
		'VNDAF' => 7,
		'BVPF' => 8,
		'ETFVN30' => 9,
		'TCFF' => 10,
		'SSIBF' => 11,
		'ETFVN50' => 12,
		'VFC' => 13,
		'VF4' => 14,
		'TBF' => 15,
		'FIF' => 16,
		'TCEF' => 17,
		'VEOF' => 18,
		'VFF' => 19,
		'VIBF' => 20,
		'VCAMBF' => 21,
		'VNDBF' => 22,
	];

	public function checkLogin(){
//		dump('Check Login');

		$this->crawler = $this->client->request('GET',  self::URL_HOME);
		$isLogin = $this->crawler->getUri() === self::URL_HOME;

		if (!$isLogin){
			return $this->doLogin();
		}

		return true;
	}

	public function doLogin(){
//		dump('Do login');

		$this->crawler = $this->client->request('GET', self::URL_LOGIN);

		if ($this->crawler->getUri() != self::URL_LOGIN){
			$form = $this->crawler->selectButton('Đăng nhập')->form();
			$this->crawler = $this->client->submit($form, [
				'email' => self::USERNAME,
				'password' => self::PASSWORD
			]);
		}

		$this->saveCookie();

		return true;
	}

	private function crawlData($code){
		$code = strtoupper($code);

		if (!isset($this->ccqs[$code])){
			return false;
		}

		$ccq_id = $this->ccqs[$code];

		$url = self::URL_DATA . $ccq_id;

		$this->crawler = $this->client->request('GET', $url);
		$response = false;

		if ($data = $this->crawler->getUri() === $url ? json_decode($this->crawler->text()) : false){
			foreach ($data as $row){
				$response[$row[0]] = $row[1];
			}
		}

		return $response;
	}

	public function crawl($ccq_code = false){
		if (!$this->checkLogin()){
			return false;
		}

		$ccq_code = empty($ccq_code) ? array_keys($this->ccqs) : $ccq_code;
		$codes = is_array($ccq_code) ? $ccq_code : [$ccq_code];
		$response = [];

		foreach ($codes as $code){
			$response[$code] = $this->crawlData($code);
		}

		return $response;
	}
}
