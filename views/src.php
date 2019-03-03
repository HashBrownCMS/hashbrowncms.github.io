<!DOCTYPE html>
<html>
    <?php include __DIR__ . '/inc/head.php'; ?>

    <body>
        <header class="container header">
            <div class="widget widget--breadcrumbs">
                <?php 
                    $breadcrumb_link = '/';
                    
                    foreach(PATH as $i => $namespace) {
                        if(!$namespace) { continue; }

                        if($breadcrumb_link !== '/') {
                            echo ' / ';
                        }

                        $breadcrumb_link = path_join($breadcrumb_link, $namespace);

                        echo '<a class="widget--breadcrumbs__link" ' . ($i < sizeof(PATH) - 1 ? 'href="' . $breadcrumb_link . '"' : '') . '>' . $namespace . '</a>';
                    }
                ?>
            </div>
        </header>

        <main class="container">
            <?php if(substr(URI, -3) !== '.js') { ?>
                <?php
                    
                $page_url = 'https://github.com/HashBrownCMS/hashbrown-cms/tree/master' . URI;
                $page_contents = file_get_contents($page_url);
                $page_links = [];
                
                preg_match_all('/<a.+class="js-navigation-open" title="([^"]+)"/', $page_contents, $page_links);

                ?>

                <ul>
                    <?php foreach($page_links[1] as $page_link) { ?>
                        <?php if($page_link === 'index.js') { continue; } ?>

                        <li><a href="<?php echo path_join(URI, $page_link); ?>"><?php echo $page_link; ?></a></li>
                    <?php } ?>
                </ul>

            <?php } else { ?>
                <?php 

                $file_url = 'https://raw.githubusercontent.com/HashBrownCMS/hashbrown-cms/master' . URI; 
                $file_contents = file_get_contents($file_url);

                $class_name = [];
                preg_match("/\nclass ([^ ]+)/", $file_contents, $class_name);

                $class_description = [];
                preg_match("/\* ([^@][^\n]+)/", $file_contents, $class_description);

                $class_member_vars = [];
                preg_match_all("/this\.def\(([^,]+), '([^']+)'/", $file_contents, $class_member_vars, PREG_SET_ORDER);

                $class_methods = [];
                preg_match_all("/\/\*\*\n +[^\(]+\([^'\)]*\) {/", $file_contents, $class_methods);

                ?>

                <?php if(sizeof($class_name) > 0) { ?>
                    <h1><?php echo $class_name[1]; ?></h1>
                <?php } ?>
                
                <?php if(sizeof($class_description) > 0) { ?>
                    <h2><?php echo $class_description[1]; ?></h2>
                <?php } ?>

                <?php if(sizeof($class_member_vars) > 0) { ?>
                    <h3>Variables</h3>

                    <ul>
                        <?php foreach($class_member_vars as $class_member_var) { ?>
                            <li><span class="code code--type"><?php echo $class_member_var[1]; ?></span> <?php echo $class_member_var[2]; ?></li>
                        <?php } ?>
                    </ul>    
                <?php } ?>
                
                <?php if(sizeof($class_methods) > 0) { ?>
                    <h3>Functions</h3>

                    <?php foreach($class_methods[0] as $class_method) { ?>
                        <?php
                
                        $class_method_name = [];
                        preg_match("/([a-zA-Z]+)\(/", $class_method, $class_method_name);
                
                        $class_method_description = [];
                        preg_match("/\* ([^@][^\n]+)/", $class_method, $class_method_description);

                        $class_method_is_static = preg_match("/static/", $class_method);
                        $class_method_is_async = preg_match("/async/", $class_method);
                        
                        $class_method_example = [];
                        preg_match("/@example ([^\n]+)/", $class_method, $class_method_example);

                        $class_method_params = [];
                        preg_match_all("/@param.+{([^}]+)} ([^\n]+)/", $class_method, $class_method_params, PREG_SET_ORDER);
                        
                        $class_method_returns = [];
                        preg_match("/@return.+{([^}]+)} ([^\n]+)/", $class_method, $class_method_returns);

                        ?>

                        <section> 
                            <?php if(sizeof($class_method_name) > 0) { ?>
                                <?php if($class_method_name[1] === 'structure') { continue; } ?>
                                
                                <h4> 
                                    <?php echo $class_method_name[1]; ?>

                                    (

                                    <?php if(sizeof($class_method_params) > 0) { ?>
                                        <?php foreach($class_method_params as $i => $class_method_param) { ?>
                                            <span class="code code--type"><?php echo $class_method_param[1]; ?></span>
                                            <?php echo $class_method_param[2]; if($i < sizeof($class_method_params) - 1) { echo ', '; } ?>
                                        <?php } ?>
                                    <?php } ?>

                                    )

                                    <?php if($class_method_is_static) { ?>
                                        <span class="tag">static</span>
                                    <?php } ?>

                                    <?php if($class_method_is_async) { ?>
                                        <span class="tag">async</span>
                                    <?php } ?>
                                </h4>
                            <?php } ?>

                            <?php if(sizeof($class_method_description) > 0) { ?>
                                <h5><?php echo $class_method_description[1]; ?></h5>
                            <?php } ?>
                            
                            <?php if(sizeof($class_method_example) > 0) { ?>
                                <p>Example <span class="code code--uri"><?php echo $class_method_example[1]; ?></span></p>
                            <?php } ?>

                            <?php if(sizeof($class_method_returns) > 0) { ?>
                                <p>
                                    Returns
                                    <span class="code code--type"><?php echo $class_method_returns[1]; ?></span>
                                    <?php echo $class_method_returns[2]; ?>
                                </p>
                            <?php } ?>
                        </section>
                    <?php } ?>
                <?php } ?>

                <h3>Source</h3>

                <pre class="code code--source"><?php echo $file_contents; ?></pre>
            <?php } ?>
        </main>
    </body>
</html>
