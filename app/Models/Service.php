<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasUuids;
    use HasFactory;

    public function products()
    {
        return $this->belongsToMany(Product::class, 'service_products')
            ->withPivot(['qty', 'price', 'total']);
    }

    public function repairs()
    {
        return $this->belongsToMany(Repair::class, 'service_repairs')
            ->withPivot(['employee_id', 'qty', 'price', 'total']);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
