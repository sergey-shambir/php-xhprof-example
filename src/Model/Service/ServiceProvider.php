<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Common\Database\ConnectionProvider;
use App\Common\Database\Synchronization;
use App\Database\ArticleQueryService;
use App\Database\ArticleRepository;
use App\Database\TagRepository;

final class ServiceProvider
{
    private ?ArticleService $articleService = null;
    private ?ArticleQueryService $articleQueryService = null;
    private ?ArticleRepository $articleRepository = null;
    private ?TagRepository $tagRepository = null;

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
            $synchronization = new Synchronization(ConnectionProvider::getConnection());
            $this->articleService = new ArticleService($synchronization, $this->getArticleRepository(), $this->getTagRepository());
        }
        return $this->articleService;
    }

    public function getArticleQueryService(): ArticleQueryService
    {
        if ($this->articleQueryService === null)
        {
            $this->articleQueryService = new ArticleQueryService(ConnectionProvider::getConnection());
        }
        return $this->articleQueryService;
    }

    private function getArticleRepository(): ArticleRepository
    {
        if ($this->articleRepository === null)
        {
            $this->articleRepository = new ArticleRepository(ConnectionProvider::getConnection());
        }
        return $this->articleRepository;
    }

    private function getTagRepository(): TagRepository
    {
        if ($this->tagRepository === null)
        {
            $this->tagRepository = new TagRepository(ConnectionProvider::getConnection());
        }
        return $this->tagRepository;
    }
}
