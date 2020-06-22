<?php

if(!isset($group)) {
    $group = $json['url'];
}

?>

<ul>
    <?php foreach($json['relatedContent'] as $page) { ?>
        <li><a href="<?php echo $page['url']; ?>"><?php echo $page['name']; ?></a></li>
    <?php } ?>
</ul>
