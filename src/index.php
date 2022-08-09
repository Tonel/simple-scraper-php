<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once  __DIR__ . '/constants.php';
require_once  __DIR__ . '/utils/scraping.php';

use voku\helper\HtmlDomParser;

// initializing the cURL request
$curl = curl_init();
// setting the URL to reach with a GET HTTP request
curl_setopt($curl, CURLOPT_URL, 'https://scrapeme.live/shop');
// to make the cURL request follow eventual redirects
// and reach the final page of interest
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
// to get the data returned by the cURL request as a string
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// setting the User-Agent header
curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);
// executing the cURL request and
// get the HTML of the page as a string
$html = curl_exec($curl);
// releasing the cURL resources
curl_close($curl);

// initializing HtmlDomParser
$htmlDomParser = HtmlDomParser::str_get_html($html);

// retrieving the HTML pagination elements with
// the ".page-numbers a" CSS selector
$paginationElements = $htmlDomParser->find(".page-numbers a");
$paginationLinks = [];
foreach ($paginationElements as $paginationElement) {
    // populating the paginationLinks set with the URL
    // extracted from the href attribute of HTML pagination element
    $paginationLink = $paginationElement->getAttribute("href");
    // avoiding duplicates in the list of URLs
    if (!in_array($paginationLink, $paginationLinks)) {
        $paginationLinks[] = $paginationLink;
    }
}

// removing all non-numeric characters in the last element of
// the $paginationLinks array to retrieve the highest pagination number
$highestPaginationNumber = preg_replace("/\D/", '', end($paginationLinks));

$productDataList = array();
// iterate over all "/shop/pages/X" pages and retrieve all product data
for ($paginationNumber = 1; $paginationNumber <= $highestPaginationNumber; $paginationNumber++) {
    $productDataList = array_merge($productDataList, scrapeShopPage($paginationNumber));
}

echo json_encode($productDataList);

// writing the data scraped to a database/file