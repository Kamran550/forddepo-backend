<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WarehouseResource;
use App\Repositories\Interfaces\WarehouseRepoInterface;
use App\Services\WarehouseService\WarehouseService;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class WarehouseController extends Controller
{

    use Loggable;

    private WarehouseRepoInterface $warehouseRepository;

    /**
     * @param WarehouseRepoInterface $warehouseRepository
     */
    public function __construct(WarehouseRepoInterface $warehouseRepository)
    {
        parent::__construct();
        $this->warehouseRepository = $warehouseRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Log::info('index');
        $warehouses = $this->warehouseRepository->warehousesPaginate($request->all());
        $myWare = WarehouseResource::collection($warehouses);
        \Log::info('menim warehousem:', ['w:', $myWare]);
        return WarehouseResource::collection($warehouses);
    }





    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
