<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'serial_number',
        'value',
        'purchase_date',
        'condition',
        'department_id',
        'description',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
