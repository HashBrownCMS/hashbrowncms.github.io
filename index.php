<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './vendor/autoload.php';
require_once './functions.php';

// Settings
$cache_timeout = 60 * 60 * 24;

// Check cache
$cache_path = __DIR__ . '/.cache';
$cache_data = unserialize(@file_get_contents($cache_path));

if(!$cache_data || time() - filemtime($cache_path) >= $cache_timeout) {
    $cache_data = [];

    // API docs overview
    $cache_data['/docs/api'] = [
        '@context' => 'http://schema.org',
        '@type' => 'WebPage',
        'name' => 'API docs',
        'description' => 'The documentation for website developers',
        'url' => '/docs/api',
        'text' => 'To authorise an API request, you must get an API token, like this: <pre>POST { username: myusername, password: mypassword } /api/user/login?persist=true|false</pre>',
        'mainContentOfPage' => [],
    ];
    
    foreach(recurse_directory(__DIR__ . '/repo/src/Server/Controller') as $file) {
        $data = parse_source_file(@file_get_contents($file), pathinfo($file, PATHINFO_FILENAME));

        if(empty($data)) { continue; }

        $cache_data['/docs/api']['mainContentOfPage'][] = $data;
    }
    
    // Source docs overview
    $cache_data['/docs/src'] = [
        '@context' => 'http://schema.org',
        '@type' => 'CollectionPage',
        'name' => 'Source docs',
        'description' => 'The documentation for HashBrown developers',
        'url' => '/docs/src',
        'relatedContent' => [],
    ];

    // Init markdown
    $converter = new League\CommonMark\CommonMarkConverter();

    foreach(recurse_directory(__DIR__ . '/repo') as $file) {
        if(
            basename($file) === 'Home.md' ||
            basename($file) === 'index.js'|| 
            strpos($file, 'Controller') !== false
        ) { continue; }

        // Get extension
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if($extension !== 'md' && $extension !== 'js') { continue; }

        // Get file data
        $data = @file_get_contents($file);

        if(empty($data)) { continue; }

        // Init JSON
        $json = [
            '@context' => 'http://schema.org',
            '@type' => 'WebPage',
        ];

        // Index page
        if(basename($file) === 'README.md') {
            $json['name'] = 'HashBrown CMS';
            $json['description'] = 'A free and open-source headless CMS built with Node.js and MongoDB';
            $json['url'] = '/';
            
            $data = preg_replace('/^.+\n/', '', $data);
            $data = preg_replace('/^.+\n/', '', $data);
            
            $json['text'] = $converter->convertToHtml($data);

        // Documentation
        } else if($extension === 'js') {
            foreach(parse_source_file($data, pathinfo($file, PATHINFO_FILENAME)) as $key => $value) {
                $json[$key] = $value;                        
            }

            $json['url'] = strtolower('/docs/src' . str_replace(__DIR__ . '/repo/src', '', dirname($file)) . '/' . pathinfo($file, PATHINFO_FILENAME));

            if(!$json['member_of']) { continue; }

            if(!isset($cache_data['/docs/src']['relatedContent'][$json['member_of']])) {
                $cache_data['/docs/src']['relatedContent'][$json['member_of']] = [];
            }

            $cache_data['/docs/src']['relatedContent'][$json['member_of']][] = $json;

        }

        $cache_data[$json['url']] = $json;
    }
                
    ksort($cache_data['/docs/src']['relatedContent']);
    
    file_put_contents($cache_path, serialize($cache_data));
}

$url = strtok($_SERVER['REQUEST_URI'], '?');

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
    case 'CollectionPage':
        require './collection-page.php';
        break;
    
    case 'ApiClass':
        require './api-class.php';
        break;
    
    default:
        require './web-page.php';
        break;
}
