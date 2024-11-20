<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasUuids;
    use HasFactory;

    protected $guarded = [];


    public function user()
    {
        return $this->hasMany(User::class);
    }
}
