<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('URI', $_SERVER['REQUEST_URI']);
define('PATH', array_values(array_filter(explode('/', URI), function($value) { return $value !== ''; })));
define('ROOT_DIR', __DIR__);
define('SRC_CLASS_ROOT_URL', 'https://raw.githubusercontent.com/HashBrownCMS/hashbrown-cms/stable');
define('SRC_DIR_ROOT_URL', 'https://github.com/HashBrownCMS/hashbrown-cms/tree/stable');

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

function parse_type($string) {
    if(strpos($string, 'HashBrown.') === false) { return $string; }

    $parts = explode('.', $string);

    return $parts[sizeof($parts) - 1];
}

function parse_dir($url) {
    $dir_contents = @file_get_contents($url);

    if(!$dir_contents) { not_found(); }

    $page_links = [];
    preg_match_all('/<a.+class="js-navigation-open" title="([^"]+)"/', $dir_contents, $page_links);

    if(sizeof($page_links) > 0) {
        $page_links = array_filter($page_links[1], function($link) {
            return
                $link !== 'index.js' &&
                $link !== 'server.js' &&
                $link !== 'client.js' &&
                $link !== 'environment.js' &&
                $link !== 'common.js' &&
                $link !== 'demo.js' &&
                $link !== 'dashboard.js' &&
                $link !== 'utilities.js' &&
                $link !== 'helpers.js' &&

                $link !== 'ApiController.js' &&
                $link !== 'ViewController.js' &&
                $link !== 'Controller.js' &&

                $link !== 'Schemas' &&
                $link !== 'Routes' &&
                $link !== 'Style' &&
                $link !== 'Controllers' &&
                $link !== 'Views' &&

                !strpos($link, ' ') &&
                
                $link !== 'icons.json';
        });
    }

    return ['links' => $page_links];
}

function parse_source_file($url) {
    $output = [];
    
    $file_contents = @file_get_contents($url);

    if(!$file_contents) { http_response_code(404); die('Not found'); }

    $output['name'] = PATH[sizeof(PATH) - 1];

    $output['source'] = $file_contents;
    
    // Description
    $class_description = [];
    preg_match("/\* ([^@][^\n]+)/", $file_contents, $class_description);
    $output['description'] = isset($class_description[1]) ? $class_description[1] : '';

    // Remove the class meta docs to prevent confusion
    $file_contents = preg_replace("/\/\*\*[^\/]+\//", '', $file_contents, 1);

    // Member variables
    $output['member_vars'] = [];
    preg_match_all("/this\.def\(([^,]+), '([^']+)'/", $file_contents, $output['member_vars'], PREG_SET_ORDER);

    // Methods
    $output['methods'] = [];
    preg_match_all("/\/\*\*\n +[^\(]+\([^'\)]*\) {/", $file_contents, $output['methods']);
    if(sizeof($output['methods']) > 0) {
        $output['methods'] = $output['methods'][0];
    }
    if(sizeof($output['methods']) > 0) {
        foreach($output['methods'] as $i => $method_string) {
            $method = [];
            
            $method['name'] = [];
            preg_match("/([a-zA-Z]+)\(/", $method_string, $method['name']);
            $method['name'] = isset($method['name'][1]) ? $method['name'][1] : '';

            if(!$method['name'] || $method['name'] === 'structure') {
                unset($output['methods'][$i]);    
                continue;
            }

            $method['description'] = [];
            preg_match("/\* ([^@][^\n]+)/", $method_string, $method['description']);
            $method['description'] = isset($method['description'][1]) ? $method['description'][1] : '';

            $method['is_static'] = preg_match("/static/", $method_string);
            $method['is_async'] = preg_match("/async/", $method_string);
            
            $method['example'] = [];
            preg_match("/@example ([^\n]+)/", $method_string, $method['example']);
            $method['example'] = isset($method['example'][1]) ? $method['example'][1] : '';

            $method['params'] = [];
            preg_match_all("/@param.+{([^}]+)} ([^\n]+)/", $method_string, $method['params'], PREG_SET_ORDER);
            
            $method['returns'] = [];
            preg_match("/@return.+{([^}]+)} ([^\n]+)/", $method_string, $method['returns']);

            if(isset($method['returns'][1]) && $method['returns'][1] === 'Promise') {
                $method['is_async'] = true;
            }

            $output['methods'][$i] = $method;
        }
    }

    return $output;
}

function not_found() {
    http_response_code(404);
    die('Not found');
}

function render_page() {
    $html = get_output_cache();

    if(!$html) {
        ob_start();
        
        switch(get_path(0)) {
            case 'docs':
                switch(get_path(1)) {
                    case 'src':
                        $page = [];

                        if(substr(URI, -1) === '/') {
                            $page_is_dir = true;

                            $file_url = SRC_DIR_ROOT_URL . str_replace('/docs/src', '/src', URI);
                            $page = parse_dir($file_url);

                            $page_title = PATH[sizeof(PATH) - 1];

                            if($page_title === 'src') {
                                $page_title = 'Source docs';
                            }

                            define('PAGE_TITLE', $page_title);
                            define('PAGE_DESCRIPTION', '');

                        } else {
                            $page_is_dir = false;

                            $file_url = SRC_CLASS_ROOT_URL . str_replace('/docs/src', '/src', URI) . '.js';
                            $page = parse_source_file($file_url);
                            
                            define('PAGE_TITLE', $page['name']);
                            define('PAGE_DESCRIPTION', $page['description']);
                        }
                        
                        include __DIR__ . '/views/docs/src.php';
                        break;

                    case 'api':
                        $page = [];

                        if(substr(URI, -1) === '/') {
                            $page_is_dir = true;

                            $file_url = SRC_DIR_ROOT_URL . str_replace('/docs/api', '/src/Server/Controllers', URI);
                            $page = parse_dir($file_url);

                            $page_title = PATH[sizeof(PATH) - 1];
                            
                            if($page_title === 'api') {
                                $page_title = 'API docs';
                            }

                            define('PAGE_TITLE', $page_title);
                            define('PAGE_DESCRIPTION', '');

                        } else {
                            $page_is_dir = false;

                            $file_url = SRC_CLASS_ROOT_URL . str_replace('/docs/api', '/src/Server/Controllers', URI) . 'Controller.js';
                            $page = parse_source_file($file_url);
                            
                            define('PAGE_TITLE', $page['name']);
                            define('PAGE_DESCRIPTION', '');
                        }
                        
                        include __DIR__ . '/views/docs/api.php';
                        break;

                    default:
                        not_found();
                }
                break;

            default:
                require_once(__DIR__ . '/lib/hashbrown-driver/index.php');

                HashBrown\init(__DIR__);

                $page = HashBrown\get_current_page();

                if(!$page) { not_found(); }

                if(isset($page->title)) {
                    define('PAGE_TITLE', $page->title);
                } else {
                    define('PAGE_TITLE', '');
                }

                if(isset($page->description)) {
                    define('PAGE_DESCRIPTION', $page->description);
                } else {
                    define('PAGE_DESCRIPTION', '');
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
