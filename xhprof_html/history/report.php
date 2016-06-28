<?php
/**
 * XHProf GUI History Report
 * Shows multiple runs side by side for comparison and trends
 * Author: Kevin Jones <kevin@shopblocks.com>
 */

require 'config.php';

// debug function
function pa($a) {
    echo '<pre>'.print_r($a,1).PHP_EOL.'</pre>';
}

if(!is_dir($xhprof_webroot)) {
    throw new \Exception('XHProf directory should be set in history/report.php config');
}

// get recent xhprof reports and order by modified time
$files = glob(ini_get("xhprof.output_dir") . '/*');

if(!empty($files)) {
    // order files by modified time descending so we always get the newest reports first
    usort($files, function($a, $b) {
        return filemtime($a) < filemtime($b);
    });

    // limit how many runs will be analysed
    $file_limit = isset($_GET['limit']) ? $_GET['limit'] : $history_default_file_limit;
    
    $count = 0;

    foreach($files as $file) {
        $filename = basename($file);

        if(preg_match('/^([^\.]+)\./', $filename, $match)) {
            // record namespace for the menu used to switch page
            $source_slug = str_replace([$match[1] . '.', '.xhprof'], '', $filename);
            $sources[$source_slug] = str_replace('_', '/', $source_slug);

            if(preg_match('/'.preg_quote($source).'/', $filename)) {
                $file_run_ids[$match[1]] = [
                    'mtime' => filemtime($file),
                    'name' => $filename,
                    'source' => $source_slug,
                ];
            }
        }

        $count++;

        if($count >= $file_limit) {
            break;
        }
    }

    if(!empty($sources)) {
        $sources['_all'] = 'Please select';
        ksort($sources);

        // if only one source is available, redirect the user to it
        // see javascript redirect in view
        if(count($sources) == 2) {
            end($sources);
            $firstSource = key($sources);
            reset($sources);
        }
    }

    if(!empty($file_run_ids) && !empty($_GET['source'])) {
        // reverse order so that the last run is on the right in our table/chart
        $file_run_ids = array_reverse($file_run_ids, true);

        // build data
        foreach($file_run_ids as $run_id => $file) {
            $reports[$run_id] = $xhprof_runs_impl->get_run($run_id, $file['source'], $description);
            $symbol_tabs[$run_id] = xhprof_compute_flat_info($reports[$run_id], $stats);
            if(isset($symbol_tabs[$run_id]['main()'])) {
                $file_modified = date('Y-m-d H:i:s', $file['mtime']);
                $total_time = $symbol_tabs[$run_id]['main()']['wt'] / 1000000;
                $total_function_calls = 0;

                foreach($symbol_tabs[$run_id] as $stats) {
                    $total_function_calls += $stats['ct'];
                }

                $data[] = [
                    'run_id' => $run_id,
                    'source' => $file['source'],
                    'date' => $file_modified,
                    'datetime' => $file['mtime'],
                    'time' => round($total_time, 2),
                    'calls' => $total_function_calls,
                ];

                $google_chart_x_axis_type = 'datetime';
                $google_chart_x_axis_type = 'string';
                switch($google_chart_x_axis_type) {
                case 'datetime':
                    $xdata = 'new Date('.date('Y, m, d, H, i, s', $file['mtime']).')';
                case 'string':
                    $xdata = '"' . substr($run_id, -4) . '"';
                }
                $google_chart_rows[] = '[' . $xdata . ', '.round($total_time, 2).', '.$total_function_calls.']';
            }
        }
    }
} else {
    $sources['empty'] = 'No files found';
}

require 'history-view.html.php';

