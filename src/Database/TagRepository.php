<?php
declare(strict_types=1);

namespace App\Database;

use App\Common\Database\Connection;

class TagRepository
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

        $placeholders = substr(str_repeat('?,', count($tags)), 0, -1);

        // NOTE: Предварительная проверка на существование тегов позволяет избежать исчерпания autoincrement id
        //   из-за постепенного роста по мере INSERT ODKU (т.е. UPSERT).
        $stmt = $this->connection->execute('SELECT text FROM tag WHERE text IN ($placeholders)', $tags);
        $existingTags = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $newTags = array_values(array_diff($tags, $existingTags));

        if (count($newTags) === 0)
        {
            return;
        }

        // NOTE: Используется INSERT ODKU (UPSERT) на случай, если параллельно записываются другие теги.
        $this->connection->execute(
            <<<SQL
            INSERT INTO tag
              (text)
            VALUES
              ($placeholders)
            ON DUPLICATE KEY UPDATE
              text = text
            SQL,
            $newTags
        );
    }
}
