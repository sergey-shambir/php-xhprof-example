<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Article;

interface ArticleRepositoryInterface
{
    public function findOne(int $id): ?Article;

    public function save(Article $article): int;

    /**
     * @param int[] $ids
     * @return void
     */
    public function delete(array $ids): void;
}
