<section class="section section--feature-group">
    <div class="container text-center">
        <?php if(isset($content->heading)) { ?>
            <h2><?php echo $content->heading; ?></h2>
        <?php } ?>
        
        <?php if(isset($content->subheading)) { ?>
            <p><?php echo $content->subheading; ?></p>
        <?php } ?>

        <?php if(isset($content->features)) { ?>
            <div class="grid">
                <?php foreach($content->features as $array_item) { ?>
                    <?php $feature = $array_item->value; ?>

                    <div class="grid__item">
                        <?php if(isset($feature->icon)) { ?>
                            <i class="grid__item__background fa <?php echo $feature->icon; ?>"></i>
                        <?php } ?>

                        <?php if(isset($feature->heading)) { ?>
                            <h3 class="grid__item__heading"><?php echo $feature->heading; ?></h3>
                        <?php } ?>
                        
                        <?php if(isset($feature->body)) { ?>
                            <div class="text-left">
                                <?php echo $feature->body; ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>
