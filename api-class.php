<!DOCTYPE html>
<html>
    <?php require './head.php'; ?>

    <body>
        <?php require './header.php'; ?>
        
        <h1><?php echo $json['name']; ?></h1>
        <p><?php echo $json['description']; ?></p>

        <?php if(!empty($json['extends'])) { ?>
            <p>
                Extends
                <a href="/docs/src/<?php echo str_replace('.', '/', strtolower(str_replace('HashBrown.', '', $json['extends']))) ?>">
                    <?php echo str_replace('HashBrown.', '', $json['extends']); ?>
                </a>
            </p>
        <?php } ?>

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
            <h2><?php echo $method['name'] ?></h2>
            <p><?php echo $method['description']; ?></p>

            <code>
                <?php 
                    if($method['isStatic']) {
                        echo '<var>static</var> ';
                    }
                    
                    if($method['isAsync']) {
                        echo '<var>async</var> ';
                    }
            
                    echo $method['name'] . ' ( ';

                    $params = [];

                    foreach($method['params'] as $param) {
                        $params[] = $param[2] . ' : <var>'. $param[1] . '</var>';
                    }

                    echo implode(', ', $params);
                    echo ' )';

                    if(isset($method['returns'][1])) {
                        echo ' » <var>' . $method['returns'][1] . '</var>';
                    }
                ?>
            </code>
        <?php } ?>

        <h2>Source</h2>
        <pre><code><?php echo $json['source']; ?></code></pre>
    </body>
</html>
