<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $fillable = ['name', 'phone', 'amount','transaction_id','transaction_name','transaction_type','date','balance','user_id'];
}
