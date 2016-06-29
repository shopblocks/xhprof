<link rel="stylesheet" href="<?=$history_uri?>/history-style.css">

<h1>XHProf: Page Load History</h1>
<p>View all page loads for one specific namespace over time</p>

<div id="menu">
    <div class="col">
        <form method="get">
            <label>Namespace:</label>
            <select id="in_source" name="source" onchange="window.location='<?=$xhprof_base_url?>/history.php?source=' + this.value;">
                <?php foreach($sources as $source_slug => $source_label): ?>
                    <option value="<?=$source_slug?>"<?=($source_slug == $source ? ' selected' : '')?>><?=$source_label?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="col">
        <p><a href="<?=$history_uri?>/clear.php" onclick="return confirm('Are you sure?');">Clear all XHProf reports</a></p>
    </div>
</div>

<?php if(!empty($data)): ?>
    <div id="chart"></div>
    <div id="chart_options">
        X-Axis:
        <a href="<?=$xhprof_base_url?>/history.php?source=<?=$source?>&xaxis=datetime">Date</a>
        <a href="<?=$xhprof_base_url?>/history.php?source=<?=$source?>&xaxis=string">run_id</a>
    </div>

    <form action="<?=$xhprof_base_url?>/index.php" method="get" target="_blank">
        <input type="hidden" name="run1" value="">
        <input type="hidden" name="run2" value="">
        <input type="hidden" name="source" value="<?=$data[0]['source']?>">
        
        <table id="table_history" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <th></th>
                <?php foreach($data as $header): ?>
                    <th>
                        <?=substr($header['run_id'], -4)?>
                        <br>
                        <a href="<?=$xhprof_base_url?>/index.php?source=<?=$header['source']?>&run=<?=$header['run_id']?>">
                            <?php echo date('Y-m-d<\b\r>H:i:s', $header['datetime']); ?>
                        </a>
                    </th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th>Load Time (s)</th>
                <?php foreach($data as $row): ?>
                    <td class="right"><?=$row['time']?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th>Function calls</th>
                <?php foreach($data as $row): ?>
                    <td class="right"><?=$row['calls']?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th>Compare</th>
                 <?php foreach($data as $row): ?>
                 <td class="center td_compare"><input class="chk_compare" type="checkbox" value="<?=$row['run_id']?>"></td>
                <?php endforeach; ?>
            </tr>           
        </table>

        <div class="compare">
            <input class="submit_compare" type="submit" value="Compare two reports" disabled>
        </div>
    </form>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
    <script>
        var chartRows = [<?= implode(',', $google_chart_rows); ?>];
        var xAxisType = '<?= $google_chart_x_axis_type ?>';
        var xAxisLabel = '<?= $google_chart_x_axis_label ?>';
    </script>
    <script type="text/javascript" src="<?=$history_uri?>/history-chart.js"></script>

<?php elseif(!empty($sources)): ?>
    <div class="info">
        <p>Please select a namespace to view a history report.</p>
    </div>


    <?php if(!empty($firstSource)): ?>
    <script>
    window.location = '<?=$xhprof_base_url?>/history.php?source=<?=$firstSource?>';
    </script>
    <?php endif; ?>

<?php else: ?>
    <div class="info">
        <p>No XHProf files were found in your output directory [<?=ini_get("xhprof.output_dir")?>]</p>
        <p>Enable XHProf profiling and ensure output files are being saved before trying again.</p>
    </div>

<?php endif; ?>

