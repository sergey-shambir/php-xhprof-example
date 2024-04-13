<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Model\Article;
use PHPUnit\Framework\TestCase;

/**
 * Этот тест несёт мало пользы и добавлен в целях иллюстрации подхода к модульному тестированию.
 */
class ArticleTest extends TestCase
{
    public function testEditArticle(): void
    {
        // Шаг 1. Arrange (подготовка состояния)
        $firstAuthorId = 307;
        $secondAuthorId = 417;
        $article = new Article(
            id: 10,
            version: 1,
            title: '(Черновик) B+ деревья',
            content: <<<TEXT
                B+-деревья — это основа физической структуры реляционных баз данных.
                
                Именно они ответственны за сочетание двух характеристик реляционных СУБД...
                TEXT
            ,
            tags: ['MySQL', 'PostgreSQL'],
            createdAt: new \DateTimeImmutable(),
            createdBy: $firstAuthorId
        );

        // Шаг 2. Act (выполнение действия)
        $article->edit(
            userId: $secondAuthorId,
            title: 'B+ деревья',
            content: <<<TEXT
                    B+-деревья — это основа физической структуры реляционных баз данных.
                    
                    Именно они ответственны за сочетание двух характеристик реляционных СУБД:
                    
                    - Высокая скорость работы как для небольших запросов, так и для больших 
                    - Устойчивость данных к перезагрузке при условии сохранности внешнего диска
                    TEXT,
            tags: ['MySQL', 'B+-деревья', 'Индексы'],
        );

        // Шаг 3. Assert (проверка утверждений)
        $this->assertEquals('B+ деревья', $article->getTitle());
        $this->assertEquals(['MySQL', 'B+-деревья', 'Индексы'], $article->getTags());
        $this->assertEquals($firstAuthorId, $article->getCreatedBy());
        $this->assertEquals($secondAuthorId, $article->getUpdatedBy());
    }
}
