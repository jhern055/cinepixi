<?php $sessioMode=$this->session->userdata("sessionMode_pathFile");?>
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

// certificado y key subidos 
var files ="";

$("div.image_file > div.imUp > div#files > tr").each(function(i){

	files+="&files["+i+"][images_name]="+$(this).find("td.images_name").text()
			;
});
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
		    // console.log(response);
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
		url="<?php echo base_url(); ?>";

$("#dialog > p").text("");
$("#dialog > p").text("Realmente desea eliminar este registro");
$("#dialog > p").dialog({
resizable: false,
modal: true,
    buttons: {
        Si: function() {
        	

			$.ajax({

				    url: url+"cinepixi/pathFile/pathFile_delete",
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
            						{window.location.href="<?php echo base_url().'cinepixi/pathFile/'; ?>";}

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

<script src='<?php echo base_url() ?>js/jQueryFileUpload/jquery.fileupload.js'></script>
<script src='<?php echo base_url() ?>js/jQueryFileUpload/jquery.fileupload-ui.js'></script>
<!-- para subir archivos por ajax -->
<?php //require_once(FCPATH."js/upload-ajax.php"); ?>  

<!-- agregar y quitar categorias -->
<script>
$(document).on("click","div#product-category > i.fa-minus-circle",function(){
$(this).parent().remove();
});
</script>