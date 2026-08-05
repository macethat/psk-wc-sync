<?php
$_SERVER['SERVER_NAME'] = 'suplementospanama.net';
$_SERVER['HTTP_HOST'] = 'suplementospanama.net';
require_once 'www/suplementospanama.net/public_html/wp-load.php';
if (class_exists('Siteground_Optimizer\Supercacher\Supercacher')) {
    Siteground_Optimizer\Supercacher\Supercacher::purge_cache();
    echo "CACHE_PURGED\n";
}
