<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasUuids;
    use HasFactory;

    protected $guarded = [];

    public function sale_products()
    {
        return $this->hasMany(SaleProduct::class);
    }
}
