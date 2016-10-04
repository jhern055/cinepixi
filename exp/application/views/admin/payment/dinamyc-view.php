<?php $sessioMode=$this->session->userdata("sessionMode_payment");?>
<div class="dinamic_record">
    <?php echo $this->load->view($module_data["link"]."dinamyc-inputs",$data,true); ?>
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

	$("div.paymentListContainer.documentViewDefault > div.area2 > div.data > div.itemPayment").each(function(i){

	// id_payment :"<?php  echo (isset($id)?$id:'');?>",

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
		    url: url+"<?php echo 'payment/paymentView/'; ?>",
		    type: 'POST',
		    dataType: 'json',
		    data: formData,
		    beforeSend: function(response){
		    	console.log(response);

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

				    url: url+"admin/sale/payment_delete",
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
            						{window.location.href="<?php echo base_url().'sale/payment/'; ?>";}

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

<!-- Agregar un pago -->
<script>

$(document).on("click","div.add_payment",function(){

// esconder el boton momentaneamente el de agregar
// $(this).hide();
var url ="<?php echo base_url().'payment/add_payment';?>";

    $.ajax({
        type: "POST",
        url: url,
        async:true,
        dataType:"json",
        data:{MODE:"do_it"}, 
        beforeSend:  function(response) {
// ajax
			$("input").prop("disabled",true);
			$("button").prop("disabled",true);
			$("div#ajax_loading").addClass("ajax_loading");
// ...        	
        },
        success: function(response){

	    	$("div.paymentListContainer > div.area2 > div.data").append(response);
	    	$("input#import").focus();
// ajax
		    $("input").prop("disabled",false);
		    $("button").prop("disabled",false);
		    $("div#ajax_loading").removeClass("ajax_loading");
// ...
        }

    });

});

// Boton de ACEPTAR que deseo agregarlo
// $(document).on("click","div.paymentListContainer > div.area2 > div.data > div.itemPayment > div.editionActions > button.accept",function() {
$(document).on("click","button.submit_payment",function() {

var this_boton=$(this),
	this_boton_get=$(this).get(0),
	import_bill =$("label.importBill").text(),
	payment     =$("label.payment").text(),
	residuary   =$("label.residuary").text()
	;

var url ="<?php echo base_url().'payment/add_payment_do';?>";

var c=0,
MODE=$(this).parent().parent().find("div#hidden").find("input[name='MODE']").val();
;

var payment_details=[];

// contar los divs para saber que key le pondre al arreglo 
$("div.paymentListContainer > div.area2 > div.data > div.itemPayment").each(function(i){
	c++;
});

if(MODE=="do_it"){
	payment_details[c]= {
	module :"<?php echo (!empty($_GET['module'])?$_GET['module']:""); ?>",
	id:$(this).parent().parent().find("div.id").data("id"),
	method:$(this).parent().parent().find("div.method").find("select#method").val(),
	import:$(this).parent().parent().find("div.import").find("div.value").find("input#import").val(),
	type_of_currency:$(this).parent().parent().find("div.type_of_currency").find("select#type_of_currency").val(),
	exchange_rate:$(this).parent().parent().find("div.exchange_rate").find("input#exchange_rate").val(),
	comment:$(this).parent().parent().find("div.comment").find("input#comment").val(),
	date:$(this).parent().parent().find("div.date").find("input#date").val(),
	};
}
else{
	payment_details[c]= {
	module :"<?php echo (!empty($_GET['module'])?$_GET['module']:""); ?>",
	id:$(this).parent().parent().find("div.id").data("id"),
	method:$(this).parent().parent().find("div.method").data("method_id"),
	import:$(this).parent().parent().find("div.import").find("div.value").text(),
	type_of_currency:$(this).parent().parent().find("div.type_of_currency").data("type_of_currency_id"),
	exchange_rate:$(this).parent().parent().find("div.exchange_rate").text(),
	comment:$(this).parent().parent().find("div.comment").text(),
	date:$(this).parent().parent().find("div.date").text(),
	};
}

    $.ajax({
        type: "POST",
        url: url,
        async:true,
        dataType:"json",
        data:{
        	payment_details:payment_details,
        	MODE:MODE,
        	edit:true,
        	module:"<?php echo (!empty($_GET['module'])?$_GET['module']:""); ?>",
        	id_record:"<?php echo (!empty($_GET['id'])?$_GET['id']:""); ?>",
        	source_module:"<?php echo (!empty($_GET['source_module'])?$_GET['source_module']:""); ?>",
        }, 
        beforeSend:  function(response) {

// ajax
			$("input").prop("disabled",true);
			$("button").prop("disabled",true);
			$("div#ajax_loading").addClass("ajax_loading");
// ...
        },
        success: function(response){

        	if(response.status){
		    	$(this_boton).parent().parent().html(response.html);

				// Mostrar el boton de agregar 
				$("div.buttonsContainer > div.area4 > div.add_payment").show();

				// sumatoria de el detalle
				paymentBalanceCalculateSummary();
			}else{

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

});

$(document).on("click","div.paymentListContainer > div.area2 > div.data > div.item > div.ops > button.delete",function(){
var item=$(this).get(0);

	$("#dialog > p").text("");
	$("#dialog > p").text("Realmente desea eliminar este pago");
	$(item).parent().parent().addClass("deleteStyle");
	$("#dialog > p").dialog({
	resizable: false,
	modal: true,
	    buttons: {
	        Si: function() {
			$("#dialog").append("<p></p>");
			$(this).dialog( "close" );
			$(item).parent().parent().removeClass("deleteStyle");

// -----------------------------------
    $.ajax({
        type: "POST",
        url: "<?php echo base_url().'payment/delete_payment'; ?>",
        async:true,
        dataType:"json",
        data:{
			id:$(item).parent().parent().find("div.id").data("id"),
        	module:"<?php echo (!empty($_GET['module'])?$_GET['module']:""); ?>",
        	id_record:"<?php echo (!empty($_GET['id'])?$_GET['id']:""); ?>",
        	source_module:"<?php echo (!empty($_GET['source_module'])?$_GET['source_module']:""); ?>",
        }, 
        beforeSend:  function(response) {
// ajax
			$("input").prop("disabled",true);
			$("button").prop("disabled",true);
			$("div#ajax_loading").addClass("ajax_loading");
// ...
        },
        success: function(response){

        	if(response.status)
				paymentBalanceCalculateSummary();
			else{

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

// -----------------------------------
			// elimina el item
			$(item).parent().parent().remove();
			
			},
			No: function() {
			$("#dialog").append("<p></p>");
			$(this).dialog( "close" );
			$(item).parent().parent().removeClass("deleteStyle");

			}
		}			
	});
});

</script>

<script type="text/javascript">
// <cancel>
// Boton de Cancelar (no agregar pago)
$(document).on("click","div.paymentListContainer > div.area2 > div.data > div.itemPayment > div.editionActions > button.cancel",function() {
 obj=new Object();
 obj.id=$(this).parent().parent().find("div.id").data("id");
 obj.this_it=$(this);
 obj.url="<?php echo base_url();?>";
 obj.id_record=$("div#hidden > input[name=id]").val();
 obj.module=$("div#hidden > input[name=module]").val();

	$.ajax({
        type: "POST",
        url: obj.url+"admin/payment/get_payment_by",
        dataType:"json",
        data:{
        	id: obj.id,
        	id_record:obj.id_record,
        	source_module:obj.module
        	 }, 
        beforeSend:  function(response) {
// ajax
			$("input").prop("disabled",true);
			$("button").prop("disabled",true);
			$("div#ajax_loading").addClass("ajax_loading");
// ...        	

        },
        success: function(response){
        
		chargeCancel(response);
		if(!response)
		msg(response);

// ajax
		    $("input").prop("disabled",false);
		    $("button").prop("disabled",false);
		    $("div#ajax_loading").removeClass("ajax_loading");
// ...
        }

    });

	function chargeCancel (payment_details) {

	    $.ajax({
	        type: "POST",
	        url: obj.url+'payment/add_payment_do',
	        async:true,
	        dataType:"json",
	        data:{
	        	payment_details:payment_details,
	        	MODE:"do_it",edit:true,
	        	source_module:"<?php echo (!empty($_GET['source_module'])?$_GET['source_module']:""); ?>",
	        	module:"<?php echo (!empty($_GET['module'])?$_GET['module']:""); ?>",
        		id_record:"<?php echo (!empty($_GET['id'])?$_GET['id']:""); ?>",
	        }, 
	        beforeSend:  function(response) {

// ajax
			$("input").prop("disabled",true);
			$("button").prop("disabled",true);
			$("div#ajax_loading").addClass("ajax_loading");
// ...

	        },
	        success: function(response){
	        	if(response.status)
		    	$(obj.this_it).parent().parent().html(response.html);
		    	else{

				msg(response);
		    	$(obj.this_it).parent().parent().remove();
		    	}

				// Mostrar el boton de agregar 
				$("div.buttonsContainer > div.area4 > div.add_payment").show();

				// sumatoria de el detalle
				paymentBalanceCalculateSummary();
// ajax
		    $("input").prop("disabled",false);
		    $("button").prop("disabled",false);
		    $("div#ajax_loading").removeClass("ajax_loading");
// ...
	        }

	    });
	}

// Mostrar el boton de agregar 
$("div.buttonsContainer > div.area4 > div.add_payment").show();

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
// $(document).on("click","a#send",function(){

// var email=prompt("Poner email","");

// 	$.ajax({
//         type: "POST",
//         url: "<?php echo base_url().'email/send/';?>",
//         // async:true,
//         dataType:"json",
//         data:{  
        
//         id:"<?php echo encode_id($id);?>",
//         source_module:"<?php echo encode_id('sale/payment/');?>",
//         email:email,

//         }, 
//         beforeSend:  function(response) {
// 	    // ajax
// 	    $("input").prop("disabled",true);
// 	    $("button").prop("disabled",true);
// 	    $("div#ajax_loading").addClass("ajax_loading");
// 	    // ...
//         },
//         success: function(response){

// 		// ajax
// 		$("input").prop("disabled",false);
// 		$("button").prop("disabled",false);
// 		$("div#ajax_loading").removeClass("ajax_loading");
// 		// ...

// 			$("#dialog > p").text("");
// 			$("#dialog > p").text(response.msg);
// 			$("#dialog > p").dialog({
// 			resizable: false,
// 			modal: true,
// 			    buttons: {
// 			        Aceptar: function() {
// 					$("#dialog").append("<p></p>");
// 					$(this).dialog( "close" );
					
// 					},
// 				}			
// 			});	        		
//         }

// 	});

// });

function msg(response){

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
</script>
<?php } ?>