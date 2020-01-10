<?php

// We're assuming that this file (example.php) is in the root of the project,
// and the HashBrown PHP driver is in a /lib/hashbrown-php-driver folder.
require_once(__DIR__ . '/lib/hashbrown-php-driver/index.php');

// Initialise the driver with the root folder
HashBrown\init(__DIR__);

// Render the current page
$page = HashBrown\render_current_page();

// If no page was found, return 404
if(!$page) {
    http_response_code(404);
    die('Not found');
}

?>
