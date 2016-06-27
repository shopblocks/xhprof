<?php
/**
 * XHProf GUI History Report
 * Shows multiple runs side by side for comparison and trends
 * Author: Kevin Jones <kevin@shopblocks.com>
 */

// config
$xhprof_base_url = 'http://localhost/xhprof/xhprof_html'; // URL of xhprof_html directory without trailing slash
$xhprof_webroot = dirname(__DIR__);
$history_webroot_path = $xhprof_webroot . '/history';
$default_file_limit = 6;

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
    $file_limit = isset($_GET['limit']) ? $_GET['limit'] : $default_file_limit;
    
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
                $headers[] = $file_modified . '<br><a href="' . $xhprof_base_url . '/index.php?source=' . $file['source'] . '&run=' . $run_id . '">'.$run_id.'</a>' . PHP_EOL;
                $total_time = $symbol_tabs[$run_id]['main()']['wt'] / 1000000;
                //pa($symbol_tabs[$run_id]);
                $total_function_calls = 0;
                foreach($symbol_tabs[$run_id] as $stats) {
                    $total_function_calls += $stats['ct'];
                }

                $data[] = [
                    'date' => $file_modified,
                    'datetime' => $file['mtime'],
                    'time' => round($total_time, 2),
                    'calls' => $total_function_calls,
                ];
            }
        }
    }
} else {
    $sources['empty'] = 'No files found';
}

?>
<style>
html {font-family: verdana, arial, sans-serif}
body {padding: 15px}
div, a, label, input, select, form {margin:0; padding:0}
#chart {margin-bottom:30px}
th {background:#eee}
th,td {padding:3px;font-size:11px;font-weight:normal;border:#ccc 1px solid}
.right {text-align:right}
#menu {background: #eee; margin-bottom: 15px; padding: 0 15px}
.col {display:inline-block; margin-right: 15px}
.info {padding:5px 15px;background:#eef}
</style>

<h1>XHProf: Page Load History</h1>
<p>View all page loads for one specific namespace over time</p>

<div id="menu">
    <div class="col">
        <form method="get">
            <label>Namespace:</label>
            <select id="in_source" name="source" onchange="window.location='<?=$xhprof_base_url?>/index.php?history&source=' + this.value;">
                <?php foreach($sources as $source_slug => $source_label): ?>
                    <option value="<?=$source_slug?>"<?=($source_slug == $source ? ' selected' : '')?>><?=$source_label?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="col">
        <p><a href="<?=$xhprof_base_url?>/history/clear.php" onclick="return confirm('Are you sure?');">Clear all XHProf reports</a></p>
    </div>
</div>


<?php if(!empty($data)): ?>
    <div id="chart"></div>

    <table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <th></th>
        <?php foreach($headers as $header): ?>
        <th><?= $header ?></th>
        <?php endforeach; ?>
    </tr>
    <tr>
        <th>Load Time (s)</th>
<?php
$i = 0;
foreach($data as $row) {
    echo '<td class="right">' . $row['time'] . '</td>';
    $chart_data[] = '[new Date('.date('Y, m, d, h, i, s', $row['datetime']).'), '.$row['time'].', '.$row['calls'].']';
    $i++;
}
?>
    </tr>
    <tr>
        <th>Function calls</th>
        <?php foreach($data as $row): ?>
            <td class="right"><?=$row['calls']?></td>
        <?php endforeach; ?>
    </tr>
    </table>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(lineChart);

function lineChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('datetime', 'Page Load Datetime');
    data.addColumn('number', 'Load Time (s)');
    data.addColumn('number', 'Total Function calls');

    data.addRows([<?= implode(',', $chart_data); ?>]);

    var options = {
    pointSize: 5,
        hAxis: {
        title: 'Page load date'
    },
        vAxes: {0: {title: "Load Time (s)", logScale: false},
        1: {title: "Function Calls", logScale: false, maxValue: 2}},
        series:{
        0:{targetAxisIndex:0},
            1:{targetAxisIndex:1},
            2:{targetAxisIndex:1}},
            backgroundColor: '#f1f8e9'
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart'));
    chart.draw(data, options);
}
</script>
<?php elseif(!empty($sources)): ?>
    <div class="info">
        <p>Please select a namespace to view a history report.</p>
    </div>
<?php else: ?>
    <div class="info">
        <p>No XHProf files were found in your output directory [<?=ini_get("xhprof.output_dir")?>]</p>
        <p>Enable XHProf profiling and ensure output files are being saved before trying again.</p>
    </div>
<?php endif; ?>

