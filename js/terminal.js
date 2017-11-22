<?php 

 require_once("db.php"); //database

 if(isset($_COOKIE['ID_my_site'])){ 

        $email = $_COOKIE['ID_my_site'];  
        $pass = $_COOKIE['Key_my_site']; 
        $check = mysql_query("SELECT * FROM users WHERE email = '$email'")or die(mysql_error());
        $email = mysql_real_escape_string($_POST['email']);
        $res=mysql_query("SELECT * FROM users WHERE email='$email'");


    while($info = mysql_fetch_array( $check ))   { 
        if ($pass != $info['password']) 
            {           header("Location: ../signin.php"); 

            } 



    else 



            { 

    $emailget = $_COOKIE[ID_my_site];
    $get = mysql_query("SELECT * FROM users WHERE email = '$emailget'");

    while ($row = mysql_fetch_assoc($get)) {
        $firstname = $row["firstname"];
        $emailaddr = $row["email"];
        $lastname = $row["lastname"];
        $app_key = $row["app_key"];
    }


?> 
/*
    MMMMMMMM               MMMMMMMMVVVVVVVV           VVVVVVVVRRRRRRRRRRRRRRRRR           CCCCCCCCCCCCC
    M:::::::M             M:::::::MV::::::V           V::::::VR::::::::::::::::R       CCC::::::::::::C
    M::::::::M           M::::::::MV::::::V           V::::::VR::::::RRRRRR:::::R    CC:::::::::::::::C
    M:::::::::M         M:::::::::MV::::::V           V::::::VRR:::::R     R:::::R  C:::::CCCCCCCC::::C
    M::::::::::M       M::::::::::M V:::::V           V:::::V   R::::R     R:::::R C:::::C       CCCCCC
    M:::::::::::M     M:::::::::::M  V:::::V         V:::::V    R::::R     R:::::RC:::::C              
    M:::::::M::::M   M::::M:::::::M   V:::::V       V:::::V     R::::RRRRRR:::::R C:::::C              
    M::::::M M::::M M::::M M::::::M    V:::::V     V:::::V      R:::::::::::::RR  C:::::C              
    M::::::M  M::::M::::M  M::::::M     V:::::V   V:::::V       R::::RRRRRR:::::R C:::::C              
    M::::::M   M:::::::M   M::::::M      V:::::V V:::::V        R::::R     R:::::RC:::::C              
    M::::::M    M:::::M    M::::::M       V:::::V:::::V         R::::R     R:::::RC:::::C              
    M::::::M     MMMMM     M::::::M        V:::::::::V          R::::R     R:::::R C:::::C       CCCCCC
    M::::::M               M::::::M         V:::::::V         RR:::::R     R:::::R  C:::::CCCCCCCC::::C
    M::::::M               M::::::M          V:::::V          R::::::R     R:::::R   CC:::::::::::::::C
    M::::::M               M::::::M           V:::V           R::::::R     R:::::R     CCC::::::::::::C
    MMMMMMMM               MMMMMMMM            VVV            RRRRRRRR     RRRRRRR        CCCCCCCCCCCCC

    2017
*/

$('.shell').keypress(function (e) {
 var key = e.which;
 if(key == 13)  {
 	$(".shell").attr("disabled","disabled");
 	var old = $(".input").css("margin-top");
 	var shell_text = $(".shell").val();
 	var new_ = parseInt(old) + 15;
 	if(old == "0px") {
  		$(".input").attr("style","margin-top: 20");
  		$(".history").append('<p class="history_'+new_+'"><font color="green">pi@<?php echo $app_key ?>:</font><font color="#84b3ff">~ $ </font> <font class="command_'+new_+'" value="'+new_+'" color="white">'+shell_text+'</font>');
  		$(".shell").val("");

  			dataString = 'do='+shell_text+'&app_key=<?php echo $app_key ?>';
	  		$.ajax({
	            url: 'script/commander.php',
	            type: 'GET',
	            data: dataString,
	            beforeSend: function(data) {

	            },
	            success: function(data) {
	            	if(data.oc_status == 'success') {
	            		$(".history_"+new_).append(" <font color='#42f45f'><b>Status:</b> command successfully sent.</font> <font data-id='"+data.oc_id+"' class='responsetru response_"+data.oc_id+"' color='orange'>(waiting for response)</font>");
	            		$(".responsetru").attr('data-id',data.oc_id);
	            	} else {
	            		$(".history_"+new_).append(" <font color='red'><b>Status:</b> could not send command. (Reason: server offline)</font>");
	            	}
	            },
	            error: function(data) {
	            	window.location.href = 'signin.php';
	            	//$(".history_"+new_).append(" <font color='red'><b>Status:</b> could not send command. (Reason: server offline)</font>");
	            },
	            fail: function(data) {
	            	$(".history_"+new_).append(" <font color='red'><b>Status:</b> sending failed. (Reason: failed)</font>");
	            }
	        });
 	} else {
  		$(".input").attr("style","margin-top:"+new_);
  		$(".history").append('<p class="history_'+new_+'"><font color="green">pi@<?php echo $app_key ?>:</font><font color="#84b3ff">~ $ </font> <font class="command_'+new_+'" value="'+new_+'" color="white">'+shell_text+'</font></p>');
  		$(".shell").val("");

  			dataString = 'do='+shell_text+'&app_key=<?php echo $app_key ?>';
	  		$.ajax({
	            url: 'script/commander.php',
	            type: 'GET',
	            data: dataString,
	            beforeSend: function(data) {

	            },
	            success: function(data) {
	            	if(data.oc_status == 'success') {
	            		$(".history_"+new_).append(" <font color='#42f45f'><b>Status:</b> command successfully sent.</font> <font data-id='"+data.oc_id+"' class='responsetru response_"+data.oc_id+"' color='orange'>(waiting for response)</font>");
	            		$(".responsetru").attr('data-id',data.oc_id);
	            	} else {
	            		$(".history_"+new_).append(" <font color='red'><b>Status:</b> could not send command. (Reason: server offline)</font>");
	            	}
	            },
	            error: function(data) {
	            	$(".history_"+new_).append(" <font color='red'><b>Status:</b> could not send command. (Reason: server offline)</font>");
	            },
	            fail: function(data) {
	            	$(".history_"+new_).append(" <font color='red'><b>Status:</b> sending failed. (Reason: failed)</font>");
	            }
	        });
 	}
    $('input[name = command]').click();
    return false;  
  }

});  

$(".shell").keydown(function(e) {
	var key = e.which;
    if(key == 38)  {
    	var old = $(".input").css("margin-top");
    	if(old == '0px') {
    		var old = $(".input").css("margin-top");
		 	var shell_text = $(".shell").val();
		 	var new_ = parseInt(old);

		  	var older = $(".command_"+new_).text();
		  	$(".shell").val(older);
		  	console.log('old: '+old+' | shell_text: '+shell_text+' | new_: '+new_+' | text: '+older);
    	} else {
	    	var old = $(".input").css("margin-top");
		 	var shell_text = $(".shell").val();
		 	var new_ = parseInt(old);

		  	var older = $(".command_"+new_).text();
		  	$(".shell").val(older);
		  	console.log('old: '+old+' | shell_text: '+shell_text+' | new_: '+new_+' | text: '+older);
		}
	 }
});

$("html").on('click' ,function(){
	$(".shell").focus();
});


$("body").on('click' ,function(){
	$(".shell").focus();
});

$(document).ready(function () {
	$(".shell").focus();
});

$(document).ready(function () {
    setInterval(function(){
        check_for_response();
        //$(".0").attr("style","border: 1px solid #dddddd;margin-bottom: 5px;");
        }, 5000); /*  */
});

function check_for_response() {

	$('.responsetru').each(function(i) {
        //var lastid = $(this).attr('data-id',i);
        var arr = [parseInt($(this).data('id'))];
        var lastid = Math.max(arr);
		dataString = 'id='+lastid+'&app_key=<?php echo $app_key ?>';
    });
	//var lastid = $(".responsetru").attr("data-id");
    
	$.ajax({
		url: 'script/check_for_response.php',
		type: 'GET',
		data: dataString,
		beforeSend: function(data) {

		},
		success: function(data) {
			if(data.oc_status == 'success') {
				$(".shell").prop('disabled', false);
				$(".shell").focus();
				//$(".shell").prop('disabled', true);
				$(".response_"+data.oc_id).attr("color","#d0d0d0");
				$(".response_"+data.oc_id).text("(Command Executed)");
			} else {

			}
		}
	});
}

<?php



            } 



        } 



        } 



 else 



 



 //if the cookie does not exist, they are taken to the login screen 



 {           



 header("Location: ../signin.php"); 



 } 



 ?> 
