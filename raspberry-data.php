<?php

/* 
  @README
  tätä skriptiä ei käytetty lopullisessa projektissa mutta tämän idea olisi tulostaa käyttäjälle
  raspberry:n CPU%, RAM% ja DISK Usage ja tämä onnistuu samalla tavalla kuten Remote Terminal koodi on tehty 
  (shell.php) ja muokkaamalla vähäsen Raspberry:n koodia..
*/
 require_once("../db.php"); /* tietokanta */

 if(isset($_COOKIE['ID_my_site'])){ /* tarkistetaan onko käyttyäjällä cookie ID_my_site olemassa */

        /* HUOM! Ei turvallinen tapa luoda tunnuksia tämä pitäisi vaihtaa esimerkiksi session key systeemiin 
        + salasanat hashata (lue readme.txt) ethän käytä tätä kirjautumistyyliä missään  */
        $email = $_COOKIE['ID_my_site'];  /* COOKIE["ID_my_site"] = Käyttäjänimi */
        $pass = $_COOKIE['Key_my_site'];  /* COOKIE["Key_my_site"] = Salasana */
        /* luodaan tietokanta query :: users jossa :: email on kirjautuneen käyttäjän $email */
        $check = mysql_query("SELECT * FROM users WHERE email = '$email'") or die(mysql_error());
        $email = mysql_real_escape_string($_POST['email']);
        $res = mysql_query("SELECT * FROM users WHERE email='$email'");

    /* haetaan tietokannasta $check */
    while($info = mysql_fetch_array( $check ))   { 
        /* jos salasana ei ole sama kuin tietokannassa niin lähetetään käyttäjä takaisin signin.php sivulle */
        if ($pass != $info['password']) {           
                header("Location: ../signin"); 
            } else { 

    /* käyttäjän email */
    $emailget = $_COOKIE[ID_my_site];
    $get = mysql_query("SELECT * FROM users WHERE email = '$emailget'");

    while ($row = mysql_fetch_assoc($get)) {
        /* luodaan käyttäjän tiedoille parametrit jotka haetaan $get query:istä */
        $firstname = $row["firstname"]; /* käyttäjän etunimi */
        $lastname = $row["lastname"]; /* käyttäjän sukunimi */
        $emailaddr = $row["email"]; /* käyttäjän sähköposti */
        $app_key = $row["app_key"]; /* käyttäjän app key */
    }

    setlocale(LC_TIME, array('fi_FI.UTF-8','fi_FI@euro','fi_FI','finnish')); 
    date_default_timezone_set('Europe/Tallinn');


?> <!--DOCTYPE html -->

<html>
  <head>
    <title>My Berry</title>
    <meta charset="utf-8">
    <link href="https://fonts.googleapis.com/css?family=Palanquin" rel="stylesheet">

  </head>
  <body style="font-family:'Palanquin', sans-serif;">

<div class="loader" style="opacity: 0.5;">
  <div class="inner one"></div>
  <div class="inner two"></div>
  <div class="inner three"></div>
  <p class="loader-text" style="margin-top: 70px;color:black;margin-left:-9px;">Yhdistetään</p>
</div>


    <center>
      <div class="my-berry" style="display: none;">
        <div class="berry-item">
            <div class="berry-content gauge cpu" id="cpu" data-value=""></div>
            <div class="berry-content gauge ram" id="ram" data-value=""></div>
            <div class="berry-content gauge disk" id="disk" data-value=""></div>
        </div>
      </div>
    </center>
  </body>

<script src="https://marcosraudkett.com/mvrclabs/code/scripts/admin/js/jquery-1.8.3.min.js"></script>
<script src="https://marcosraudkett.com/mvrclabs/code/scripts/admin/js/jquery-ui-1.10.3.custom.min.js"></script>
<script src="../js/raphael-2.1.4.min.js"></script>
<script src="../js/justgage.js"></script>
 <script>
  document.addEventListener("DOMContentLoaded", function(event) {

    var dflt = {
      min: 0,
      max: 100,
      donut: true,
      gaugeWidthScale: 0.8,
      counter: true,
      hideInnerShadow: true
    }

    var cpu = new JustGage({
      id: 'cpu',
      title: 'CPU Käyttö (%)',
      defaults: dflt
    });

    var ram = new JustGage({
      id: 'ram',
      title: 'RAM Käyttö (%)',
      defaults: dflt
    });

    var disk = new JustGage({
      id: 'disk',
      title: 'DISK Monitoring (%)',
      defaults: dflt
    });


  });
  </script>
<style>
.gauge {
    width: 250px;
    height: 250px;
    display: inline-block;
}
.my-berry {
    width: 600px;
    margin: 100px auto;
    text-align: center;
}
.loader {
  position: absolute;
  top: calc(50% - 32px);
  left: calc(50% - 32px);
  width: 64px;
  height: 64px;
  border-radius: 50%;
  perspective: 800px;
}

.inner {
  position: absolute;
  box-sizing: border-box;
  width: 100%;
  height: 100%;
  border-radius: 50%;  
}

.inner.one {
  left: 0%;
  top: 0%;
  animation: rotate-one 1s linear infinite;
  border-bottom: 3px solid black;
}

.inner.two {
  right: 0%;
  top: 0%;
  animation: rotate-two 1s linear infinite;
  border-right: 3px solid black;
}

.inner.three {
  right: 0%;
  bottom: 0%;
  animation: rotate-three 1s linear infinite;
  border-top: 3px solid black;
}

@keyframes rotate-one {
  0% {
    transform: rotateX(35deg) rotateY(-45deg) rotateZ(0deg);
  }
  100% {
    transform: rotateX(35deg) rotateY(-45deg) rotateZ(360deg);
  }
}

@keyframes rotate-two {
  0% {
    transform: rotateX(50deg) rotateY(10deg) rotateZ(0deg);
  }
  100% {
    transform: rotateX(50deg) rotateY(10deg) rotateZ(360deg);
  }
}

@keyframes rotate-three {
  0% {
    transform: rotateX(35deg) rotateY(55deg) rotateZ(0deg);
  }
  100% {
    transform: rotateX(35deg) rotateY(55deg) rotateZ(360deg);
  }
}
</style>


<script>
$(document).ready(function () {
    setInterval(function(){
    check_server_status();
    //$(".0").attr("style","border: 1px solid #dddddd;margin-bottom: 5px;");
    }, 5000); /*  */
});
 
function check_server_status() {
    <?php if($_GET["debug"] == 'true') {
        echo 'dataString="app_key='.$app_key.'&debug=true";';
    } else {
        echo "dataString = 'app_key=".$app_key."';";
    } ?>

    $.ajax({
        url: '../script/status/',
        type: 'POST',
        data: dataString,
        beforeSend: function(data) {
            //document.title = "Connecting - (Remote Terminal)";
            $(".loader-text").html('Yhdistetään');
            $(".loader-text").css("margin-left","-9px");
            $(".loader-text").css("width","250px");
            $(".loader-text").css("color","black");
            $(".one").css("animation","rotate-one 1s linear infinite");
            $(".two").css("animation","rotate-two 1s linear infinite");
            $(".three").css("animation","rotate-three 1s linear infinite");
        },
        success: function(data) {
            if(data.oc_status == 'offline') {
                            window.location.href = '../signin';
            } else {
                if(data.oc_status == 'success') {
                    $("body").css("cursor","text");
                    $(".Connected").val(data.oc_ip_address);
                    $(".user").text(data.oc_app_key+"@"+data.oc_ip_address);
                    document.title = "Raspberry Data - "+data.oc_app_key+"@"+data.oc_ip_address;

                    $(".loader-text").text("Successfully Connected.").fadeOut(1200);
                    $(".loader-text").css("color","#94ffb0");
                    $(".loader-text").css("margin-left","-47px");

                    $(".inner.one").css("animation","rotate-one 2s linear infinite");
                    $(".inner.two").css("animation","rotate-two 2s linear infinite");
                    $(".inner.three").css("animation","rotate-three 2s linear infinite");

                    $(".my-berry").fadeIn();
                    $(".loader").fadeOut();
                    //$(".cpu").data("value");
                    //$(".title").fadeIn(2500);
                } else {
                    $("body").css("cursor","default");
                    document.title = "Raspberry Offline - (My-Berry)";
                    $(".loader-text").html('Raspberry offline. <a style="color:green;" onClick="check_server_status()" href="#">Retry</a>');
                    $(".loader-text").css("margin-left","-45px");
                    $(".loader-text").css("width","250px");
                    $(".loader-text").css("color","#ff7373");
                    $(".inner.one").css("animation","rotate-one 5s linear infinite");
                    $(".inner.two").css("animation","rotate-two 5s linear infinite");
                    $(".inner.three").css("animation","rotate-three 5s linear infinite");

                    $(".my-berry").fadeOut();
                    $(".loader").fadeIn();
                    //$(".loader").fadeOut(200);
                }
            }
        },
        error: function(data) {
            $("body").css("cursor","default");
            document.title = "Error occurred while connecting - (My-Berry)";

            $(".loader-text").html('Error occurred while connecting. <a style="color:green;" onClick="check_server_status()" href="#">Retry</a>');
            $(".loader-text").css("margin-left","-85px");
            $(".loader-text").css("width","300px");
            $(".loader-text").css("color","#ff7373");
            $(".inner.one").css("animation","rotate-one 5s linear infinite");
            $(".inner.two").css("animation","rotate-two 5s linear infinite");
            $(".inner.three").css("animation","rotate-three 5s linear infinite");

            $(".my-berry").fadeOut();
            $(".loader").fadeIn();
            //$(".loader").fadeOut(200);
        },
        fail: function(data) {
            $("body").css("cursor","default");
            document.title = "Connection failed - (My-Berry)";

            $(".loader-text").html('Connection Failed. <a style="color:green;" onClick="check_server_status()" href="#">Retry</a>');
            $(".loader-text").css("margin-left","-49px");
            $(".loader-text").css("width","300px");
            $(".loader-text").css("color","#ff94c1");
            $(".inner.one").css("animation","rotate-one 5s linear infinite");
            $(".inner.two").css("animation","rotate-two 5s linear infinite");
            $(".inner.three").css("animation","rotate-three 5s linear infinite");

            $(".my-berry").fadeOut();
            $(".loader").fadeIn();
            //$(".loader").fadeOut(200);
        }
    });
}
</script>

</html>



<?php



            } 



        } 



        } else {           



 header("Location: signin"); 



 } 



 ?> 
