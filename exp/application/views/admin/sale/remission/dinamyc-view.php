<?php $sessioMode=$this->session->userdata("sessionMode_remission");?>
<div class="dinamic_record">
    <?php echo $this->load->view($data['module_data']["link"]."dinamyc-inputs",$data,true); ?>
</div>
<!-- /.container-fluid -->
<script>
form = Object();
form.submit=function(mode,item){
		var	url="<?php echo base_url(); ?>"
		id="<?php echo encode_id($id); ?>",
		formData=""
		;

	 var details ="";

	$("div.articleListContainer.documentViewDefault > div.area2 > div.data > div.itemArticle").each(function(i){

	// id_remission :"<?php  echo (isset($id)?$id:'');?>",

	details+="&details["+i+"][id]="+$(this).find("div.id").text()
			+"&details["+i+"][stockModification]="+( $(this).find("input#stockModification:checked:eq(0)").length ? "1" : "0")
			+"&details["+i+"][quantity]="+$(this).find("div.quantity").text()
			+"&details["+i+"][article]="+$(this).find("div.article").text()
			+"&details["+i+"][description]="+$(this).find("div.description").text()
			+"&details["+i+"][price]="+$(this).find("div.price").text()
			+"&details["+i+"][totalSub]="+$(this).find("div.totalSub").text()
			+"&details["+i+"][taxIeps]="+$(this).find("div.taxIeps").text()
			+"&details["+i+"][taxIva]="+$(this).find("div.taxIva").text()
			+"&details["+i+"][taxIvaRetained]="+$(this).find("div.taxIvaRetained").text()
			+"&details["+i+"][taxIsr]="+$(this).find("div.taxIsr").text()
			;

	});

		if(mode=="do_it")
		formData=$("form.formBasic").serialize()+"&details="+details;

		if(mode=="cancel")
		formData={MODE:"cancel",id:id};

		if(mode=="add")
		formData={MODE:"add",id:null};
	
// envia la informacion
		$.ajax({
		    url: url+"<?php echo $data['module_data_method_do_it']; ?>",
		    type: 'POST',
		    dataType: 'json',
		    data: formData,
		    beforeSend: function(response){

// ajax
			$("input").prop("disabled",true);
			$("button").prop("disabled",true);
			$("div#ajax_loading").addClass("ajax_loading");
// ...		    	

		    },
		    success: function(response){

		    	if(response.status==1)
		    	$("div.dinamic_record").html(response.html); 
		    	else{

		    		if(response.redirect){
						$("#dialog > p").text(response.redirect);
						$("#dialog").html("<p></p>");
		    			return;
		    		}
						$("#dialog > p").text("");
						$("#dialog > p").text(response.msg);
						$("#dialog > p").dialog({
							resizable: false,
							modal: true,
								buttons: {
									Aceptar: function() {

										$("#dialog").append("<p></p>");
										$(this).dialog( "close" );
									}
								}
						});
		    	}

// ajax
		    $("input").prop("disabled",false);
		    $("button").prop("disabled",false);
		    $("div#ajax_loading").removeClass("ajax_loading");
// ...

		    }
		 });

	return false;	
};

$(document).on("click","div#submit",function(){
form.submit("do_it",$(this).get(0));
});
$(document).on("click","div#cancel",function(){
form.submit("cancel",$(this).get(0));
});
$(document).on("click","span#add",function(){
form.submit("add",$(this).get(0));
});

$(document).on("click","div#delete",function(){

var 	id="<?php echo encode_id($id); ?>",
		url="<?php echo base_url(); ?>"
		;

$("#dialog > p").text("");
$("#dialog > p").text("Realmente desea eliminar este registro");
$("#dialog > p").dialog({
resizable: false,
modal: true,
    buttons: {
        Si: function() {
        	
			$.ajax({

				    url: url+"admin/sale/remission/remission_delete",
				    type: 'POST',
				    dataType: 'json',
				    data: {
				    	id:id
				    },
				    beforeSend: function(response){

				    // ajax
				    $("input").prop("disabled",true);
				    $("button").prop("disabled",true);
				    $("div#ajax_loading").addClass("ajax_loading");
				    // ...

				    },
				    success: function(response){

				    		$("#dialog > p").text("");
							$("#dialog > p").text(response.msg);
							$("#dialog > p").dialog({
							resizable: false,
							modal: true,
							    buttons: {
							        Correcto: function() {
							        	
							        if(response.status)
            						{window.location.href="<?php echo base_url().'admin/sale/remission/'; ?>";}

							       	$("#dialog").append("<p></p>");
        							$(this).dialog( "close" );
							        }
							     }

							 });      		

					// ajax
					$("input").prop("disabled",false);
					$("button").prop("disabled",false);
					$("div#ajax_loading").removeClass("ajax_loading");
					// ...	

				    }
			 });


        $("#dialog").append("<p></p>");
        $(this).dialog( "close" );

        },
        No: function() {

        $("#dialog").append("<p></p>");
        $(this).dialog( "close" );
        }
    }
});




 });
    
</script>

<script>
function alert_danger(field,msg){
var error_html='<div class="alert alert-danger">'
                +'<a class="close" data-dismiss="alert" href="#">&times;</a>'
                +' <a href="#" class="alert-link not-active">'+field+'</a> '
                +'<p>'+msg+'.</p>'
                +'</div>'
                ;
    return error_html;
}
</script>

<!-- Agregar un articulo -->
<script>

$(document).on("click","div.add_detail",function(){

// esconder el boton momentaneamente el de agregar
// $(this).hide();
var url ="<?php echo base_url().'article/add_detail';?>";

    $.ajax({
        type: "POST",
        url: url,
        async:true,
        dataType:"json",
        data:{MODE:"do_it"}, 
        beforeSend:  function(response) {
        },
        success: function(response){

	    	$("div.articleListContainer > div.area2 > div.data").append(response);

        }

    });

});

// Boton de ACEPTAR que deseo agregarlo
// $(document).on("click","div.articleListContainer > div.area2 > div.data > div.itemArticle > div.editionActions > button.accept",function() {
$(document).on("click","button.submit_article",function() {

var this_boton=$(this),
	this_boton_get=$(this).get(0);

var url ="<?php echo base_url().'article/add_detail_do';?>";

var c=0,
MODE=$(this).parent().parent().find("div#hidden").find("input[name='MODE']").val();
;

var details=[];

// contar los divs para saber que key le pondre al arreglo 
$("div.articleListContainer > div.area2 > div.data > div.itemArticle").each(function(i){c++;});

if(MODE=="do_it"){
	details[c]= {
	id:$(this).parent().parent().find("div.id").text() ,
	stockModification:$(this).parent().parent().find("div.stockModification").find("input#stockModification:checked:eq(0)").length ? "1" : "0" ,
	quantity:$(this).parent().parent().find("div.quantity").find("input#quantity").val(),
	article:$(this).parent().parent().find("div.article").find("input#article").val(),
	description:$(this).parent().parent().find("div.description").find("input#description").val(),
	price:$(this).parent().parent().find("div.price").find("input#price").val(),
	totalSub:$(this).parent().parent().find("div.totalSub").find("span.totalSub").text(),
	taxIeps:$(this).parent().parent().find("div.taxIeps").find("input#taxIeps").val(),
	taxIva:$(this).parent().parent().find("div.taxIva").find("input#taxIva").val(),
	taxIvaRetained:$(this).parent().parent().find("div.taxIvaRetained").find("input#taxIvaRetained").val(),
	taxIsr:$(this).parent().parent().find("div.taxIsr").find("input#taxIsr").val()
	};
}
else{
	details[c]= {
	id_remission :"<?php  echo (isset($id)?$id:'');?>",
	id:$(this).parent().parent().find("div.id").text() ,
	stockModification:$(this).parent().parent().find("div.stockModification").find("input#stockModification:checked:eq(0)").length ? "1" : "0" ,
	quantity:$(this).parent().parent().find("div.quantity").text(),
	article:$(this).parent().parent().find("div.article").text(),
	description:$(this).parent().parent().find("div.description").text(),
	price:$(this).parent().parent().find("div.price").text(),
	totalSub:$(this).parent().parent().find("div.totalSub").text(),
	taxIeps:$(this).parent().parent().find("div.taxIeps").text(),
	taxIva:$(this).parent().parent().find("div.taxIva").text(),
	taxIvaRetained:$(this).parent().parent().find("div.taxIvaRetained").text(),
	taxIsr:$(this).parent().parent().find("div.taxIsr").text()
	};
}

    $.ajax({
        type: "POST",
        url: url,
        async:true,
        dataType:"json",
        data:{
        	details:details,
        	MODE:MODE,
        	edit:true,
	        DAD_MODE:"do_it",

        }, 
        beforeSend:  function(response) {
        },
        success: function(response){

	    	$(this_boton).parent().parent().html(response);

			// Mostrar el boton de agregar 
			$("div.buttonsContainer > div.area4 > div.add_detail").show();

			// sumatoria de el detalle
			detailsCalculateSummary();

        }

    });

});

$(document).on("click","div.articleListContainer > div.area2 > div.data > div.item > div.ops > button.delete",function(){
var this_it=$(this);

	$("#dialog > p").text("");
	$("#dialog > p").text("Realmente desea eliminar este articulo");
	$("#dialog > p").dialog({
	resizable: false,
	modal: true,
	    buttons: {
	        Si: function() {
			$("#dialog").append("<p></p>");
			$(this).dialog( "close" );
			$(this_it).parent().parent().remove();
			},
			No: function() {
			$("#dialog").append("<p></p>");
			$(this).dialog( "close" );
			}
		}			
	});
});

</script>

<script type="text/javascript">
// <cancel>
// Boton de Cancelar (no agregar articulo)
$(document).on("click","div.articleListContainer > div.area2 > div.data > div.itemArticle > div.editionActions > button.cancel",function() {
 obj=new Object();
 obj.id=$(this).parent().parent().find("div.id").text();
 obj.this_it=$(this);
 obj.url="<?php echo base_url();?>";

	$.ajax({
        type: "POST",
        url: obj.url+"article/get_detail_by",
        async:true,
        dataType:"json",
        data:{
        	id: obj.id,
        	source_module:"remission",
        	module:"<?php echo encode_id('remission'); ?>",
    		id_record:"<?php echo encode_id($id); ?>",

        	 }, 
        beforeSend:  function(response) {
        },
        success: function(response){
		chargeCancel(response);
        }

    });

	function chargeCancel (details) {

	    $.ajax({
	        type: "POST",
	        url: obj.url+'article/add_detail_do',
	        async:true,
	        dataType:"json",
	        data:{
	        	details:details,
	        	MODE:"do_it",
	        	edit:true,
	        	module:"<?php echo encode_id('remission'); ?>",
        		id_record:"<?php echo encode_id($id); ?>",
	        	DAD_MODE:"do_it",

	        }, 
	        beforeSend:  function(response) {
	        },
	        success: function(response){

		    	$(obj.this_it).parent().parent().html(response);

				// Mostrar el boton de agregar 
				$("div.buttonsContainer > div.area4 > div.add_detail").show();

				// sumatoria de el detalle
				detailsCalculateSummary();

	        }

	    });
	}

// Mostrar el boton de agregar 
$("div.buttonsContainer > div.area4 > div.add_detail").show();

});
// </cancel>
</script>
<!-- F5 -->
<?php if($sessioMode=="do_it" and !empty($id)){ ?>
<script> $(document).ready(function(){$("div#submit").focus().click(); }); </script>
<?php } ?>

<!-- SEND ENVIAR -->
<?php if(!empty($id)){ ?>
<script>
$(document).on("click","a#send",function(){

var client_email=$("input#client_email").val(),
	email="";
	
	email=prompt("Emails separados por comas",client_email);

	$.ajax({
        type: "POST",
        url: "<?php echo base_url().'email/send/';?>",
        // async:true,
        dataType:"json",
        data:{  
        
        id:"<?php echo encode_id($id);?>",
        source_module:"<?php echo encode_id('admin/sale/remission/');?>",
        email:email,
        document_type:4,

        }, 
        beforeSend:  function(response) {
	    // ajax
	    $("input").prop("disabled",true);
	    $("button").prop("disabled",true);
	    $("div#ajax_loading").addClass("ajax_loading");
	    // ...
        },
        success: function(response){

		// ajax
		$("input").prop("disabled",false);
		$("button").prop("disabled",false);
		$("div#ajax_loading").removeClass("ajax_loading");
		// ...

			$("#dialog > p").text("");
			$("#dialog > p").text(response.msg);
			$("#dialog > p").dialog({
			resizable: false,
			modal: true,
			    buttons: {
			        Aceptar: function() {
					$("#dialog").append("<p></p>");
					$(this).dialog( "close" );
					
					},
				}			
			});	        		
        }

	});

});

</script>
<?php } ?>