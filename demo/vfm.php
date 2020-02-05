<?php

require "../vendor/autoload.php";

$crawl = new \Dz0x44\Mi\VfmCrawler();

$data = $crawl->crawl('VFMVFB', '01/11/2019', '01/02/2020');

dd($data);