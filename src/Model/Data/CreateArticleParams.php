<?php
declare(strict_types=1);

namespace App\Model\Data;

class CreateArticleParams
{
    private int $userId;
    private string $title;
    /** @var string[] */
    private array $tags;

    /**
     * @param int $userId
     * @param string $title
     * @param string[] $tags
     */
    public function __construct(
        int $userId,
        string $title,
        array $tags
    )
    {
        $this->userId = $userId;
        $this->title = $title;
        $this->tags = $tags;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
