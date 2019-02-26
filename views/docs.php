<?php
    $file_path = path_join(ROOT_DIR, 'docs', 'json');

    foreach(PATH as $namespace) {
        if($namespace === 'docs' || !$namespace) { continue; }
    
        $file_path = path_join($file_path, $namespace);
    }
?>
<!DOCTYPE html>
<html>
    <?php include __DIR__ . '/inc/head.php'; ?>

    <body>
        <header class="container header">
            <div class="widget widget--breadcrumbs">
                <?php 
                    $breadcrumb_link = '/docs/';
                    
                    echo '<a class="widget--breadcrumbs__link" href="' . $breadcrumb_link . '">docs</a>';

                    foreach(PATH as $i => $namespace) {
                        if($namespace === 'docs' || !$namespace) { continue; }

                        $breadcrumb_link .= $namespace . '/';

                        echo ' / <a class="widget--breadcrumbs__link" ' . ($i < sizeof(PATH) - 1 ? 'href="' . $breadcrumb_link . '"' : '') . '>' . $namespace . '</a>';
                    }
                ?>
            </div>
        </header>

        <main class="container">
            <?php if(get_path(2) === 'Controllers') { ?>
                <div class="widget widget--notice">
                    <p class="widget--notice__heading">To authorise an API request, you must get an API token, like this:</p>
                    <pre class="widget widget--code">POST { username: myusername, password: mypassword } /api/user/login?persist=true|false</pre>
                </div>
            <?php } ?>

            <?php if(file_exists($file_path . '.json')) { ?>
                <?php
                    $entries = file_get_contents($file_path . '.json');
                    $entries = json_decode($entries);
                ?>

                <?php foreach($entries as $entry) { ?>
                    <?php if($entry->kind !== 'class') { continue; } ?>
            
                    <div class="class">            
                        <h1 class="class__name"><?php echo $entry->name; ?></h1>

                        <?php if(isset($entry->classdesc)) { ?>
                            <h2 class="class__description"><?php echo $entry->classdesc; ?></h2>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php foreach($entries as $entry) { ?>
                    <?php
                        if(isset($entry->undocumented) || $entry->kind !== 'function') { continue; }

                        if(isset($entry->returns) && $entry->returns[0]->type->names[0] === 'Promise') {
                            $entry->async = true;
                        }
                    ?>

                    <div class="function">
                        <h3 class="function__name">
                            <?php echo $entry->name; ?>
                        </h3>

                        <ul class="function__meta">                        
                            <?php if($entry->scope === 'static') { ?>
                                <li class="function__meta__name">static</li>
                            <?php } ?>
                            
                            <?php if(isset($entry->async) && $entry->async) { ?>
                                <li class="function__meta__name">async</li>
                            <?php } ?>
                        </ul>

                        <?php if(isset($entry->description)) { ?>
                            <h4 class="function__description"><?php echo $entry->description; ?></h4>
                        <?php } ?>

                        <?php if(sizeof($entry->params) > 0) { ?>
                            <h5>Parameters</h5>

                            <ul class="function__params">
                                <?php foreach($entry->params as $param) { ?>
                                    <li class="function__param">
                                        <span class="function__param__type"><?php echo $param->type->names[0]; ?></span>
                                        <span class="function__param__name"><?php echo $param->name; ?></span>
        
                                        <?php if(isset($param->description)) { ?>
                                            <span class="function__param__description"><?php echo $param->description; ?></span>
                                        <?php } ?>                
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                        
                        <?php if(isset($entry->returns)) { ?>
                            <h5>Returns</h5>

                            <p>
                                <span class="function__returns"><?php echo $entry->returns[0]->type->names[0]; ?></span>
                                <?php echo $entry->returns[0]->description; ?>
                            </p>
                        <?php } ?>
                        
                        <?php if(isset($entry->examples)) { ?>
                            <?php foreach($entry->examples as $example) { ?>
                                <p class="function__example">Example <span class="function__example__code"><?php echo $example; ?></span></p>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>

            <?php } else if(file_exists($file_path)) { ?>
                <ul class="widget widget--list">       
                    <?php foreach(scandir($file_path) as $class) { ?>
                        <?php if($class === '.' || $class === '..') { continue; } ?>

                        <?php $class = basename($class, '.json'); ?>

                        <li class="widget--list__item">
                            <a href="<?php echo path_join(URI, $class); ?>"><?php echo $class; ?></a>
                        </li>
                    <?php } ?>
                </ul>

            <?php } else { ?>
                Not found

            <?php } ?>
        </main>
    </body>
</html>
