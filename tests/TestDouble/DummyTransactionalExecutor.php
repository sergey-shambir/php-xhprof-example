<?php
declare(strict_types=1);

namespace App\Tests\TestDouble;

use App\Common\Database\TransactionalExecutorInterface;

class DummyTransactionalExecutor implements TransactionalExecutorInterface
{
    public function doWithTransaction(\Closure $action)
    {
        return $action();
    }
}
