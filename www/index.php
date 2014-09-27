<?php
define('WWW_DIR', __DIR__);
// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

$container = require __DIR__ . '/../app/bootstrap.php';

$container->getService('application')->run();
