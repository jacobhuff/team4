DATABASE TEAM - SPRING 2019 

Merging Vehicle Specs and Vehicle Specs Additional

RESULT-VEHICLE_SPECS_MERGE should be used as the reference table for vehicle specs in future programs needing that info



Defects- multiple duplicate copies in Additional table(RESOLVED by using select distinct * to create a new copy)
	reduced rows to ~21,000 from ~29,000

	- Cases where make,model name,and model year
	were the same as well as most other column values except for curbweight being different in Additional table.
	The reason for this is that the files imported for the additional table were labeled with a year between
	1971-2018 but just because it has a year labeled didn't mean that it only had car specs for that year.
	RESOLVED BY- reimporting the files in order from 1971-2018 and having an ID column that auto increments.
	We can then create a new table with select the max ID as well as all the other columns and grouping by make,model and year.
	reduced number of rows to ~15,000
	
Extracted # of doors,Drive info from Model name if avaliable
Merged the two tables on make,model,height,width,length,wheelbase,doors,drive and curbweight.Multiple merges tables were done to account for 
missing values in some columns. They were then compared to one another.
Best one was
FROM VEHICLE_SPECS_ADDITIONAL_Test_copy VA #use VA to identify the vehicle additional distinct table
INNER JOIN VEHICLE_SPECS_copy VS # use vs to identify vehicle specs table
ON VA.Make=VS.model_make_id # same makes
AND VA.YEAR=VS.model_year
AND VA.Model LIKE CONCAT('%',VS.model_name,'%') #if the model name in vehicle specs is found in the model column of vehicle additional distinct table
AND VA.overall_length BETWEEN VS.model_length_mm-1.5 AND VS.model_length_mm+1.5  # if the measurments are in range of one another																									#rounded up and other times they rounded down.
AND VA.overall_width BETWEEN VS.model_width_mm-1.5 AND VS.model_width_mm+1.5
AND VA.overall_height BETWEEN VS.model_height_mm-1.5 AND VS.model_height_mm+1.5
AND VA.wheelbase BETWEEN VS.model_wheelbase_mm-1.5 AND VS.model_wheelbase_mm+1.5
AND VA.curbweight BETWEEN VS.model_weight_kg-15 AND VS.model_weight_kg+15;


VEHICLE_SPECS_merge table  ~78,000 rows (Maintenance still to run to clean up model_name and model_trim)
	Column Inserted to keep track of what was there,been inserted or merged.
	Inserted=0-----Inserted
	Inserted=2-----Merged
	Inserted=3-----Already in Vehicle Specs and no match was found to merge
	Model name for rows that where inserted=0 had info beyond just the model_name and would be considered model_trim.
	Split column model_name from rows that have inserted=0 in order to fill model_trim with some info so the user sees something with
	searching for the model_trim in the app or UI

ENCYRPTION-IN PROGRESS
	ISSUE -App uses php/mysql to communicate with the server while the QT UI being built by another team uses c++/mysql.

	1. Encrypting certain fields such as address,Date of Birth and Phone number which will display encrypted info when viewing from the
	Database, but can be decrypted to be viewed by the user 

	2. Hashing the password, which transforms the given password using an algorithim to another string and is only ONE-WAY. Which means the 
	there is no way to decrypt it. To verify the password you would hash the inputted password by the user when logging in and check it 
	against the hashed password in the database.

	Possible methods
	MYSQL- AES_encrypt and AES_decrypt for Encrypting fields, PHP/C++ OpenSSL encryption
	PHP - Built in function PASSWORD_HASH(password,PASSWORD_DEFAULT) and verifying with PASSWORD_VERIFY(inputted password,hashed password in database)
	C++- Implementing the bcrypt algrithim to allow for hashing and verification.Or another hasing method that also works in php
	Getting the user to login to the website and the QT UI collecting the username of the person who logged in,thereby not needing
	a login for the UI.


	MYSQL Example encrypt-INSERT INTO OCCUPANT_INFO (First_Name,Seatbelt,Impairment,Address,Case_number,Salt) VALUES ('John','NO','NO',AES_ENCRYPT(CONCAT('something','somewhere'),'key1234'),'10','something');
	DEcrypt example  SELECT First_name,Seatbelt,Impairment,replace(AES_DECRYPT(Address,'key1234'),salt,''),Case_number FROM OCCUPANT_INFO WHERE Case_number = '$case_number'"))


	