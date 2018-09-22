<style type="text/css">
body {font-family:verdana,arial,sans-serif}
#nav a {margin-right: 30px}
</style>

<div id="nav" style="background: #ddd;padding:5px">
    <p>
        <?php if (!empty($_GET['source'])): ?>
        <a href="<?= $xhprof_base_url ?>/history.php?source=<?= $_GET['source'] ?>">Back to History</a>
        <?php endif; ?>
    </p>
</div>
