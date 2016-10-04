<?php 
$MODE=(empty($MODE)?"view":$MODE);

$form["MODE"]=form_hidden("MODE",$MODE);
$form["id"]=form_hidden("id",encode_id($id));


if($MODE=="do_it"):

$form["name"]=form_input("name",$name," id='name'  placeholder='nombre'" );
$txt_boton="Guardar";

else:

$form["name"]=$name;
$txt_boton="Editar";

endif;
 ?>
    <div class="row">
        <div class="col-lg-12">
		
       	<div class="panel panel-default">
            <?php echo $this->load->view("recycled/menu/panel_heading","",true); ?>
	        <div class="panel-body">
	            <div class="row">

<?php $attributes_form = array('class' => 'formBasic'); ?>
<?php  echo form_open("form",$attributes_form);?>

							<div class="form-group" style='display:none' id="hidden">
	                            <?php echo $form["MODE"]."/"; ?>
	                            <?php echo $form["id"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Nombre:"); ?>
	                            <?php echo $form["name"]; ?>
	                        </div>

<?php  echo form_close();?>
	                        
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