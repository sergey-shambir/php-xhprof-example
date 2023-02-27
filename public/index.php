<?php
declare(strict_types=1);

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$isProduction = getenv('APP_ENV') === 'prod';

$app = AppFactory::create();

// Регистрация middlewares фреймворка Slim.
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(!$isProduction, true, true);

$app->get('/articles/list', \App\Controller\ArticleApiController::class . ':listArticles');
$app->delete('/articles/batch-delete', \App\Controller\ArticleApiController::class . ':batchDeleteArticles');
$app->get('/article', \App\Controller\ArticleApiController::class . ':getArticle');
$app->post('/article', \App\Controller\ArticleApiController::class . ':createArticle');
$app->post('/article/edit', \App\Controller\ArticleApiController::class . ':editArticle');
$app->delete('/article/delete', \App\Controller\ArticleApiController::class . ':deleteArticle');

$app->run();
