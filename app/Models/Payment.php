<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'pledge_id',
        'small_group_offering_id',
        'transaction_id', // Flutterwave ID
        'reference', // Our unique ref
        'status', // succeeded, pending, failed
        'amount',
        'currency',
        'category',
        'description',
        'phone_number',
        'network',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function pledge()
    {
        return $this->belongsTo(Pledge::class);
    }

    public function smallGroupOffering()
    {
        return $this->belongsTo(SmallGroupOffering::class);
    }
}
