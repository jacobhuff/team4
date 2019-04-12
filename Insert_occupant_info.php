<?php

function send_status($status,$message) {//function that sends either a failure status if there is an issue or a success status
        $arr = array('success' => $status, 'message' => $message);
        echo json_encode($arr,true);
}

 // Testing Json file for inserting into occupant info table in the database
 $json = '{
	 "First_name": "J2",
	 "Seatbelt": "Y",
	 "Impairment": "N",
	 "Address": "Somewhere BLVD",
	 "DOB": "04/25/82",
	 "Phone_no": "1234567",
	 "Driver":"Y",
	 "Case_Number":"5",
	 "Last_name":"Z2"
 }';

// decoding the received JSON and store into $obj variable
//go through the $obj variable and store each individual value into a variable
$obj = json_decode($json,true);
$fname = $obj['First_name'];
$seatbelt = $obj['Seatbelt'];
$Impariment = $obj['Impairment'];
$Address = $obj['Address'];
$DOB = $obj['DOB'];
$Phone_no= $obj['Phone_no'];
$Driver=$obj['Driver'];
$case_number=$obj['Case_Number'];
$last_name=$obj['Last_name'];
$salt=(bin2hex(random_bytes(10)));//generates a random string with a length of 10, this will be used to salt your database
									//this is appended to the start of whatever variable needs to be encrypted in order to 
									//not have the same encrypted string appear twice if people have the same address or DOB
									//this is also inserted into a column in the datbase to allow you to decrypt 

// Importing DBConfig.php file.
include 'DBConfig.php';
$conn = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 if($conn -> connect_error){//checking if you can connect to the database
        send_status(false,"Connection failed.");
        die("Connection failed: " . $conn->connect_error);
 }
if(empty($fname)) {//if statements checking for empty values in first name,address,DOB,and Phone because we're going to encrypt the last three and we want to make
					//sure the json file is correctly being decoded
        send_status(false,"Please enter a first name.");
}
else if(empty($Address)) {
        send_status(false,"Please enter an address.");
}
else if(empty($DOB)) {
        send_status(false, "Please enter Date of Birth.");
}
else if(empty($Phone_no)) {
        send_status(false, "Please enter a Phone number.");
}
else{
	//case number is a primary key in this table and you should check if there already isn't a case with that case number 
	//if so output failure status to user
	if(mysqli_num_rows(mysqli_query($conn, "SELECT Case_Number FROM OCCUPANT_INFO WHERE Case_Number= '$case_number'")) >0){
		send_status(false,"Case_Number already exists, please try another.");	
	}
	else{
		//uses AES_ENCRYPT on Address,DOB, and phone_no by concating the salt to the entered value and then using the KEY 'key1234' for encryption. This KEY is just for testing purposes
		//Typical format for AES_ENCRYPT(value,KEY).
        $insert_query="INSERT INTO OCCUPANT_INFO (First_Name,Seatbelt,Impairment,Address,DOB,Phone_No,Driver,Case_number,Last_Name,Salt) VALUES ('$fname','$seatbelt','$Impariment',AES_ENCRYPT(CONCAT('$salt','$Address'),'key1234'),AES_ENCRYPT(CONCAT('$salt','$DOB'),'key1234'),AES_ENCRYPT(CONCAT('$salt','$Phone_no'),'key1234'),'$Driver','$case_number','$last_name','$salt')";
        if(mysqli_query($conn, $insert_query)) {//if query was successful output a success message
                // successfully inserted
                send_status(true,"Your case has been saved!");
        }
        else {
                // insertion failed
            send_status(false,"failed to insert into database.");
        }
    }
}
mysqli_close($conn);//close the connection
?>

	
	