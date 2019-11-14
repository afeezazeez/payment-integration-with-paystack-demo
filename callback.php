<?php
session_start();
$curl = curl_init();
$reference = isset($_GET['reference']) ? $_GET['reference'] : '';
if(!$reference){
  die('No reference supplied');
}

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    "accept: application/json",
    "authorization: Bearer sk_test_edff749db7d9a1b9f9c00228d4e79867c17f2708",
    "cache-control: no-cache"
  ],
));

$response = curl_exec($curl);
$err = curl_error($curl);

if($err){
    // there was an error contacting the Paystack API
  die('Curl returned error: ' . $err);
}

$tranx = json_decode($response);

if(!$tranx->status){
  // there was an error from the API
  die('API returned error: ' . $tranx->message);
}

if('success' == $tranx->data->status){
    $con= mysqli_connect('localhost','root','','paystack');
  	$message='';
    $sender_id=$_SESSION['sender_id'];
	$recipient_id=$_SESSION['recipient_id'];
	$amount=$_SESSION['amount']*0.01;
	$query="
			UPDATE users SET acct_bal = (acct_bal-$amount) WHERE id='$sender_id'";

	$dbc=mysqli_query($con,$query);
if ($dbc) {
	echo "done";
}


$sql="
			UPDATE users SET acct_bal = (acct_bal+$amount) WHERE id='$recipient_id'";

	$result=mysqli_query($con,$sql);
if ($result) {
	echo "done";
}
session_destroy();
  
}



?>