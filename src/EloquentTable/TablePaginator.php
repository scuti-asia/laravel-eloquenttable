<?php

namespace Scuti\EloquentTable;

use Illuminate\Pagination\Paginator;

class TablePaginator extends Paginator
{
    use TableTrait;
}