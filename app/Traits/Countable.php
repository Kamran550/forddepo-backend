<?php

namespace App\Traits;

use App\Helpers\ResponseError;
use App\Models\Language;
use App\Models\Product;
use App\Models\Stock;
use App\Services\ProductService\ProductService;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @property Collection|Stock[] $stocks
 * @property int|null $stocks_count
 */
trait Countable
{
    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function addInStock(array $data): void
    {
        Log::info('Trait is basinda');
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');
        $lang   = request('lang', $locale);
        try {
            DB::transaction(function () use ($data, $lang) {

                $extras = data_get($data, 'extras', []);

                if (data_get($data, 'delete_ids')) {
                    $this->stocks()->whereIn('id', data_get($data, 'delete_ids'))->delete();
                }

                $notDeleteIds = [];
                $addons = [];

                foreach ($extras as $i => $item) {

                    $ids = data_get($item, 'ids');

                    // when trying to add duplicate stock
                    foreach ($extras as $k => $extra) {

                        $duplicateIds = data_get($extra, 'ids', []);

                        if (
                            $i !== $k && is_array($ids)
                            && is_array($duplicateIds)
                            && empty(array_diff($ids, $duplicateIds))
                        ) {

                            throw new Exception(
                                __('errors.' . ResponseError::ERROR_119, locale: $lang),
                                119
                            );
                        }
                    }

                    $warehouseData = data_get($item, 'warehouse');

                    $warehouseId = data_get($warehouseData, 'value');
                    Log::info('Trait is basinda id ware:', ['id:', $warehouseId]);
                    Log::info('item trait:', ['item:', $item]);
                    if (data_get($item, 'stock_id')) {
                        Log::info('trait update');
                        $stock = Stock::find(data_get($item, 'stock_id'));

                        $stock->update([
                            'countable_type' => Product::class,
                            'price'          => data_get($item, 'price'),
                            'quantity'       => data_get($item, 'quantity'),
                            'warehouse_id'   => $warehouseId, // Warehouse ID əlavə edin
                            'sku'            => data_get($item, 'sku'),
                            'addon'          => $this->addon
                        ]);
                    } else if ($this->addon) {
                        Log::info('trait updateOrCreate');
                        $stock = $this->stocks()->updateOrCreate([
                            'countable_id'   => $this->id,
                            'countable_type' => Product::class,
                        ], [
                            'addon'          => true,
                            'price'          => data_get($item, 'price'),
                            'quantity'       => data_get($item, 'quantity'),
                            'warehouse_id'   => $warehouseId, // Warehouse ID əlavə edin
                            'sku'            => data_get($item, 'sku'),
                        ]);
                    } else {
                        Log::info('trait create');

                        $stock = $this->stocks()->create([
                            'countable_id'   => $this->id,
                            'countable_type' => Product::class,
                            'warehouse_id'   => $warehouseId, // Warehouse ID əlavə edin
                            'addon'          => $this->addon,
                            'price'          => data_get($item, 'price'),
                            'quantity'       => data_get($item, 'quantity'),
                            'sku'            => data_get($item, 'sku'),
                        ]);
                    }

                    if (data_get($data, 'inventory_items.0')) {
                        $stock->inventoryItems()->delete();
                        foreach ($data['inventory_items'] as $inventoryItem) {
                            $stock->inventoryItems()->create($inventoryItem);
                        }
                    }

                    if (empty($ids)) {
                        DB::table('stock_extras')->where('stock_id', $stock->id)->delete();
                    }

                    if (empty(data_get($item, 'addons'))) {
                        $stock->addons()->delete();
                    }

                    if (data_get($data, 'images.0')) {
                        $stock->uploads(data_get($data, 'images'));
                    }

                    if (is_array($ids)) {
                        $stock->stockExtras()->sync($ids);
                    }

                    if (is_array(data_get($item, 'addons')) && count(data_get($item, 'addons')) > 0) {

                        if ($item['addons'][0] == 'all') {

                            if (count($addons) === 0) {
                                $addons = Product::with('stock')
                                    ->whereHas('stock', fn($q) => $q->where('quantity', '>', 0))
                                    ->where('addon', true)
                                    ->whereIn('status', [Product::PENDING, Product::PUBLISHED])
                                    ->where('shop_id', $data['shop_id'])
                                    ->pluck('id')
                                    ->toArray();
                            }

                            $item['addons'] = $addons;
                        }

                        (new ProductService)->syncAddons($stock, data_get($item, 'addons'));
                    }

                    $notDeleteIds[] = $stock->id;
                }

                if (count($notDeleteIds) > 0) {
                    $this->fresh(['stocks'])->stocks()->whereNotIn('id', $notDeleteIds)->delete();
                }
            });
        } catch (Throwable $e) {
            throw new Exception(
                $e->getMessage() . ' | ' . $e->getFile() . ' | ' . $e->getLine(),
                400
            );
        }
    }

    /**
     * @return HasMany
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'countable_id');
    }
}
