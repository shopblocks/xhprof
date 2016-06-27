<?php
$files = glob(ini_get("xhprof.output_dir") . '/*');

foreach($files as $file) {
    $filename = basename($file);

    if(preg_match('/^([^\.]+)\./', $filename, $match)) {
        unlink($file);
        echo 'Deleted ' . $filename .'<br>';
    }
}

header('Location: ../index.php?history');

