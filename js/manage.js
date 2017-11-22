$(document).ready(function(){
    if($(".notification").length) {

    } else {
      $("<center><h4 class='none_found'>Ei löydetty yhtäkään havaintoa.</h4></center>").insertAfter("#empty");
      $("#empty").hide();
    }

    $(".tooltip").hide();
});

$(document).ready(function() {
    $(".tab_link").click(function(e) {
        window.location.hash = $(this).attr("id");
        e.preventDefault();
    });
});

$(document).ready(function() {

    $( "#tab1_" ).click(function() {
        document.title = "Organized Chaos - Hallintapaneeli";
        $('.test').each(function() {
            $(this).attr("class","col-md-9"); /* RESETOI LASKIMEN */
        });
    });

    if($( "#tab1_" ).hasClass("active")) {
        document.title = "Organized Chaos - Hallintapaneeli";
    };

    //setInterval(function() {
        var hash = window.location.hash;
        console.log("ready");
        if(window.location.hash == '#tab1_') {
            $("#tab1__").attr("class","tab-pane active");
            $("#tab2__").attr("class","tab-pane");
            $("#tab3__").attr("class","tab-pane");

            $("#tab1").attr("class","tab-pane active");
            $("#tab2").attr("class","tab-pane");
            $("#tab3").attr("class","tab-pane");
        }
        if(window.location.hash == '#tab2_') {
            $("#tab1__").attr("class","tab-pane");
            $("#tab2__").attr("class","tab-pane active");
            $("#tab3__").attr("class","tab-pane");

            $("#tab1").attr("class","tab-pane");
            $("#tab2").attr("class","tab-pane active");
            $("#tab3").attr("class","tab-pane");
        }
        if(window.location.hash == '#tab3_') {
            $("#tab1__").attr("class","tab-pane");
            $("#tab2__").attr("class","tab-pane");
            $("#tab3__").attr("class","tab-pane active");

            $("#tab1").attr("class","tab-pane");
            $("#tab2").attr("class","tab-pane");
            $("#tab3").attr("class","tab-pane active");
        }
    //}(), 1000);
});

$(document).ready(function () {
    setInterval(function(){
        etsi_uutta();
        $(".0").attr("style","border: 1px solid #dddddd;margin-bottom: 5px;");
        }, 2500); /*  */
});

/* !!REAALIAIKAINEN HAKU!! */
function etsi_uutta() {


    if($(".notification").length) {

        if($("#empty").is(":visible")) {
            $("#empty").show();
            $(".none_found").hide();
        } else {
            $("#empty").show();
            $(".none_found").hide();

        }

        /* HAKEE UUSIMMAN ILMOITUKSEN */
        $('.notification').each(function(i) {
            $(this).attr('data-count',i);
            $(this).attr('class',"notification col-md-12 older new " +i);
            //$('.new').attr("class","notification newest col-md-12 new 0");
            var arr = [parseInt($(this).data('count'))];
            var max = Math.max(arr);
            var min = Math.min(arr);
            //console.log('Uusin: '+max);
            var alltogether = [i];
        });
            $('.notification').each(function(i) {
                $(this).attr('data-count',i);
                $(this).attr("class","notification col-md-12 older " +i);
            });

    } else {
        if($("#empty").is(":visible")) {
            $("#empty").show();
            $(".none_found").hide();
        } else {
            $(".none_found").hide();
            $("#empty").show();

        }
    }
    
        /*$('.new').each(function(i) {
            $(this).attr("class","notification newest col-md-12 new");
        });*/


        $(".0").attr("class","notification newest col-md-12 new 0");
        var id = $(".0").attr("id");
        dataString = 'id='+id;

        $.ajax({
            url: 'script/scanner/',
            type: 'GET',
            data: dataString,
            beforeSend: function(data) {

            },
            success: function(data) {
                if(data.oc_status == 'success') {
                    if($(".notification").length) {
                        var hash = window.location.hash;
                        if($('#tab1__').hasClass('active')) {
                            var audio = new Audio('assets/sound/OC_notification.mp3');
                            audio.play();
                        }
                        $('<div id="'+data.oc_id+'" style="background:#d3f7ff;border: 1px solid #dddddd;margin-bottom: 5px;" class="notification col-md-12 new" data-new=""> <div class="col-md-9 test"> <h6>Liikettä havaittu!</h6> <p>Aika: <b>'+data.oc_timestamp+'</b></p><p>Kuva: <a data-title="Havainto: '+data.oc_timestamp+'" data-lightbox="havainto" rel="'+data.oc_img+'" href="uploads/'+data.oc_img+'"><img alt="'+data.oc_img+'" style="height: 45px;margin-top: -5px;" src="uploads/'+data.oc_img+'"></a></p><h6 style="margin-right:-170px;background:#e4e4e4;padding:4px;border-radius:1px;float: right;margin-top: -65px;"> Uusi!</h6></div></div>').insertBefore(".0");
                    } else {
                        $('<div id="'+data.oc_id+'" style="background:#d3f7ff;border: 1px solid #dddddd;margin-bottom: 5px;" class="notification col-md-12 new" data-new=""> <div class="col-md-9 test"> <h6>Liikettä havaittu!</h6> <p>Aika: <b>'+data.oc_timestamp+'</b></p><p>Kuva: <a data-title="Havainto: '+data.oc_timestamp+'" data-lightbox="havainto" rel="'+data.oc_img+'" href="uploads/'+data.oc_img+'"><img alt="'+data.oc_img+'" style="height: 45px;margin-top: -5px;" src="uploads/'+data.oc_img+'"></a></p><h6 style="margin-right:-170px;background:#e4e4e4;padding:4px;border-radius:1px;float: right;margin-top: -65px;"> Uusi!</h6></div></div>').insertAfter('#empty');

                    }

                    $('.test').each(function(i) {
                        $(this).attr('data-new',i);
                        var arr_2 = parseInt($(this).data('new'));
                        var alltogether = [i];
                        //$(this).attr('class',"notification col-md-12 " +i);
                        var max_new = Math.max(arr_2);
                        //console.log(alltogether);
                        if(alltogether == 0) {
                            var total = data.oc_amount;
                            document.title = "("+total+") Organized Chaos - Hallintapaneeli";
                        } else {
                            var total = +alltogether + 1;
                            document.title = "("+total+") Organized Chaos - Hallintapaneeli";
                            if(total >= 5) {
                                $('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Huom!</strong>Yli 5 uutta liikettä on tunnistettu!</div>').insertBefore("#empty");
                            }
                        }
                    });


                }
            }
        });
} 
