<section>
    <h2><?php echo $element['name']; ?></h2>
    <p><?php echo $element['description']; ?></p>
    
    <?php foreach($element['methods'] as $method) { ?>
        <?php if(!isset($method['example']) || empty($method['example'])) { continue; } ?>

        <pre><?php echo $method['example']; ?></pre>
        <p><?php echo $method['description']; ?></p>
    <?php } ?>
</section>
