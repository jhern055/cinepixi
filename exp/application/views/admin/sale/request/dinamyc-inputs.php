<?php
require_once(APPPATH."views/recycled/details/detail-summary.php"); 

$MODE=(empty($MODE)?"view":$MODE);

$form["MODE"]=form_hidden("MODE",$MODE);
$form["id"]=form_hidden("id",encode_id($id));

if($MODE=="do_it"):

$add_other = array(
    'name'        => 'add_other',
    'id'          => 'add_other',
    'checked'     => false
    );

$form["add_other"]=form_checkbox($add_other);

$form["folio"]=!empty($sys["config"]["invoice_folio_automatic"])?"Automatico":form_input("folio",$folio," id='folio'  placeholder='folio'" );

$form["client"]=form_input("client",$client," id='client'  placeholder='cliente'" );
$form["client_subsidiary"]=form_dropdown('client_subsidiary',$client_subsidiaries,$client_subsidiary,"id='client_subsidiary'");
$form["status"]=form_dropdown('status',$sys["forms_fields"]["request_status"],$status,"id='status'");
$form["comment"]=form_textarea(array('name'=>'comment', 'id'=>'comment', 'rows'=>'3', 'value'=>$comment,"placeholder"=>"Comentario" ) );
$form["date"]=form_input(array('name'=>'date', 'id'=>'date', 'value'=>$date,"placeholder"=>"Fecha" ) );
$form["subsidiary"] =form_dropdown('subsidiary', $subsidiaries, $subsidiary);
$form["method_of_payment"] =form_dropdown('method_of_payment', $sys["forms_fields"]["method_of_payment"], $method_of_payment);
$form["payment_condition"] =form_dropdown('payment_condition', $sys["forms_fields"]["payment_condition"], $payment_condition);

$form["type_of_currency"] =form_dropdown('type_of_currency', $type_of_currencies, $type_of_currency);
$form["exchange_rate"]=form_input("exchange_rate",$exchange_rate," id='exchange_rate'  placeholder='T.C.'" );

$txt_boton="Guardar";

else:

$form["folio"]=($folio?:"");
$form["client"]=($client?anchor(base_url()."admin/client/clientView/".$client,array_search($client, array_flip($clients))," target=_blank") :"");
$form["client_subsidiary"]=($client_subsidiary?anchor(base_url()."admin/client/clientView/".$client,array_search($client_subsidiary, array_flip($client_subsidiaries))," target=_blank"):"");
$form["date"]=($date?:"");
$form["status"]=($status?array_search($status, array_flip($sys["forms_fields"]["request_status"]) ):"");
$form["comment"]=($comment?:"");
$form["subsidiary"]=($subsidiary?anchor(base_url()."config/subsidiaryView/".$subsidiary,array_search($subsidiary, array_flip($subsidiaries)) ," target=_blank"):"");
$form["method_of_payment"]=($method_of_payment?array_search($method_of_payment, array_flip($sys["forms_fields"]["method_of_payment"]) ):"");
$form["payment_condition"]=($payment_condition?array_search($payment_condition, array_flip($sys["forms_fields"]["payment_condition"]) ):"");

$form["type_of_currency"]=($type_of_currency?array_search($type_of_currency, array_flip($type_of_currencies) ):"");
$form["exchange_rate"]=($exchange_rate?:"");

$txt_boton="Editar";

endif;

$form["client_email"]=form_input("client_email",(!empty($client_email)?$client_email:"")," id='client_email'  placeholder='cliente'" );

$add_detail='<div class="btn-primary btn-sm add_detail" tabindex="12">Agregar articulo</div>';

 ?>
    <div class="row">
       	<div class="panel panel-default">
            <?php echo $this->load->view("recycled/menu/panel_heading","",true); ?>

	        <!-- /.panel-heading -->
<?php $col="col-sm-6 col-md-6 col-lg-6 border_bottom"; ?>
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

							<!-- .col -->
							<div class="<?php echo $col; ?>">
			                    <div class="form-group">
	                            <?php echo form_label("folio:"); ?>
	                            <?php echo $form["folio"]; ?>
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

								<div class="form-group clientEmailContainer" style="display:none;">
	                            <?php echo $form["client_email"]; ?>
		                        </div>      	                        
							</div>
							<!-- /.col -->

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
							<div class="<?php echo $col; ?>">
			                    <div class="form-group">
	                            <?php echo form_label("Tipo de cambio:"); ?>
	                            <?php echo $form["type_of_currency"].$form["exchange_rate"]; ?>
		                        </div> 
							</div>
							<!-- /.col -->
							
	                        <!-- .col -->
				            	<div id="emailInfoContainer">
				           	<?php echo $this->load->view("email/email_view","",true); ?>
				            	</div>  
							<!-- /.col -->

	                        <?php if($MODE=="do_it" and !$id): ?>
	                        <div class="form-group">
	                            <?php echo form_label("Agregar otro?:"); ?>
	                            <?php echo $form["add_other"]; ?>
	                        </div>
	                    	<?php endif; ?>
<?php  echo form_close();?>

	                        <div class="form-group">

	                        	<div class="area3 containerButtons buttonsActions">
		                        	<a class="button" href="javascript:void(0)" id="send">
										Enviar
									<span class='at'></span>
									</a>

	                        		<?php if(!empty($id)):?>
									<a class="button" href="<?php echo base_url().'pdf/?source_module='.encode_id($module_data["link"]).'&id='.encode_id($id);?>">
										Imprimir
									<span class='pdf'></span>
									</a>
									<?php endif; ?>

	                        	</div>


	                        	<div class="area3 containerButtons">

	                        	<?php if($status!=3 or $status==1){ ?>
	                        	<div class="btn btn-primary" id="submit"><?php echo $txt_boton; ?></div>
	                        	<?php } ?>
	                        	<?php if(!empty($id)): ?>
	                        	    <?php if($MODE=="do_it"): ?>
	                        		<div class="btn btn-warning" id="cancel"><?php echo "Cancelar"; ?></div>
	                        		<?php endif; ?>
	                        	<div class="btn btn-danger" id="delete"><?php echo "Eliminar"; ?></div>
	                        	<?php endif; ?>

								</div>

	                        	<?php if($MODE=="do_it"): ?>

	                        	<div class="area4 containerButtons">
						    	<?php echo $add_detail; ?>
								</div>
	                    		<?php endif; ?>

	                        </div>

							<div class="articleListContainer documentViewDefault col-sm-12 col-md-12 col-lg-12">
								<div class="area1">
									detalle
								</div>
								<div class="area2">
									<div class="header">
										<div class="stockModification"><span class="help" title="modificar inventario activo?, si está activa al procesar la compra se elimina la cantidad de artículos introducida de las existencias actuales.">Sto</span></div>
										<div class="quantity"><span class="help" title="cantidad">cant.</span></div>
										<div class="article">artículo</div>
										<div class="description">descripción</div>
										<div class="price">precio u.</div>
										<div class="totalSub">subtotal</div>
										<div class="taxIeps" title="porcentaje ieps">% ieps</div>
										<div class="taxIepsTotal" title="ieps">ieps</div>
										<div class="taxIva" title="porcentaje iva">% iva</div>
										<div class="taxIvaTotal" title="iva">iva</div>
										<div class="taxIvaRetained" title="porcentaje iva retenido">% iva r.</div>
										<div class="taxIvaRetainedTotal" title="iva retenido">iva r.</div>
										<div class="taxIsr" title="porcentaje isr">% isr</div>
										<div class="taxIsrTotal" title="isr">isr</div>
									</div>
									<div class="data">
										<!-- modified dinamically with js-->
									</div>
								</div>
							</div>
							<div class="container articleListSummaryContainer col-sm-12 col-md-12 col-lg-12">
								<div class="area1">global </div>
								<div class="area2">
									<div class="quantity">
										<span class="number"></span>
									</div>
									<div class="totalSub">
										<span class="name">subtotal</span><span class="currencySign">$</span><span class="number"></span>
									</div>
									<div class="taxIeps">
										<span class="name">ieps</span><span class="currencySign">$</span><span class="number"></span>
									</div>
									<div class="taxIva">
										<span class="name">iva</span><span class="currencySign">$</span><span class="number"></span>
									</div>
									<div class="taxIvaRetained">
										<span class="name">iva retenido</span><span class="currencySign">$</span><span class="number"></span>
									</div>
									<div class="taxIsr">
										<span class="name">isr</span><span class="currencySign">$</span><span class="number"></span>
									</div>
									<div class="total">
										<span class="name">total</span><span class="currencySign">$</span><span class="number"></span>
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

<script>

  $(function() {

     $( "#date" ).datepicker();

  });
</script>
<!-- TOKEN INPUT PROVEEDOR -->
<?php if($MODE=="do_it"):?>
<script>


$(document).ready(function() {

	client_subsidiary_information= new Object();
	client_subsidiary_information.get=function(id){

		var id=id;
	 	// d0!!
		$("div#clientSubsidiaryInformationContainer").text("");

		// ...query string
		$.ajax({
			type: "POST",
			dataType:"json",
			url:"<?php echo base_url().'client/client_subsidiary_info'; ?>",
			data:{
			id:id,

			},
		beforeSend:function(response) {
		},
		complete:function(response) {

			var request=JSON.parse(response.responseText),
				request=request["data"];
				
				html="<span><a href='<?php echo base_url()."admin/client/clientView/";?>"+request.fk_client+"' target=_blank>";
				html+="<span "+(request.city?"style='color:black' ":"style='color:red'")+"> Ciudad: </span> "+request.city;
				html+="<span "+(request.colony?"style='color:black' ":"style='color:red'")+">  Colonia: </span>"+request.colony;
				html+="<span "+(request.delegation?"style='color:black' ":"style='color:red'")+">  Delegación: </span>"+request.delegation;
				html+="<span "+(request.street?"style='color:black' ":"style='color:red'")+">  Calle: </span>"+request.street;
				html+="<span "+(request.outside_number?"style='color:black' ":"style='color:red'")+">  # exterior: </span>"+request.outside_number;
				
				html+="<span "+(request.inside_number?"style='color:black' ":"style='color:red'")+">  # interior: </span>"+request.inside_number;
				
				html+="<span "+(request.zip_code?"style='color:black' ":"style='color:red'")+">  # c.p.: </span>"+request.zip_code;
				
				html+="<span "+(request.email?"style='color:black' ":"style='color:red'")+">  email: </span>"+request.email;
				html+="<span "+(request.phone?"style='color:black' ":"style='color:red'")+">  telefono:</span> "+request.phone;
				html+="</span>";
				html+="</a><span>";


			$("div#clientSubsidiaryInformationContainer").html("");
			$("div#clientSubsidiaryInformationContainer").html(html);
			$("input#client_email").val("");
			
			if(request.email)
			$("input#client_email").val(request.email);

			// alert(request.msg);
		}
		});

	 };

	$(document).on("change","select#client_subsidiary",function(){

		client_subsidiary_information.get($(this).val());
	});

	// TOKEN INPUT DEL PROVEEDOR
    $("#client").tokenInput("<?php echo base_url().'client/client_tokeninput'; ?>", {
        queryParam:"request[name]",
		hintText:"escribe para buscar coincidencias",
		noResultsText:"no hubo coincidencias",
		searchingText:"buscando...",
		tokenLimit:1,
		onAdd:function(item){

			var jqueryObj=$("#client_subsidiary");
				
				jqueryObj.children().remove();

				if(item.subsidiaries.length>1)
				 jqueryObj.append($("<option />").val("").text(""));

				$.each(item.subsidiaries,function() {

					jqueryObj.append($("<option />").val(this.id).text(this.name));

				});

				if(item.subsidiaries.length>1) {

					jqueryObj.prop("disabled",false);
					jqueryObj.get(0).focus();

				}

				if(item.subsidiaries.length==1)
				client_subsidiary_information.get(item.subsidiaries[0].id);
				else
				$(client_subsidiary_information.container).text("");

		},
		<?php if(($MODE=="do_it") and !empty($client) ): ?>
			prePopulate:[
				{id:<?php echo json_encode($client); ?>,name:<?php echo json_encode( (!empty($client)?array_search($client, array_flip($clients) ) :'' ) ); ?>,},
			],
		<?php endif; ?>

    });

});
</script>
<?php endif; ?>

<!-- Cargar los articulos -->
<!-- Los inputs de detalle -->
<script>

$(document).ready(function(){

 	details=new Object();

 	details.get=function(){
	var url ="<?php echo base_url().'article/details';?>",
		id_record =$("div#hidden > input[name=id]").val()
		;

	    $.ajax({
	        type: "POST",
	        url: url,
	        async:true,
	        dataType:"json",
	        data:{  
	        
	        id_record:id_record,
	        source_module:"request",
	        DAD_MODE:"<?php echo $MODE;?>",

	        }, 
	        beforeSend:  function(response) {
	        },
	        success: function(response){

	    	if(response.status==1)
	    	$("div.articleListContainer > div.area2 > div.data").append(response.html);

	        }

	    });


	return false;

	};

 	details.get();	

});
</script>