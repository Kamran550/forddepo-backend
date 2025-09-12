<?php

namespace App\Repositories\Interfaces;

use App\Models\Warehouse;

interface WarehouseRepoInterface
{
    public function warehousesPaginate(array $filter);
}
