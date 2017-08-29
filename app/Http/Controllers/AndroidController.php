<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use App\Token;

use App\Loan;

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
      "password" => md5($userDetails->password)
    ]);

    $user['status'] = 'created';
    $user['user'] = User::where('phone', $userDetails->phone)->first();
    $u = User::where('phone', $userDetails->phone)->first();
    $user['token'] = $this->generateToken($u->id);

    echo json_encode($user);

  }

  public function authenticateUser(Request $request) {
    $user = array();
    $username = $request->username;
    $password = $request->password;

    $username = "0725145304";
    $password = "Kenkode1!";

    $details = array(
      'username' => $username,
      'password' => $password
    );

    //$auth = User::where($details);

    $auth = User::where('password', '=', md5($password))
            ->where(function ($query) use ($username){
                $query->where('email', '=', $username)
                      ->orWhere('phone', '=', $username);
            });

    echo md5($password);


    $check = User::where('email', '=', $username)->orWhere('phone', '=', $username)->count();

    if($auth->exists()) {
      //$africas = new AfricasTalkingController();
      $u = $auth->first();
      $user['status'] = 'exist';
      $user['user'] = $u;
      $user['token'] = $this->generateToken($u->id);
      //$user['pin'] = $africas->sendMessage(1, array("+" . $phone));
    }else {
      $user['status'] = 'unavailable';
    }
    
    echo json_encode($user);
  }

  public function validateUser(Request $request) {
    $user = $request->user;

    if(User::where('id', $user)->exists()) {
      echo "E";
    }else {
      "DNE";
    }

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

  public function applyLoan(Request $request) {
    $userid = $request->user_id;
    $amount = $request->amount;

    /*$userid = 1;
    $amount = 500;*/

    $loan = new Loan;
    $loan->user_id = $userid;
    $loan->amount = $amount;
    $loan->status = 1;
    $loan->save();

    echo "Loan successfully approved";

  }

  public function getLoans(Request $request) {
    $userid = $request->user_id;

    /*$userid = 1;
    $amount = 500;*/

    $loans = Loan::where('user_id',$userid)->get();

    echo json_encode($loans);

  }

  public function loanHistory(Request $request) {
    $userid = $request->user_id;

    /*$userid = 1;
    $amount = 500;*/

    $loan = Loan::where('user_id',$userid)->count();

    echo json_encode($loan);

  }

  public function loanStatus(Request $request) {
    $userid = $request->user_id;

    /*$userid = 1;
    $amount = 500;*/

    $loans = Loan::where('user_id',$userid)->get();

    echo json_encode($loans);

  }

}

