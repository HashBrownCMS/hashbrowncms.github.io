<!DOCTYPE html>
<html>
    <?php include __DIR__ . '/inc/head.php'; ?>
    
    <body>
        <?php include __DIR__ . '/inc/menu.php'; ?>

        <main class="container">
            <?php if(isset($page->sections)) { ?>
                <?php foreach($page->sections as $array_item) { ?>
                    <?php HashBrown\render_view(__DIR__ . '/inc/sections/' . $array_item->schemaId . '.php', $array_item->value); ?>
                <?php } ?>
            <?php } ?>
        </main>

        <?php include __DIR__ . '/inc/footer.php'; ?>
    </body>
</html>
