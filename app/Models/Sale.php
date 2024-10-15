<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasUuids;
    use HasFactory;

    protected $guarded = [];

    public function sale_products()
    {
        return $this->hasMany(SaleProduct::class,);
    }

    public function jurnals()
    {
        return $this->morphMany(Jurnal::class, 'transactable');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
