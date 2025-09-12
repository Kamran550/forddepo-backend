<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'manager',
        'phone',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, Stock::class, 'warehouse_id', 'id', 'id', 'product_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    // Helper methods
    public function getTotalProductsCount()
    {
        return $this->stocks()->count();
    }

    public function getTotalStockQuantity()
    {
        return $this->stocks()->sum('quantity');
    }

    public function getLowStockCount($threshold = 10)
    {
        return $this->stocks()->where('quantity', '<=', $threshold)->count();
    }

    public function getOutOfStockCount()
    {
        return $this->stocks()->where('quantity', 0)->count();
    }

    // Static methods
    public static function getBaku()
    {
        return static::where('code', 'BAK')->first();
    }

    public static function getGanja()
    {
        return static::where('code', 'GAN')->first();
    }

    public static function getActiveWarehouses()
    {
        return static::where('is_active', true)->get();
    }
}
