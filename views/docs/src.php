<!DOCTYPE html>
<html>
    <?php include __DIR__ . '/../inc/head.php'; ?>

    <body>
        <?php include __DIR__ . '/../inc/menu.php'; ?>

        <header class="header">
            <?php include __DIR__ . '/../inc/header.php'; ?>
            <?php include __DIR__ . '/breadcrumbs.php'; ?>
        </header>
        
        <main class="container">
            <?php if($page_is_dir) { ?>
                <ul class="widget widget--directory">
                    <?php foreach($page['links'] as $link) { ?>
                        <?php

                        $is_file = strpos($link, '.js') !== false;

                        $link_text = basename($link, '.js');
                        $link_href = path_join(URI, $link_text);

                        if(!$is_file) {
                            $link_href .= '/';
                        }

                        ?>
                            
                        <li class="widget--directory__item">
                            <a href="<?php echo $link_href ?>"><?php echo $link_text; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            
            <?php } else { ?>
                <?php if(sizeof($page['member_vars']) > 0) { ?>
                    <h3>Member variables</h3>

                    <ul class="widget widget--parameter-list">
                        <?php foreach($page['member_vars'] as $class_member_var) { ?>
                            <li class="widget--parameter-list__item"><span class="code code--type"><?php echo parse_type($class_member_var[1]); ?></span> <?php echo $class_member_var[2]; ?></li>
                        <?php } ?>
                    </ul>    
                <?php } ?>
                
                <?php if(sizeof($page['methods']) > 0) { ?>
                    <h2>Functions</h2>

                    <?php foreach($page['methods'] as $class_method) { ?>
                        <section class="section"> 
                            <h3> 
                                <?php echo $class_method['name']; ?>
                                <?php if($class_method['is_static']) { ?>
                                    <span class="tag">static</span>
                                <?php } ?>

                                <?php if($class_method['is_async']) { ?>
                                    <span class="tag">async</span>
                                <?php } ?>
                            </h3>

                            <?php if($class_method['description']) { ?>
                                <p><?php echo $class_method['description']; ?></p>
                            <?php } ?>
                            
                            <?php if($class_method['example']) { ?>
                                <h4>Example</h4>

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

                <h2>Source</h2>

                <pre class="code code--source"><?php echo $page['source']; ?></pre>
            <?php } ?>
        </main>

        <?php include __DIR__ . '/../inc/footer.php'; ?>
    </body>
</html>
