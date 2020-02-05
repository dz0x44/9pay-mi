<?php

require "../vendor/autoload.php";

$crawl = new \Dz0x44\Mi\FinhayCrawler();

$data = $crawl->crawl(); // Crawl all FUND

//$data = $crawl->crawl('BVBF'); // Crawl single FUND

//$data = $crawl->crawl(['BVBF', 'TCBF']); // Crawl multi FUND


dd($data);