<?php 
$MODE=(empty($MODE)?"view":$MODE);
$MODE_clientSubsidiary=(empty($MODE_clientSubsidiary)?"view":$MODE_clientSubsidiary);

$form["MODE"]=form_hidden("MODE",$MODE);
$form["id"]=form_hidden("id",encode_id($id));
$form["fk_client"]=form_hidden("fk_client",encode_id($fk_client));

if($MODE=="do_it"):

$form["name"]=form_input("name",$name," id='name'  placeholder='nombre'" );
$form["rfc"]=form_input("rfc",$rfc," id='rfc'  placeholder='nombre'" );
$txt_boton="Guardar";

else:

$form["name"]=$name;
$form["rfc"]=$rfc;
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

				<!-- nav-tabs -->
					<ul class="nav nav-tabs">
					  <li class="active"><a data-toggle="tab" href="#tab1">Basica</a></li>
	                        <?php if($id): ?>

					  		<li><a data-toggle="tab" href="#tab2">Sucursales</a></li>

	                        <?php endif; ?>
					</ul>
					<!-- / nav-tabs -->
					<div class="tab-content">

						  	<div id="tab1" class="tab-pane fade in active">

							<div class="form-group" style='display:none' id="hidden">
	                            <?php echo $form["MODE"]."/"; ?>
	                            <?php echo $form["id"]; ?>
	                            <?php echo $form["fk_client"]; ?>
	                            
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Nombre:"); ?>
	                            <?php echo $form["name"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Rfc:"); ?>
	                            <?php echo $form["rfc"]; ?>
	                        </div>

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

					<!-- </>tab1 -->
<?php  echo form_close();?>
					
					<!-- tab2 -->
						  	<div id="tab2" class="tab-pane fade">
						  	
						  	<!-- Boton agregar -->


		                        <?php if(!empty($client_subsidiaries)): ?>
		                        	<?php foreach($client_subsidiaries as $row): ?>

		                        	<?php echo $this->load->view("admin/client/clientSubsidiary/dinamyc-inputs",$row,true); ?>

		                    		<?php endforeach; ?>
		                    	<?php endif; ?>

		                        <div id="dataTab2"></div>

		                     	<div class="form-group">
		                        	<div class="btn btn-primary btn-xs btn-add" id="add_clientSubsidiary">+</div>
		                        </div>

							</div>
							<!-- </>tab2 -->
							


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