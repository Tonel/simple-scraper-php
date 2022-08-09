<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use voku\helper\HtmlDomParser;

function scrapeShopPage($paginationNumber) {
    $productDataList = array();

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://scrapeme.live/shop/page/$paginationNumber");
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $pageHtml = curl_exec($curl);
    curl_close($curl);

    $paginationHtmlDomParser = HtmlDomParser::str_get_html($pageHtml);

    // retrieving the list of products on the page
    $productElements = $paginationHtmlDomParser->find("li.product");

    foreach ($productElements as $productElement) {
        $productDataList[] = scrapeProduct($productElement);
    }

    return $productDataList;
}

function scrapeProduct($productElement) {
    // extracting the product data
    $url = $productElement->findOne("a")->getAttribute("href");
    $image = $productElement->findOne("img")->getAttribute("src");
    $name = $productElement->findOne("h2")->text;
    $price = $productElement->findOne(".price span")->text;

    // transforming the product data in an associative array
    return array(
        "url" => $url,
        "image" => $image,
        "name" => $name,
        "price" => $price
    );
}