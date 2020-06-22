<!DOCTYPE html>
<html>
    <?php require './head.php'; ?>

    <body>
        <?php require './header.php'; ?>

        <h1><?php echo $json['name']; ?></h1>
        <p><?php echo $json['description']; ?></p>

        <?php foreach($json['apiGroups'] as $group => $pages) { ?>
            <h2><?php echo $group; ?></h2>
            <ul>
                <?php foreach($pages as $page) { ?>
                    <li><a href="<?php echo $page['url']; ?>"><?php echo $page['name']; ?></a></li>
                <?php } ?>
            </ul>
        <?php } ?>
    </body>
</html>
