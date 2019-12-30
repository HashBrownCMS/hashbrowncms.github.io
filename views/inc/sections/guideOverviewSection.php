<section class="section section--guide-overview">
    <div class="container">
        <ul class="widget widget--directory">
            <?php foreach(HashBrown\get_content_children(HashBrown\get_current_page()->id) as $child) { ?>
                <li class="widget--directory__item"><a href="<?php echo $child->url; ?>"><?php echo $child->title; ?></a></li>
            <?php } ?>
        </ul>
    </div>
</section>
