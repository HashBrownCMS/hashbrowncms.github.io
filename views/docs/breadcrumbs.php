<?php if(sizeof(PATH) > 1) { ?>
    <nav class="widget widget--breadcrumbs">
        <div class="container">
            <?php 
                $breadcrumb_link = '/docs/';
                
                foreach(PATH as $i => $namespace) {
                    if(!$namespace || $namespace === 'docs') { continue; }

                    if($breadcrumb_link !== '/docs/') {
                        echo ' / ';
                    }

                    $breadcrumb_link = path_join($breadcrumb_link, $namespace . '/');

                    $namespace = str_replace('-', ' ', $namespace);

                    echo '<a class="widget--breadcrumbs__link" ' . ($i < sizeof(PATH) - 1 ? 'href="' . $breadcrumb_link . '"' : '') . '>' . $namespace . '</a>';
                }
            ?>
        </div>
    </nav>
<?php } ?>
