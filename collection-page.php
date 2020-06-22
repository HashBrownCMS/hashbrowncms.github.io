<!DOCTYPE html>
<html>
    <?php require './head.php'; ?>

    <body>
        <?php require './header.php'; ?>

        <h1><?php echo $json['name']; ?></h1>
        <p><?php echo $json['description']; ?></p>

        <?php if(isset($json['text'])) { ?>
            <?php echo $json['text']; ?>
        <?php } ?>

        <?php require './nested-list.php'; ?>
    </body>
</html>
