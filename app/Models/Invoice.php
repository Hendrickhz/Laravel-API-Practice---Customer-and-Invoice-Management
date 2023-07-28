<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'billed_date', 'amount', 'paid_date', 'status'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
