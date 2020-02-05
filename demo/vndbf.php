<?php

require "../vendor/autoload.php";

$crawl = new \Dz0x44\Mi\VndbfCrawler();

$data = $crawl->crawl();

dd($data);