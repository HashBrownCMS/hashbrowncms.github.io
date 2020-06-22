<?php

$cache_timeout = 60 * 60 * 24;

$cache_path = __DIR__ . '/.cache';
$cache_data = unserialize(@file_get_contents($cache_path));

if(isset($_GET['debug']) || !$cache_data || time() - filemtime($cache_path) >= $cache_timeout) {
    $cache_data = [];
    
    build_src_docs($cache_data);
    build_api_docs($cache_data);
    build_markdown_pages($cache_data);
 
    file_put_contents($cache_path, serialize($cache_data));
}
