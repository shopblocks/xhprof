<link rel="stylesheet" href="<?=$history_uri?>/history-style.css">

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
        <p><a href="<?=$history_uri?>/clear.php" onclick="return confirm('Are you sure?');">Clear all XHProf reports</a></p>
    </div>
</div>

<?php if(!empty($data)): ?>
    <div id="chart"></div>

    <table cellpadding="0" cellspacing="0" border="0">
        <tr>
            <th></th>
            <?php foreach($headers as $header): ?>
                <th><?=$header?></th>
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
    </table>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>var chartRows = [<?= implode(',', $chart_data); ?>];</script>
    <script type="text/javascript" src="<?=$history_uri?>/history-chart.js"></script>

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

