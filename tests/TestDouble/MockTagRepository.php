<?php
declare(strict_types=1);

namespace App\Tests\TestDouble;

use App\Model\Repository\TagRepositoryInterface;

/**
 * Класс оставлен в целях иллюстрации.
 * Модульный тест использует средства mock-ирования библиотеки PHPUnit вместо этого класса
 * @see \App\Tests\Unit\ArticleServiceUnitTest
 */
class MockTagRepository implements TagRepositoryInterface
{
    /**
     * @var array<string,true> - множетсво тегов на основе ассоциативного массива
     */
    private $tagsSet = [];

    public function addTags(array $tags): void
    {
        foreach ($tags as $tag)
        {
            $this->tagsSet[$tag] = true;
        }
    }

    public function getTags(): array
    {
        return array_keys($this->tagsSet);
    }
}