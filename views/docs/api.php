<!DOCTYPE html>
<html>
    <?php include __DIR__ . '/../inc/head.php'; ?>

    <body>
        <?php include __DIR__ . '/../inc/menu.php'; ?>

        <header class="header">
            <?php include __DIR__ . '/../inc/header.php'; ?>
            <?php include __DIR__ . '/breadcrumbs.php'; ?>

            <div class="widget widget--notice">
                <div class="container">
                    <p class="widget--notice__heading">To authorise an API request, you must get an API token, like this:</p>
                    <pre class="code code--uri">POST { username: myusername, password: mypassword } /api/user/login?persist=true|false</pre>
                </div>
            </div>
        </header>
        
        <main class="container">
            <?php if($page_is_dir) { ?>
                <ul class="widget widget--directory">
                    <?php foreach($page['links'] as $page_link) { ?>
                        <?php

                        $link_text = basename($page_link, '.js');
                        $link_text = str_replace('Controller', '', $link_text);
                        $link_href = path_join(URI, $link_text);

                        ?>
                            
                        <li class="widget--directory__item">
                            <a href="<?php echo $link_href; ?>"><?php echo $link_text; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            
            <?php } else { ?>
                <?php if(sizeof($page['methods']) > 0) { ?>
                    <h2>Endpoints</h2>

                    <?php foreach($page['methods'] as $class_method) { ?>
                        <?php if(!$class_method['example']) { continue; } ?>

                        <section class="section"> 
                            <?php if($class_method['description']) { ?>
                                <h3><?php echo $class_method['description']; ?></h3>
                            <?php } else if($class_method['name']) { ?>
                                <h3><?php echo $class_method['name']; ?></h3>
                            <?php } ?>

                            <?php if($class_method['example']) { ?>
                                <p class="code code--uri"><?php echo $class_method['example']; ?></p>
                            <?php } ?>

                            <?php if(sizeof($class_method['params']) > 0) { ?>
                                <h4>Parameters</h4>

                                <ul class="widget widget--parameter-list">
                                    <?php foreach($class_method['params'] as $i => $class_method_param) { ?>
                                        <li class="widget--parameter-list__item">
                                            <span class="code code--type"><?php echo $class_method_param[1]; ?></span>
                                            <?php echo $class_method_param[2]; ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            
                            <?php if(sizeof($class_method['returns']) > 0) { ?>
                                <h4>Return value</h4>

                                <p>
                                    <span class="code code--type"><?php echo $class_method['returns'][1]; ?></span>
                                    <?php echo $class_method['returns'][2]; ?>
                                </p>
                            <?php } ?>
                        </section>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </main>

        <?php include __DIR__ . '/../inc/footer.php'; ?>
    </body>
</html>
