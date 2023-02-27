<?php
declare(strict_types=1);

namespace App\Controller\Response;

use App\Model\Article;
use App\Model\Data\ArticleSummary;

class ArticleApiResponseFormatter
{
    public static function formatArticle(Article $article): array
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'tags' => $article->getTags(),
            'created_at' => $article->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'created_by' => $article->getCreatedBy(),
            'updated_at' => $article->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
            'updated_by' => $article->getUpdatedBy(),
        ];
    }

    public static function formatArticleSummary(ArticleSummary $article): array
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'tags' => $article->getTags(),
        ];
    }

    /**
     * @param ArticleSummary[] $articles
     * @return array
     */
    public static function formatArticleSummaryList(array $articles): array
    {
        return array_map(static fn($article) => self::formatArticleSummary($article), $articles);
    }
}
