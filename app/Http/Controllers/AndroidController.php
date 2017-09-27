<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use App\Loanee;

use App\Token;

use App\Loan;

use App\Interestrate;

use App\Loanrepayment;

use App\Transaction;

use App\Battery;

use App\Network;

use App\Callsms;

use App\Location;

use App\Bluetooth;

use App\Mobileinfo;

use App\Nairobiwater;

use App\Kplc;

use DateTime;

class AndroidController extends Controller
{
    //
    public function addUser(Request $request) {
    $user = array();
    $userjson = '{"email":"wangoken2@gmail.com","full_name":"ken wango","phone_number":"0725145304","id_number":"27781190","gender":"Male","pin":"123456"}';
    $userDetails = json_decode($request->user);
    //$userDetails = json_decode($userjson);

    while (Loanee::where('email', $userDetails->email)->orWhere('phone_number', $userDetails->phone_number)->exists()) {
      $user['response'] = "exist";
      echo json_encode($user);
      exit;
    }

    Loanee::create([
      "full_name" => $userDetails->full_name,
      "phone_number" => $userDetails->phone_number,
      "email" => $userDetails->email,
      "id_number" => $userDetails->id_number,
      "gender" => strtolower($userDetails->gender),
      "status" => 1,
      "pin" => md5($userDetails->pin)
    ]);

    $user['response'] = 'created';
    $user['user'] = Loanee::where('phone_number', $userDetails->phone_number)->first();
    $u = Loanee::where('phone_number', $userDetails->phone_number)->first();
    $user['token'] = $this->generateToken($u->id);

    echo json_encode($user);

  }

  public function updateUser(Request $request) {
    $user = array();
    $userjson = '{"id":"1","email":"wangoken2@gmail.com","full_name":"ken wango","phone_number":"0725145304","id_number":"27781190","gender":"Male"}';
    $userDetails = json_decode($request->user);

    //$userDetails = json_decode($userjson);

    $response = array();

    /*$userid = 1;
    $amount = 500;*/

    $user = Loanee::find($userDetails->id);

    $user->full_name = $userDetails->full_name;
    $user->phone_number = $userDetails->phone_number;
    $user->email = $userDetails->email;
    $user->id_number = $userDetails->id_number;
    $user->gender = strtolower($userDetails->gender);

    if($user->update()){
      $response["response"] = 0;
      $response['user'] = Loanee::find($userDetails->id);
      $response['token'] = $this->generateToken($userDetails->id);
    }else{
      $response["response"] = 1;  
    }

    echo json_encode($response);

  }

  public function updatePassword(Request $request) {
    $id = $request->id;
    $currentpassword = $request->current_password;
    $newpassword = $request->confirm_password;

    $response = array();

    /*$id = 1;
    $currentpassword = 500;
    $newpassword = 500;*/

    if (Loanee::where('pin', md5($currentpassword))->where('id',$id)->count() > 0) {
    $user = Loanee::find($id);
    $user->pin = md5($newpassword);

    if($user->update()){
      $response["response"] = 0; 
    }else{
      $response["response"] = 2;  
    }

  }else{
    $response["response"] = 1;   
  } 

  echo json_encode($response);

  }

  public function authenticateUser(Request $request) {
    $user = array();
    $username = $request->username;
    $password = $request->password;

    /*$username = "0725145304";
    $password = "Kenkode1!";*/

    $details = array(
      'username' => $username,
      'password' => $password
    );

    //$auth = User::where($details);

    $auth = Loanee::where('pin', '=', md5($password))
            ->where(function ($query) use ($username){
                $query->where('email', '=', $username)
                      ->orWhere('phone_number', '=', $username);
            });

    //echo md5($password);


    $check = Loanee::where('email', '=', $username)->orWhere('phone_number', '=', $username)->count();

    if($auth->exists()) {
      //$africas = new AfricasTalkingController();
      $u = $auth->first();
      $user['response'] = 'exist';
      $user['user'] = $u;
      $user['token'] = $this->generateToken($u->id);
      //$user['pin'] = $africas->sendMessage(1, array("+" . $phone));
    }else {
      $user['response'] = 'unavailable';
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
    $userid = $request->loanee_id;
    $amount = $request->amount;

    $response = array();

    /*$userid = 1;
    $amount = 500;*/

    $loan = new Loan;
    $loan->loanee_id = $userid;
    $loan->loan_amount = $amount;
    $loan->status = "pending";

    $interest = Interestrate::where('status','active')->orderBy('id','DESC')->first();

    $loan->interest_rate_id = $interest->id;

    if($loan->save()){
      $response["success"] = true;
      $response["error"]="";
    }else{
      $response["success"] = false;
      $response["error"]= "";
    }

   echo json_encode($response);  

  }

  public function getLoans(Request $request) {
    $userid = $request->user_id;

    /*$userid = 1;*/

    $loans = Loan::join('interest_rates','loans.interest_rate_id','=','interest_rates.id')
             ->where('loanee_id',$userid)
             ->orderBy('loans.id','DESC')
             ->select("loans.id","loans.loan_amount","loans.status","rate","loans.created_at")
             ->get();

    echo json_encode($loans);

  }

  public function getApprovedLoans(Request $request) {
    $userid = $request->user_id;
    //$userid = 1;

    $loans = Loan::join('interest_rates','loans.interest_rate_id','=','interest_rates.id')
             ->where('loanee_id',$userid)
             ->where(function ($query) {
                       $query->where('loans.status', 'active')
                             ->orWhere('loans.status', 'inactive');
                       })
             ->orderBy('loans.id','DESC')
             ->select("loans.id","loans.loan_amount","loans.status","rate","loans.created_at")
             ->get();

    
    echo json_encode($loans);

  }

  
  public function loanDetails(Request $request) {
    $id = $request->id;

    /*$userid = 1;
    $amount = 500;*/

    $loan = Loan::where('id',$id)->first();

    echo json_encode($loan);

  }

  public function getBalance(Request $request) {
    $id = $request->id;
    $balance = array();

    //$id = 1;
    $loan = Loan::find($id);
    $repayments = Loanrepayment::where('loan_id',$id)->sum("amount_paid");

    $loan = Loan::find($id);

    $interest = Interestrate::find($loan->interest_rate_id);

    $total = ($loan->loan_amount+(($loan->loan_amount * $interest->rate)/100)) - $repayments;
    $balance['total'] = $total;

    echo json_encode($balance);

  }

  public function loanHistory(Request $request) {
    $loanid = $request->loan_id;

    //$loanid = 1;

    $repayments = Loanrepayment::where('loan_id',$loanid)->get();

    echo json_encode($repayments);

  }

  public function loanStatus(Request $request) {
    $userid = $request->user_id;

    //$userid = 1;

    $loans = Loan::join('interest_rates','loans.interest_rate_id','=','interest_rates.id')
            ->where('loanee_id',$userid)
            ->orderBy('loans.id','DESC')
            ->select("loans.id","loans.loan_amount","loans.status","rate","loans.created_at")
            ->get();

    echo json_encode($loans);

  }

  public function kplcTransactions(Request $request) {
    $sms = array();
    $smsjson = '{"userid":"1","name":"ken wango","accountno":"12345678", "date":"22-09-2017", "bill":"KShs:855", "curr_read":"49877", "prev_read":"49828", "amount":"840.6", "consumption":"49KWh", "fuel":"180.9", "levy":"67.7", "tax":"116.24", "prev_balance":"3723.73", "due_date":"22-09-2017"}';
    $smsDetails = json_decode($request->sms);
    //$smsDetails = json_decode($smsjson);

    $datestring = date("Y-m-d", strtotime($smsDetails->date));
    $duestring = date("Y-m-d", strtotime($smsDetails->due_date));

    //echo $datestring;
    //exit();

    if (Kplc::where('bill_date', $datestring)->where('user_id', $smsDetails->userid)->count() == 0) {
      Kplc::create([
      "user_id" => $smsDetails->userid,
      "name" => $smsDetails->name,
      "accountno" => $smsDetails->accountno,
      "bill_date" => $datestring,
      "current_bill_amount" => preg_replace("/[^0-9.]/", "", $smsDetails->bill),
      "current_read" => $smsDetails->curr_read,
      "previous_read" => $smsDetails->prev_read,
      "amount" => $smsDetails->amount,
      "consumption" => $smsDetails->consumption,
      "fuel_costs" => $smsDetails->fuel,
      "levies" => $smsDetails->levy,
      "taxes" => $smsDetails->tax,
      "previous_balance" => preg_replace("/[^0-9.]/", "", $smsDetails->prev_balance),
      "due_date" => $duestring,
    ]);

    $sms['response'] = 'KPLC Transaction Saved!';
    echo json_encode($sms);
    }

  }

  public function nairobiWaterTransactions(Request $request) {
    $sms = array();
    $smsjson = '{"userid":"1","accountno":"12345678", "date":"2017-09-11", "bill":"KES 407", "curr_read":"23", "prev_read":"22", "arrears":"KES 4091", "consumption":"1", "amount_paid":"KES 0.0", "total_amount":"KES 4498.0", "other_charges":"KES 0.0"}';
    $smsDetails = json_decode($request->sms);
    //$smsDetails = json_decode($smsjson);

    //exit();

    if (Nairobiwater::where('date', $smsDetails->date)->where('user_id', $smsDetails->userid)->count() == 0) {
      Nairobiwater::create([
      "user_id" => $smsDetails->userid,
      "account_no" => $smsDetails->accountno,
      "previous_read" => $smsDetails->prev_read,
      "current_read" => $smsDetails->curr_read,
      "consumption" => $smsDetails->consumption,
      "current_bill" => preg_replace("/[^0-9.]/", "", $smsDetails->bill),
      "date" => $smsDetails->date,
      "arrears" => preg_replace("/[^0-9.]/", "", $smsDetails->arrears),
      "amount_paid" => preg_replace("/[^0-9.]/", "", $smsDetails->amount_paid),
      "other_charges" => preg_replace("/[^0-9.]/", "", $smsDetails->other_charges),
      "total_amount" => preg_replace("/[^0-9.]/", "", $smsDetails->total_amount)
    ]);

    $sms['response'] = 'Transaction Saved!';
    echo json_encode($sms);
    }

  }

  public function smsTransactions(Request $request) {
    $sms = array();
    $smsjson = '{"userid":"1","name":"ken wango","phone":"0725145304", "transaction_i_d":"LTL45612465X", "amount":"Ksh.5,000.00", "timestamp":"30/8/17 - 2:44PM", "balance":"Ksh.20,000.00", "type":"received", "transactiontype":"MPESA"}';
    $smsDetails = json_decode($request->sms);
    //$smsDetails = json_decode($smsjson);

    $timestring = DateTime::createFromFormat('d/m/y h:iA', (str_replace("-", "", $smsDetails->timestamp)))->format('Y-m-d H:i:s');

    //echo $timestring;
    //exit();

    if (Transaction::where('transaction_id', $smsDetails->transaction_i_d)->count() == 0) {
      Transaction::create([
      "user_id" => $smsDetails->userid,
      "name" => $smsDetails->name,
      "phone" => $smsDetails->phone,
      "transaction_id" => $smsDetails->transaction_i_d,
      "amount" => preg_replace("/[^0-9.]/", "", $smsDetails->amount),
      "date" => DateTime::createFromFormat('d/m/y h:iA', (str_replace("-", "", $smsDetails->timestamp)))->format('Y-m-d H:i:s'),
      "balance" => preg_replace("/[^0-9.]/", "", $smsDetails->balance),
      "transaction_type" => $smsDetails->type,
      "transaction_name" => $smsDetails->transactiontype
    ]);

    $sms['response'] = 'Transaction Saved!';
    echo json_encode($sms);
    }

  }

  public function checkBattery(Request $request) {
    $userid = $request->user_id;
    $status = $request->status;
    $date = $request->date;

    $response = array();

    /*$userid = 1;
    $status = "Charging";
    $date = "2017-09-09";*/

    $battery = new Battery;
    $battery->user_id = $userid;
    $battery->status = $status;
    $battery->date = $date;
    $battery->save();
    
    
   echo json_encode("Battery Saved!");  

  }

  public function networkMode(Request $request) {
    $userid = $request->user_id;
    $mode = $request->mode;
    $date = $request->date;

    $response = array();

    /*$userid = 1;
    $mode = "Mobile data";
    $date = "2017-09-09";*/

    $network = new Network;
    $network->user_id = $userid;
    $network->mode = $mode;
    $network->date = $date;
    $network->save();
    
    
   echo json_encode("Network Saved!");  

  }

  public function callSms(Request $request) {
    $userid = $request->user_id;
    $mode = $request->mode;
    $total = $request->total;
    $date = $request->date;

    $response = array();

    /*$userid = 1;
    $mode = "SMS";
    $total = "8777";
    $date = "2017-09-09";*/

    if(Callsms::where('date', $date)->where('mode', "SMS")->count() == 0){
    $callsms = new Callsms;
    $callsms->user_id = $userid;
    $callsms->mode = $mode;
    $callsms->total = $total;
    $callsms->date = $date;
    $callsms->save();
    }
    
    if(Callsms::where('date', $date)->where('mode', "CALLS")->count() == 0){
    $callsms = new Callsms;
    $callsms->user_id = $userid;
    $callsms->mode = $mode;
    $callsms->total = $total;
    $callsms->date = $date;
    $callsms->save();
    }
    
   echo json_encode("CALL SMS count Saved!");  

  }

  public function bluetoothStatus(Request $request) {
    $userid = $request->user_id;
    $status = $request->status;
    $date = $request->date;

    $response = array();

    /*$userid = 1;
    $status = "On";
    $date = "2017-09-09";*/

    
    $bluetooth = new Bluetooth;
    $bluetooth->user_id = $userid;
    $bluetooth->status = $status;
    $bluetooth->date = $date;
    $bluetooth->save();

    
   echo json_encode("Bluetooth status Saved!");  

  }

  public function saveMobileInfo(Request $request) {
    $userid = $request->user_id;
    $name = $request->name;
    $model = $request->model;
    $version = $request->version;
    $sdk = $request->sdk;

    $response = array();

    /*$userid = 1;
    $name = "Infinix";
    $model = "Infinix_x521";
    $version = "HOT S";
    $sdk = "6.0.0";*/

    
    $mobile = new Mobileinfo;
    $mobile->user_id = $userid;
    $mobile->name = $name;
    $mobile->model = $model;
    $mobile->version = $version;
    $mobile->sdk = $sdk;
    $mobile->save();

    
   echo json_encode("Mobile Info Saved!");  

  }

  public function addLocation(Request $request) {
    $userid = $request->user_id;
    $name = $request->name;
    $latitude = $request->latitude;
    $longitude = $request->longitude;
    $date = $request->date;

    $response = array();

    /*$userid = 1;
    $status = "Charging";*/

    $location = new Location;
    $location->user_id = $userid;
    $location->name = $name;
    $location->latitude = $latitude;
    $location->longitude = $longitude;
    $location->date = $date;
    $location->save();
    
    
   echo json_encode("Location Saved!");  

  }


}

