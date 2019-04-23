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
            <ul class="widget widget--directory">
                <?php foreach($page['links'] as $link) { ?>
                    <?php

                    $link_text = str_replace('-', ' ', $link[1]);
                    $link_href = '/guides/' . $link[1];

                    ?>
                        
                    <li class="widget--directory__item">
                        <a href="<?php echo $link_href ?>"><?php echo $link_text; ?></a>
                    </li>
                <?php } ?>
            </ul>
            
            <?php echo $page['body']; ?> 
        </main>

        <?php include __DIR__ . '/../inc/footer.php'; ?>
    </body>
</html>
