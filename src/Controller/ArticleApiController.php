<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Request\ArticleApiRequestParser;
use App\Controller\Request\RequestValidationException;
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
        $articles = ServiceProvider::getInstance()->getArticleQueryService()->listArticles();
        $responseData = ArticleApiResponseFormatter::formatArticleSummaryList($articles);

        return $this->success($response, $responseData);
    }

    public function batchDeleteArticles(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $ids = ArticleApiRequestParser::parseIntegerArray($request->getQueryParams(), 'ids');
        }
        catch (RequestValidationException $exception)
        {
            return $this->badRequest($response, $exception->getFieldErrors());
        }

        ServiceProvider::getInstance()->getArticleService()->batchDeleteArticles($ids);
        return $response;
    }

    public function getArticle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $id = ArticleApiRequestParser::parseInteger($request->getQueryParams(), 'id');
            $article = ServiceProvider::getInstance()->getArticleService()->getArticle($id);
        }
        catch (RequestValidationException $exception)
        {
            return $this->badRequest($response, $exception->getFieldErrors());
        }
        catch (ArticleNotFoundException $e)
        {
            return $this->badRequest($response, ['id' => $e->getMessage()]);
        }
        return $this->success($response, ArticleApiResponseFormatter::formatArticle($article));
    }

    public function createArticle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $params = ArticleApiRequestParser::parseCreateArticleParams((array)$request->getParsedBody());
        }
        catch (RequestValidationException $exception)
        {
            return $this->badRequest($response, $exception->getFieldErrors());
        }

        $articleId = ServiceProvider::getInstance()->getArticleService()->createArticle($params);

        return $this->success($response, ['id' => $articleId]);
    }

    public function editArticle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $params = ArticleApiRequestParser::parseEditArticleParams((array)$request->getParsedBody());
        }
        catch (RequestValidationException $exception)
        {
            return $this->badRequest($response, $exception->getFieldErrors());
        }

        ServiceProvider::getInstance()->getArticleService()->editArticle($params);

        return $this->success($response, []);
    }

    public function deleteArticle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $id = ArticleApiRequestParser::parseInteger($request->getQueryParams(), 'id');
        }
        catch (RequestValidationException $exception)
        {
            return $this->badRequest($response, $exception->getFieldErrors());
        }

        ServiceProvider::getInstance()->getArticleService()->deleteArticle($id);
        return $this->success($response, []);
    }

    private function success(ResponseInterface $response, array $responseData): ResponseInterface
    {
        return $this->withJson($response, $responseData)->withStatus(self::HTTP_STATUS_OK);
    }

    private function badRequest(ResponseInterface $response, array $errors): ResponseInterface
    {
        $responseData = ['errors' => $errors];
        return $this->withJson($response, $responseData)->withStatus(self::HTTP_STATUS_BAD_REQUEST);
    }

    private function withJson(ResponseInterface $response, array $responseData): ResponseInterface
    {
        try
        {
            $responseBytes = json_encode($responseData, JSON_THROW_ON_ERROR);
            $response->getBody()->write($responseBytes);
            return $response->withHeader('Content-Type', 'application/json');
        }
        catch (\JsonException $e)
        {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
