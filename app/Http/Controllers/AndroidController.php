<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use App\Token;

class AndroidController extends Controller
{
    //
    public function addUser(Request $request) {
    $user = array();
    $userjson = '{"email":"wangoken2@gmail.com","name":"ken wango","phone":"0725145304","password":"123456"}';
    $userDetails = json_decode($request->user);
    //$userDetails = json_decode($userjson);

    while (User::where('email', $userDetails->email)->orWhere('phone', $userDetails->phone)->exists()) {
      $user['status'] = "exist";
      echo json_encode($user);
      exit;
    }

    User::create([
      "name" => $userDetails->name,
      "phone" => $userDetails->phone,
      "email" => $userDetails->email,
      "password" => $userDetails->password
    ]);

    $user['status'] = 'created';
    $user['user'] = User::where('phone', $userDetails->phone)->first();
    $u = User::where('phone', $userDetails->phone)->first();
    $user['token'] = $this->generateToken($u->id);

    echo json_encode($user);

  }

  public function generateToken($user) {
    $gen = strtotime(date("D M j G:i:s T Y"));
    $prevToken = Token::where('user_id', $user)->where('status', 0);
    if($prevToken->exists()) {
      $prevToken->update([
        'status' => 1
      ]);
    }

    $token = Bcrypt(sha1($gen));
    Token::create([
      "id" => $token,
      "user_id" => $user,
      "status" => 0
    ]);
    return $token;
  }

}

