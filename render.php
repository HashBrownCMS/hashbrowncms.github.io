<?php

$url = strtok($_SERVER['REQUEST_URI'], '?');
$url = '/' . implode('/', array_filter(explode('/', $url)));

// Page
if(isset($cache_data[$url])) {
    $json = $cache_data[$url];

// 404
} else {
    http_response_code(404);

    $json = [
        '@context' => 'http://schema.org',
        '@type' => 'WebPage',
        'name' => 'Not found',
        'description' => 'The page ' . $url . ' could not be found',
        'text' => '<a href="/">Return to home page</a>'
    ];
}

switch($json['@type']) {
    case 'ApiSummary':
        require './api-summary.php';
        break;
    
    case 'ApiOverview':
        require './api-overview.php';
        break;
    
    case 'ApiClass':
        require './api-class.php';
        break;

    case 'CollectionPage':
        require './collection-page.php';
        break;

    default:
        require './web-page.php';
        break;
}
