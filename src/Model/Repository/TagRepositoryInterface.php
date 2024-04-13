<?php
declare(strict_types=1);

namespace App\Model\Repository;

interface TagRepositoryInterface
{
    /**
     * @param string[] $tags
     * @return void
     */
    public function addTags(array $tags): void;
}
