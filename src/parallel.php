<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once  __DIR__ . '/constants.php';
require_once  __DIR__ . '/utils/scraping.php';

$from = null;
$to = null;

if (isset($_GET["from"]) && is_numeric($_GET["from"])) {
    $from = $_GET["from"];
}

if (isset($_GET["to"]) && is_numeric($_GET["to"])) {
    $to = $_GET["to"];
}

if (is_null($from) || is_null($to) || $from > $to) {
    die("Invalid from and to parameters!");
}

$productDataList = array();

for ($paginationNumber = $from; $paginationNumber <= $to; $paginationNumber++) {
    $productDataList = array_merge($productDataList, scrapeShopPage($paginationNumber));
}

// writing the data scraped to a database/file