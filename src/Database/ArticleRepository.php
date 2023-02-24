<?php
declare(strict_types=1);

namespace App\Database;

use App\Common\Database\Connection;
use App\Model\Article;

class ArticleRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findOne(int $id): ?Article
    {
    }

    /**
     * @return Article[]
     */
    public function findAll(): array
    {
    }

    public function save(Article $article): int
    {
    }

    /**
     * @param int[] $ids
     * @return void
     */
    public function delete(array $ids): void
    {
    }
}
