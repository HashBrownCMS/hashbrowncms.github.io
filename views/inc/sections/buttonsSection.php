<section class="section section--buttons">
    <div class="container">
        <?php if(isset($content->text)) { ?>
            <?php echo $content->text; ?>
        <?php } ?>
        
        <?php if(isset($content->buttons)) { ?>
            <?php foreach($content->buttons as $array_item) { ?>
                <?php $button = $array_item->value; ?>

                <a class="widget widget--button" href="<?php echo $button->href; ?>"><?php echo $button->text; ?></a>
            <?php } ?>
        <?php } ?>
        
        <?php if(isset($content->links)) { ?>
            <?php foreach($content->links as $array_item) { ?>
                <?php $link = $array_item->value; ?>

                <a href="<?php echo $link->href; ?>"><?php echo $link->text; ?></a>
            <?php } ?>
        <?php } ?>
    </div>
</section>
