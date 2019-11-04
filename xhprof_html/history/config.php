<?php
if (!defined('XHPROF_TRACES')) {
    define('XHPROF_TRACES', '/var/www/sites/current/admin.myshopblocks.com/traces'); // location of xhprof trace files
}

$xhprof_base_url = '/xhprof/xhprof_html'; // URL of xhprof_html directory without trailing slash
$xhprof_webroot = dirname(__DIR__);

$history_uri = $xhprof_base_url . '/history';
$history_webroot_path = $xhprof_webroot . '/history';
$history_default_file_limit = 99;
$history_default_xaxis_type = 'string';

