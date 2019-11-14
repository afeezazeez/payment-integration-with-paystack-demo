<?php
session_start();
$curl = curl_init();

$con= mysqli_connect('localhost','root','','paystack');
if (isset($_POST['submit'])) {
	$sender_id=$_POST['sender'];
	$recipient_id=$_POST['recipient'];
	$amount=$_POST['amount'];
	$email="azeez@yahoo.com";
	
	$_SESSION['sender_id']=$sender_id;
	$_SESSION['recipient_id']=$recipient_id;
	$_SESSION['amount']=$amount;
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode([
    'amount'=>$amount,
    'sender_id'=>$sender_id,
     'email'=>$email,
    'recipient_id'=>$recipient_id,
    'callback_url' => $callback_url
  ]),
  CURLOPT_HTTPHEADER => [
    "authorization: Bearer sk_test_edff749db7d9a1b9f9c00228d4e79867c17f2708", //replace this with your own test key
    "content-type: application/json",
    "cache-control: no-cache"
  ],
));

$response = curl_exec($curl);
$err = curl_error($curl);

if($err){
  // there was an error contacting the Paystack API
  die('Curl returned error: ' . $err);
}

$tranx = json_decode($response, true);

if(!$tranx->status){
  // there was an error from the API
  print_r('API returned error: ' . $tranx['message']);
}

// comment out this line if you want to redirect the user to the payment page
print_r($tranx);
header('Location: ' . $tranx['data']['authorization_url']);


}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Paystack</title>
</head>
<body>
		<form method="post" action="">
	From: <select name="sender">
		<?php
		$query="select * from users";
		$dbc=mysqli_query($con,$query);
		while ($row=mysqli_fetch_array($dbc)) {
		?>
	
		<option value="<?=$row['id']?>"><?=$row['name']?></option>
		<?php
			}
		?>
	</select><br><br>
	To: <select name="recipient">
		<?php
		$query="select * from users";
		$dbc=mysqli_query($con,$query);
		while ($row=mysqli_fetch_array($dbc)) {
		?>
	
		<option value="<?=$row['id']?>"><?=$row['name']?></option>
		<?php
			}
		?>
	</select><br><br>

	Amount to transfer:<input type="number" name="amount"><br><br>

	<button type="submit" name="submit">Transfer Money</button>
	</form>


</body>
</html>