<?php
declare(strict_types=1);

namespace App\Model\Data;

class EditArticleParams
{
    private int $id;
    private int $userId;
    private string $title;
    private string $content;
    /** @var string[] */
    private array $tags;

    /**
     * @param int $id
     * @param string $title
     * @param string $content
     * @param string[] $tags
     * @param int $userId
     */
    public function __construct(
        int $id,
        int $userId,
        string $title,
        string $content,
        array $tags
    )
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->content = $content;
        $this->tags = $tags;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
