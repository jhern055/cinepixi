<?php 
require_once(APPPATH."libraries/php/myCFDX/MyCFDX.php");
$myCFDXobj=new myCFDX("CFDI","3.2");

// invoice mode config
if(!empty($file_cer)):
$_INVOICE_MODE_CONFIG=invoice_mode_config("admin/sale/","cfdi");

$file_path_key=$_INVOICE_MODE_CONFIG["shcp_file_upload_storage_path"].$id."/".$file_key;
$file_path_cer=$_INVOICE_MODE_CONFIG["shcp_file_upload_storage_path"].$id."/".$file_cer;

$data["file_cer_date_start"]=myCFDX::getCertificateDate($file_path_key,"start");
$data["file_cer_date_end"]=myCFDX::getCertificateDate($file_path_cer,"end");
endif;
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

	                        <div class="form-group mShcp_file">

			                <!-- <imagen Importar> -->
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

	                        <?php echo form_label("Subir </br> .CER </br> .KEY"); ?>

			                <div class="cerUp">
			                        <?php echo form_open(base_url().'file/doUploadFile/?process='.encode_id("shcp_file")."&id=".encode_id($id),$attributes_img); ?>   
			                   
			                    <div class="upload">

			                        <?php echo form_upload($data_file); ?>

			                    </div>
			                        <?php echo form_close(); ?>

			                    
			                    <div id="files"></div>

			                </div>
			                <!-- </imagen Importar> -->	 

	                        </div>

	                        <div class="form-group">
	                        	<table>

	                        <?php if(!empty($file_key)): ?>
							<tr>

								<td>
									<a class="button <?php echo (!empty($file_path_key)?'':'buttonNotFile'); ?>" href="<?php echo $file_path_key? base_url().'file/download_file/?name_file='.encode_id($file_key).'&file_path='.encode_id($file_path_key):'javascript:void(0);' ?>" <?php echo file_exists($file_path_key)?"":""; ?>>
									<span class="keyFile"></span>
									</a>
								</td>
								<td>
									<span class="name_encode"  style="display:none"><?php echo encode_id($file_key); ?></span>
									<span class="file_id"  style="display:none">0</span>
									<span class="process"  style="display:none"><?php echo encode_id("shcp_file"); ?></span>
									<span class="delete"></span> 
								</td>
							</tr>
							<?php endif; ?>

							<?php if(!empty($file_cer)): ?>
							<tr>
								<td>
									<a class="button <?php echo (!empty($file_path_cer)?'':'buttonNotFile'); ?>" href="<?php echo $file_path_cer? base_url().'file/download_file/?name_file='.encode_id($file_key).'&file_path='.encode_id($file_path_cer):'javascript:void(0);' ?>" <?php echo file_exists($file_path_cer)?"":""; ?>>
									<span class="certificate"></span>
									</a>
								</td>
								<td>
									<span class="name_encode"  style="display:none"><?php echo encode_id($file_cer); ?></span>
									<span class="file_id"  style="display:none">0</span>
									<span class="process"  style="display:none"><?php echo encode_id("shcp_file"); ?></span>
									<span class="delete" style='margin-top:20px;' data-file_encode="<?php echo encode_id($file_cer); ?>"></span></br>:::<?php echo "Empieza:".$data["file_cer_date_start"]; ?></br>:::<?php echo "Termina:".$data["file_cer_date_end"]; ?> 
								</td>
								
							</tr>
							<?php endif; ?>

	                        	</table>
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