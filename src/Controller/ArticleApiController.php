<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Response\ArticleApiResponseFormatter;
use App\Model\Exception\ArticleNotFoundException;
use App\Model\Service\ServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ArticleApiController
{
    private const HTTP_STATUS_OK = 200;
    private const HTTP_STATUS_BAD_REQUEST = 400;

    public function listArticles(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $articles = ServiceProvider::getInstance()->getArticleListService()->listArticles();
        $responseData = ArticleApiResponseFormatter::formatArticleList($articles);

        return $this->success($response, $responseData);
    }

    public function batchDeleteArticles(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $ids = $request->getQueryParams()['ids'] ?? null;
        if (!$this->isIntegerArrayParameter($ids))
        {
            return $this->badRequest($response, 'ids', 'Invalid article IDs');
        }

        ServiceProvider::getInstance()->getArticleListService()->batchDeleteArticles($ids);
        return $response;
    }

    public function getArticle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = $request->getQueryParams()['id'] ?? null;
        if (!$this->isIntegerParameter($id))
        {
            return $this->badRequest($response, 'id', 'Invalid article ID');
        }

        try
        {
            $article = ServiceProvider::getInstance()->getArticleService()->getArticle($id);
        }
        catch (ArticleNotFoundException $e)
        {
            return $this->badRequest($response, 'id', 'Unknown article ID');
        }
        return $this->success($response, ArticleApiResponseFormatter::formatArticle($article));
    }

    public function createArticle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // TODO: Реализовать метод API
        throw new \LogicException("This method is not implemented");
    }

    public function editArticle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // TODO: Реализовать метод API
        throw new \LogicException("This method is not implemented");
    }

    public function deleteArticle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = $request->getQueryParams()['id'] ?? null;
        if (!$this->isIntegerParameter($id))
        {
            return $response->withStatus(self::HTTP_STATUS_BAD_REQUEST);
        }

        ServiceProvider::getInstance()->getArticleService()->deleteArticle($id);
        return $this->success($response, []);
    }

    private function success(ResponseInterface $response, array $responseData): ResponseInterface
    {
        return $this->withJson($response, $responseData)->withStatus(self::HTTP_STATUS_OK);
    }

    private function badRequest(ResponseInterface $response, string $field, string $error): ResponseInterface
    {
        $responseData = [
            'errors' => [
                $field => $error,
            ]
        ];
        return $this->withJson($response, $responseData)->withStatus(self::HTTP_STATUS_BAD_REQUEST);
    }

    private function withJson(ResponseInterface $response, array $responseData): ResponseInterface
    {
        try
        {
            $responseBytes = json_encode($response, JSON_THROW_ON_ERROR);
            $response->getBody()->write($responseBytes);
            return $response->withHeader('Content-Type', 'application/json');
        }
        catch (\JsonException $e)
        {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function isIntegerArrayParameter(mixed $ids): bool
    {
        if (!is_array($ids))
        {
            return false;
        }
        foreach ($ids as $id)
        {
            if (!$this->isIntegerParameter($id))
            {
                return false;
            }
        }
        return true;
    }

    public function isIntegerParameter(mixed $id): bool
    {
        return is_numeric($id) && ctype_digit($id);
    }
}
