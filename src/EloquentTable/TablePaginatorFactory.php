<?php

namespace Scuti\EloquentTable;

use Illuminate\Pagination\Factory;

class TablePaginatorFactory extends Factory
{
    public function make(array $items, $total, $perPage = null)
    {
        $paginatedInstance = new TablePaginator($this, $items, $total, $perPage);

        return $paginatedInstance->setupPaginationContext();
    }
}