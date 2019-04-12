<?php

function send_status($status,$message) {
        $arr = array('success' => $status, 'message' => $message);
        echo json_encode($arr,true);
}

 // json file for testing purposes, simply use the case number used when inserting 
 $json = '{
	"case number": "4"	
 }';

// decoding the received JSON and store into $obj variable
$obj = json_decode($json,true);
$case_number=$obj['case number'];

// Importing DBConfig.php file.
include 'DBConfig.php';
$conn = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 if($conn -> connect_error){//check if connection is successful
        send_status(false,"Connection failed.");
        die("Connection failed: " . $conn->connect_error);
 }
 
 //uses the AES_DECRYPT to decrypt the inputted value if case number is in database.Have to use the same key used in encrypting it. 
 //The replace function is used after AES_DECRYPT is carried out. The replace function replaces the salt in the string returned by AES_DECRYPT
 //with nothing, to allow the user to see the original inputted value.
 if(!$case = mysqli_query($conn, "SELECT First_name,Seatbelt,Impairment,replace(AES_DECRYPT(Address,'key1234'),salt,''),replace(AES_DECRYPT(DOB,'key1234'),salt,''),replace(AES_DECRYPT(Phone_no,'key1234'),salt,''),Driver,Case_number,Last_name FROM OCCUPANT_INFO WHERE Case_number = '$case_number'")) {
                // query failed
                send_status(false,"Query failed.");
        }
else{
	$row=mysqli_fetch_array($case);//gets array of query
	echo "First Name ".$row[0]."<br>";//Simply outputs certain values from the array to verify the accuracy of the encryption
	echo "Seatbelt ". $row[1]. "<br>";
	echo "Impairment". $row[2]. "<br>";
	echo "Address ".$row[3]."<br>";
	echo "DOB ".$row[4]."<br>";
	echo "Phone number ".$row[5]."<br>";
	echo "Driver ".$row[6]."<br>";
	}
	
mysqli_close($conn);
?>