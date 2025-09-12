<?php

namespace App\Repositories\WarehouseRepository;

use App\Exports\ProductReportExport;
use App\Exports\StockExport;
use App\Exports\StockReportExport;
use App\Helpers\ResponseError;
use App\Http\Resources\ProductReportResource;
use App\Jobs\ExportJob;
use App\Models\Language;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\UserActivity;
use App\Models\Warehouse;
use App\Repositories\CoreRepository;
use App\Repositories\Interfaces\ProductRepoInterface;
use App\Repositories\Interfaces\WarehouseRepoInterface;
use App\Repositories\ReportRepository\ChartRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Throwable;

class WarehouseRepository extends CoreRepository implements WarehouseRepoInterface
{
    protected function getModelClass(): string
    {
        return Warehouse::class;
    }

    public function warehousesPaginate(array $filter = [])
    {
        $warehouse = $this->model();

        $query = $warehouse->query();

        // Always filter only active warehouses
        $query->where('is_active', true);
        return $query->paginate();
    }
}
