<?php 
 require_once("db.php"); /* tietokanta */

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
                header("Location: signin"); 
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
    date_default_timezone_set('Europe/Helsinki');


?> <!--DOCTYPE html -->


<?php 

/* 
    @README *DONE 21.11.2017*
    tämän scriptin voisi luoda paremmin sillai että tehtäis yksi function jossa haettais aina -1 päivä jatkuvasti
    eli ei tarvitsis kirjoittaa jokaista päivää niinkun tässä scriptissä on tehty.
    Jos tekis sillai niin olisi mahdollista myös lisätä sivulle asetus josta voi vaihtaa aikaväliä esimerkiksi kun tämä
    skripti hetkellä toimii VAIN 6 edellisen päivän kanssa..
*/

/* 
  esim $secondday -1 päivä nykyisestä eli jos nyt olisi maanantai niin haettais sunnuntaita.
  ucwords() -> muuttaa ensimmäisen kirjaimen isoksi seuraavasti: maanantai -> Maanantai
*/
         
/* Function joka toimii loputtomasti(?) voi rajata vaikka koko vuoden */
function tilastot($dates, $amount) {
  /* tilastot funktio $dates = päivämäärät ja $amount = määrä */
  $new = str_replace(' ',',', $dates); /* lisätään päivämäärien välille pilkku */
  $box_array = explode(',', $new);

  $i = 0; /* lasku on 0 */
  if($i != $amount) {
    if (is_array($box_array) || is_object($box_array))  {
      foreach ($box_array as $this_date) {
        /* haetaan käyttäjän appkey */
        $emailget = $_COOKIE[ID_my_site];
        $get = mysql_query("SELECT * FROM users WHERE email = '$emailget'");
        while ($row = mysql_fetch_assoc($get)) { $app_key = $row["app_key"]; }

       if($amount >= 60) { 
         /* tähän voi muokata tilastonnäkymää(?) että esim jos käyttäjä on rajannut enemmän kuin 60 pv niin sitten tulostuu kk eikä päivät */
         /* haetaan kyseisen päivän -1 päivä $amount */
         $date_day = ucwords(strftime('%A', strtotime($this_date, '-'.$amount.' day')));
         $date_date = ucwords(strftime('%d.%m', strtotime($this_date, '-'.$amount.' day')));
         $date_fulldate = ucwords(strftime('%d.%m.%Y', strtotime($this_date, '-'.$amount.' day')));
       } else {
         $date_day = ucwords(strftime('%A', strtotime($this_date, '-'.$amount.' day')));
         $date_date = ucwords(strftime('%d.%m', strtotime($this_date, '-'.$amount.' day')));
         $date_fulldate = ucwords(strftime('%d.%m.%Y', strtotime($this_date, '-'.$amount.' day')));
       }

       /* kyseinen päivä kello 00:00:00 - 23:59:59 */
       $date_mysql = ucwords(strftime('%Y-%m-%d 00:00:00', strtotime($this_date, '-'.$amount.' day')));
       $date_mysql_2 = ucwords(strftime('%Y-%m-%d 23:59:59', strtotime($this_date, '-'.$amount.' day')));
       /* tehdään tietokanta query jossa kello on $date_mysql ja $date_mysql_2 välillä */
       $get = mysql_query("SELECT * FROM notifications WHERE notification_appkey = '$app_key' AND notification_time BETWEEN '$date_mysql' AND '$date_mysql_2' ORDER BY notification_id DESC");
       $get_rows = mysql_num_rows($get);
       if($date_fulldate != '01.01.1970') { /* jos pvm on väärä niin ei tulosteta */
         echo "['".$date_day." (".$date_date.")', ".$get_rows."],"; /* tulostetaan kaavaan */
       }
       $i++; /* lisätään jokaisen pvm kohdalla +1 laskuriin */
     }
    }
  }
}
     
/* tämä päivä (tänään) */
$currentday = ucwords(strftime( '%A' )); date_default_timezone_set('Europe/Helsinki');
$current_date = strftime( '%d.%m' ); date_default_timezone_set('Europe/Helsinki');
$current_fulldate = strftime( '%d.%m.%Y' ); date_default_timezone_set('Europe/Helsinki');
$currentday_mysqlfulldate_1 = strftime( '%Y-%m-%d 00:00:00' ); date_default_timezone_set('Europe/Helsinki');
$currentday_mysqlfulldate = strftime( '%Y-%m-%d 23:59:59' ); date_default_timezone_set('Europe/Helsinki');



?>



<?php 
/* haetaan asetukset */
$get = mysql_query("SELECT * FROM settings");
while ($row = mysql_fetch_assoc($get)) {
    $amount = $row["setting_amount"];
}

while ($row = mysql_fetch_assoc($get)) {
    $id = $row["notification_id"];
    $time = $row["notification_time"];
    $img = $row["notification_img"];
    
    setlocale(LC_TIME, array('fi_FI.UTF-8','fi_FI@euro','fi_FI','finnish'));
    $new_time = ucwords(strftime('%d.%m.%Y, %A klo %H:%M:%S', strtotime($row["notification_time"]))); 
}
?>
<html>
  <head>
    <title>Tilastot</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="https://marcosraudkett.com/calendar/assets/plugins/datepicker/fi.js"></script>
    <img style="height: 56px;float:left;margin-top: -9px;" src="https://marcosraudkett.com/mvrclabs/code/scripts/admin/images/organizedchaos_withouttext.png">
   <center>
    <form action="" method="GET">
      <input style="height: 30px;width: 150px;padding: 5px;" id="datepicker" value="<?php if(isset($_GET["from"])) { echo $_GET["from"]; } else { echo date('01.m.Y'); } ?>" name="from" type="text" placeholder="Päivämäärästä"> - 
      <input style="height: 30px;width: 150px;padding: 5px;" id="datepicker1" value="<?php if(isset($_GET["to"])) { echo $_GET["to"]; } else { echo date('d.m.Y'); } ?>" name="to" type="text" placeholder="Päivämäärään">
      <button type="submit" style="padding: 7px;margin-top: 2px;background: #4eb9ba;text-decoration: none;color: white;font-family: sans-serif;border-radius: 0px;">Päivitä</button>
    </form>
   </center> 
   <hr style="opacity:0.2;">
   <script type="text/javascript">
      google.charts.load('current', {'packages':['line']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          /* tässä tulostetaan seuraavasti ['Maanantai (20.07)', MÄÄRÄ] */
          ['Päivä', 'Havaintoja'],
          <?php
            $from = $_GET["from"];
            $to = $_GET["to"];

            /* haetaan päivämäärät $from ja $to välillä */
            $period = new DatePeriod(
               new DateTime($from),
               new DateInterval('P1D'),
               new DateTime($to)
            );

            $begin = new DateTime($from);
            $end = new DateTime($to);

            $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
            
            /* lasketaan kuinka monta päivämäärää $from ja $to välillä on (määrä) esim 01.11 - 07.11 = 6 */
            $amount = iterator_count($period);
            foreach($daterange as $date){
                /* haetaan päivämäärät d.m.y muodossa */
                $dates = $date->format("d.m.Y").' ';
                /* tilastot funktio $dates = päivämäärät ja $amount = määrä */
                tilastot($dates, $amount);
            }            

          ?>
          ['<?php echo 'Tänään ('.$current_date.')'; ?>',  <?php echo $current_rows; ?>],
        ]);

        var options = {
          title: 'Edellisen 7 päivän havainnot',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.charts.Line(document.getElementById('line_top_x'));

        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <center>
      <div id="line_top_x" style="width: 1200px; height: 500px"></div>
    </center>
  </body>
  <style>
  .ui-dialog {
    border-radius: 0px !important;
  } 
  .ui-dialog-titlebar {
    background: #4eb9ba !important;
    border-radius: 0px !important;
  }
  .ui-state-hover {
    background: white !important;
  }
  .ui-state-highlight {
    border: 1px solid #4eb9ba !important;
    background: rgba(87, 193, 134, 0.08) !important;
  }
  .ui-state-default {
    background: #efefef !important;
  }
  .ui-state-default:hover {
    background: #d8d8d8 !important;
  }
  .ui-datepicker {
    border-radius: 0px !important;
    font-family: 'Open Sans', 'Segoe UI', 'Droid Sans', Tahoma, Arial, sans-serif !important;
  }
  .ui-datepicker-header {
    background: #4eb9ba !important;
    color: #FFFFFF !important;
    border-radius: 0px !important;
  }
  .ui-datepicker-calendar tr {
    background: white !important;
  }
</style>
  <script>
  $(function(){
$("#datepicker").datepicker();
  $("#datepicker").datepicker({
    language:"fi",
    showWeek:1,
    firstDay:1,
    showButtonPanel:!0,
    dateFormat:"dd.mm.yy"
  }).val()
})
  $(function(){
$("#datepicker1").datepicker();
  $("#datepicker1").datepicker({
    language:"fi",
    showWeek:1,
    firstDay:1,
    showButtonPanel:!0,
    dateFormat:"dd.mm.yy"
  }).val()
})
</script>
</html>
<?php
          } 
        } 
      } else {  
 /* jos käyttäjä ei olekkaan kirjautunut niin se lähetetään kirjautumissivulle. */         
 echo '<script>window.location.href = "../signin";</script>';
 } 



 ?> 
