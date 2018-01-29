<?php
require_once('config.php');

session_start();
//for cleaning and validating form inputs.
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

// Method: POST, PUT, GET etc
// Data: array("param" => "value") ==> index.php?param=value
function CallAPI($method, $url, $data = false){
    $curl = curl_init();

    switch ($method)
    {
    	case "DELETE":
    		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 1);
    		break;
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
     // transfer the session and cookie data//
//      	curl_setopt( $curl, CURLOPT_COOKIESESSION, true );
//     curl_setopt ($curl, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
// 	curl_setopt ($curl, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');

    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

/* Functions to deal with user data */
function loginPage(){
	header("Location: ".'/login.php');
}
function homePage(){
	header("Location: ".'/index.php');
}
// encrypt a string using bcrypt 512
function encryptPass($pass){
	return password_hash($pass, PASSWORD_DEFAULT);
}
// call the verify user function and log the user out if the id and token do not match OR the time the user has been logged in expired
function checkUser(){
	if(!verifyUser()){
		logout();
		header('Location: /login.php');
	}
}
// this function takes the session data to validate a login. The user is only able to stay logged in if
// the user id and token match a record and the time is current
function verifyUser(){
	if(!isset($_SESSION['user_id'])){
		return false;
	}
	$user = $_SESSION['user_id'];
	if(!isset($_SESSION['token'])){
		return false;
	}
	$token = $_SESSION['token'];
	$db = new Database();
	$sql = "SELECT `user_tokenexpire` from `user` WHERE `user_id` = ? AND `user_token` = ?";
	$sql = $db->prepareQuery($sql, $user, $token);
	$result = $db->query($sql);
	$row = mysqli_fetch_row($result);
	$expire = $row[0]; // grab user_tokenexpire date
	$date = new DateTime();
	$x = new DateTime($expire);
	if($x > $date){
		return true;
	}
	return false;
}
// delete session data as well as remove the token from the database
function logout(){

	if(isset($_SESSION['user_id'])){
		$db = new Database();
		$user = $_SESSION['user_id'];
		$date = date("Y-m-d H:i:s");
		// destroy the token to keep the session
		$empty = '';
		$sql = "UPDATE `user` SET `user_token` = ?, `user_tokenexpire` = ?";
		$sql = $db->prepareQuery($sql, $empty, $date);
		$result = $db->query($sql);
		loginPage();
	}
	// remove all session variables
	session_unset();
	// destroy the session
	session_destroy();
}
// create a secure random login token that the user needs to login
function createLoginToken($id){
	$token = bin2hex(random_bytes(16));
	$db = new Database();
	$loginFromNow = date("Y-m-d H:i:s", strtotime('+6 hours'));
	$sql = "UPDATE `user` SET `user_token` = ?, `user_tokenexpire` = ? WHERE `user_id` = ?";
	$sql = $db->prepareQuery($sql, $token, $loginFromNow, $id);
	$result = $db->query($sql);
	return ($token);
}
// create a secure random login token that the user needs to reset their password
function createResetPasswordKey($email){
	$token = bin2hex(random_bytes(6));
	$db = new Database();
	$loginFromNow = date("Y-m-d H:i:s", strtotime('+1 hours'));
	$sql = "UPDATE `user` SET `user_token` = ?, `user_tokenexpire` = ? WHERE `user_email` = ?";
	$sql = $db->prepareQuery($sql, $token, $loginFromNow, $email);
	$result = $db->query($sql);
	return ($token);
}
//
// function resetPassword($username, $pass, $token){
// 	$db = new Database();
// 	$pass = encryptPass($pass);
// 	$time = date("Y-m-d H:i:s", strtotime('+1 hours'));
// 	$sql = "UPDATE `user` SET `user_password` = ? WHERE `user_username` = ? AND `user_token` = ? AND `user_tokenexpire` < ?";
// 	$sql = $db->prepareQuery($sql, $pass, $username, $token, $time);
// 	$result = $db->query($sql);
// 	if($result){
// 	echo 'Password Reset!';
// 		return true;
// 	}else{ return false; }
// }
// function to validate a user login. Check if the username exists and the corresponding password (hash) matches a record
// if the user logged in successfully set the session data.
function login($username, $pass){
		//echo '<script> console.log(\'Trying to login\'); </script>';

	$db = new Database();
	$sql = "select `user_id`, `user_password`, `user_verified` from `user` where `user_username` = ?";
	$sql = $db->prepareQuery($sql, $username);
	$result = $db->query($sql);

	if(mysqli_num_rows($result) == 1){
		$row = mysqli_fetch_row($result);
		$hash = $row[1];
		$user = $row[0];
		$veri = $row[2];
		$_SESSION['token'] = createLoginToken($user);
		$_SESSION['user_id'] = $user;
		//echo '<script> console.log(\'You have logged in as user id '.$user.'\'); </script>';
		if($veri){
			if(password_verify($pass, $hash)){
				header("Location: Schedule2.php");
				return true;
			}
		}
		else{
			$_POST['error'] = 'Account is not verified.';
			return false;
		}
	}
	$_POST['error'] = 'Username or password incorrect.';
	return false;
}
// a function to send an email to a user
function sendResetPassword($email){
	$token = createResetPasswordKey($email);
	$to  = $email;
	$emailFrom = 'jacosta@g.clemson.edu';
	$subject = 'Password Reset';
	$headers ="MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
	$headers = "From: ".$emailFrom." \r\n";
$message = 'You recently requested to reset your password for your account.
Please navigate to the login page to reset your password. When prompted to enter the case sensitve code please enter
'.$token.'
If you did not request a password reset, please ignore this email. This password reset token is only valid for the next 1 hour.
jC';
	mail($to, $subject, $message, $headers);
}
function sendVerification($id, $email, $username){
	$db = new Database();
	$token = bin2hex(random_bytes(5));
	$date = date("Y-m-d H:i:s");
	$sql = "UPDATE `user` SET `user_key` = ?, `user_key_create` = ? where `user_id` = ?";
	$sql = $db->prepareQuery($sql, $token, $date, $id);
	$result = $db->query($sql);
	if($result){
		$to  = $email;
		$emailFrom = 'jacosta@g.clemson.edu';
		$subject = 'Verification Email';
		$headers ="MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
		$headers = "From: ".$emailFrom." \r\n";

$message = 'Hi '.$username.'
Please verify your email address for by going to the login and typing in the case sensitive code below
'.$token.'
jC';
		if(mail($to, $subject, $message, $headers)){

		error_log("Sent mail to ".$to." email");
			}else{
			error_log("Unable to send mail to ".$to." email");
			}
	}else{
	error_log(mysqli_error($db));
		echo mysqli_error($db);
	}
}
function getMyUserData(){
	$db = new Database();
	$userID = $_SESSION['user_id'];
	$sql = "SELECT user_username, user_email, user_fname, user_lname from user where user_id = ? LIMIT 1";
	$sql = $db->prepareQuery($sql, $userID);
	$results = $db->select($sql);
	if(isset($results[0]['user_username'])){ // there was a username at id
		return array(
		'username' => $results[0]['user_username'],
		'email' => $results[0]['user_email'],
		'first_name' => $results[0]['user_fname'],
		'last_name' => $results[0]['user_lname']
		);
	}else return array(); // there is not a department at id
}

function updateUser($username, $email, $fname, $lname){
	$db = new Database();
	$userID = $_SESSION['user_id'];
	$sql = "UPDATE `user` SET `user_username` = ?, `user_email` = ?, `user_fname` = ?, `user_lname` = ? WHERE user_id = ?";
	$sql = $db->prepareQuery($sql, $username, $email, $fname, $lname, $userID);
	$result = $db->query($sql);
	if(strpos(mysqli_error($db->link()),"for key 'user_username'") != FALSE){
		$_POST['error'] = "Username is already taken. Please enter in a unique username";
		//	echo $_SESSION['error'];
	}
	else if($result){
		return true;
	}else{
		return false;
	}
}
function createUser($username, $email, $pass, $first, $last){
	$db = new Database();
	$pass = encryptPass($pass);
	$username = strtolower($username);
	$email = strtolower($email);
	$verified = 0;
	$sql = "INSERT INTO `user`(`user_email`, `user_username`, `user_password`, `user_verified`, `user_fname`, `user_lname`) VALUES (?, ?, ?, ?, ?, ?)";
	$sql = $db->prepareQuery($sql, $email, $username, $pass, $verified, $first, $last);
	$result = $db->query($sql);
	if(strpos(mysqli_error($db->link()),"for key 'user_email'") != FALSE){
		$_POST['error'] = "Email is in use by another user. Try forgot password to recover account.";
		//echo $_SESSION['error'];
	}if(strpos(mysqli_error($db->link()),"for key 'user_username'") != FALSE){
		$_POST['error'] = "Username is already taken. Please enter in a unique username";
		//	echo $_SESSION['error'];
	}
	$sql2 = "SELECT `user_id` from `user` where `user_email` = ? AND `user_username` = ?";
	$sql2 = $db->prepareQuery($sql2, $email, $username);
	$result2 = $db->query($sql2);
	if($result && $result2){
		$row = mysqli_fetch_row($result2);
		sendVerification($row[0], $email, $username);
		return true;
	}
	return false;

}
function verifyUserAccountCreation($username, $token){
	if($token == ''){
		$_POST['error'] = 'Token Empty';
		return false;
	}
	$username = strtolower($username);
	$db = new Database();
	$sql = "SELECT * from `user` where `user_username` = ? AND `user_key` = ?";
	$sql = $db->prepareQuery($sql, $username, $token);
	$result = $db->query($sql);
	if($result){
		if(mysqli_num_rows($result) == 1){
			$row = mysqli_fetch_row($result);
			$userID = $row[0];
			initUserRole($userID);
			$verified = 1;
			$empty = '';
			$sql2 = "UPDATE `user` SET `user_verified` = ?, `user_key` = ? where `user_username` = ?";
			$sql2 = $db->prepareQuery($sql2, $verified, $empty, $username);
			$result2 = $db->query($sql2);
			return $result2;
		}else{
			$_POST['error'] = 'Username or token entered incorrectly.';
			//echo $_SESSION['error'];
		}
		return false;
	}
}
function changePassword($email, $pass, $token){
	$db = new database();
	$pass = encryptPass($pass);
	$time = date("Y-m-d H:i:s");

	$sql = "UPDATE user SET user_password = ?, user_token = '', user_tokenexpire = ?  where user_email = ? AND user_token = ? AND user_tokenexpire > ?";
	$sql = $db->prepareQuery($sql, $pass, $time, $email, $token, $time);
	$result = $db->query($sql);
	$e = mysqli_error($db->link());
	if($e != null){
		$_POST['error'] = $e;
	}
	if($result && mysqli_affected_rows($db->link()) == 1) { return true; } else  { return false; }

}

function searchForUser($s){
	$db = new Database();
	$s = '%'.$s.'%';
	$sql = "SELECT user_id, user_username, user_fname, user_lname from user where user_username LIKE ? OR user_fname LIKE ? OR user_lname LIKE ?";
	$sql = $db->prepareQuery($sql, $s, $s, $s);
	$results = $db->query($sql);
	$people = array();
	foreach ($results as $result){
		$people[] = array('id' => $result['user_id'], 'username' => $result['user_username'], 'firstName' => $result['user_fname'], 'lastName' => $result['user_lname']);
	}
	return $people;
}



// ---------------------------------------------------------------------------//
/*
Functions for departments
GET, INSERT, UPDATE, DELETE
*/
function getDepartments(){
	$db = new Database();
	$sql = "SELECT * FROM department";
	$results = $db->query($sql);
	$departments = array();
	foreach($results as $result){ // return all of the departments
		$departments[] = array('id' =>$result['department_id'], 'dept_short' => $result['department_short'], 'dept_long' => $result['department_long']);
	}
	return $departments;
}
function getDepartment($id){
	$db = new Database();
	$sql = "SELECT * FROM department WHERE `department_id` = ? LIMIT 1";
	$sql = $db->prepareQuery($sql, $id);
	$results = $db->select($sql);
	if(isset($results[0]['department_id'])){ // there was a department at id
		return array(
		'department_id' => $results[0]['department_id'],
		'department_short' => $results[0]['department_short'],
		'department_long' => $results[0]['department_long']
		);
	}else return array(); // there is not a department at id
}
function searchDepartments($s){
	$db = new Database();
	$s = '%'.$s.'%';
	$sql = "SELECT * FROM department WHERE department_short LIKE ? OR department_long LIKE ?";
	$sql = $db->prepareQuery($sql, $s, $s);
	$results = $db->query($sql);
	 $departments = array();
	foreach($results as $result){ //returns all of the departments where $s is contained in short or long
		$departments[] = array('id' =>$result['department_id'], 'dept_short' => $result['department_short'], 'dept_long' => $result['department_long']);
	}
	return $departments;
}
// //FUNCTION BELOW MAY BE PRONE TO SQL INJECTION
// function searchDepartments($s){
// 	$s = '%'.$s.'%';
// 	$db = new Database();
// 	$sql = "SELECT * FROM department WHERE department_short LIKE '$s' OR department_long LIKE '$s'";
// 	$results = $db->query($sql);
// 	// $departments = array();
// 	foreach($results as $result){ //returns all of the departments where $s is contained in short or long
// 		$departments[] = array('id' =>$result['department_id'], 'dept_short' => $result['department_short'], 'dept_long' => $result['department_long']);
// 	}
// 	return $departments;
// }
function updateDepartment($id, $short, $long){
	$db = new Database();
	$sql = "UPDATE department SET `department_short` = ?, `department_long` = ? WHERE `department_id` = ?";
	$sql = $db->prepareQuery($sql, $short, $long, $id);
	$db->query($sql);
	return array( // return the newly updated department
		'department_id' => $id,
		'department_short' => $short,
		'department_long' => $long
		);
}
function insertDepartment($short, $long){
	$db = new Database();
	$sql = "INSERT INTO department (`department_short`, `department_long`) VALUES (?, ?)";
	$sql = $db->prepareQuery($sql, $short, $long);
	if($db->query($sql)){
		return true;
		}else return false;

}
function deleteDepartment($id){
	$db = new Database();
	$sql = "DELETE FROM department WHERE `department_id` = ?";
	$sql = $db->prepareQuery($sql, $id);
	if($db->query($sql)){
		return true;
	}else return mysqli_error($db);

}
//					END DEPARTMENTS					//


/*
Functions for professors
GET, INSERT, UPDATE, DELETE
*/
function getProfessors(){
	$db = new Database();
	$sql = "SELECT * FROM professor";
	$results = $db->query($sql);
	$professors = array();
	foreach($results as $result){
		$professors[] = array('id' => $result['prof_id'], 'prof_name' => $result['prof_name'], 'prof_email' => $result['prof_email'], 'office' => $result['office_loc']);
	}
	return $professors;
}
function getProfessor($id){
	$db = new Database();
	$sql = "SELECT * FROM professor WHERE 'prof_id' = ? LIMIT 1";
	$sql = $db->prepareQuery($sql, $id);
	$results = $db->select($sql);
	if(isset($results[0]['prof_id'])){
		return array(
		'prof_id' => $results[0]['prof_id'],
		'prof_name' => $results[0]['prof_name'],
		'prof_email' => $results[0]['prof_email'],
		'office_loc' => $results[0]['office_loc']
		);
	}else return array();
}
function searchProfessors($s){
	$db = new Database();
	$s = '%'.$s.'%';
	$sql = "SELECT * FROM professor WHERE prof_name LIKE ? OR prof_email LIKE ?";
	$sql = $db->prepareQuery($sql, $s, $s);
	$results = $db->query($sql);
	$professors = array();
	foreach($results as $result) {
		$professors[] = array('id' => $result['prof_id'], 'prof_name' => $result['prof_name'], 'prof_email' => $result['prof_email'], 'office' => $result['office_loc']);
	}
	return $professors;
}
function updateProfessor($id, $name, $email, $office){
	$db = new Database();
	$sql = "UPDATE professor SET `prof_name` = ?, `prof_email` = ?, `office_loc` = ? WHERE `prof_id` = ?";
	$sql = $db->prepareQuery($sql, $name, $email, $office);
	$db->query($sql);
	return array( // return the newly updated professor
		'prof_id' => $id,
		'prof_name' => $name,
		'prof_email' => $email,
		'office_loc' => $office
		);
}
function insertProfessor($name, $email, $office){
	$db = new Database();
	$sql = "INSERT INTO professor (`prof_name`, `prof_email`, `office_loc`) VALUES (?, ?, ?)";
	$sql = $db->prepareQuery($sql, $name, $email, $office);
	if($db->query($sql)){
		return true;
	}else return false;

}
function deleteProfessor($id){
	$db = new Database();
	$sql = "DELETE FROM professor WHERE `prof_id` = ?";
	$sql = $db->prepareQuery($sql, $id);
	if($db->query($sql)){
		return true;
	}else return mysqli_error($db);
}
//					END PROFESSORS					//

/*
Functions for events
GET, INSERT, UPDATE, DELETE
*/

function getEvents(){
	$db = new Database();
	$sql = "SELECT * FROM event ORDER BY event_start";
	$results = $db->query($sql);
	$events = array();
	foreach($results as $result){
		$events[] = array('id' => $result['event_id'], 'day' => date('Y-m-d', strtotime($result['event_start'])), 'start' => date('H:i:s', strtotime($result['event_start'])), 'end' => date('H:i:s', strtotime($result['event_end'])), 'name' => $result['event_name'], 'desc' => $result['event_desc']);
	}
	return $events;
}
function getEvent($id){
	$db = new Database();
	$sql = "SELECT * FROM event WHERE `event_id` = ? LIMIT 1";
	$sql = $db->prepareQuery($sql, $id);
	$results = $db->select($sql);
	if(isset($results[0]['event_id'])){
		return array(
			'event_id' => $results[0]['event_id'],
			'event_start' => $results[0]['event_start'],
			'event_end' => $results[0]['event_end'],
			'event_name' => $results[0]['event_name'],
			'event_desc' => $results[0]['event_desc']
		);
	}else return array();
}
function searchEvents($s){
	$db = new Database();
	$s = '%'.$s.'%';
	$sql = "SELECT * FROM event WHERE event_name LIKE ? OR event_desc LIKE ?";
	$sql = $db->prepareQuery($sql, $s, $s);
	$results = $db->query($sql);
	$events = array();
	foreach($results as $result){
		$events[] = array('id' => $result['event_id'], 'start' => $result['event_start'], 'end' => $result['event_end'], 'name' => $result['event_name'], 'desc' => $result['event_desc']);
	}
	return $events;
}
function updateEvent($id, $start, $end, $name, $desc){
	$db = new Database();
	$sql = "UPDATE event SET `event_start` = ?, `event_end` = ?, `event_name` = ?, `event_desc` = ? WHERE `event_id` = ?";
	$sql = $db->prepareQuery($sql, $start, $end, $name, $desc, $id);
	$db->query($sql);
	 return array( // return the newly updated event
		'event_id' => $id,
		'event_start' => $start,
		'event_end' => $end,
		'event_name' => $name,
		'event_desc' => $desc
		);
}

// if a user creates an event automatically insert the event in user_events;
function insertEvent($start, $end, $name, $desc){
	$db = new Database();
	$sql = "INSERT INTO event (`event_start`, `event_end`, `event_name`, `event_desc`) VALUES (?, ?, ?, ?)";
	$sql = $db->prepareQuery($sql, $start, $end, $name, $desc);
	$result = $db->query($sql);

	$eid = mysqli_insert_id($db->link());
	$result2 = insertUserEvent($eid);
	if($result && $result2){
		return true;
		}else
		return false;


}
function deleteEvent($id){
	$db = new Database();
	$sql = "DELETE FROM event WHERE `event_id` = ?";
	$sql = $db->prepareQuery($sql, $id);
	if($db->query($sql)) {
		return true;
	}else return mysqli_error($db);
}
//					END EVENTS					//

/*
Functions for user_roles
GET, INSERT, DELETE
*/
//	to get users that can see your data
function getMyViewers(){
	$db = new Database();
	$user = $_SESSION['user_id'];

	$sql = "SELECT ur.useradmin_fk, ur.accepted, ur.userview_fk, u.user_id, u.user_username, u.user_fname, u.user_lname from user_roles ur LEFT JOIN user u ON ur.userview_fk = u.user_id where ur.accepted = 1 AND ur.userview_fk != ? AND ur.useradmin_fk = ?";

	$sql = $db->prepareQuery($sql, $user, $user);
	$results = $db->query($sql);
	$people = array();
	foreach ($results as $result){
		$people[] = array('id' => $result['user_id'], 'username' => $result['user_username'], 'firstName' => $result['user_fname'], 'lastName' => $result['user_lname']);
	}
	return $people;
}
//  to get the users whose data you can see
function getUsersWhoViewMe(){
	$db = new Database();
	$user = $_SESSION['user_id'];

	$sql = "SELECT ur.useradmin_fk, ur.accepted, ur.userview_fk, u.user_id, u.user_username, u.user_fname, u.user_lname from user_roles ur LEFT JOIN user u ON ur.useradmin_fk = u.user_id where ur.accepted = 1 AND ur.useradmin_fk != ? AND ur.userview_fk = ?";

	$sql = $db->prepareQuery($sql, $user, $user);
	$results = $db->query($sql);
	$people = array();
	foreach ($results as $result){
		$people[] = array('id' => $result['user_id'], 'username' => $result['user_username'], 'firstName' => $result['user_fname'], 'lastName' => $result['user_lname']);
	}
	return $people;
}
function getUserRoles(){
	$db = new Database();
	$user = $_SESSION['user_id'];
	$sql = "Select * from user_roles WHERE `useradmin_fk` = ? AND `accepted` = 1";
	$sql = $db->prepareQuery($sql, $user);
	$results = $db->query($sql);
	$userRoles = array();
	foreach ($results as $result){
		$userRoles[] = array('id' => $result['userview_fk']);
	}
	return $userRoles;
}
function getUserRole($user){
	$db = new Database();
	$userID = $_SESSION['user_id'];
	$sql = "Select * from user_roles WHERE `useradmin_fk` = ? AND `userview_fk` = ?";
	$sql = $db->prepareQuery($sql, $userID, $user);
	$results = $db->query($sql);
	$userRoles = array();
	foreach ($results as $result){
		$userRoles[] = array('accepted' => $result['accepted']);
	}
	return $userRoles;
}
function initUserRole($user){
	$db = new Database();
	$a = 1;
	$sql = "INSERT INTO user_roles (`useradmin_fk`, `userview_fk`, `accepted`) VALUES (?, ?, ?)";
	$sql = $db->prepareQuery($sql, $user, $user, $a);
	if($db->query($sql)){
		return true;
	}else return false;
}

function insertUserRole($user){
	$db = new Database();
	$userID = $_SESSION['user_id'];
	$a = 1;
	$sql = "INSERT INTO user_roles (`useradmin_fk`, `userview_fk`, `accepted`) VALUES (?, ?, ?)";
	$sql = $db->prepareQuery($sql, $userID, $user, $a);
	if($db->query($sql)){
		return true;
	} else return false;
}

function deleteRole($user){
	$db = new Database();
	$userID = $_SESSION['user_id'];
	$sql = "DELETE FROM user_roles WHERE `useradmin_fk` = ? AND `userview_fk` = ?";
	$sql = $db->prepareQuery($sql, $user, $userID);
	if($db->query($sql)){
		return true;
	}else return false;

}
//					END User_roles					//

/*
Functions for user_events
GET, INSERT, UPDATE, DELETE
*/
function getUserEvents(){
	$db = new Database();
	$userID = $_SESSION['user_id'];
	//echo 'My USERID is \'' . $_SESSION['user_id'] .'\'<br>';
// 	$userID = 1;
	$sql = "select * from event INNER JOIN user_event ON user_event.user_fk = ? and user_event.event_fk = event.event_id LEFT JOIN course on event.event_id = course.event_fk LEFT JOIN department on course.department_fk = department.department_id LEFT JOIN professor ON course.prof_fk = professor.prof_id LEFT JOIN course_semester ON course.course_id = course_semester.cs_cid LEFT JOIN semester ON course_semester.cs_sid = semester.semester_id";
	$sql = $db->prepareQuery($sql, $userID);
	//echo $sql."<br>";
	/*
select * from event INNER JOIN user_event ON user_event.user_fk = 1 and user_event.event_fk = event.event_id LEFT JOIN course on event.event_id = course.event_fk LEFT JOIN department on course.department_fk = department.department_id LEFT JOIN professor ON course.prof_fk = professor.prof_id LEFT JOIN course_semester ON course.course_id = course_semester.cs_cid LEFT JOIN semester ON course_semester.cs_sid = semester.semester_id
	*/
	$results = $db->query($sql);
	$userEvents = array();
	foreach ($results as $result){
		//echo $result['event_id']." ".$result['event_start']." ".$result['event_end']." ".$result['event_name']." ". $result['event_desc']." ".$result['date_created'];
		if($result['course_id'])
			$userEvents[] = array('id' => $result['event_id'], 'start' => $result['event_start'], 'end' => $result['event_end'], 'name' => $result['event_name'], 'desc' => $result['event_desc'],'semester' => $result['semester'], 'course_name' => $result['department_short']." ".$result['course_num'], 'course_desc' => $result['course_desc'] , 'days_of_week' => $result['course_days'] , 'homepage' => $result['course_homepage'], 'professor' => $result['prof_name'], 'prof_office' => $result['office_loc'], 'prof_email' => $result['prof_email']);
		else
			$userEvents[] = array('id' => $result['event_id'], 'start' => $result['event_start'], 'end' => $result['event_end'], 'name' => $result['event_name'], 'desc' => $result['event_desc']);

	}
	return $userEvents;
}
function insertUserEvent($eventID){
	$db = new Database();
	$userID = $_SESSION['user_id'];
	$sql = "INSERT INTO user_event (`user_fk`, `event_fk`, `date_created`) VALUES (?, ?, Now())";
	$sql = $db->prepareQuery($sql, $userID, $eventID);
	if($db->query($sql)){
		return true;
	} else return false;
}
function deleteUserEvent($eventID){
	$db = new Database();
	$userID = $_SESSION['user_id'];
	$sql = "DELETE FROM user_event WHERE `user_fk` = ? AND `event_fk` = ?";
	$sql = $db->prepareQuery($sql, $userID, $eventID);
	if($db->query($sql)){
		return true;
	}	else return false;
}

function getUsersDataForID($id){
	$db = new Database();

	$sql = "select * from event INNER JOIN user_event ON user_event.user_fk = ? and user_event.event_fk = event.event_id LEFT JOIN course on event.event_id = course.event_fk LEFT JOIN department on course.department_fk = department.department_id LEFT JOIN professor ON course.prof_fk = professor.prof_id LEFT JOIN course_semester ON course.course_id = course_semester.cs_cid LEFT JOIN semester ON course_semester.cs_sid = semester.semester_id";
	$sql = $db->prepareQuery($sql, $id);
	$results = $db->query($sql);
	$userEvents = array();
	foreach ($results as $result){
		//echo $result['event_id']." ".$result['event_start']." ".$result['event_end']." ".$result['event_name']." ". $result['event_desc']." ".$result['date_created'];
		if($result['course_id'])
			$userEvents[] = array('id' => $result['event_id'], 'start' => $result['event_start'], 'end' => $result['event_end'], 'name' => $result['event_name'], 'desc' => $result['event_desc'],'semester' => $result['semester'], 'course_name' => $result['department_short']." ".$result['course_num'], 'course_desc' => $result['course_desc'] , 'days_of_week' => $result['course_days'] , 'homepage' => $result['course_homepage'], 'professor' => $result['prof_name'], 'prof_office' => $result['office_loc'], 'prof_email' => $result['prof_email']);
		else
			$userEvents[] = array('id' => $result['event_id'], 'start' => $result['event_start'], 'end' => $result['event_end'], 'name' => $result['event_name'], 'desc' => $result['event_desc']);

	}
	return $userEvents;
}
//					END User_events					//

/*
Functions for Course ****THIS IS A PLACEHOLDER BECAUSE WE ARE UNABLE TO GET ALL COURSES****
GET, INSERT, UPDATE, DELETE
*/

function getCourses(){
	$db = new Database();
	$sql = "Select * from course INNER JOIN event ON course.event_fk = event.event_id INNER JOIN department ON course.department_fk = department.department_id INNER JOIN professor ON course.prof_fk = professor.prof_id INNER JOIN course_semester ON course.course_id = course_semester.cs_cid INNER JOIN semester ON semester.semester_id = course_semester.cs_sid";
	$results = $db->query($sql);
	$courses = array();
	foreach ($results as $result){
		$courses[] = array('id' => $result['course_id'], 'start' => $result['event_start'], 'end' => $result['event_end'], 'event_name' => $result['event_name'],'semester' => $result['semester'], 'course' => $result['department_short']." ".$result['course_num'], 'course_desc' => $result['course_desc'], 'course_homepage' => $result['course_homepage'], 'days_of_week' => $result['course_days'], 'professor' => $result['prof_name'], 'office' => $result['office_loc'], 'email' => $result['prof_email']);
	}
	return $courses;
}
function getCourse($id){
	$db = new Database();
	$sql = "Select * from course INNER JOIN event ON course.event_fk = event.event_id INNER JOIN department ON course.department_fk = department.department_id INNER JOIN professor ON course.prof_fk = professor.prof_id INNER JOIN course_semester ON course.course_id = course_semester.cs_cid INNER JOIN semester ON semester.semester_id = course_semester.cs_sid WHERE course.course_id = ?";
	$sql = $db->prepareQuery($sql, $id);
	$results = $db->select($sql);
	if(isset($results[0]['course_id'])){
		return array('id' => $results[0]['course_id'], 'start' => $results[0]['event_start'], 'end' => $results[0]['event_end'], 'event_name' => $results[0]['event_name'],'semester' => $results[0]['semester'], 'course' => $results[0]['department_short']." ".$results[0]['course_num'], 'course_desc' => $results[0]['course_desc'], 'course_homepage' => $results[0]['course_homepage'], 'days_of_week' => $results[0]['course_days'], 'professor' => $results[0]['prof_name'], 'office' => $results[0]['office_loc'], 'email' => $results[0]['prof_email']);
	}else return array();
}
function insertCourse($event, $dept, $prof, $number, $desc, $website, $days, $semesterID){
	$db = new Database();
	$sql = "Insert into course (event_fk, department_fk, prof_fk, course_num, course_desc, course_homepage, course_days) VALUES (?, ?, ?, ?, ?, ?, ?)";
	$sql = $db->prepareQuery($sql, $event, $dept, $prof, $number, $desc, $website, $days);
	$result = $db->query($sql);
	$cid = $db->insert_id;
	$sql2 = "Insert into course_semester (cs_cid, cs_sid) VALUES (?, ?)";
	$sql2 = $db->prepareQuery($sql2, $cid, $semesterID);
	$result2 = $db->query($sql2);
	if($result && $result2){
		return true;
	}else{
		return false;
	}
}
function deleteCourse($id){
	$db = new Database();
	$sql = "Delete from course where course_id = ?";
	$sql = $db->prepareQuery($sql, $id);
	if($db->query($sql)){
		return true;
	}else {
		return false;
	}

}

function weekIntToString($i){
				if ($i == 0){
						return 'Sunday';
				}
				if ($i == 1){
						return 'Monday';
				}
				if ($i == 2){
						return 'Tuesday';
				}
				if ($i == 3){
						return 'Wednesday';
				}
				if ($i == 4){
						return 'Thursday';
				}
				if ($i == 5){
						return 'Friday';
				}
				if ($i == 6){
						return 'Saturday';
				}
}
// function to create a date from a day of week and a time string
function dateCreate($s, $t){
	$date =  date('Y-m-d', strtotime("now", strtotime($s)));
	$time = date('H:i:s', strtotime("now", strtotime($t)));
	//echo $date . '<br>' . $time;
	return date('Y-m-d H:i:s', strtotime($date. $time));
		//return date('Y-m-d H:i:s', strtotime("now", strtotime($s, $t)));
}
?>
