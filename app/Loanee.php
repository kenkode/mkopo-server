<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loanee extends Model
{
    //
    protected $fillable = [
        'full_name', 'email', 'pin','phone_number','id_number','gender','status'
    ];
}
