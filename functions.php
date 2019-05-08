<?php 



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('URI', $_SERVER['REQUEST_URI']);
define('PATH', array_values(array_filter(explode('/', URI), function($value) { return $value !== ''; })));
define('ROOT_DIR', __DIR__);
define('WIKI_PAGE_ROOT_URL', 'https://github.com/HashBrownCMS/hashbrown-cms/wiki');
define('SRC_CLASS_ROOT_URL', 'https://raw.githubusercontent.com/HashBrownCMS/hashbrown-cms/stable');
define('SRC_DIR_ROOT_URL', 'https://github.com/HashBrownCMS/hashbrown-cms/tree/stable');

/**
 * Gets the HTML output cache for the current page, if applicable
 *
 * @return string
 */
function get_output_cache() {
    if(!file_exists(ROOT_DIR . '/cache')) { return null; }

    $key = base64_encode(URI);

    if(!file_exists(ROOT_DIR . '/cache/' . $key)) { return null; }

    $json = file_get_contents(ROOT_DIR . '/cache/' . $key);
    $json = json_decode($json);

    if($json->expires < time()) { return null; }

    return $json->html;
}

/**
 * Sets the HTML output cache based on the current URI
 *
 * @param string html
 */
function set_output_cache($html) {
    if(!file_exists(ROOT_DIR . '/cache')) { return; }
    
    $key = base64_encode(URI);

    $json = json_encode([ 'expires' => time() + 60, 'html' => $html ]);

    file_put_contents(ROOT_DIR . '/cache/' . $key, $json);
}

/**
 * Gets a part of a URI
 *
 * @param int start
 * @param int levels
 *
 * @return string
 */
function get_path($start, $levels = 1) {
    $path = [];

    for($level = $start; $level < $start + $levels; $level++) {
        if(!isset(PATH[$level]) || !PATH[$level]) { continue; }

        array_push($path, PATH[$level]);
    }

    return implode('/', $path);
}

/**
 * Gets the inner HTML content of a DOMNode
 *
 * @param {DOMNode} $node
 * @param {bool} keep_attributes
 *
 * @return {string} HTML
 */
function get_inner_html($node, $keep_attributes = false) {
    $innerHTML = ""; 
    $children  = $node->childNodes;

    foreach ($children as $child) 
    {
        $html = $node->ownerDocument->saveHTML($child);

        if(!$keep_attributes) {
            $html = preg_replace("/class=\".*?\"/", "", $html);
            $html = preg_replace("/id=\".*?\"/", "", $html);
            $html = preg_replace("/<svg.*?\/svg>/", "", $html);
            $html = preg_replace("/<pre/", "<pre class=\"code code--source\"", $html);
        }

        $innerHTML .= $html;
    }

    return $innerHTML; 
}

/**
 * A wrapper around XPath queries for single results
 *
 * @param {string} query
 *
 * @return {mixed} Result
 */
function query_selector($query, $node = null) {
    $xpath = new DOMXPath($GLOBALS['page']);

    $results = $xpath->query($query, $node);

    if(!$results) { return null; }

    foreach($results as $result) {
        if(isset($result->value)) { return $result->value; }
    }

    return $results[0];
}

/**
 * A wrapper around XPath queries for multiple results
 *
 * @param {string} query
 *
 * @return {mixed} Result
 */
function query_selector_all($query) {
    $xpath = new DOMXPath($GLOBALS['page']);

    $results = $xpath->query($query);

    if(!$results) { return []; }

    return $results;
}


/**
 * Joins any number of string arugments into a path
 *
 * @param string(s) args
 *
 * @return string
 */
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

/**
 * Gets a class type without the "HashBrown." prefix
 *
 * @param string string
 *
 * @return string
 */
function parse_type($string) {
    if(strpos($string, 'HashBrown.') === false) { return $string; }

    $parts = explode('.', $string);

    return $parts[sizeof($parts) - 1];
}

/**
 * Parses a directory page on GitHub
 *
 * @param string url
 *
 * @return array
 */
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

/**
 * Parses a page by URL
 *
 * @param string url
 *
 * @return array
 */
function parse_page($url) {
    $output = [];

    $file_contents = @file_get_contents($url);
    
    if(!$file_contents) { http_response_code(404); die('Not found'); }

    $dom = new DOMDocument();
    @$dom->loadHTML($file_contents);

    return $dom;
}

/**
 * Parses a source file with JSDoc markup
 *
 * @param string url
 *
 * @return array
 */
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

/**
 * Returns a 404 response
 */
function not_found() {
    http_response_code(404);
    die('Not found');
}

?>
