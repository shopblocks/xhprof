<?php
require 'config.php';
$files = glob(XHPROF_TRACES . '/*');

foreach($files as $file) {
    $filename = basename($file);

    if(preg_match('/^([^\.]+)\..+xhprof$/', $filename, $match)) {
        unlink($file);
        echo 'Deleted ' . $filename .'<br>';
    }
}

header('Location: ../history.php');

