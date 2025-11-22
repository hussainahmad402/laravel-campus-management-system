<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $fillable = ['fee_id', 'amount', 'payment_date', 'payment_method'];

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }
}

