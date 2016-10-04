<?php 
$MODE=(empty($MODE)?"view":$MODE);

$form["MODE"]=form_hidden("MODE",$MODE);
$form["id"]=form_hidden("id",encode_id("enterprise_fiscal"));
@$file_logo=APPPATH.$sys["storage"]["enterprise_fiscal"]."enterprise_fiscal/".$sys["enterprise_fiscal"]["logo"];
@$file_cedule=APPPATH.$sys["storage"]["enterprise_fiscal"]."enterprise_fiscal/".$sys["enterprise_fiscal"]["cedule"];

if($MODE=="do_it"):

$form["name"]           =form_input("name",$name,"id='name'  placeholder='nombre'");
$form["rfc"]            =form_input("rfc",$rfc,"id='rfc'  placeholder='rfc'");
$form["country"]        =form_input("country",$country,"id='country'  placeholder='pais'");
$form["state"]          =form_input("state",$state,"id='state'  placeholder='estado'");
$form["city"]           =form_input("city",$city,"id='city'  placeholder='ciudad'");
$form["town"]           =form_input("town",$town,"id='town'  placeholder='municipio'");
$form["colony"]           =form_input("colony",$colony,"id='colony'  placeholder='colonia'");
$form["street"]         =form_input("street",$street,"id='street'  placeholder='calle'");
$form["inside_number"]  =form_input("inside_number",$inside_number,"id='inside_number'  placeholder='numero interior'");
$form["outside_number"] =form_input("outside_number",$outside_number,"id='outside_number'  placeholder='numero exterior'");
$form["zip_code"]       =form_input("zip_code",$zip_code,"id='zip_code'  placeholder='codigo postal'");
$form["cedule"]         =form_input("cedule",$cedule,"id='cedule'  placeholder='cédula'");
$form["logo"]           =form_input("logo",$logo,"id='logo'  placeholder='nombre'");
$form["email"]           =form_input("email",$email,"id='email'  placeholder='email'");
$form["phone"]           =form_input("phone",$phone,"id='phone'  placeholder='Telefono'");
$form["tax_regime"]      =form_input("tax_regime",$tax_regime,"id='tax_regime'  placeholder='Telefono'");
$txt_boton="Guardar";

else:

$form["name"]           =$name;
$form["rfc"]            =$rfc;
$form["country"]        =!$country?"":array_search($country, array_flip($countries));
$form["state"]          =!$state?"":array_search($state, array_flip($states) );
// $form["city"]           =empty($city_name)?$city_name:"";
$form["city"]           =$city;
$form["town"]          =!$town?"":array_search($town, array_flip($towns) );
$form["colony"]         =$colony;
$form["street"]         =$street;
$form["inside_number"]  =$inside_number;
$form["outside_number"] =$outside_number;
$form["zip_code"]       =$zip_code;
$form["cedule"]         =$cedule;
$form["logo"]           =$logo;
$form["email"]           =$email;
$form["phone"]           =$phone;
$form["tax_regime"]      =$tax_regime;
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
	                        <div class="form-group">
	                            <?php echo form_label("Rfc:"); ?>
	                            <?php echo $form["rfc"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Pais:"); ?>
	                            <?php echo $form["country"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Estado:"); ?>
	                            <?php echo $form["state"]; ?>
	                        </div>
	                     	<div class="form-group">
	                            <?php echo form_label("Ciudad:"); ?>
	                            <?php echo $form["city"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Municipio:"); ?>
	                            <?php echo $form["town"]; ?>
	                        </div>
	        	            <div class="form-group">
	                            <?php echo form_label("Colonia:"); ?>
	                            <?php echo $form["colony"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Calle:"); ?>
	                            <?php echo $form["street"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Num. Ext:"); ?>
	                            <?php echo $form["outside_number"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("Num. Int:"); ?>
	                            <?php echo $form["inside_number"]; ?>
	                        </div>
	                        <div class="form-group">
	                            <?php echo form_label("C.P:"); ?>
	                            <?php echo $form["zip_code"]; ?>
	                        </div>
	                       	<div class="form-group">
	                            <?php echo form_label("Email:"); ?>
	                            <?php echo $form["email"]; ?>
	                        </div>
							<div class="form-group">
	                            <?php echo form_label("Telefono:"); ?>
	                            <?php echo $form["phone"]; ?>
	                        </div>
							<div class="form-group">
	                            <?php echo form_label("Tax regimen:"); ?>
	                            <?php echo $form["tax_regime"]; ?>
	                        </div>	                        
<?php  echo form_close();?>

			                <!-- <imagen Cedule> -->
	                        <div class="form-group Cedule">

			                <?php 
			            $data_file = array(
			                'name'     => 'file',
			                'id'       => 'file', 
			                'type'     => 'button',
			                'tabindex' => 1,
			                'class'    =>'ui-button-text',
			                'multiple' =>true,
			                );
			            $attributes_cedule = array(
			                "role"=>"form",
			                'id'=>'form_file_upload_cedule',
			                "name"=>'form_file_upload_cedule',
			                "method"=>"POST",
			                "enctype"=>"multipart/form-data"
			                );
			                 ?>

	                        <?php echo form_label("Subir cedula </br>{.jpg .png .bmp}"); ?>

			                <div class="cerUp">
			                        <?php echo form_open(base_url().'file/doUploadFile/?process='.encode_id("enterprise_fiscal")."&id=".encode_id("enterprise_fiscal")."&file=".encode_id("cedule"),$attributes_cedule); ?>   
			                   
			                    <div class="upload">

			                        <?php echo form_upload($data_file); ?>

			                    </div>
			                        <?php echo form_close(); ?>

			                    
			                    <div id="files"></div>

		                        <?php if(!empty($cedule)): ?>
	                        	<div  class="form-group">	     

	                        	<table>
								<tr>
									<td>
										<a class="button <?php echo (!empty($file_cedule)?'':'buttonNotFile'); ?>" href="<?php echo $file_cedule? base_url().'file/download_file/?name_file='.encode_id($cedule).'&file_path='.encode_id($file_cedule):'javascript:void(0);' ?>" <?php echo file_exists($file_cedule)?"":""; ?>>
										<span class="cedule"></span>
										</a>
									</td>
									<td><span class="delete" data-file_encode="<?php echo encode_id($cedule); ?>" data-file="<?php echo encode_id("cedule"); ?>"></span> </td>
								</tr>
		                        </table>
			               		
			               		</div>
								<?php endif; ?>


			                </div>

	                        </div>
			                <!-- </imagen Cedule> -->	 

			                <!-- <imagen logo> -->
	                        <div class="form-group logo">

			                <?php 
			            $data_file = array(
			                'name'     => 'file',
			                'id'       => 'file', 
			                'type'     => 'button',
			                'tabindex' => 1,
			                'class'    =>'ui-button-text',
			                'multiple' =>true,
			                );
			            $attributes_cedule = array(
			                "role"=>"form",
			                'id'=>'form_file_upload_logo',
			                "name"=>'form_file_upload_logo',
			                "method"=>"POST",
			                "enctype"=>"multipart/form-data"
			                );
			                 ?>

	                        <?php echo form_label("Subir logo </br>{.jpg .png .bmp}"); ?>

			                <div class="cerUp">
			                        <?php echo form_open(base_url().'file/doUploadFile/?process='.encode_id("enterprise_fiscal")."&id=".encode_id("enterprise_fiscal")."&file=".encode_id("logo"),$attributes_cedule); ?>   
			                   
			                    <div class="upload">

			                        <?php echo form_upload($data_file); ?>

			                    </div>
			                        <?php echo form_close(); ?>

			                    
			                    <div id="filesLog"></div>

			                </div>

		                        <?php if(!empty($logo)): ?>
	                        	<div  class="form-group">	                        	                        	                        	                        	                        	                        	                        	                        
			                        <table >


									<tr>
										<td>
											<a class="button <?php echo (!empty($file_logo)?'':'buttonNotFile'); ?>" href="<?php echo $file_logo? base_url().'file/download_file/?name_file='.encode_id($logo).'&file_path='.encode_id($file_logo):'javascript:void(0);' ?>" <?php echo file_exists($file_logo)?"":""; ?>>
											<span class="file_imagen"></span>
											<a>
										</td>
										<td><span class="delete" data-file_encode="<?php echo encode_id($logo); ?>" data-file="<?php echo encode_id("logo"); ?>"></span> </td>
									</tr>
			                        </table>
	                        	</div>	                        	                        	                        	                        	                        	                        	                        	                        
								<?php endif; ?>


	                        </div>	                        	                        	                        	                        	                        	                        	                        	                        
			                <!-- </imagen logo> -->	 
	                        
	                        <div class="form-group">
	                        	<div class="btn btn-primary" id="submit"><?php echo $txt_boton; ?></div>
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

<!-- TOKEN INPUT PROVEEDOR -->
<?php if($MODE=="do_it"):?>
<script>
$(document).ready(function() {

	// TOKEN INPUT DEL PROVEEDOR
    $("#country").tokenInput("<?php echo base_url().'world/country_tokeninput'; ?>", {
        queryParam:"request[name]",
		hintText:"escribe para buscar coincidencias",
		noResultsText:"no hubo coincidencias",
		searchingText:"buscando...",
		tokenLimit:1,
		<?php if(($MODE=="do_it") and !empty($country) ): ?>
			prePopulate:[
				{id:<?php echo json_encode($country); ?>,name:<?php echo json_encode( (!empty($country)?array_search($country, array_flip($countries) ) :'' ) ); ?>,},
			],
		<?php endif; ?>

    });

    $("#state").tokenInput("<?php echo base_url().'world/state_tokeninput'; ?>", {
        queryParam:"request[name]",
		hintText:"escribe para buscar coincidencias",
		noResultsText:"no hubo coincidencias",
		searchingText:"buscando...",
		tokenLimit:1,
		<?php if(($MODE=="do_it") and !empty($state) ): ?>
			prePopulate:[
				{id:<?php echo json_encode($state); ?>,name:<?php echo json_encode( (!empty($state)?array_search($state, array_flip($states) ) :'' ) ); ?>,},
			],
		<?php endif; ?>

    });

  //   $("#city").tokenInput("<?php echo base_url().'world/city_tokeninput'; ?>", {
  //       queryParam:"request[name]",
		// hintText:"escribe para buscar coincidencias",
		// noResultsText:"no hubo coincidencias",
		// searchingText:"buscando...",
		// tokenLimit:1,
		// <?php if(($MODE=="do_it") and !empty($city) ): ?>
		// 	prePopulate:[
		// 		{id:<?php echo json_encode($city); ?>,name:<?php echo json_encode( (!empty($city)?array_search($city, array_flip($states) ) :'' ) ); ?>,},
		// 	],
		// <?php endif; ?>

  //   });
    $("#town").tokenInput("<?php echo base_url().'world/town_tokeninput'; ?>", {
        queryParam:"request[name]",
		hintText:"escribe para buscar coincidencias",
		noResultsText:"no hubo coincidencias",
		searchingText:"buscando...",
		tokenLimit:1,
		// onResult:function(response){
		// },
		<?php if(($MODE=="do_it") and !empty($town) ): ?>
			prePopulate:[
				{id:<?php echo json_encode($town); ?>,name:<?php echo json_encode( (!empty($town)?array_search($town, array_flip($towns) ) :'' ) ); ?>,},
			],
		<?php endif; ?>

    });
});
</script>
<?php endif; ?>

<script>

$(document).ready(function(){ 
    $('#form_file_upload_cedule').fileUploadUI({
        uploadTable: $('#files'),
        downloadTable: $('#files'),
        buildUploadRow: function (files, index) {

// ajax
        $("input").prop("disabled",true);
        $("button").prop("disabled",true);
        $("div#ajax_loading").addClass("ajax_loading");
// ...
            return $('<tr><td>' + files[index].name + '<\/td>' +
            '<td class     ="file_upload_progress"><div><\/div><\/td>' +
            '<td class     ="file_upload_cancel">' +
            '<button class ="ui-state-default ui-corner-all" title="Cancel">' +
            '<span class   ="ui-icon ui-icon-cancel">Cancel<\/span>' +
            '<\/button><\/td><\/tr>');
        },
        buildDownloadRow: function (file) {
            var url = "<?php echo base_url(); ?>";

        	// ajax
        $("input").prop("disabled",false);
        $("button").prop("disabled",false);
        $("div#ajax_loading").removeClass("ajax_loading");
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
		var tr='<tr>'
                    +'<td>'
                    +'<span class="'+file.classSpan+'"></span>'
                    +'<\/td>' 
                    +'<td>'
                    +'<span class="delete" data-file_encode="'+file.name_encode+'" data-file="'+file.fileType+'"></span>'
                    +'</td>' 
                    +'<td class="file_name" style="display:none">'
                    + file.name_encode
                    +'</td>' 
                    +'<\/tr>'
                    +'</table>';

            return $(tr);
        },
        // parseResponse: function (file) {console.log(file);},

    });


    $('#form_file_upload_logo').fileUploadUI({
        uploadTable: $('#filesLog'),
        downloadTable: $('#filesLog'),
        buildUploadRow: function (files, index) {

// ajax
        $("input").prop("disabled",true);
        $("button").prop("disabled",true);
        $("div#ajax_loading").addClass("ajax_loading");
// ...
            return $('<tr><td>' + files[index].name + '<\/td>' +
            '<td class     ="file_upload_progress"><div><\/div><\/td>' +
            '<td class     ="file_upload_cancel">' +
            '<button class ="ui-state-default ui-corner-all" title="Cancel">' +
            '<span class   ="ui-icon ui-icon-cancel">Cancel<\/span>' +
            '<\/button><\/td><\/tr>');
        },
        buildDownloadRow: function (file) {
            var url = "<?php echo base_url(); ?>";

        	// ajax
        $("input").prop("disabled",false);
        $("button").prop("disabled",false);
        $("div#ajax_loading").removeClass("ajax_loading");
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
		var tr='<tr>'
                    +'<td>'
                    +'<span class="'+file.classSpan+'"></span>'
                    +'<\/td>' 
                    +'<td>'
                    +'<span class="delete" data-file_encode="'+file.name_encode+'" data-file="'+file.fileType+'"></span>'
                    +'</td>' 
                    +'<td class="file_name" style="display:none">'
                    + file.name_encode
                    +'</td>' 
                    +'<\/tr>'
                    +'</table>';

            return $(tr);
        },
        // parseResponse: function (file) {console.log(file);},

    });
});
</script>
<!-- delete imagen -->
<script>
$(document).on("click","td > span.delete",function(){

var url="<?php echo base_url(); ?>",
	item=$(this)
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
						process:"<?php echo encode_id('enterprise_fiscal');?>",
						id:"<?php echo encode_id('enterprise_fiscal'); ?>",
						file_name:$(item).data("file_encode"),
						file:$(item).data("file")
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