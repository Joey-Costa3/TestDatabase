function grabEBI(id)
/*====================================================
	- this is just a getElementById shortcut
=====================================================*/
{
	return document.getElementById( id );
}

function callDeleteThroughAPI(path, id){
	console.log("here");
	var fullURL = '' + path + '/api/v1/events?id=' + id;
	console.log(fullURL);
	$.ajax({
		// action in
		url:fullURL,
		type: 'GET',
		success: function (json) {
			console.log(json);

			if(json.data == true){
				showAlert('Success', 'The record was deleted', 'alert-success');
			}else{
				showAlert('Error', 'The record could not be deleted.', 'alert-warning');
			}
		}  // end success
	}); // end ajax
}

// show the alert with the message given
function showAlert(title, message, style){
	var alert = grabEBI("alert");
	alert.className = "alert " + style;			
	alert.innerHTML = '<a href="#" class="close" data-dismiss="alert" style="display: none">&times;</a><strong>'+title+'</strong><br>' + message;
}
// Specifically hide the alert.
function hideAlert(){
	grabEBI("alert").className = "";
	grabEBI("alert").innerHTML = "";
}

window.onload = function(){
	grabEBI("table").style.padding = "10px";
}