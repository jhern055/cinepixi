<?php $sessioMode=$this->session->userdata("sessionMode_provider");?>
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

		    	if(response.status==1){
					if( $("input#add_other").is(":checked") )
					{$("form.formBasic").get(0).reset();}
					else
					$("div.dinamic_record").html(response.html);	
		    	}
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

				    url: url+"admin/provider/provider_delete",
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
            						{window.location.href="<?php echo base_url().'admin/provider/'; ?>";}

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
<!-- F5 -->
<?php if($sessioMode=="do_it" and !empty($id)){ ?>
<script> $(document).ready(function(){$("div#submit").focus().click(); }); </script>
<?php } ?>

<!-- providerSubsidiary  -->
<script>

form.submitTab2=function(mode,item){

var formData="";
		var	url="<?php echo base_url(); ?>",
		 	fk_provider=$("input[name='fk_provider']").val(),
		 	formInputs=$(item).parent().parent().get(0)
		 	;

		if(mode=="view")
		formData="MODE=view&fk_provider="+fk_provider;

		if(mode=="do_it")
		formData=$(formInputs).serialize()+"&fk_provider="+fk_provider;

		// if(mode=="cancel")
		// formData={MODE:"cancel",id:id};

	$.ajax({

	    url: url+"admin/provider/providerSubsidiaryView",
	    type: 'POST',
	    dataType: 'json',
	    data:formData ,
	    beforeSend: function(response){
// ajax
			$("input").prop("disabled",true);
			$("button").prop("disabled",true);
			$("div#ajax_loading").addClass("ajax_loading");
// ...	    	
	    },
	    success: function(response){
		    	if(response.status==1){

		    		if(mode=="do_it")
		    		$(formInputs).remove(); 

		    		$("div#tab2 > div#dataTab2").append(response.html); 
					
					if(mode=="view")
					$(formInputs).find("div.form-group > ul.token-input-list :input:first").focus();
		    		// $(formInputs).find("input#city").focus();

				}
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

	}) ;

};


$(document).on("click","div#add_providerSubsidiary",function(){
form.submitTab2("view",$(this).get(0));
});

$(document).on("click","div#submitproviderSubsidiary",function(){
form.submitTab2("do_it",$(this).get(0));
});
// $(document).on("click","div#cancel",function(){
// form.submitTab2("cancel",$(this).get(0));
// });
// $(document).on("click","span#add",function(){
// form.submitTab2("add",$(this).get(0));
// });

</script>

<script>
$(document).on("click","div#delete_providerSubsidiary",function(){

var 	id=$(this).data("provider_subsidiary")
		url="<?php echo base_url(); ?>",
		item=$(this).get(0)
		;

$(item).parent().removeClass("form-group");
$(item).parent().addClass("deleteBackground");
$("#dialog > p").text("");
$("#dialog > p").text("Realmente desea eliminar este registro");
$("#dialog > p").dialog({
resizable: false,
modal: true,
    buttons: {
        Si: function() {

			$.ajax({

				    url: url+"admin/provider/providerSubsidiary_delete",
				    type: 'POST',
				    dataType: 'json',
				    data: {
				    	id:id
				    },
				    beforeSend: function(response){
				    },
				    success: function(response){
				    
				    	if(response.status){
						$(item).parent().parent().remove();
				    		$("#dialog > p").text("");
							$("#dialog > p").text("se elimino con exito");
							$("#dialog > p").dialog({
							resizable: false,
							modal: true,
							    buttons: {
							        Correcto: function() {


							       	$("#dialog").append("<p></p>");
        							$(this).dialog( "close" );
									$(item).parent().parent().remove();

							        }
							     }

							 });      		
				    	}

				    }
			 });


        $("#dialog").append("<p></p>");
        $(this).dialog( "close" );

        },
        No: function() {

		$(item).parent().addClass("form-group");

        $("#dialog").append("<p></p>");
        $(this).dialog( "close" );
        }
    }
});

});

</script>
<!-- /providerSubsidiary -->