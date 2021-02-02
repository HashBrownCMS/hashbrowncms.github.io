<!DOCTYPE html>
<html>
    <?php require './head.php'; ?>

    <body>
        <?php require './header.php'; ?>
        
        <h1><?php echo $json['name']; ?></h1>
        <p><?php echo $json['description']; ?></p>
        
        <?php if(isset($json['notice'])) { ?>
            <aside><?php echo $json['notice']; ?></aside>
        <?php } ?>

        <?php if(isset($json['apiClasses'])) { ?>
            <?php foreach($json['apiClasses'] as $class) { ?>
                <?php if(sizeof($class['methods']) < 1) { continue; } ?>
                
                <section>
                    <h2><?php echo $class['name']; ?></h2>
                    
                    <?php foreach($class['methods'] as $method) { ?>
                        <?php if(!isset($method['example']) || empty($method['example'])) { continue; } ?>

                        <h3><?php echo $method['name']; ?></h3>
                        <p><?php echo $method['description']; ?></p>
                        <pre><?php echo $method['example']; ?></pre>
                    <?php } ?>
                </section>
            <?php } ?>
        <?php } ?>
    </body>
</html>
