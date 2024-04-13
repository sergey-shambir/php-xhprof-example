<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Common\Database\TransactionalExecutor;
use App\Database\ArticleRepository;
use App\Database\TagRepository;
use App\Model\Article;
use App\Model\Data\CreateArticleParams;
use App\Model\Data\EditArticleParams;
use App\Model\Exception\ArticleNotFoundException;

readonly class ArticleService
{
    public function __construct(
        private TransactionalExecutor $transactionalExecutor,
        private ArticleRepository $articleRepository,
        private TagRepository $tagRepository)
    {
    }

    /**
     * @param int $id
     * @return Article
     * @throws ArticleNotFoundException
     */
    public function getArticle(int $id): Article
    {
        $article = $this->articleRepository->findOne($id);
        if (!$article)
        {
            throw new ArticleNotFoundException("Cannot find article with id $id");
        }
        return $article;
    }

    public function createArticle(CreateArticleParams $params): int
    {
        return $this->transactionalExecutor->doWithTransaction(function () use ($params) {
            $this->tagRepository->addTags($params->getTags());

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
            return $this->articleRepository->save($article);
        });

    }

    /**
     * @param EditArticleParams $params
     * @return void
     * @throws ArticleNotFoundException
     */
    public function editArticle(EditArticleParams $params): void
    {
        $this->transactionalExecutor->doWithTransaction(function () use ($params) {
            $this->tagRepository->addTags($params->getTags());

            $article = $this->getArticle($params->getId());
            $article->edit($params->getUserId(), $params->getTitle(), $params->getContent(), $params->getTags());
            $this->articleRepository->save($article);
        });
    }

    public function deleteArticle(int $id): void
    {
        $this->articleRepository->delete([$id]);
    }

    /**
     * @param int[] $ids
     * @return void
     */
    public function batchDeleteArticles(array $ids): void
    {
        $this->articleRepository->delete($ids);
    }
}
