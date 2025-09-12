<?php

namespace App\Services\WarehouseService;

use App\Helpers\ResponseError;
use App\Models\Category;
use App\Models\Product;
use App\Models\Settings;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Services\CoreService;
use App\Services\Interfaces\ProductServiceInterface;
use App\Traits\SetTranslations;
use DB;
use Exception;
use Throwable;

class WarehouseService extends CoreService
{
    use SetTranslations;

    protected function getModelClass(): string
    {
        return Warehouse::class;
    }

    /**
     * @param array $data
     * @return array
     */
}
