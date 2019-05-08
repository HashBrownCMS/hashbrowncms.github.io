<?php

require_once('./functions.php');

$html = get_output_cache();

if(!$html) {
    ob_start();
    
    switch(get_path(0)) {
        case 'docs':
            switch(get_path(1)) {
                case 'guides':
                    $file_url = WIKI_PAGE_ROOT_URL . str_replace('docs/guides/', '', URI);

                    $GLOBALS['page'] = parse_page($file_url);

                    $title = query_selector_all('//h1')[1]->nodeValue;
                    $description = '';

                    if($title === 'Home') {
                        $title = 'Guides';
                        $description = 'Learn how to get along with HashBrown';
                    }

                    define('PAGE_TITLE', $title);
                    define('PAGE_DESCRIPTION', $description);
                    
                    include __DIR__ . '/views/docs/guide.php';
                    break;

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

?>
