<section class="section section--rich-text">
    <div class="container">
        <?php if(isset($content->text)) { ?>
            <?php echo str_replace('<code>', '<code class="code code--source">', $content->text); ?>
        <?php } ?>
    </div>
</section>
