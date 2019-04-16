<?php

function send_status($status,$message) {
        $arr = array('success' => $status, 'message' => $message);
        echo json_encode($arr,true);
}
        
// Importing DBConfig.php file.
include 'DBConfig.php';

// Creating connection.
$conn = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

 // Getting the received JSON into $json variable.
 //$json = file_get_contents('php://input');
 
 //json created for testing purposes
  $json = '{
	 "email": "jeff1@gmail.com",
	 "pword": "1234"
 }';

 if($conn -> connect_error){
        send_status(false,"Connection failed.");
        die("Connection failed: " . $conn->connect_error);
 }
$pass='1234';
$check=password_hash($pass,PASSWORD_BCRYPT);
//echo $check;
// decoding the received JSON and store into $obj variable
$obj = json_decode($json,true);
$email = $obj['email'];
$pword = $obj['pword'];
if(empty($email)) {
        send_status(false, "Please enter an email.");
}
else if(empty($obj['pword'])) {
        send_status(false, "Please enter a password.");
}
else {
        // all fields entered successfully
        if(!$user = mysqli_query($conn, "SELECT Email,password FROM Login WHERE email = '$email'")) {
                // query failed
                send_status(false,"Query failed.");
        }
        else if(mysqli_num_rows($user) === 0) {
                // invalid email
                send_status(false,"No user exists for this email.");
        }
        else if(password_verify($pword,mysqli_fetch_row($user)[1]))
		 {
                // pword valid
                send_status(true,"Welcome.");
        }
				}
mysqli_close($conn);
?>

