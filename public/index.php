<?php
declare(strict_types=1);

use App\Controller\WikiBackendAppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = WikiBackendAppFactory::createApp();
$app->run();
