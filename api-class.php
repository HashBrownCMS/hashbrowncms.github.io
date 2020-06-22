<!DOCTYPE html>
<html>
    <?php require './head.php'; ?>

    <body>
        <?php require './header.php'; ?>
        
        <h1><?php echo $json['name']; ?></h1>
        <p><?php echo $json['description']; ?></p>

        <?php if(sizeof($json['memberVariables']) > 0) { ?>
            <h2>Member variables</h2>
            
            <table>
                <?php foreach($json['memberVariables'] as $variable) { ?>
                    <tr><td><?php echo $variable[2]; ?></td><td><code><?php echo $variable[1]; ?></code></td></tr>
                <?php } ?>
            </table>
            
            <hr>
        <?php } ?>

        <?php foreach($json['methods'] as $method) { ?>
            <h2><?php echo $method['name']; ?></h2>

            <?php if($method['isStatic'] || $method['isAsync']) { ?>
                <h6>
                    <?php if($method['isStatic']) { ?>async<?php } ?>
                    <?php if($method['isAsync']) { ?>static<?php } ?>
                </h6>
            <?php } ?>

            <p><?php echo $method['description']; ?></p>

            <?php if(sizeof($method['params']) > 0) { ?>
                <h3>Parameters</h3>
                
                <table>
                    <?php foreach($method['params'] as $param) { ?>
                        <tr><td><?php echo $param[2]; ?></td><td><code><?php echo $param[1]; ?></code></td></tr>
                    <?php } ?>
                </table>
            <?php } ?>

            <?php if(isset($method['returns'][1])) { ?>
                <h3>Returns</h3>
                <code><?php echo $method['returns'][1]; ?></code>
            <?php } ?>

            <hr>
        <?php } ?>

        <h2>Source</h2>
        <pre><code><?php echo $json['source']; ?></code></pre>
    </body>
</html>
