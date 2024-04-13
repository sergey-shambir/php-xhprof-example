<?php
declare(strict_types=1);

namespace App\Tests\TestDouble;

use App\Model\Repository\TagRepositoryInterface;

class DummyTagRepository implements TagRepositoryInterface
{
    public function addTags(array $tags): void
    {
    }
}
