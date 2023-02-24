<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Database\ArticleRepository;
use App\Model\Article;
use App\Model\Data\CreateArticleParams;
use App\Model\Data\EditArticleParams;
use App\Model\Exception\ArticleNotFoundException;

class ArticleService
{
    private ArticleRepository $repository;

    public function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $id
     * @return Article
     * @throws ArticleNotFoundException
     */
    public function getArticle(int $id): Article
    {
        $article = $this->repository->findOne($id);
        if (!$article)
        {
            throw new ArticleNotFoundException("Cannot find article with id $id");
        }
        return $article;
    }

    public function createArticle(CreateArticleParams $params): int
    {
        $article = new Article(
            null,
            1,
            $params->getTitle(),
            '',
            $params->getTags(),
            new \DateTimeImmutable(),
            $params->getUserId(),
            null,
            null
        );
        return $this->repository->save($article);
    }

    /**
     * @param EditArticleParams $params
     * @return void
     * @throws ArticleNotFoundException
     */
    public function editArticle(EditArticleParams $params): void
    {
        $article = $this->getArticle($params->getId());
        $article->edit($params->getUserId(), $params->getTitle(), $params->getContent(), $params->getTags());
        $this->repository->save($article);
    }

    public function deleteArticle(int $id): void
    {
        $this->repository->delete([$id]);
    }
}
