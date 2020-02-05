<?php


namespace Dz0x44\Mi;


use Goutte\Client;
use Symfony\Component\BrowserKit\CookieJar;

class CrawlerBase{

	protected $client = null;
	CONST COOKIE_FILE = 'cookies.dat';

	protected function saveCookie(){
//		$cookieJar = $this->client->getCookieJar();
//
//		file_put_contents(self::COOKIE_FILE, serialize($cookieJar->all()));
	}

	public function __construct(){
		$cookieJar = new CookieJar();
//		if (file_exists(self::COOKIE_FILE)){
//			$cookies = file_get_contents(self::COOKIE_FILE);
//			$cookies = unserialize($cookies);
//			foreach ($cookies as $cookie){
//				$cookieJar->set($cookie);
//			}
//		}
		$this->client = new Client(null, null, $cookieJar);
	}
}