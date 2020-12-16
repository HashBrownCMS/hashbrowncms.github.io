<!DOCTYPE html>
<html>
    <?php require './head.php'; ?>

    <body>
        <?php require './header.php'; ?>
        
        <h1><?php echo $json['name']; ?></h1>
        <p><?php echo $json['description']; ?></p>

        <ul>
            <?php foreach($json['relatedLink'] as $link) { ?>
            <li><a href="<?php echo $link['url']; ?>"><?php echo $link['name']; ?></a></li>
            <?php } ?>
        <ul>
    </body>
</html>
