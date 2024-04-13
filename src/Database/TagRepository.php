<?php
declare(strict_types=1);

namespace App\Database;

use App\Common\Database\Connection;
use App\Model\Repository\TagRepositoryInterface;

class TagRepository implements TagRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string[] $tags
     * @return void
     */
    public function addTags(array $tags): void
    {
        if (count($tags) === 0)
        {
            return;
        }

        // NOTE: Предварительная проверка на существование тегов позволяет избежать исчерпания autoincrement id
        //   из-за постепенного роста по мере INSERT ODKU (т.е. UPSERT).
        $placeholders = self::getCommaSeparatedList('?', count($tags));
        $stmt = $this->connection->execute("SELECT text FROM tag WHERE text IN ($placeholders)", $tags);
        $existingTags = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $newTags = array_values(array_diff($tags, $existingTags));

        if (count($newTags) === 0)
        {
            return;
        }

        // NOTE: Используется INSERT ODKU (UPSERT) на случай, если параллельно записываются другие теги.
        $placeholders = self::getCommaSeparatedList('(?)', count($newTags));
        $this->connection->execute(
            <<<SQL
            INSERT INTO tag
              (text)
            VALUES
              $placeholders
            ON DUPLICATE KEY UPDATE
              text = text
            SQL,
            $newTags
        );
    }

    public static function getCommaSeparatedList(string $item, int $count): string
    {
        $items = array_fill(0, $count, $item);
        return implode(', ', $items);
    }
}
