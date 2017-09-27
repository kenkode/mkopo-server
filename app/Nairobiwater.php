<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nairobiwater extends Model
{
    //
    protected $table = "nairobi_water";

    protected $fillable = [
        'account_no', 'date','current_bill','current_read','previous_read','arrears',
        'consumption','amount_paid','other_charges','total_amount','user_id'
    ];
}
