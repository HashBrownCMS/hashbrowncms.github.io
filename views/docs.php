<!DOCTYPE html>
<html>
    <?php include __DIR__ . '/inc/head.php'; ?>

    <body>
        <?php include __DIR__ . '/inc/menu.php'; ?>

        <header class="header">
            <div class="container header__container">
                <?php if($page_title) { ?>
                    <h1><?php echo $page_title; ?></h1>
                <?php } ?>
                
                <?php if($page_description) { ?>
                    <p><?php echo $page_description; ?></p>
                <?php } ?>
            </div>
            
            <?php if(sizeof(PATH) > 1) { ?>
                <nav class="widget widget--breadcrumbs">
                    <div class="container">
                        <?php 
                            $breadcrumb_link = '/';
                            
                            foreach(PATH as $i => $namespace) {
                                if(!$namespace) { continue; }

                                if($breadcrumb_link !== '/') {
                                    echo ' / ';
                                }

                                $breadcrumb_link = path_join($breadcrumb_link, $namespace);

                                if($namespace === 'docs') {
                                    $namespace = 'Documentation';
                                }
                                
                                if($namespace === 'Controllers') {
                                    $namespace = 'API';
                                }

                                echo '<a class="widget--breadcrumbs__link" ' . ($i < sizeof(PATH) - 1 ? 'href="' . $breadcrumb_link . '"' : '') . '>' . $namespace . '</a>';
                            }
                        ?>
                    </div>
                </nav>
            <?php } ?>

            <?php if(get_path(2) === 'Controllers') { ?>
                <div class="widget widget--notice">
                    <div class="container">
                        <p class="widget--notice__heading">To authorise an API request, you must get an API token, like this:</p>
                        <pre class="code code--uri">POST { username: myusername, password: mypassword } /api/user/login?persist=true|false</pre>
                    </div>
                </div>
            <?php } ?>
        </header>
        
        <main class="container">
            <?php if($page_is_dir) { ?>
                <ul class="widget widget--directory">
                    <?php foreach($page_links[1] as $page_link) { ?>
                        <?php if(!check_doc_link($page_link)) { continue; } ?>

                        <?php

                        $link_text = basename($page_link, '.js');

                        if($page_link === 'Controllers') {
                            $page_link = 'API';
                        }

                        if($page_is_api) {
                            $link_text = str_replace('Controller', '', $link_text);
                        }

                        ?>
                            
                        <li class="widget--directory__item">
                            <a href="<?php echo path_join(URI, $page_link); ?>"><?php echo $link_text; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            
            <?php } else if($page_is_view) { ?>
                <h2>Source</h2>

                <pre class="code code--source"><?php echo $file_contents; ?></pre>

            <?php } else if($page_is_class) { ?>
                <?php if(sizeof($class_member_vars) > 0) { ?>
                    <h3>Member variables</h3>

                    <ul class="widget widget--parameter-list">
                        <?php foreach($class_member_vars as $class_member_var) { ?>
                            <li class="widget--parameter-list__item"><span class="code code--type"><?php echo $class_member_var[1]; ?></span> <?php echo $class_member_var[2]; ?></li>
                        <?php } ?>
                    </ul>    
                <?php } ?>
                
                <?php if(sizeof($class_methods) > 0) { ?>
                    <h2>Functions</h2>

                    <?php foreach($class_methods[0] as $class_method) { ?>
                        <?php
                
                        $class_method_name = [];
                        preg_match("/([a-zA-Z]+)\(/", $class_method, $class_method_name);
                        $class_method_name = isset($class_method_name[1]) ? $class_method_name[1] : '';

                        if(!$class_method_name || $class_method_name === 'structure') { continue; }

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

                        if(isset($class_method_returns[1]) && $class_method_returns[1] === 'Promise') {
                            $class_method_is_async = true;
                        }

                        ?>

                        <section class="section"> 
                            <h3> 
                                <?php echo $class_method_name; ?>
                                <?php if($class_method_is_static) { ?>
                                    <span class="tag">static</span>
                                <?php } ?>

                                <?php if($class_method_is_async) { ?>
                                    <span class="tag">async</span>
                                <?php } ?>
                            </h3>

                            <?php if(sizeof($class_method_description) > 0) { ?>
                                <p><?php echo $class_method_description[1]; ?></p>
                            <?php } ?>
                            
                            <?php if(sizeof($class_method_example) > 0) { ?>
                                <h4>Example</h4>

                                <p class="code code--uri"><?php echo $class_method_example[1]; ?></p>
                            <?php } ?>

                            <?php if(sizeof($class_method_params) > 0) { ?>
                                <h4>Parameters</h4>

                                <ul class="widget widget--parameter-list">
                                    <?php foreach($class_method_params as $i => $class_method_param) { ?>
                                        <li class="widget--parameter-list__item">
                                            <span class="code code--type"><?php echo $class_method_param[1]; ?></span>
                                            <?php echo $class_method_param[2]; ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            
                            <?php if(sizeof($class_method_returns) > 0) { ?>
                                <h4>Return value</h4>

                                <p>
                                    <span class="code code--type"><?php echo $class_method_returns[1]; ?></span>
                                    <?php echo $class_method_returns[2]; ?>
                                </p>
                            <?php } ?>
                        </section>
                    <?php } ?>
                <?php } ?>

                <h2>Source</h2>

                <pre class="code code--source"><?php echo $file_contents; ?></pre>
            <?php } ?>
        </main>

        <?php include __DIR__ . '/inc/footer.php'; ?>
    </body>
</html>
