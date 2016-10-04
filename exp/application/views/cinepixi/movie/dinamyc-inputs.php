<?php 
$MODE=(empty($MODE)?"view":$MODE);
$form["MODE"]=form_hidden("MODE",$MODE);
$form["id"]=form_hidden("id",encode_id($id));

if($MODE=="do_it"):

$form["name"]        =form_input("name",$name," id='name'  placeholder='nombre'" );
$form["category_id"] =form_input("category_id",$category_id," id='category_id'  placeholder='categoria'" );
$form["pathFile_id"] =form_input("pathFile_id",$pathFile_id," id='pathFile_id'  placeholder='Path'" );
// $form["pathFile"] =form_dropdown('pathFile', $pathFile_array, "var");
$form["resolution"]=form_dropdown('resolution',$sys["forms_fields"]["resolution"],null,"id='resolution'");

$add_other = array(
    'name'        => 'add_other',
    'id'          => 'add_other',
    'checked'     => false
    );


$form["add_other"]=form_checkbox($add_other);

$txt_boton="Guardar";

else:

$form["name"]        =$name;
$form["category_id"] =(!empty($category_id)?array_search($category_id,array_flip($movie_categories) ):0);
$form["pathFile"] =(!empty($pathFile)?array_search($pathFile,array_flip($pathFile_array) ):0);
$form["resolution"]        =(!empty($resolution)?$resolution:"");

$txt_boton="Editar";

endif;


?>
    <div class="row">
        <div class="col-lg-12">
		
       	<div class="panel panel-default">
        <?php echo $this->load->view("recycled/menu/panel_heading","",true); ?>

	        <!-- /.panel-heading -->
	       
	        <div class="panel-body">
	            <div class="row">

<?php $attributes_form = array('class' => 'formBasic'); ?>
<?php  echo form_open("form",$attributes_form);?>

							<div class="form-group" style='display:none' id="hidden">
	                            <?php echo $form["MODE"]."/"; ?>
	                            <?php echo $form["id"]; ?>
	                        </div>
							<div class="form-group">
								<div id="message"></div>
	                        </div>
	                  
	                        <div class="form-group">
	                            <?php echo form_label("Nombre:"); ?>
	                            <?php echo $form["name"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Categoria:"); ?>
	                            <?php echo $form["category_id"]; ?>
	                        </div>	
		                                                
	                        <div class="form-group">
	                            <?php echo form_label("Resolución"); ?>
	                            <?php echo $form["resolution"]; ?>
	                        </div>	

	                        <?php if($MODE=="do_it" and !$id): ?>
	                        <div class="form-group">
	                            <?php echo form_label("Agregar otro?:"); ?>
	                            <?php echo $form["add_other"]; ?>
	                        </div>
	                    	<?php endif; ?>

<?php  echo form_close();?>

				                <!-- <imagen Importar> -->
		                        <div class="form-group image_file">

				                <?php 
									$data_file = array(
									'name'     => 'file',
									'id'       => 'file', 
									'type'     => 'button',
									'tabindex' => 1,
									'class'    =>'ui-button-text',
									'multiple' =>true,
									);
									$movie = array(
									"role"=>"form",
									'id'=>'form_file_upload',
									"name"=>'form_file_upload',
									"method"=>"POST",
									"enctype"=>"multipart/form-data"
									);
				                 ?>

		                        <?php echo form_label("Subir </br>Pelicula"); ?>

						                <div class="imUp">
						                        <?php echo form_open(base_url().'file/doUploadFile/?process='.encode_id("movie")."&id=".encode_id($id),$movie); ?>   
	                        
	                        <div class="form-group">
	                            <?php echo form_label("Path File:"); ?>
	                            <?php echo $form["pathFile_id"]; ?>
	                        </div>						                   

						                    <div class="upload">
						                        <?php echo form_upload($data_file); ?>
						                    </div>

						                        <?php echo form_close(); ?>

						                    
						                    <div id="files"></div>

						                </div>

		                        </div>

				                <!-- </imagen Importar> -->	 

	                        <div class="form-group">
	                        	<div class="btn btn-primary" id="submit"><?php echo $txt_boton; ?></div>
	                        	<?php if(!empty($id)): ?>
	                        	    <?php if($MODE=="do_it"): ?>
	                        		<div class="btn btn-warning" id="cancel"><?php echo "Cancelar"; ?></div>
	                        		<?php endif; ?>	  	                        	
	                        	<div class="btn btn-danger" id="delete"><?php echo "Eliminar"; ?></div>
	                        	<?php endif; ?>
	                        </div>


                </div>
	            <!-- /.row (nested) -->
	        </div>
	        <!-- /.panel-body -->
	    </div>
		<!-- /.panel-default -->
	    </div>
        <!-- /.col-lg-12 -->
       	</div>
		<!-- /.row -->

    </div>

<script>
    	// TOKEN INPUT DEL PROVEEDOR
    $("#category_id").tokenInput("<?php echo base_url().'cinepixi/movie/category/category_tokeninput'; ?>", {
        queryParam:"request[name]",
		hintText:"escribe para buscar coincidencias",
		noResultsText:"no hubo coincidencias",
		searchingText:"buscando...",
		tokenLimit:1,
		resultsFormatter:function(item){
			// console.log(item);
			return  "<li>"
						+item.id+" - "+item.name
					+"</li>";

		},
		<?php if(($MODE=="do_it") and !empty($category_id) ): ?>
			prePopulate:[
				{id:<?php echo json_encode($category_id); ?>,name:<?php echo json_encode( (!empty($category_id)?array_search($category_id, array_flip($movie_categories) ) :'' ) ); ?>,},
			],
		<?php endif; ?>

    });
</script>

<script>
$(document).ready(function(){ 

    $('#form_file_upload').fileUploadUI({
        uploadTable: $('#files'),
        downloadTable: $('#files'),
        buildUploadRow: function (files, index) {
// ajax
        // $("input").prop("disabled",true);
        // $("button").prop("disabled",true);
        // $("div#ajax_loading").addClass("ajax_loading");
// ...
            return $('<tr><td>' + files[index].name + '<\/td>' +
            '<td style="width:150px; height:17px;" class     ="file_upload_progress"><div><\/div><\/td>' +
            '<td class     ="file_upload_cancel">' +
            '<button class ="ui-state-default ui-corner-all" title="Cancel">' +
            '<span class   ="ui-icon ui-icon-cancel">Cancel<\/span>' +
            '<\/button><\/td><\/tr>');
        },
        buildDownloadRow: function (file) {
            var url = "<?php echo base_url(); ?>";

        	// ajax
        // $("input").prop("disabled",false);
        // $("button").prop("disabled",false);
        // $("div#ajax_loading").removeClass("ajax_loading");
// ...

		if(!file.status){

			$("#dialog > p").text("");
			$("#dialog > p").text(file.msg);
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
		
			return false;
		}

		// console.log(file);

		var tr='<tr>'
                    +'<td>'
                    +'<span class="'+file.classSpan+'"></span>'
                    +'<\/td>' 
                    +'<td>'
                    +'<span class="name_encode"  style="display:none">'+file.name_encode+'</span>'
                    +'<span class="file_id"  style="display:none">'+file.file_id+'</span>'
                    +'<span class="process"  style="display:none">'+file.process+'</span>'
                    +'<span class="delete"></span>'
                    +'</td>' 
                    +'<td class="file_name" style="display:none">'
                    + file.name_encode
                    +'</td>' 
					+'<td class="file_see">'
                    + '<img src='+file.friendly_path+' />'
                    +'</td>' 
                    +'<\/tr>'
                    +'</table>';

            // return $(tr);
        },
        parseResponse: function (file) {console.log(file);},

    });

});
</script>

<!-- delete imagen -->
<script>
$(document).on("click","td > span.delete",function(){

var url="<?php echo base_url(); ?>",
	item=$(this),
	process=$(this).data("process"),
	file_id=$(this).data("file_id")
	;

	$("#dialog > p").text("");
	$("#dialog > p").text("Realmente desea eliminar?");
	$(item).parent().parent().addClass("deleteStyle");
	$("#dialog > p").dialog({
	resizable: false,
	modal: true,
	    buttons: {
	        Si: function() {

					$.ajax({

					type:"POST",
					url:url+"file/delete",
					dataType:"json",
					data:{
						process:$(item).parent().find("span.process").text(), //encode_id()
						id:"<?php echo encode_id($id); ?>",
						file_name:$(item).parent().find("span.name_encode").text(),
						file_id:$(item).parent().find("span.file_id").text()
					},
					beforeSend:function(response) {
			// ajax
			        $("input").prop("disabled",true);
			        $("button").prop("disabled",true);
			        $("div#ajax_loading").addClass("ajax_loading");
			// ...
					},
					complete:function(response) {

						$(item).parent().parent().remove();
				        $("input").prop("disabled",false);
				        $("button").prop("disabled",false);
				        $("div#ajax_loading").removeClass("ajax_loading");

					}

					});

	        $("#dialog").append("<p></p>");
	        $(this).dialog( "close" );

        	},
        	No: function() {

	        $("#dialog").append("<p></p>");
	        $(this).dialog( "close" );
			$(item).parent().parent().removeClass("deleteStyle");


        	},
	    }
	});

});
</script>

<script>
    // TOKEN INPUT DEL PROVEEDOR
    $("#pathFile_id").tokenInput("<?php echo base_url().'cinepixi/pathFile/pathFile_tokeninput'; ?>", {
        queryParam:"request[name]",
		hintText:"escribe para buscar coincidencias",
		noResultsText:"no hubo coincidencias",
		searchingText:"buscando...",
		tokenLimit:1,
		resultsFormatter:function(item){
			return  "<li>"
						+item.id+" - "+item.name
					+"</li>";

		},
		<?php if(($MODE=="do_it") and !empty($pathFile_id) ): ?>
			prePopulate:[
				{id:<?php echo json_encode($pathFile_id); ?>,name:<?php echo json_encode( (!empty($pathFile_id)?array_search($pathFile_id, array_flip($movie_categories) ) :'' ) ); ?>,},
			],
		<?php endif; ?>

    });

</script>