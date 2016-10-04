

    </div>
    <!-- /mainContainer -->

    </div>
    <!-- /.col main -->

    </div>
    <!-- /#wrapper -->
    </div>
    <!-- /#col bootrap -->

    <!-- jQuery -->

    </div>
    <!-- /.container-fluid -->
    
    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo base_url().'css/bootstrap/admin2/bower_components/bootstrap/dist/js/bootstrap.min.js'; ?>"></script>

<div id="dialog" title="">
  <p> </p>
</div>
 

 <script>
$(document).on("click","button.btn-default",function(e){
   $("div.sidebar").toggle();
   $("div.container-fluid").css( { "margin-left" : "0px" } );
 });


$(function() {
    // $(this).bind("contextmenu", function(e) {
    //     e.preventDefault();
    // });
}); 

$(document).ready(function(){

$("ul > li > a#a_menu_click").mousedown(function(e){
    
var module=$(this).data("href"),
    url="<?php echo base_url(); ?>"
    ;
if(!module)
return false;
    //1: izquierda, 2: medio/ruleta, 3: derecho
    if(e.which == 2)
    window.open(url+module,'_blank');
    else{

        // if(e.which == 3)
        // {window.location.href=url+module+'View'; }
        // else{
        console.log(module);

            $.ajax({
                type: 'POST',
                url: url+module,
                dataType:"json",
                async:false,
                data:{uri_string:module,ajax:1},
                beforeSend: function(response){
                    // ajax
        console.log(response);
                    
                    $("input").prop("disabled",true);
                    $("button").prop("disabled",true);
                    $("div#ajax_loading").addClass("ajax_loading");
                    // ...
                },
                success: function(response){
        // console.log(response);

                    $('div#mainContainer').html(response.html);
                    $('div#mainContainer').focus();

                    // ajax
                    $("input").prop("disabled",false);
                    $("button").prop("disabled",false);
                    $("div#ajax_loading").removeClass("ajax_loading");
                    // ...

                }
            });
        // }
    }

});

// menu ajax
// $(document).on("click","ul > li > a.a_menu",function(e){

// var module=$(this).data("href"),
//     url="<?php echo base_url(); ?>"
//     ;

    // if(module){

    //     $.ajax({
    //         type: 'POST',
    //         url: url+module,
    //         dataType:"json",
    //         data:{uri_string:module,ajax:1},
    //         beforeSend: function(response){

    //             // ajax
    //             $("input").prop("disabled",true);
    //             $("button").prop("disabled",true);
    //             $("div#ajax_loading").addClass("ajax_loading");
    //             // ...
    //         },
    //         success: function(response){

    //             $('div#mainContainer').html(response.html);
    //             $('div#mainContainer').focus();

    //             // ajax
    //             $("input").prop("disabled",false);
    //             $("button").prop("disabled",false);
    //             $("div#ajax_loading").removeClass("ajax_loading");
    //             // ...

    //         }
    //     });
    // }
// });


});
// .......................
 </script>


<!-- buscar un modulo en el menu -->
  <script>
$(document).on("keyup", "input#search_module", function() {

    $.ajax({

        type: "POST",
        data: {ajax:1,input_search:$(this).val()},
        dataType:'json',
        // contentType: "application/json",
        url: "<?php echo base_url().'getMenu/'; ?>",
        beforeSend:function (html) {

        },
        success: function(html) {
       // console.log(html);

        $('div#cssmenu').html('');
        $('div#cssmenu').html(html.menu);

        }

     });

});

 </script>

<!-- Menu Toggle Script -->
<script>
$("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");

    if( $(this).hasClass( "leftarrow" ) )
    {$(this).removeClass("leftarrow"); $(this).addClass("rightarrow");}
    else{
        if( $(this).hasClass( "rightarrow" ) )
        {$(this).removeClass("rightarrow"); $(this).addClass("leftarrow");}
    }

});
</script>

</body>

</html>
