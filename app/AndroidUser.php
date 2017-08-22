<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AndroidUser extends Model {

	protected $table = "users";

    protected $fillable = [
      "id",
      "fname",
      "lname",
      "phone",
      "birthday",
      "email"
    ];
}

 ?>
