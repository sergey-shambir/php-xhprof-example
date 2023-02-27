<?php
declare(strict_types=1);

namespace App\Database;

use App\Common\Database\Connection;
use App\Common\Database\DatabaseDateFormat;
use App\Model\Article;
use App\Model\Exception\OptimisticLockException;

class ArticleRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findOne(int $id): ?Article
    {
        $query = $this->getSelectArticlesQuery(['a.id = ?']);
        $params = [$id];
        $stmt = $this->connection->execute($query, $params);
        if ($row = $stmt->fetch(\PDO::FETCH_ASSOC))
        {
            return $this->hydrateArticle($row);
        }
        return null;
    }

    /**
     * @return Article[]
     */
    public function findAll(): array
    {
        $query = $this->getSelectArticlesQuery();
        $stmt = $this->connection->execute($query);

        return array_map(
            fn($row) => $this->hydrateArticle($row),
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    public function save(Article $article): int
    {
        // TODO: Добавить управление транзакцией
        // TODO: обновлять список тегов

        if ($article->getId())
        {
            $this->updateArticle($article);
            return $article->getId();
        }
        return $this->insertArticle($article);
    }

    /**
     * @param int[] $ids
     * @return void
     */
    public function delete(array $ids): void
    {
        $placeholders = substr(str_repeat('?,', 5), 0, -1);
        $this->connection->execute(
            <<<SQL
            DELETE FROM article WHERE id IN ($placeholders)
            SQL,
            $ids
        );
    }

    private function getSelectArticlesQuery(array $conditions = []): string
    {
        $conditionsString = count($conditions) > 0 ? implode(' AND ', $conditions) : 'TRUE';

        return <<<SQL
            SELECT
              a.id,
              a.version,
              a.title,
              a.content,
              GROUP_CONCAT(t.text) AS tags,
              a.created_at,
              a.created_by,
              a.updated_at,
              a.updated_by
            FROM article a
              LEFT JOIN article_tag at on a.id = at.article_id
              LEFT OUTER JOIN tag t on t.id = at.tag_id
            WHERE $conditionsString
            GROUP BY a.id
            SQL;
    }

    private function hydrateArticle(array $row): Article
    {
        try
        {
            return new Article(
                (int)$row['id'],
                (int)$row['version'],
                (string)$row['title'],
                (string)$row['content'],
                json_decode($row['tags'], true, 512, JSON_THROW_ON_ERROR),
                new \DateTimeImmutable($row['created_at']),
                (int)$row['created_by'],
                new \DateTimeImmutable($row['updated_at']),
                (int)$row['updated_by']
            );
        }
        catch (\Exception $e)
        {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function insertArticle(Article $article): int
    {
        $query = <<<SQL
            INSERT INTO article
              (version, title, content, created_at, created_by, updated_at, updated_by)
            VALUES
              (:version, :title, :content, :created_at, :created_by, :updated_at, :updated_by)
            SQL;
        $params = [
            ':version' => $article->getVersion(),
            ':title' => $article->getTitle(),
            ':content' => $article->getContent(),
            ':created_at' => $this->formatDateTimeOrNull($article->getCreatedAt()),
            ':created_by' => $article->getCreatedBy(),
            ':updated_at' => $this->formatDateTimeOrNull($article->getUpdatedAt()),
            ':updated_by' => $article->getUpdatedBy()
        ];

        $this->connection->execute($query, $params);

        return $this->connection->getLastInsertId();
    }

    private function updateArticle(Article $article): void
    {
        // NOTE: Оптимистичная блокировка реализована за счёт
        //  1. Условия "version = :version" в WHERE
        //  2. Проверки числа изменённых колонок
        $query = <<<SQL
            UPDATE article
            SET
              id = :id,
              version = version + 1,
              title = :title,
              content = :content,
              created_at = :created_at,
              created_by = :created_by,
              updated_at = :updated_at,
              updated_by = :updated_by
            WHERE id = :id
              AND version = :version
            SQL;
        $params = [
            ':id' => $article->getId(),
            ':version' => $article->getVersion(),
            ':title' => $article->getTitle(),
            ':content' => $article->getContent(),
            ':created_at' => $this->formatDateTimeOrNull($article->getCreatedAt()),
            ':created_by' => $article->getCreatedBy(),
            ':updated_at' => $this->formatDateTimeOrNull($article->getUpdatedAt()),
            ':updated_by' => $article->getUpdatedBy()
        ];

        $stmt = $this->connection->execute($query, $params);
        if (!$stmt->rowCount())
        {
            throw new OptimisticLockException("Optimistic lock failed for article {$article->getId()}");
        }
    }

    private function formatDateTimeOrNull(?\DateTimeImmutable $dateTime): ?string
    {
        return $dateTime?->format(DatabaseDateFormat::MYSQL_DATETIME_FORMAT);
    }
}
