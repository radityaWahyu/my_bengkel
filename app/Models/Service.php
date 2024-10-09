<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasUuids;
    use HasFactory;


    protected $guarded = [];


    public function service_repairs()
    {
        return $this->hasMany(ServiceRepair::class,);
    }

    public function service_products()
    {
        return $this->hasMany(ServiceProduct::class,);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function jurnals()
    {
        return $this->morphMany(Jurnal::class, 'transactable');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
