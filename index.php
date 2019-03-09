<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('URI', $_SERVER['REQUEST_URI']);
define('PATH', array_values(array_filter(explode('/', URI), function($value) { return $value !== ''; })));
define('ROOT_DIR', __DIR__);

function get_output_cache() {
    if(!file_exists(ROOT_DIR . '/cache')) { return null; }

    $key = base64_encode(URI);

    if(!file_exists(ROOT_DIR . '/cache/' . $key)) { return null; }

    $json = file_get_contents(ROOT_DIR . '/cache/' . $key);
    $json = json_decode($json);

    if($json->expires < time()) { return null; }

    return $json->html;
}

function set_output_cache($html) {
    if(!file_exists(ROOT_DIR . '/cache')) { return; }
    
    $key = base64_encode(URI);

    $json = json_encode([ 'expires' => time() + 60, 'html' => $html ]);

    file_put_contents(ROOT_DIR . '/cache/' . $key, $json);
}

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

function check_doc_link($link) {
    return
        $link !== 'index.js' &&
        $link !== 'server.js' &&
        $link !== 'client.js' &&
        $link !== 'common.js' &&
        $link !== 'demo.js' &&
        $link !== 'dashboard.js' &&
        $link !== 'utilities.js' &&
        $link !== 'helpers.js' &&

        $link !== 'ApiController.js' &&
        $link !== 'Controller.js' &&

        $link !== 'Schemas' &&
        $link !== 'Routes' &&
        $link !== 'Style' &&

        !strpos($link, ' ') &&
        
        $link !== 'icons.json';
}

function render_page() {
    $html = get_output_cache();

    if(!$html) {
        ob_start();
        
        switch(get_path(0)) {
            case 'docs':
                $page_is_class = substr(URI, -3) === '.js';
                $page_is_view = substr(URI, -4) === '.pug';
                $page_is_api = strpos(URI, '/API') !== false;
                $page_is_dir = !$page_is_class && !$page_is_view;
                
                $page_title = PATH[sizeof(PATH) - 1];
                $page_description = '';

                if($page_is_class) {
                    $file_url = 'https://' . path_join('raw.githubusercontent.com/HashBrownCMS/hashbrown-cms/stable', str_replace('docs/', 'src/', URI)); 

                    if($page_is_api) {
                        $file_url = str_replace('/API', '/Controllers', $file_url);
                    }

                    $file_contents = file_get_contents($file_url);

                    $class_name = $page_title;

                    $class_description = [];
                    preg_match("/\* ([^@][^\n]+)/", $file_contents, $class_description);
                    $class_description = isset($class_description[1]) ? $class_description[1] : ''; 
                    $page_description = $class_description;

                    // Remove the class meta docs to prevent confusion
                    $file_contents = preg_replace("/\/\*\*[^\/]+\//", '', $file_contents, 1);

                    $class_member_vars = [];
                    preg_match_all("/this\.def\(([^,]+), '([^']+)'/", $file_contents, $class_member_vars, PREG_SET_ORDER);

                    $class_methods = [];
                    preg_match_all("/\/\*\*\n +[^\(]+\([^'\)]*\) {/", $file_contents, $class_methods);
                    
                    include __DIR__ . '/views/docs.php';

                } else if($page_is_view) {
                    $file_url = 'https://' . path_join('raw.githubusercontent.com/HashBrownCMS/hashbrown-cms/stable', str_replace('docs/', 'src/', URI)); 
                    $file_contents = file_get_contents($file_url);
                    
                    include __DIR__ . '/views/docs.php';

                } else if($page_is_dir) {
                    $page_url = 'https://' . path_join('github.com/HashBrownCMS/hashbrown-cms/tree/stable', str_replace('docs/', 'src/', URI));
                    
                    if($page_title === 'docs') {
                        $page_title = 'Documentation';
                    }

                    if($page_title === 'API') {
                        $page_url = str_replace('/API', '/Controllers', $page_url);
                    }

                    $page_contents = file_get_contents($page_url);

                    $page_links = [];
                    preg_match_all('/<a.+class="js-navigation-open" title="([^"]+)"/', $page_contents, $page_links);
                    
                    include __DIR__ . '/views/docs.php';
                }
                break;

            default:
                require_once(__DIR__ . '/lib/hashbrown-driver/index.php');

                HashBrown\init(__DIR__);

                $page = HashBrown\get_current_page();

                if(isset($page->title)) { 
                    $page_title = $page->title;
                }
                
                if(isset($page->description)) { 
                    $page_description = $page->description;
                }

                HashBrown\render_current_page();
                break;
        }

        $html = ob_get_clean();
        
        set_output_cache($html);
    }

    echo $html;
}

render_page();

?>
