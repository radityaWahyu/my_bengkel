<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProduct extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = "service_products";
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
