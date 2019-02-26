<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('URI', $_SERVER['REQUEST_URI']);
define('PATH', array_values(array_filter(explode('/', URI), function($value) { return $value !== ''; })));
define('ROOT_DIR', __DIR__);

function get_path($start, $levels = 1) {
    $path = [];

    for($level = $start; $level < $start + $levels; $level++) {
        if(!isset(PATH[$level]) || !PATH[$level]) { continue; }

        array_push($path, PATH[$level]);
    }

    return implode('/', $path);
}

function path_join() {
    $path = [];

    $use_prefix_slash = false;
    $use_suffix_slash = false;

    for($i = 0 ; $i < func_num_args(); $i++) {
        if($i === 0 && substr(func_get_arg($i), 0, 1) === '/') { 
            $use_prefix_slash = true;
        }
        
        if($i === func_num_args() - 1 && substr(func_get_arg($i), -1) === '/') { 
            $use_suffix_slash = true;
        }
        
        $path = array_merge($path, explode('/', func_get_arg($i)));
    }

    $path = array_filter($path);
    $path = array_values($path);
    $path = implode('/', $path); 

    if($use_prefix_slash) {
        $path = '/' . $path;
    }

    if($use_suffix_slash) {
        $path = $path . '/';
    }
    
    return $path;
}

if(get_path(0) === 'docs') {
    include __DIR__ . '/views/docs.php';

} else {    
    echo 'PAGES';

    require_once(__DIR__ . '/lib/hashbrown-driver/index.php');

    HashBrown\init(__DIR__);

    HashBrown\render_current_page();
}

?>
