<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Common\Database\ConnectionProvider;
use App\Database\ArticleRepository;

final class ServiceProvider
{
    private ?ArticleService $articleService = null;
    private ?ArticleListService $articleListService = null;
    private ?ArticleRepository $articleRepository = null;

    public static function getInstance(): self
    {
        static $instance = null;
        if ($instance === null)
        {
            $instance = new self();
        }
        return $instance;
    }

    public function getArticleService(): ArticleService
    {
        if ($this->articleService === null)
        {
            $this->articleService = new ArticleService($this->getArticleRepository());
        }
        return $this->articleService;
    }

    public function getArticleListService(): ArticleListService
    {
        if ($this->articleListService === null)
        {
            $this->articleListService = new ArticleListService($this->getArticleRepository());
        }
        return $this->articleListService;
    }

    private function getArticleRepository(): ArticleRepository
    {
        if ($this->articleRepository === null)
        {
            $this->articleRepository = new ArticleRepository(ConnectionProvider::getConnection());
        }
        return $this->articleRepository;
    }
}
