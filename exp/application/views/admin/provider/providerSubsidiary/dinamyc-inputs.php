<?php 
$MODE=(empty($MODE)?"view":$MODE);

$form["MODE"]=form_hidden("MODE",$MODE);
$form["id"]=form_hidden("id",encode_id($id));

if($MODE=="do_it"):

$form["name"]            =form_input("name",$name," id='name'  placeholder='Nombre' tabindex=11" );
$form["country"]         =form_input("country",$country," id='country_$id'  placeholder='Pais' tabindex=11" );
$form["state"]           =form_input("state",$state," id='state_$id'  placeholder='Estado' tabindex=11" );
$form["town"]            =form_input("town",$town," id='town_$id'  placeholder='Municipio' tabindex=11" );
$form["city"]            =form_input("city",$city," id='city'  placeholder='Ciudad' tabindex=11" );
$form["colony"]          =form_input("colony",$colony," id='colony'  placeholder='Colonia' tabindex=11" );
$form["street"]          =form_input("street",$street," id='street'  placeholder='Calles' tabindex=11" );
$form["inside_number"]   =form_input("inside_number",$inside_number," id='inside_number'  placeholder='Numero interior' tabindex=11" );
$form["outside_number"]  =form_input("outside_number",$outside_number," id='outside_number'  placeholder='Numero exterior' tabindex=11" );
$form["zip_code"]        =form_input("zip_code",$zip_code," id='zip_code'  placeholder='C.P.' tabindex=11" );
$form["reference"]       =form_input("reference",$reference," id='reference'  placeholder='Referencia' tabindex=11" );
$form["working_hours"]   =form_input("working_hours",$working_hours," id='working_hours'  placeholder='Horario laboral' tabindex=11" );
$form["reception_days"]  =form_input("reception_days",$reception_days," id='reception_days'  placeholder='Dias de recepción' tabindex=11" );
$form["reception_hours"] =form_input("reception_hours",$reception_hours," id='reception_hours'  placeholder='Horario de recepción' tabindex=11" );
$form["website"]         =form_input("website",$website," id='website'  placeholder='Sitio web' tabindex=11" );
$form["email"]           =form_input("email",$email," id='email'  placeholder='Email' tabindex=11" );
$form["phone"]           =form_input("phone",$phone," id='phone'  placeholder='Telefono' tabindex=11" );
$form["contact"]         =form_input("contact",$contact," id='contact'  placeholder='Contacto' tabindex=11" );
$form["paydays"]         =form_input("paydays",$paydays," id='paydays'  placeholder='Dias de pago' tabindex=11" );
$form["payhours"]        =form_input("payhours",$payhours," id='payhours'  placeholder='Dias de cobro' tabindex=11" );

$txt_boton="Guardar";

else:

$form["name"]            =$name;
$form["country"]        =!$country?"":array_search($country, array_flip($countries));
$form["state"]          =!$state?"":array_search($state, array_flip($states) );
$form["town"]          =!$town?"":array_search($town, array_flip($towns) );

$form["city"]            =$city;
$form["colony"]          =$colony;
$form["street"]          =$street;
$form["inside_number"]   =$inside_number;
$form["outside_number"]  =$outside_number;
$form["zip_code"]        =$zip_code;
$form["reference"]       =$reference;
$form["working_hours"]   =$working_hours;
$form["reception_days"]  =$reception_days;
$form["reception_hours"] =$reception_hours;
$form["website"]        =$website;
$form["email"]           =$email;
$form["phone"]           =$phone;
$form["contact"]         =$contact;
$form["paydays"]         =$paydays;
$form["payhours"]        =$payhours;

$txt_boton="Editar";

endif;

 ?>

<?php $attributes_form = array('class' => 'formproviderSubsidiary'); ?>
<?php  echo form_open("formTab2",$attributes_form);?>

							<div class="form-group" style='display:none' id="hidden">
	                            <?php echo $form["MODE"]."/"; ?>
	                            <?php echo $form["id"]; ?>
	                        </div>
							<div class="form-group">
								<div id="message"></div>
	                        </div>

						 	<div class="form-group">
				        		<label>Nombre</label>
							<?php echo $form["name"]; ?>
							</div>

						 	<div class="form-group">
				        		<label>Pais</label>
							<?php echo $form["country"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Estado</label>
							<?php echo $form["state"]; ?>

							</div>

				        	<div class="form-group">
				        		<label>Municipio</label>
							<?php echo $form["town"]; ?>

							</div>

				        	<div class="form-group">
				        		<label>Ciudad</label>
							<?php echo $form["city"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Colonia</label>
							<?php echo $form["colony"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Calles</label>
							<?php echo $form["street"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Numero interior</label>
							<?php echo $form["inside_number"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Numero exterior</label>
							<?php echo $form["outside_number"]; ?>
							</div>
	
				        	<div class="form-group">
				        		<label>C.P</label>
							<?php echo $form["zip_code"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Referencia</label>
							<?php echo $form["reference"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Horario laboral</label>
							<?php echo $form["working_hours"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Dias recepción</label>
							<?php echo $form["reception_days"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Hra. Recepción</label>
							<?php echo $form["reception_hours"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Sitio-web</label>
							<?php echo $form["website"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Email</label>
							<?php echo $form["email"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Telefono</label>
							<?php echo $form["phone"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Contacto</label>
							<?php echo $form["contact"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Dias de cobro</label>
							<?php echo $form["paydays"]; ?>
							</div>

				        	<div class="form-group">
				        		<label>Hra de cobro</label>
							<?php echo $form["payhours"]; ?>
							</div>


	                        <div class="form-group">
	                        	<div class="btn btn-primary btn-xs" id="submitproviderSubsidiary"><?php echo $txt_boton; ?></div>
	                        	<?php if(!empty($id)): ?>
	                        	<div class="btn btn-danger btn-xs" id="delete_providerSubsidiary" data-provider_subsidiary="<?php echo encode_id($id); ?>"><?php echo "Eliminar"; ?></div>
	                        	<?php endif; ?>
	                        </div>
<?php  echo form_close();?>
<!-- TOKEN INPUT PROVEEDOR -->
<?php if($MODE=="do_it"):?>
<script>


$(document).ready(function() {

	// TOKEN INPUT DEL PROVEEDOR
    $("#country_<?php echo $id;?>").tokenInput("<?php echo base_url().'world/country_tokeninput'; ?>", {
        queryParam:"request[name]",
		hintText:"escribe para buscar coincidencias",
		noResultsText:"no hubo coincidencias",
		searchingText:"buscando...",
		tokenLimit:1,
		onAdd:function() {
			$("#country").parent().find("ul >li.token-input-token span").prop("tabindex",11).focus();
			$("#state").parent().find("ul.token-input-list :input").focus();

		},
		onDelete:function() {
			$("#state").tokenInput("clear");
			$("#town").tokenInput("clear");

		},	
		onReady:function() {
			$("#country").parent().find("ul.token-input-list :input").prop("tabindex",11);

		},
		<?php if(($MODE=="do_it") and !empty($country) ): ?>
			prePopulate:[
				{id:<?php echo json_encode($country); ?>,name:<?php echo json_encode( (!empty($country)?array_search($country, array_flip($countries) ) :'' ) ); ?>,},
			],
		<?php endif; ?>

    });

    $("#state_<?php echo $id;?>").tokenInput("<?php echo base_url().'world/state_tokeninput'; ?>", {
        queryParam:"request[name]",
		hintText:"escribe para buscar coincidencias",
		noResultsText:"no hubo coincidencias",
		searchingText:"buscando...",
		tokenLimit:1,
		onAdd:function() {
			$("#state").parent().find("ul >li.token-input-token span").prop("tabindex",11).focus();
			$("#town").parent().find("ul.token-input-list :input").focus();

		},
		onDelete:function(){
			$("#town").tokenInput("clear");
		},
		onReady:function() {
			$("#state").parent().find("ul.token-input-list :input").prop("tabindex",11);
		},		
		<?php if(($MODE=="do_it") and !empty($state) ): ?>
			prePopulate:[
				{id:<?php echo json_encode($state); ?>,name:<?php echo json_encode( (!empty($state)?array_search($state, array_flip($states) ) :'' ) ); ?>,},
			],
		<?php endif; ?>

    });

    $("#town_<?php echo $id;?>").tokenInput("<?php echo base_url().'world/town_tokeninput'; ?>", {
        queryParam:"request[name]",
		hintText:"escribe para buscar coincidencias",
		noResultsText:"no hubo coincidencias",
		searchingText:"buscando...",
		tokenLimit:1,
		onAdd:function() {
			$("#town").parent().find("ul >li.token-input-token span").prop("tabindex",11).focus();
			$("input#city").focus();

		},
		onReady:function() {
			$("#town").parent().find("ul.token-input-list :input").prop("tabindex",11);
		},	
		// onResult:function(response){// 	console.log(response); // },
		<?php if(($MODE=="do_it") and !empty($town) ): ?>
			prePopulate:[
				{id:<?php echo json_encode($town); ?>,name:<?php echo json_encode( (!empty($town)?array_search($town, array_flip($towns) ) :'' ) ); ?>,},
			],
		<?php endif; ?>

    });
});
</script>
<?php endif; ?>