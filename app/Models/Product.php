<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'category_id',
        'supplier_id',
        'quantity',
        'price',
        'reorder_level',
        'warehouse_location',
        'image',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'reorder_level' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getTotalValueAttribute(): float
    {
        return $this->quantity * $this->price;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-success-custom',
            'inactive' => 'bg-warning-custom',
            'discontinued' => 'bg-danger-custom',
            default => 'bg-secondary',
        };
    }
}
