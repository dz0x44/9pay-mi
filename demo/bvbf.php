<?php

require "../vendor/autoload.php";

$crawl = new \Dz0x44\Mi\BvbfCrawler();

$data = $crawl->crawl();

dd($data);