<?php 

namespace HashBrown;

$root_dir = '';
$current_page = null;
$content_cache = [];

/**
 * Initialises the driver
 *
 * @param {string} path
 */
function init($path) {
    global $root_dir;
   
    $root_dir = $path;
}

/**
 * Gets the config
 *
 * @return {array} Config
 */
function get_config() {
    global $root_dir;
    global $config;

    if(!file_exists($root_dir . '/config.php')) {
        file_put_contents($root_dir . '/config.php', '<?php $config = [ \'paths\' => [ \'content\' => \'/content\', \'media\' => \'/media\', \'views\' => \'/views\' ] ]; ?>');
    }

    require_once($root_dir . '/config.php');

    return $config;
}

/**
 * Gets all root Content nodes
 *
 * @return {array} Content nodes
 */
function get_root_contents() {
    $all_contents = get_all_contents();
    
    $contents = [];

    foreach($all_contents as $content) {
        if($content->parentId) { continue; }

        array_push($contents, $content);
    }    

    usort($contents, function($a, $b) {
        return $a->sort > $b->sort;
    });

    return $contents;
}

/**
 * Gets all Content nodes
 *
 * @param {string} language
 *
 * @return {array} Content nodes
 */
function get_all_contents($language = null) {
    global $content_cache;
    global $root_dir;

    if($content_cache && sizeof($content_cache) > 0) {
        return $content_cache;
    }

    $config = get_config();
    
    $contents = [];
    
    $dir_path = $root_dir . $config['paths']['content'];

    if($language) { $dir_path .= '/' . $language; }

    foreach(glob($dir_path . '/**/*') as $file_path) {
        $json_string = file_get_contents($file_path);
        $json_decoded = json_decode($json_string); 

        array_push($contents, $json_decoded);
    }

    $content_cache = $contents;

    return $contents;
}

/**
 * Gets child Content nodes
 *
 * @param {string} id
 *
 * @return {array} Children
 */
function get_content_children($id) {
    $children = [];

    $parent = get_content_by_id($id);

    if(!$parent) { return; }

    foreach(get_all_contents() as $child) {
        if(isset($parent->language) && (!isset($child->language) || $child->language !== $parent->language)) { continue; }

        if($child->parentId === $id) {
            array_push($children, $child);
        }
    }

    usort($children, function($a, $b) {
        $a_sort = 0;
        $b_sort = 0;

        if(isset($a->sort)) { $a_sort = $a->sort; }
        if(isset($b->sort)) { $b_sort = $b->sort; }
        
        if($a_sort > $b_sort) { return 1; }
        if($a_sort < $b_sort) { return -1; }

        return 0;
    });

    return $children;
}

/**
 * Gets a Content node by id
 *
 * @param {string} id
 * @param {string} language
 *
 * @return {object} Content
 */
function get_content_by_id($id, $language = 'en') {
    global $root_dir;
   
    $config = get_config();
    
    $dir_path = $root_dir . $config['paths']['content'];

    foreach(glob($dir_path . '/' . $language . '/' . $id . '.*') as $file_path) {
        $json_string = file_get_contents($file_path);
        $json_decoded = json_decode($json_string); 

        return $json_decoded;
    }

    return null;
}

/**
 * Gets a Content node by URL
 *
 * @param {string} url
 *
 * @return {object} Content
 */
function get_content_by_url($url) {
    global $root_dir;

    $url = parse_url($url)['path'];

    if($url[0] !== '/') {
        $url = '/' . $url;
    }

    if($url[strlen($url) - 1] !== '/') {
        $url .= '/';
    }

    foreach(get_all_contents() as $content) {
        if(isset($content->url) && $content->url == $url) {
            return $content;
        }
    }

    return null; 
}

/**
 * Gets the current page
 *
 * @return {object} Page
 */
function get_current_page() {
    global $current_page;
    
    if(!$current_page) {
        $current_page = get_content_by_url($_SERVER['REQUEST_URI']);
    }

    return $current_page;
}

/**
 * Renders a page by the current URL
 *
 * @return {object} Page
 */
function render_current_page() {
    global $root_dir;
    global $current_page;

    $config = get_config();

    $page = get_current_page();

    if(!$page) { return; }

    $view_path = $root_dir . $config['paths']['views'] . '/' . $page->schemaId . '.php';

    if(!file_exists($view_path)) {
        throw new \Exception('View file ' . $view_path . ' could not be found');
    }

    require($view_path);

    return $page;
}

/**
 * Gets a Media MIME type
 * 
 * @return {string} MIME type
 */
function get_media_mime_type($id) {
    global $root_dir;
    
    $config = get_config();
    
    foreach(glob($root_dir . $config['paths']['media'] . '/' . $id . '/*') as $file_path) {
        return mime_content_type($file_path);
    }

    return '';
}

/**
 * Gets a Media URL
 * 
 * @return {string} URL
 */
function get_media_url($id) {
    global $root_dir;
    
    $config = get_config();
    
    foreach(glob($root_dir . $config['paths']['media'] . '/' . $id . '/*') as $file_path) {
        return str_replace($root_dir, '', $file_path);
    }

    return '';
}

/**
 * Renders a Media object
 *
 * @param {string} id
 * @param {array} attributes
 */
function render_media($id, $attributes = []) {
    $file_path = get_media_url($id);

    if(!$file_path) { return; }

    $mime_type = get_media_mime_type($id);
    $content_type = preg_split('#/#', $mime_type)[0];

    $attributes_string = '';

    foreach($attributes as $key => $value) {
        if(!$key || !$value) { continue; } 

        $attributes_string .= ' ' . $key . '="' . $value . '"';
    }

    switch($content_type) {
        case 'video':
            echo '<video src="' . $file_path . '" ' . $attributes_string . '>';
            break;
        
        case 'image':
            echo '<img src="' . $file_path . '" ' . $attributes_string . '>';
            break;
    }
}

/**
 * Renders a view with optional content
 *
 * @param {string} view_path
 * @param {object} content
 */
function render_view($view_path, $content = null) {
    if(!file_exists($view_path)) {
        echo 'View ' . $view_path . ' could not be found.';
        return;
    }

    include($view_path);
}

?>
