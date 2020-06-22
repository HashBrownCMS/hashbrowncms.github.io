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
            
        <?php if(isset($json['mainContentOfPage'])) { ?>
            <?php foreach($json['mainContentOfPage'] as $element) { ?>
                <?php if(!isset($element['@type'])) { continue; } ?>
                <?php if(sizeof($element['methods']) < 1) { continue; } ?>
                
                <?php switch($element['@type']) {
                    case 'ApiClass':
                        require './api-class-embed.php';
                        break;
                } ?>
            <?php } ?>
        <?php } ?>
    </body>
</html>
