
// with jQuery 
$(document).ready(function(){ 
	
	$(".submitBtn").click(function(){
		var valid = 1;
		var name = $("#name").val();
		
		name = $.trim(name);
		var email = $("#email").val();
		email = $.trim(email);
		var message = $("#message").val();
		
		if(name.length==0){
			valid = 0;
			$("#name").focus();
			$(".nameError").html("Please enter name");
			$(".nameError").show();
		}else{
			$(".nameError").html("");
			$(".nameError").hide();
		}
		if(email.length==0){
			valid = 0;
			$("#email").focus();
			$(".emailError").html("Please enter email address");
			$(".emailError").show();
		}else{
			if(!isValidEmailAddress(email)){
				valid = 0;
				$("#email").focus();
				$(".emailError").html("Please valid email address");
				$(".emailError").show();
			}else{
				$(".emailError").html("");
				$(".emailError").hide();
			}
		}
		if(message.length==0){
			valid = 0;
			$("#message").focus();
			$(".messageError").html("Please enter your message");
			$(".messageError").show();
		}else{
			$(".messageError").html("");
			$(".messageError").hide();
		}
		if(valid ==1){
			$("#enquiryFrom").submit();
		}
		
	});
	
});
function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);
    return pattern.test(emailAddress);
};