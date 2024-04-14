<?php
declare(strict_types=1);

namespace App\Controller;

use Slim\App;
use Slim\Factory\AppFactory;

class WikiBackendAppFactory
{
    public static function createApp(): App
    {
        $isProduction = getenv('APP_ENV') === 'prod';

        $app = AppFactory::create();

        // Регистрация middlewares фреймворка Slim.
        $app->addRoutingMiddleware();
        $app->addErrorMiddleware(!$isProduction, true, true);

        $app->get('/articles/list', ArticleApiController::class . ':listArticles');
        $app->delete('/articles/batch-delete', ArticleApiController::class . ':batchDeleteArticles');
        $app->get('/article', ArticleApiController::class . ':getArticle');
        $app->post('/article', ArticleApiController::class . ':createArticle');
        $app->post('/article/edit', ArticleApiController::class . ':editArticle');
        $app->delete('/article/delete', ArticleApiController::class . ':deleteArticle');

        return $app;
    }
}
