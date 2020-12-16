<!DOCTYPE html>
<html>
    <?php require './head.php'; ?>

    <body>
        <?php require './header.php'; ?>
        
        <h1><?php echo $json['name']; ?></h1>
        <h4><?php echo $json['description']; ?></h4>

        <?php if(isset($json['text'])) { ?>
            <?php echo $json['text']; ?>
        <?php } ?>
    </body>
</html>
