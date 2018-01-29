$(function() {

    $('#login-form-link').click(function(e) {
		$("#login-form").delay(100).fadeIn(100);
 		$("#register-form").fadeOut(100);
		$('#register-form-link').removeClass('active');
    $("#verification-form").fadeOut(100);
    $('#verification-form-link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});
	$('#register-form-link').click(function(e) {
		$("#register-form").delay(100).fadeIn(100);
 		$("#login-form").fadeOut(100);
		$('#login-form-link').removeClass('active');
    $("#verification-form").fadeOut(100);
		$('#verification-form-link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});
  $('#verification-form-link').click(function(e) {
	  $("#verification-form").delay(100).fadeIn(100);
	  $("#register-form").fadeOut(100);
	  $('#register-form-link').removeClass('active');
	  $("#login-form").fadeOut(100);
	  $('#login-form-link').removeClass('active');
	  $(this).addClass('active');
	  e.preventDefault();
	});
});


var validate = () =>{
	var username = document.getElementById("usernameR").value;
	var password = document.getElementById("passwordR").value;
	var confirm = document.getElementById("confirm-password").value;
	var email = document.getElementById("email").value;
	var fname = document.getElementById("fname").value;
  var lname = document.getElementById("lname").value;

	if(username === "" || password === "" || confirm === "" || email === "" || fname === "" || lname === "" ){
    showError("All fields are required.");
		return false;
	}
	if(password !== confirm){
		showError("The new password and confirm password fields must match.");
		return false;
	}

	if(username.length > 25){
		showError("Your username cannot be longer than 25 characters.");
		return false;
	}
	if(password.length > 100){
		showError("Your password cannot be longer than 100 characters.");
		return false;
	}

	if(email.length > 100){
		showError("Your email cannot be longer than 100 characters.");
		return false;
	}

	if(fname.length > 50 ){
		showError("First name cannot be longer than 100 characters.");
		return false;
	}
  if(lname.length > 50 ){
		showError("Last name cannot be longer than 100 characters.");
		return false;
	}
	//we're here, no errors, hide error box
	showError(false);

	return true;
};


var onSubmit = () =>{
	if(!validate()){
		return false;
	}

	var buttonNode = document.getElementById("register-submit");
	buttonNode.innerText = "Submitting, please wait...";

	return true;
};

var onLoad = () => {
	var formNode = document.getElementById("register-form");

	formNode.onsubmit = onSubmit;

	// // if (errorMessage){
	// 	// showError(errorMessage);
	// }
};
window.addEventListener("load",onLoad,false);

var showError = (msg) =>{
	var errorNode = document.getElementById("error");
	if(msg == "" || msg == false){
		//hide error
		errorNode.style = "visibility: hidden;";
		return;
	}
	errorNode.innerText = msg;
	errorNode.style = "";
};
