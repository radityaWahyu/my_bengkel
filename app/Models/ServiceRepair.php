<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRepair extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = "service_repairs";
    protected $guarded = [];

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
