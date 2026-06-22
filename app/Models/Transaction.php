<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['transaction_code', 'subtotal', 'tax', 'total', 'payment_method',];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'transaction_items')
                    ->withPivot('quantity', 'price_at_transaction')
                    ->withTimestamps();
    }
}
