# History Report

Shows multiple XHProf reports side by side as a data table and line chart.

## Installation

Edit history/config.php and set the URL you are using to access the XHProf GUI and any other settings as appropriate.

## Viewing History Reports

Navigate to <XHPROF_WEBROOT>/index.php?history then select a namespace to analyse. The report will pick up all of the
namespaces contained in the filenames.

When saving your XHProf output files, you may find it useful to generate a namespace dynamically based on, for example,
Apache's REQUEST_URI value:

```php
$namespace_slug = preg_replace('/[^a-z0-9]+/', '_', strtolower(ltrim($_SERVER['REQUEST_URI'], '/')));
$run_id = $xhprof_runs->save_run($xhprof_data, $namespace_slug);
```

