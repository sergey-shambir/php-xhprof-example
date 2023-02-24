<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Database\ArticleRepository;
use App\Model\Article;

class ArticleListService
{
    private ArticleRepository $repository;

    public function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Article[]
     */
    public function listArticles(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @param int[] $ids
     * @return void
     */
    public function batchDeleteArticles(array $ids): void
    {
        $this->repository->delete($ids);
    }
}
