<?php
declare(strict_types=1);

use App\Controller\WikiBackendAppFactory;

require __DIR__ . '/../vendor/autoload.php';

if (extension_loaded('xhprof')) {
    require_once __DIR__ . '/../src/xhprof/xhprof_lib/utils/xhprof_lib.php';
    require_once __DIR__ . '/../src/xhprof/xhprof_lib/utils/xhprof_runs.php';
    
    xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU);
}

$app = WikiBackendAppFactory::createApp();
$app->run();

if (extension_loaded('xhprof')) {
    $xhprofData = xhprof_disable();

    $xhprofPrefix = preg_replace('/[^a-zA-Z0-9]/', '-', $_SERVER['REQUEST_URI'] ?? '/');
    $xhprofPrefix = preg_replace('/-+/', '-', $xhprofPrefix);
    $xhprofPrefix = preg_replace('/(^-|-$)/', '', $xhprofPrefix);
    $xhprofRuns = new XHProfRuns_Default();
    $run_id = $xhprofRuns->save_run($xhprofData, $xhprofPrefix);
}
