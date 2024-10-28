<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Item extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}