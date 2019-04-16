<?php

function send_status($status,$message) {
        $arr = array('success' => $status, 'message' => $message);
        echo json_encode($arr,true);
}

 // Getting the received JSON into $json variable.
 //this json file is for testing purposes
 $json = '{                    
	 "uname": "Jeff",
	 "fname": "jeff",
	 "lname": "C",
	 "email": "jeff1@gmail.com",
	 "pword": "1234",
	 "confirmpword": "1234"
 }';

// decoding the received JSON and store into $obj variable
$obj = json_decode($json,true);
$uname = $obj['uname'];
$fname = $obj['fname'];
$lname = $obj['lname'];
$email = $obj['email'];
$pword = $obj['pword'];
$confirmpword= $obj['confirmpword'];

// Importing DBConfig.php file.
include 'DBConfig.php';

// Creating connection.
$conn = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 if($conn -> connect_error){
        send_status(false,"Connection failed.");
        die("Connection failed: " . $conn->connect_error);
 }

if(empty($uname)) {
        send_status(false,"Please enter a username.");
}
else if(empty($fname)) {
        send_status(false,"Please enter a first name.");
}
else if(empty($lname)) {
        send_status(false, "Please enter a last name.");
}
else if(empty($email)) {
        send_status(false, "Please enter an email.");
}
else if(empty($obj['pword'])) {
        send_status(false, "Please enter a password.");
}
else if($obj['pword'] != $obj['confirmpword']) {
        send_status(false, "Please check your password confirmation.");
}
else {
        // all fields entered successfully
    if(mysqli_num_rows(mysqli_query($conn, "SELECT Username FROM Login WHERE username = '$uname'")) >0) {
            // check username availability
            send_status(false,"Username taken, please try another.");
    }
    else if(mysqli_num_rows(mysqli_query($conn, "SELECT email FROM Login WHERE email = '$email'")) >0) {
            // check email availability
            send_status(false,"Email in use, please try another.");
    }
    else {
            // username and email valid, insert user
	$hashPword=password_hash($pword,PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO Login (Username,Password,Email,FirstName,LastName) VALUES ('$uname','$hashPword','$email','$fname','$lname')";

        if(mysqli_query($conn, $insert_query)) {
                // successfully inserted
                send_status(true,"You have been registered! Please log in.");
        }
        else {
                // insertion failed
            send_status(false,"Registration failed, please try again.");
        }
    }
}
mysqli_close($conn);
?>
