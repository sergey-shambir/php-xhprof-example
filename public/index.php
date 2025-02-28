<?php
declare(strict_types=1);

use App\Controller\WikiBackendAppFactory;

require __DIR__ . '/../vendor/autoload.php';

if (extension_loaded('xhprof')) {
    function do_xhprof_profile()
    {
        $minDurationToProfile = 0.1;
        $startedAt = microtime(true);
        xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_NO_BUILTINS);
        try {
            register_shutdown_function(function () use ($startedAt, $minDurationToProfile) {
                register_shutdown_function(function () use ($startedAt, $minDurationToProfile) {
                    try {
                        $xhprofData = xhprof_disable();
                        $duration = microtime(true) - $startedAt;
                        if ($duration < $minDurationToProfile) {
                            return;
                        }
                        $requestUrl = $_SERVER['REQUEST_URI'] ?? '/';
                        if (str_contains($requestUrl, 'autodiscover')) {
                            return;
                        }
                        $prefix = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $requestUrl), '-')
                            . '-' . str_replace('.', '-', sprintf('%.2f', $duration)) . 's';

                        require_once __DIR__ . '/../src/xhprof/xhprof_lib/utils/xhprof_lib.php';
                        require_once __DIR__ . '/../src/xhprof/xhprof_lib/utils/xhprof_runs.php';
                        $runs = new XHProfRuns_Default();
                        $runs->save_run($xhprofData, $prefix);
                    } catch (Throwable $e) {
                    }
                });
            });
        } catch (Throwable $e) {
        }
    }
    do_xhprof_profile();
}

$app = WikiBackendAppFactory::createApp();
$app->run();
