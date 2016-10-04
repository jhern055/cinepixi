<?php
require_once(APPPATH."views/recycled/payment_details/payment_detail-summary.php"); 

$MODE=(empty($MODE)?"view":$MODE);

$form["MODE"]=form_hidden("MODE",$MODE);
$form["id"]=form_hidden("id",encode_id($id));
$form["module"]=form_hidden("module",encode_id($module));

$form["folio"]=($folio?:"");
$form["date"]=(!empty($date)?$date:"");
$form["status"]=($status?array_search($status, array_flip($sys["forms_fields"]["sales_status"]) ):"");
$form["comment"]=($comment?:"");
$form["method_of_payment"]=(!empty($method_of_payment)?array_search($method_of_payment, array_flip($sys["forms_fields"]["method_of_payment"]) ):"");
$form["payment_condition"]=(!empty($payment_condition)?array_search($payment_condition, array_flip($sys["forms_fields"]["payment_condition"]) ):"");

$form["client"]=(!empty($client)?anchor(base_url()."admin/client/clientView/".$client_data["id"],$client_data["name"]," target=_blank") :"");
$form["client_subsidiary"]=(!empty($client_subsidiary)?anchor(base_url()."admin/client/clientView/".$client,array_search($client_subsidiary, array_flip($client_subsidiaries))," target=_blank"):"");

$form["provider"]=(!empty($provider)?anchor(base_url()."admin/provider/providerView/".$provider_data["id"],$provider_data["name"]," target=_blank") :"");
$form["provider_subsidiary"]=(!empty($provider_subsidiary)?anchor(base_url()."admin/provider/providerView/".$provider,array_search($provider_subsidiary, array_flip($provider_subsidiaries))," target=_blank"):"");

$form["subsidiary"]=(!empty($subsidiary)?anchor(base_url()."config/subsidiaryView/".$subsidiary,array_search($subsidiary, array_flip($subsidiaries)) ," target=_blank"):"");

$form["uuid"]=(!empty($uuid)?$uuid:"");

$form["import"]=(!empty($import)?number_format($import,2):"");
$form["payment"]=(!empty($payment)?number_format($payment,2):"");
$form["residuary"]=(!empty($payment)?number_format($import-$payment,2):"");

$txt_boton="Editar";

$add_payment='<div class="btn-primary btn-sm add_payment" tabindex="12">AGREGAR PAGO</div>';

 ?>
    <div class="row">
       	<div class="panel panel-default">
        <?php echo $this->load->view("recycled/menu/panel_heading_id","",true); ?>
	        <!-- /.panel-heading -->
<?php $col="col-sm-6 col-md-6 col-lg-6 border_bottom"; ?>
	        <div class="panel-body">
	            <div class="row">

<?php $attributes_form = array('class' => 'formBasic'); ?>
<?php  echo form_open("form",$attributes_form);?>

							<div class="form-group" style='display:none' id="hidden">
	                            <?php echo $form["MODE"]."/"; ?>
	                            <?php echo $form["id"]; ?>
	                            <?php echo $form["module"]; ?>
	                        </div>
							<div class="form-group">
								<div id="message"></div>
	                        </div>

							<!-- .col -->
							<div class="<?php echo $col; ?>">
			                    <div class="form-group">
	                            <?php echo form_label("folio:"); ?>
	                            <?php echo $form["folio"]; ?>
	                            <?php echo "<span style='font-weight: bold; margin-left:10px;'>".$form["uuid"]."</span>"; ?>
	                            
		                        </div>
							</div>
							<!-- /.col -->
							
							<!-- .col -->
							<div class="<?php echo $col; ?>">
			                    <div class="form-group">
	                            <?php echo form_label("Sucursal:"); ?>
	                            <?php echo $form["subsidiary"]; ?>
		                        </div>
							</div>
							<!-- /.col -->

							<?php if(!empty($form["client"])): ?>
	                        <!-- .col -->
							<div class="<?php echo $col; ?>">
			                    <div class="form-group">
	                            <?php echo form_label("Cliente:"); ?>
	                            <?php echo $form["client"]; ?>
		                        </div>
			                    <div class="form-group" style="margin-top:40px;">
	                            <?php echo form_label("Cliente Sucursal:"); ?>
	                            <?php echo $form["client_subsidiary"]; ?>
		                        </div>
			                    <div id="clientSubsidiaryInformationContainer"> </div>
		                        	                        
							</div>
							<!-- /.col -->
							<?php endif; ?>

							<?php if(!empty($form["provider"])): ?>
	                        <!-- .col -->
							<div class="<?php echo $col; ?>">
			                    <div class="form-group">
	                            <?php echo form_label("Proveedor:"); ?>
	                            <?php echo $form["provider"]; ?>
		                        </div>
			                    <div class="form-group" style="margin-top:40px;">
	                            <?php echo form_label("Proveedor Sucursal:"); ?>
	                            <?php echo $form["provider_subsidiary"]; ?>
		                        </div>
			                    <div id="providerSubsidiaryInformationContainer"> </div>
		                        	                        
							</div>
							<!-- /.col -->
							<?php endif; ?>

	                        <!-- .col -->
							<div class="<?php echo $col; ?>">
			                    <div class="form-group">
	                            <?php echo form_label("Fecha:"); ?>
	                            <?php echo $form["date"]; ?>
		                        </div>
							</div>
							<!-- /.col -->

	                        <!-- .col -->
							<div class="<?php echo $col; ?>">
			                    <div class="form-group">
	                            <?php echo form_label("Metodo de pago:"); ?>
	                            <?php echo $form["method_of_payment"]; ?>
		                        </div>
							</div>
							<!-- /.col -->
	                        
	                        <!-- .col -->
							<div class="<?php echo $col; ?>">
			                    <div class="form-group">
	                            <?php echo form_label("Condicion de pago:"); ?>
	                            <?php echo $form["payment_condition"]; ?>
		                        </div>
							</div>
							<!-- /.col -->

	                        <!-- .col -->
							<div class="<?php echo $col; ?>">
			                    <div class="form-group">
	                            <?php echo form_label("Status:"); ?>
	                            <?php echo $form["status"]; ?>
		                        </div>
							</div>
							<!-- /.col -->

	                        <!-- .col -->
							<div class="<?php echo $col; ?>">
			                    <div class="form-group">
	                            <?php echo form_label("Comentario:"); ?>
	                            <?php echo $form["comment"]; ?>
		                        </div> 
							</div>
							<!-- /.col -->

	                        <!-- .col -->
							<div class="col-sm-2 col-md-2 col-lg-2">
			                    <div class="form-group">
	                            <?php echo form_label("Importe:","",array("class"=>'colorBrown')); ?>
		                            <label class='greenBalance'>
		                            	<span class='sign_price' style="float:left">$</span>
			                            	<label class='importBill'>
			                           	 	<?php echo $form["import"]; ?>
			                            	</label>
		                            </label>
		                        </div> 
							</div>
							<!-- /.col -->

	                        <!-- .col -->
							<div class="col-sm-2 col-md-2 col-lg-2">
			                    <div class="form-group ">
	                            <?php echo form_label("Pagado:","",array("class"=>'colorBrown')); ?>
		                            <label class='yellowBalance'>
		                            	<span class='sign_price'>$</span>
		                            	<label class='payment'>
		                            	<?php echo $form["payment"]; ?>
			                        </label>

								</div> 
							</div>
							<!-- /.col -->

	                        <!-- .col -->
							<div class="col-sm-2 col-md-2 col-lg-2">
			                    <div class="form-group">
	                            <?php echo form_label("Restante:","",array("class"=>'colorBrown')); ?>
		                            <label class='redBalance'>
		                            	<span class='sign_price'>$</span>
		                            	<label class='residuary'>
		                            	<?php echo $form["residuary"]; ?>
		                           		</label>
		                            </label>
		                        </div> 
							</div>
							<!-- /.col -->							
<?php  echo form_close();?>

	                        <div class="form-group">

	                        	<!-- <div class="area3 containerButtons buttonsActions"> -->
	<!-- 	                        	<a class="button" href="javascript:void(0)" id="send">
										Enviar
									<span class='at'></span>
									</a> -->
	                        		
	                        		<?php //if(!empty($id)):?>
<!-- 									<a class="button" href="<?php echo base_url().'pdf/?source_module='.encode_id($module_data["link"]).'&id='.encode_id($id);?>">
										Imprimir
									<span class='pdf'></span>
									</a> -->
									<?php //endif; ?>

	                        	<!-- </div> -->

	                        	<div class="area4 containerButtons">
						    	<?php echo $add_payment; ?>
								</div>

	                        </div>

							<div class="paymentListContainer paymentDocumentViewDefault col-sm-12 col-md-12 col-lg-12">
								<div class="area1">
									detalle
								</div>
								<div class="area2">
									<div class="header">
										<div class="id">Id</div>
										<div class="method">Metodo</div>
										<div class="import">Importe</div>
										<div class="type_of_currency">Divisa</div>
										<div class="exchange_rate">T.c</div>
										<div class="comment">Comentario</div>
										<div class="date">Fecha</div>

									</div>
									<div class="data">
										<!-- modified dinamically with js-->
									</div>
								</div>
							</div>
                </div>
	            <!-- /.row (nested) -->
	        </div>
	        <!-- /.panel-body -->
	    </div>
		<!-- /.panel-default -->
       	</div>
		<!-- /.row -->

<!-- Cargar los pagos -->
<!-- Los inputs de detalle -->
<script>

$(document).ready(function(){

 	payment_details=new Object();

 	payment_details.get=function(){
	var url ="<?php echo base_url().'payment/payment_details';?>",
		id_record =$("div#hidden > input[name=id]").val()
		module =$("div#hidden > input[name=module]").val()
		;

	    $.ajax({
	        type: "POST",
	        url: url,
	        async:true,
	        dataType:"json",
	        data:{  
	        
	        id_record:id_record,
	        source_module:module,

	        }, 
	        beforeSend:  function(response) {
	        },
	        success: function(response){

	    	if(response.status==1)
	    	$("div.paymentListContainer > div.area2 > div.data").append(response.html);

	        }

	    });


	return false;

	};

 	payment_details.get();	

});
</script>