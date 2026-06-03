<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    use HasFactory;

    protected $table = 'stock_out';

    protected $fillable = [
        'product_id',
        'quantity',
        'date',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
