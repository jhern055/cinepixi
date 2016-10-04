<?php 
$MODE=(empty($MODE)?"view":$MODE);

$form["MODE"]=form_hidden("MODE",$MODE);
$form["id"]=form_hidden("id",encode_id($id));


if($MODE=="do_it"):

$form["name"]  =form_input("name",$name," id='name'  placeholder='nombre'" );
$form["model"] =form_input("model",$model," id='model'  placeholder='Modelo'" );
$form["sku"]   =form_input("sku",$sku," id='sku'  placeholder='Sku'" );
$form["price"] =form_input("price",$price," id='price'  placeholder='precio'" );

// <tab3>
$form["category"] =form_input("category",""," id='category'  placeholder='categorias'" );
// </tab3>

$add_other = array(
    'name'        => 'add_other',
    'id'          => 'add_other',
    'checked'     => false
    );


$form["add_other"]=form_checkbox($add_other);

$txt_boton="Guardar";

else:

$form["name"]=$name;
$form["model"]=$model;
$form["sku"]=$sku;  
$form["price"]=$price;

// tab3
$form["category"] ="";
// </tab3>


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

				<!-- nav-tabs -->
					<ul class="nav nav-tabs">
					  <li class="active">
					  	<a data-toggle="tab" href="#tab1">General</a>
					  </li>

					  		<li><a data-toggle="tab" href="#tab2">Datos</a></li>
					  		<li><a data-toggle="tab" href="#tab3">Enlaces</a></li>
					  		<li><a data-toggle="tab" href="#tab4">Imagenes</a></li>

					</ul>
					<!-- / nav-tabs -->

<?php $attributes_form = array('class' => 'formBasic'); ?>
<?php  echo form_open("form",$attributes_form);?>

					<div class="tab-content">

						<!-- tab1 -->

						  	<div id="tab1" class="tab-pane fade in active">

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

	                        <?php if($MODE=="do_it" and !$id): ?>
	                        <div class="form-group">
	                            <?php echo form_label("Agregar otro?:"); ?>
	                            <?php echo $form["add_other"]; ?>
	                        </div>
	                    	<?php endif; ?>

							</div>
					<!-- </>tab1 -->

							<!-- <tab2> -->
						  	<div id="tab2" class="tab-pane fade">
		                        <div class="form-group">
		                            <?php echo form_label("Modelo:"); ?>
		                            <?php echo $form["model"]; ?>
		                        </div>
		                        <div class="form-group">
		                            <?php echo form_label("Sku:"); ?>
		                            <?php echo $form["sku"]; ?>
		                        </div>
		                        <div class="form-group">
		                            <?php echo form_label("Precio:"); ?>
		                            <?php echo $form["price"]; ?>
		                        </div>		                        		                        
							</div>
							<!-- </tab2> -->

							<!-- <tab3> -->
						  	<div id="tab3" class="tab-pane fade">
		                        <div class="form-group">
		                            <?php echo form_label("Categorias:"); ?>
		                            <?php echo $form["category"]; ?>
		                        </div>

		                        <div id="product-category" class="well well-sm" style="height: 150px; overflow: auto;">
		                        
		                        	<!-- pintar los relacionados -->

		                        	<?php if(!empty($article_category)): ?>
			                        	<?php foreach($article_category as $k=>$row): ?>
										<div id="product-category">
										<i class="fa fa-minus-circle" style="margin-right:10px"></i>
										<?php echo $row["category_name"]; ?>
										<input type="hidden" name="article_category[]" value="<?php echo $row["category_id"] ?>">
										</div>	
										<?php endforeach; ?>
									<?php endif; ?>

		                        </div>

							</div>
							<!-- </tab3> -->							
<?php  echo form_close();?>
							<!-- <tab4> -->
						  	<div id="tab4" class="tab-pane fade">

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
									$attributes_img = array(
									"role"=>"form",
									'id'=>'form_file_upload',
									"name"=>'form_file_upload',
									"method"=>"POST",
									"enctype"=>"multipart/form-data"
									);
				                 ?>

		                        <?php echo form_label("Subir </br>Imagenes"); ?>

						                <div class="imUp">
						                        <?php echo form_open(base_url().'file/doUploadFile/?process='.encode_id("article_image")."&id=".encode_id($id),$attributes_img); ?>   
						                   
						                    <div class="upload">

						                        <?php echo form_upload($data_file); ?>

						                    </div>
						                        <?php echo form_close(); ?>

						                    
						                    <div id="files"></div>

						                </div>

		                        </div>
				                <!-- </imagen Importar> -->	 

							</div>
							<!-- </tab4> -->

				</div>
							<!-- </tabcontent> -->


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
    $("#category").tokenInput("<?php echo base_url().'category/category_tokeninput'; ?>", {
        queryParam:"request[name]",
		hintText:"escribe para buscar coincidencias",
		noResultsText:"no hubo coincidencias",
		searchingText:"buscando...",
		tokenLimit:1,
		onDelete:function (argument) {
		},
		onAdd:function(item){

		var html='<div id="product-category">'
					+'<i class="fa fa-minus-circle" style="margin-right:10px"></i>'
					+item.name.toString()
					+'<input type="hidden" name="article_category[]" value="'+item.id+'">'
				+'</div>';

		$("form.formBasic > div.tab-content > div#tab3 > div#product-category").append(html);
		$("#category").tokenInput("clear");
		$("#category").parent().find("ul >li.token-input-token span").prop("tabindex",11).focus();
		$("#category").parent().find("ul.token-input-list :input").focus();

		},
		<?php if(($MODE=="do_it") and !empty($client) ): ?>
			prePopulate:[
				{id:<?php echo json_encode($client); ?>,name:<?php echo json_encode( (!empty($client)?array_search($client, array_flip($clients) ) :'' ) ); ?>,},
			],
		<?php endif; ?>

    });
</script>