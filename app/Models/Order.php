<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_code', 'amount', 'status'];
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
