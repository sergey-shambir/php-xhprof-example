<?php
declare(strict_types=1);

namespace App\Tests\TestDouble;

use App\Model\Article;
use App\Model\Repository\ArticleRepositoryInterface;

class FakeArticleRepository implements ArticleRepositoryInterface
{
    private array $articlesById = [];

    public function findOne(int $id): ?Article
    {
        return $this->articlesById[$id] ?? null;
    }

    public function save(Article $article): int
    {
        $id = $article->getId();
        if ($id === null)
        {
            // Генерируем фейковый ID (монотонно возрастающий)
            $id = $this->getNextArticleId();
            // Создаём новый объект Article с новым ID
            $article = new Article(
                $id,
                $article->getVersion(),
                $article->getTitle(),
                $article->getContent(),
                $article->getTags(),
                $article->getCreatedAt(),
                $article->getCreatedBy(),
                $article->getUpdatedAt(),
                $article->getUpdatedBy(),
            );
        }
        $this->articlesById[$id] = $article;

        return $id;
    }

    public function delete(array $ids): void
    {
        foreach ($ids as $id)
        {
            unset($this->articlesById[$id]);
        }
    }

    private function getNextArticleId(): int
    {
        static $nextId = 0;
        return ++$nextId;
    }
}
