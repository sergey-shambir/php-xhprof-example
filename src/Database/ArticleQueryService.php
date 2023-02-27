<?php
declare(strict_types=1);

namespace App\Database;

use App\Common\Database\Connection;
use App\Model\Data\ArticleSummary;

class ArticleQueryService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return ArticleSummary[]
     */
    public function listArticles(): array
    {
        $query = <<<SQL
            SELECT
              a.id,
              a.title,
              GROUP_CONCAT(t.text) AS tags
            FROM article a
              LEFT JOIN article_tag at on a.id = at.article_id
              LEFT JOIN tag t on t.id = at.tag_id
            GROUP BY a.id
            SQL;
        $stmt = $this->connection->execute($query);

        return array_map(
            fn($row) => $this->hydrateArticleSummary($row),
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    private function hydrateArticleSummary(array $row): ArticleSummary
    {
        try
        {
            return new ArticleSummary(
                (int)$row['id'],
                (string)$row['title'],
                json_decode($row['tags'], true, 512, JSON_THROW_ON_ERROR)
            );
        }
        catch (\Exception $e)
        {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
