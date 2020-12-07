<?php 

require_once './vendor/autoload.php';

/**
 * Recurses infinitely into a directory
 */
function recurse_directory(string $path) {
    try {
        return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    } catch(\Exception $e) {
        return [];
    }
}

/**
 * Parses a source file with JSDoc markup
 */
function parse_source_file(string $file_contents, ?string $name = '', bool $require_examples = false): ?array {
    if(!$file_contents) { return null; }

    if($name) {
        $output['name'] = $name;
    }

    if(
        $output['name'] === 'index' ||
        $output['name'] === 'ApiController' ||
        $output['name'] === 'ResourceController' ||
        $output['name'] === 'ViewController' ||
        $output['name'] === 'ControllerBase'
    ) { return null; }

    $output['name'] = str_replace('Controller', '', $output['name']);

    // Description
    $class_description = [];
    preg_match("/\* ([^@][^\n]+)/", $file_contents, $class_description);
    $output['description'] = isset($class_description[1]) ? $class_description[1] : '';
    
    // Member of
    $output['memberOf'] = [];
    preg_match("/@memberof ([^\n]+)/", $file_contents, $output['memberOf']);

    $output['memberOf'] = isset($output['memberOf'][1]) ? $output['memberOf'][1] : '';
    $output['memberOf'] = str_replace('{', '', $output['memberOf']);
    $output['memberOf'] = str_replace('}', '', $output['memberOf']);

    // Remove the class meta docs to prevent confusion
    $file_contents = preg_replace("/\/\*\*[^\/]+\//", '', $file_contents, 1);

    // Member variables
    $output['memberVariables'] = [];
    preg_match_all("/this\.def\(([^,]+), '([^']+)'/", $file_contents, $output['memberVariables'], PREG_SET_ORDER);

    // Methods
    $output['methods'] = [];
    preg_match_all("/\/\*\*\n +[^\(]+\([^'\)]*\) {/", $file_contents, $output['methods']);
    if(sizeof($output['methods']) > 0) {
        $output['methods'] = $output['methods'][0];
    }

    $examples = 0;

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

            $method['isStatic'] = preg_match("/static/", $method_string);
            $method['isAsync'] = preg_match("/async/", $method_string);
            
            $method['example'] = [];
            preg_match("/@example ([^\n]+)/", $method_string, $method['example']);
            $method['example'] = isset($method['example'][1]) ? $method['example'][1] : '';

            $method['params'] = [];
            preg_match_all("/@param.+{([^}]+)} ([^\n]+)/", $method_string, $method['params'], PREG_SET_ORDER);
            
            $method['returns'] = [];
            preg_match("/@return.+{([^}]+)} ([^\n]+)/", $method_string, $method['returns']);

            if(isset($method['returns'][1]) && $method['returns'][1] === 'Promise') {
                $method['isAsync'] = true;
            }

            $output['methods'][$i] = $method;

            if(!empty($method['example'])) {
                $examples++;
            }
        }
    }

    if($require_examples && $examples < 1) { return null; }

    // Source
    $output['source'] = $file_contents;

    return $output;
}

/**
 * Builds the API documentation page
 */
function build_api_docs(array &$pages) {
    $pages['/docs/api'] = [
        '@context' => 'http://schema.org',
        '@type' => 'ApiSummary',
        'name' => 'API docs',
        'description' => 'The documentation for website developers',
        'url' => '/docs/api',
        'notice' => 'To authorise an API request, you must first get a token: <pre>POST /api/user/login?persist=true|false { username: XXX, password: XXX }</pre>Then use the returned token in every subsequent request: <pre>POST|GET /api/method?token=XXX</pre>',
        'apiClasses' => [],
    ];
    
    foreach(recurse_directory(__DIR__ . '/repo/src/Server/Controller') as $file) {
        $data = parse_source_file(@file_get_contents($file), pathinfo($file, PATHINFO_FILENAME), true);

        if(empty($data)) { continue; }

        $pages['/docs/api']['apiClasses'][] = $data;
    }
}

/**
 * Build the source documentation pages
 */
function build_src_docs(array &$pages) {
    $pages['/docs/src'] = [
        '@context' => 'http://schema.org',
        '@type' => 'ApiOverview',
        'name' => 'Source docs',
        'description' => 'The documentation for HashBrown developers',
        'url' => '/docs/src',
        'apiGroups' => [],
    ];

    foreach(recurse_directory(__DIR__ . '/repo') as $file) {
        if(basename($file) === 'index.js'|| strpos($file, 'Controller') !== false) { continue; }

        // Get extension
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if($extension !== 'js') { continue; }

        // Get file data
        $data = @file_get_contents($file);

        if(empty($data)) { continue; }

        // Init JSON
        $json = parse_source_file($data, pathinfo($file, PATHINFO_FILENAME));

        $json['@context'] = 'http://schema.org';
        $json['@type'] = 'ApiClass';
        $json['url'] = strtolower('/docs/src' . str_replace(__DIR__ . '/repo/src', '', dirname($file)) . '/' . pathinfo($file, PATHINFO_FILENAME));

        if(empty($json['memberOf'])) { continue; }

        if(!isset($pages['/docs/src']['apiGroups'][$json['memberOf']])) {
            $pages['/docs/src']['apiGroups'][$json['memberOf']] = [];
        }

        $pages['/docs/src']['apiGroups'][$json['memberOf']][] = $json;

        $pages[$json['url']] = $json;
    }
    
    ksort($pages['/docs/src']['apiGroups']);
}

/**
 * Build the markdown pages
 */
function build_markdown_pages(array &$pages) {
    $converter = new League\CommonMark\CommonMarkConverter();

    foreach(recurse_directory(__DIR__ . '/repo') as $file) {
        // Get extension
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if(!empty($extension) && $extension !== 'md') { continue; }

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
            
        // Documentation
        } else {
            $json['name'] = pathinfo($file, PATHINFO_FILENAME);
            $json['description'] = '';
            $json['url'] = strtolower('/' . $json['name']);

        }
        
        $json['text'] = $converter->convertToHtml($data);

        $pages[$json['url']] = $json;
    }
}
