<?php 
$MODE=(empty($MODE)?"view":$MODE);

$form["MODE"]=form_hidden("MODE",$MODE);
$form["id"]=form_hidden("id",encode_id($id));

if($MODE=="do_it"):

$form["name"]=form_input("name",$name," id='name'  placeholder='nombre'" );
$form["serie"]     =form_input("serie",$serie," id='serie'  placeholder='serie'" );
$form["since"]     =form_input("since",$since," id='since'  placeholder='desde'" );
$form["until"]     =form_input("until",$until," id='until'  placeholder='hasta'" );
$form["current"]   =form_input("current",$current," id='current'  placeholder='siguiente folio'" );
$form["shcp_file"] =form_dropdown('shcp_file', $shcp_files, $shcp_file);
$form["pac"] =form_dropdown('pac', $pacs, $pac);
$form["date_expires"]=form_input(array('name'=>'date_expires', 'id'=>'date_expires', 'value'=>$date_expires,"placeholder"=>"Fecha" ) );
$form["status"] =form_dropdown('status', $sys["forms_fields"]["boolean_answers_status"], $status);

$txt_boton="Guardar";
$status_input='';

else:

$form["name"]=$name;
$form["serie"]=$serie;
$form["since"] =$since;
$form["until"] =$until;
$form["current"]=$current;
$form["shcp_file"] =array_search($shcp_file, array_flip($shcp_files));
$form["pac"] =array_search($pac, array_flip($pacs));
$form["date_expires"]=($date_expires?:"");
$form["pac"] =array_search($pac, array_flip($pacs));
$form["status"] =array_search($status, array_flip($sys["forms_fields"]["boolean_answers_status"]));

$txt_boton="Editar";
$status_input='disabled=disabled';

endif;

$form["subsidiary"]=form_multiselect('subsidiary[]', $subsidiaries, explode(",", $subsidiary),$status_input);
$form["document_type"]=form_multiselect('document_type[]', array_flip($sys["forms_fields"]["invoice_document_type"]), explode(",", $document_type),$status_input." id='document_type'");

 ?>
 <style type="text/css">
 

 </style>
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
	                            <?php echo form_label("Sucursales:"); ?>
	                            <?php echo $form["subsidiary"]; ?>
	                        </div>	                        
	                        <div class="form-group">
	                            <?php echo form_label("Aplica a:"); ?>
	                            <?php echo $form["document_type"]; ?>
	                        </div>	                        	                        
	                        <div class="form-group">
	                            <?php echo form_label("Serie:"); ?>
	                            <?php echo $form["serie"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Rango desde:"); ?>
	                            <?php echo $form["since"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Rango hasta"); ?>
	                            <?php echo $form["until"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Siguiente"); ?>
	                            <?php echo $form["current"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Archivos shcp"); ?>
	                            <?php echo $form["shcp_file"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Pacs"); ?>
	                            <?php echo $form["pac"]; ?>
	                        </div>	      
		                    <div class="form-group">
                            <?php echo form_label("Fecha Expiracion:"); ?>
                            <?php echo $form["date_expires"]; ?>
	                        </div>	                            
		                    <div class="form-group">
                            <?php echo form_label("Estatus"); ?>
                            <?php echo $form["status"]; ?>
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
<script>
  $(function() {$( "#date_expires" ).datepicker(); });
</script>