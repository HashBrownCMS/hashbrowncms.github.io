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
            <?php if(sizeof(PATH) < 3) { ?>
                <ul class="widget widget--directory">
                    <?php foreach(query_selector_all('//div[@id="wiki-pages-box"]//ul/li') as $li) { ?>
                        <li class="widget widget--directory__item">
                            <a href="<?php echo str_replace('HashBrownCMS/hashbrown-cms/wiki', 'docs/guides', query_selector('strong/a/@href', $li)); ?>"><?php echo $li->nodeValue; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            
            <?php } else { ?>
                <?php echo get_inner_html(query_selector('//div[@id="wiki-body"]/div[@class="markdown-body"]')); ?>
            
            <?php } ?>
        </main>

        <?php include __DIR__ . '/../inc/footer.php'; ?>
    </body>
</html>
