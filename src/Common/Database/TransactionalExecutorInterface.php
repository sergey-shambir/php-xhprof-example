<?php
declare(strict_types=1);

namespace App\Common\Database;

interface TransactionalExecutorInterface
{
    /**
     * Метод выполняет переданную функцию внутри открытой транзакции, в конце вызывая COMMIT либо ROLLBACK.
     *
     * @param \Closure $action
     * @return mixed|void
     * @throws null
     */
    public function doWithTransaction(\Closure $action);
}

