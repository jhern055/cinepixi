<?php $sessioMode=$this->session->userdata("sessionMode_menuMain");?>
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
		
		if(mode=="do_it")
		formData=$("form.formBasic").serialize();

		if(mode=="cancel")
		formData={MODE:"cancel",id:id};

		if(mode=="add")
		formData={MODE:"add",id:null};
	
// certificado y key subidos 

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

$(document).on("click","div.form-group > div#delete",function(){

var 	id=$(this).parent().parent().find("div#hidden > input[name=id]").val(),
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

				    url: url+"web/menus/main/menuMain_delete",
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
            						{window.location.href="<?php echo base_url().'web/menus/main/'; ?>";}

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
 
<!-- F5 -->
<?php if($sessioMode=="do_it" and !empty($id)){ ?>
<script> $(document).ready(function(){$("div#submit").focus().click(); }); </script>
<?php } ?>

<!-- OWN -->
<script type="text/javascript">
$(document).on("change","select#main_module",function(){
var this_it=$(this),	
	val_main_module=$(this).val();
	alert(val_main_module);
	
	$.ajax({

		url: url+"web/menus/main/menuMain_delete",
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
						{window.location.href="<?php echo base_url().'web/menus/main/'; ?>";}

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

		// var jqueryObj=$("#client_subsidiary");
				
		// 		jqueryObj.children().remove();

		// 		if(item.subsidiaries.length>1)
		// 		 jqueryObj.append($("<option />").val("").text(""));

		// 		$.each(item.subsidiaries,function() {

		// 			jqueryObj.append($("<option />").val(this.id).text(this.name));

		// 		});

		// 		if(item.subsidiaries.length>1) {

		// 			jqueryObj.prop("disabled",false);
		// 			jqueryObj.get(0).focus();

		// 		}

		// 		if(item.subsidiaries.length==1)
		// 		client_subsidiary_information.get(item.subsidiaries[0].id);
		// 		else
		// 		$(client_subsidiary_information.container).text("");

});

    </script>

<script>
$(document).on("click","input#main_module",function(){

	var html="<?php echo $main_module;?>";
	$(document).parent().append(html);

});
</script>
    
<!-- / OWN -->