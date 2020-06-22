<?php 

/**
 * Recurses infinitely into a directory
 */
function recurse_directory(string $path): RecursiveIteratorIterator {
    return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
}

/**
 * Parses a source file with JSDoc markup
 *
 * @param string url
 *
 * @return array
 */
function parse_source_file(string $file_contents, ?string $name = '') {
    if(!$file_contents) { return null; }

    $output['@type'] = 'ApiClass';

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
