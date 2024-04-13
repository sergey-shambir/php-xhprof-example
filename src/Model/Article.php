<?php
declare(strict_types=1);

namespace App\Model;

class Article
{
    /**
     * @param int|null $id
     * @param int $version
     * @param string $title
     * @param string $content
     * @param string[] $tags
     * @param \DateTimeImmutable $createdAt
     * @param int $createdBy
     * @param \DateTimeImmutable|null $updatedAt
     * @param int|null $updatedBy
     *
     * @note поле $version используется для оптимистичной блокировки сущности
     */
    public function __construct(
        private ?int $id,
        private int $version,
        private string $title,
        private string $content,
        private array $tags,
        private \DateTimeImmutable $createdAt,
        private int $createdBy,
        private ?\DateTimeImmutable $updatedAt = null,
        private ?int $updatedBy = null
    )
    {
    }

    /**
     * @param int $userId
     * @param string $title
     * @param string $content
     * @param string[] $tags
     * @return void
     */
    public function edit(int $userId, string $title, string $content, array $tags): void
    {
        $this->title = $title;
        $this->content = $content;
        $this->tags = $tags;

        $this->updatedAt = new \DateTimeImmutable();
        $this->updatedBy = $userId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUpdatedBy(): ?int
    {
        return $this->updatedBy;
    }
}
