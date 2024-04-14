<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Model\Article;
use App\Model\Data\CreateArticleParams;
use App\Model\Data\EditArticleParams;
use App\Model\Exception\ArticleNotFoundException;
use App\Model\Repository\TagRepositoryInterface;
use App\Model\Service\ArticleService;
use App\Tests\TestDouble\DummyTransactionalExecutor;
use App\Tests\TestDouble\FakeArticleRepository;
use App\Tests\TestDouble\MockTagRepository;
use PHPUnit\Framework\TestCase;

/**
 * Это ПЛОХОЙ пример: тест, который использует тестовые дублёры вместо реальных классов Repository.
 * В реальном проекте следует писать интеграционный (компонентный или функциональный) тест, а не играться с дублёрами!
 */
class ArticleServiceUnitTest extends TestCase
{
    public function testCreateArticleV1(): void
    {
        // Шаг 1. Arrange
        // В данном случае мы только создаём сервис
        $tagRepositoryMock = new MockTagRepository();
        $service = new ArticleService(
            new DummyTransactionalExecutor(),
            new FakeArticleRepository(),
            $tagRepositoryMock
        );
        $firstAuthorId = 10;

        // Шаг 2. Act
        $articleId = $service->createArticle(new CreateArticleParams(
            userId: $firstAuthorId,
            title: '(Черновик) B+ деревья',
            tags: ['MySQL', 'PostgreSQL'],
        ));

        // Шаг 3. Assert
        $article = $service->getArticle($articleId);
        $this->assertEquals('(Черновик) B+ деревья', $article->getTitle());
        $this->assertArticleTags(['MySQL', 'PostgreSQL'], $article);
        $this->assertEquals($firstAuthorId, $article->getCreatedBy());
        $this->assertEquals($tagRepositoryMock->getTags(), ['MySQL', 'PostgreSQL']);
    }

    public function testCreateArticleV2(): void
    {
        // Шаг 1. Arrange
        // В данном случае мы только создаём сервис
        $tagRepositoryMock = $this->createMock(TagRepositoryInterface::class);
        $service = new ArticleService(
            new DummyTransactionalExecutor(),
            new FakeArticleRepository(),
            $tagRepositoryMock
        );
        $firstAuthorId = 10;

        $tagRepositoryMock->expects($this->exactly(1))->method('addTags');

        // Шаг 2. Act
        $articleId = $service->createArticle(new CreateArticleParams(
            userId: $firstAuthorId,
            title: '(Черновик) B+ деревья',
            tags: ['MySQL', 'PostgreSQL'],
        ));

        // Шаг 3. Assert
        $article = $service->getArticle($articleId);
        $this->assertEquals('(Черновик) B+ деревья', $article->getTitle());
        $this->assertArticleTags(['MySQL', 'PostgreSQL'], $article);
        $this->assertEquals($firstAuthorId, $article->getCreatedBy());
    }

    public function testCreateEditArticle(): void
    {
        // Шаг 1. Arrange
        // В данном случае мы только создаём сервис
        $tagRepositoryMock = $this->createMock(TagRepositoryInterface::class);
        $service = new ArticleService(
            new DummyTransactionalExecutor(),
            new FakeArticleRepository(),
            $tagRepositoryMock
        );
        $firstAuthorId = 10;

        $tagRepositoryMock->expects($this->exactly(2))->method('addTags');

        // Шаг 2. Act
        $articleId = $service->createArticle(new CreateArticleParams(
            userId: $firstAuthorId,
            title: '(Черновик) B+ деревья',
            tags: ['MySQL', 'PostgreSQL'],
        ));

        // Шаг 3. Assert
        $article = $service->getArticle($articleId);
        $this->assertEquals('(Черновик) B+ деревья', $article->getTitle());
        $this->assertArticleTags(['MySQL', 'PostgreSQL'], $article);
        $this->assertEquals($firstAuthorId, $article->getCreatedBy());

        // Шаг 1. Arrange
        $secondAuthorId = 17;

        // Шаг 2. Act
        $service->editArticle(new EditArticleParams(
            id: $articleId,
            userId: $secondAuthorId,
            title: 'B+ деревья',
            content: <<<TEXT
                    B+-деревья — это основа физической структуры реляционных баз данных.
                    
                    Именно они ответственны за сочетание двух характеристик реляционных СУБД:
                    
                    - Высокая скорость работы как для небольших запросов, так и для больших 
                    - Устойчивость данных к перезагрузке при условии сохранности внешнего диска
                    TEXT,
            tags: ['MySQL', 'B+-деревья', 'Индексы'],
        ));

        // Шаг 3. Assert
        $article = $service->getArticle($articleId);
        $this->assertEquals('B+ деревья', $article->getTitle());
        $this->assertArticleTags(['MySQL', 'B+-деревья', 'Индексы'], $article);
        $this->assertEquals($firstAuthorId, $article->getCreatedBy());
        $this->assertEquals($secondAuthorId, $article->getUpdatedBy());

        // Шаг 2. Act
        $service->deleteArticle($articleId);

        // Шаг 3. Assert
        $this->expectException(ArticleNotFoundException::class);
        $service->getArticle($articleId);
    }

    public function testDeleteArticle(): void
    {
        // Шаг 1. Arrange
        // В данном случае мы только создаём сервис
        $tagRepositoryMock = $this->createMock(TagRepositoryInterface::class);
        $service = new ArticleService(
            new DummyTransactionalExecutor(),
            new FakeArticleRepository(),
            $tagRepositoryMock
        );
        $firstAuthorId = 10;

        $tagRepositoryMock->expects($this->exactly(1))->method('addTags');

        // Шаг 2. Act
        $articleId = $service->createArticle(new CreateArticleParams(
            userId: $firstAuthorId,
            title: '(Черновик) B+ деревья',
            tags: ['MySQL', 'PostgreSQL'],
        ));
        $service->deleteArticle($articleId);

        // Шаг 3. Assert
        $this->expectException(ArticleNotFoundException::class);
        $service->getArticle($articleId);
    }

    public function testBatchDeleteArticles(): void
    {
        // Шаг 1. Arrange
        // В данном случае мы только создаём сервис
        $tagRepositoryMock = $this->createMock(TagRepositoryInterface::class);
        $service = new ArticleService(
            new DummyTransactionalExecutor(),
            new FakeArticleRepository(),
            $tagRepositoryMock
        );
        $authorId = 10;

        // Шаг 2. Act
        $firstArticleId = $service->createArticle(new CreateArticleParams(
            userId: $authorId,
            title: 'B+ деревья',
            tags: ['MySQL', 'PostgreSQL'],
        ));
        $secondArticleId = $service->createArticle(new CreateArticleParams(
            userId: $authorId,
            title: 'Индексы',
            tags: ['MySQL', 'PostgreSQL', 'SQL'],
        ));
        $thirdArticleId = $service->createArticle(new CreateArticleParams(
            userId: $authorId,
            title: 'План выполнения запроса',
            tags: ['MySQL', 'EXPLAIN', 'SQL'],
        ));
        $service->batchDeleteArticles([$firstArticleId, $secondArticleId]);

        // Шаг 3. Assert
        $article = $service->getArticle($thirdArticleId);
        $this->assertEquals('План выполнения запроса', $article->getTitle());
        $this->assertArticleTags(['MySQL', 'EXPLAIN', 'SQL'], $article);

        $this->assertThrows(
            static fn() => $service->getArticle($firstArticleId),
            ArticleNotFoundException::class
        );
        $this->assertThrows(
            static fn() => $service->getArticle($secondArticleId),
            ArticleNotFoundException::class
        );
    }

    private function assertThrows(\Closure $closure, string $exceptionClass): void
    {
        $actualExceptionClass = null;
        try
        {
            $closure();
        }
        catch (\Throwable $e)
        {
            $actualExceptionClass = $e::class;
        }
        $this->assertEquals($exceptionClass, $actualExceptionClass, "$exceptionClass exception should be thrown");
    }

    private function assertArticleTags(array $expected, Article $article): void
    {
        $actual = $article->getTags();
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual, 'article tags');
    }
}
