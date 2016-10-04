<?php 
$MODE=(empty($MODE)?"view":$MODE);

$form["MODE"]=form_hidden("MODE",$MODE);
$form["id"]=form_hidden("id",encode_id($id));


if($MODE=="do_it"):

$form["name"]         =form_input("name",$name," id='name'  placeholder='nombre'" );
$form["host"]         =form_input("host",$host," id='host' placeholder='host'");
$form["port"]         =form_input("port",$port," id='port' placeholder='port'");
$form["username"]     =form_input("username",$username," id='username' placeholder='username'");
$form["userpassword"] =form_input("userpassword",$userpassword," id='userpassword' placeholder='userpassword'");

	$ssl_enabled_dat = array(
	'name'        => 'ssl_enabled',
	'id'          => 'ssl_enabled',
	'value'          => 'true',
	'checked'     => (!empty($ssl_enabled)?true:false) 
	);

$form["ssl_enabled"] =form_checkbox($ssl_enabled_dat);
// $form["ssl_enabled"]     =form_input("checkbox",$ssl_enabled,"type='checkbox' id='ssl_enabled'");
$form["comment"]     =form_input("comment",$comment," id='comment' placeholder='comment'");
$form["quantity"]    =form_input("quantity",$quantity," id='quantity' placeholder='quantity'");
$form["massive"]     =form_input("massive",$massive," id='massive' placeholder='massive'");

$txt_boton="Guardar";
$status_input='';

else:

$form["name"]         =$name;
$form["host"]         =$host;
$form["port"]         =$port;
$form["username"]     =$username;
$form["userpassword"] =$userpassword;
$form["ssl_enabled"]  =$ssl_enabled;
$form["comment"]      =$comment;
$form["quantity"]     =$quantity;
$form["massive"]      =$massive;

$txt_boton="Editar";

$status_input='disabled=disabled';

endif;

$form["apply_to"]=form_multiselect('apply_to[]', $sys["forms_fields"]["document_email_type"], explode(",", $apply_to),$status_input);

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
	                        <div class="form-group">
	                            <?php echo form_label("Aplica para:"); ?>
	                            <?php echo $form["apply_to"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Host:"); ?>
	                            <?php echo $form["host"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Puerto:"); ?>
	                            <?php echo $form["port"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Usuario:"); ?>
	                            <?php echo $form["username"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Password:"); ?>
	                            <?php echo $form["userpassword"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Ssl Activo:"); ?>
	                            <?php echo $form["ssl_enabled"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Comentario:"); ?>
	                            <?php echo $form["comment"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Cantindad:"); ?>
	                            <?php echo $form["quantity"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Masivo:"); ?>
	                            <?php echo $form["massive"]; ?>
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