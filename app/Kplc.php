<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kplc extends Model
{
    //
    protected $table = "kplc";

    protected $fillable = [
        'name', 'accountno', 'bill_date','current_bill_amount','current_read','previous_read','amount',
        'consumption','fuel_costs','levies','taxes','previous_balance','due_date','user_id'
    ];
}
