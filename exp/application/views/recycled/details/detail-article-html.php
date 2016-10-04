<?php 
$DAD_MODE=(empty($DAD_MODE)?"view":$DAD_MODE);
$MODE=(empty($MODE)?"view":$MODE);
$edit=(empty($edit)?false:$edit);

$hd["MODE"]=form_hidden("MODE",$MODE);

if($details)
foreach ($details as $k0 => $v0) {

if($MODE=="do_it"):

$hd["quantity"]=form_input(
			array(	'name'=> 'quantity',
					'id'=> 'quantity',
					'title'=>'',
					'placeholder'=> '',
					'tabindex'=> 15,
					'value'=>(isset($v0["quantity"])?$v0["quantity"]:'')

				)
			); 

$hd["article"]=form_input(
			array(	'name'=> 'article',
					'id'=> 'article',
					'title'=>'',
					'placeholder'=> 'articulo',
					'tabindex'=> 15,
					'value'=>(isset($v0["article"])?$v0["article"]:'')
				)
			); 

$hd["description"]=form_input(
			array(	'name'=> 'description',
					'id'=> 'description',
					'title'=>'',
					'placeholder'=> 'descripción',
					'tabindex'=> 15,
					'value'=>(isset($v0["description"])?$v0["description"]:'')
				)
			);

$hd["price"]=form_input(
			array(	'name'=> 'price',
					'id'=> 'price',
					'title'=>'',
					'placeholder'=> 'precio',
					'tabindex'=> 15,
					'value'=>(isset($v0["price"])?$v0["price"]:'')
				)
			); 

$hd["taxIeps"]=form_input(
			array(	'name'=> 'taxIeps',
					'id'=> 'taxIeps',
					'title'=>'',
					'placeholder'=> '',
					'tabindex'=> 15,
					'value'=>(isset($v0["taxIeps"])?$v0["taxIeps"]:'')
				)
			); 

$hd["taxIva"]=form_input(
			array(	'name'=> 'taxIva',
					'id'=> 'taxIva',
					'title'=>'',
					'placeholder'=> '',
					'tabindex'=> 15,
					'value'=>(isset($v0["taxIva"])?$v0["taxIva"]:'')
				)
			); 
 
$hd["taxIvaRetained"]=form_input(
			array(	'name'=> 'taxIvaRetained',
					'id'=> 'taxIvaRetained',
					'title'=>'',
					'placeholder'=> '',
					'tabindex'=> 15,
					'value'=>(isset($v0["taxIvaRetained"])?$v0["taxIvaRetained"]:'')
				)
			); 

$hd["taxIsr"]=form_input(
			array(	'name'=> 'taxIsr',
					'id'=> 'taxIsr',
					'title'=>'',
					'placeholder'=> '',
					'tabindex'=> 15,
					'value'=>(isset($v0["taxIsr"])?$v0["taxIsr"]:'')
				)
			); 


else:


// $hd["stockModification"] =(isset($v0["stockModification"])?$v0["stockModification"]:'');
$hd["quantity"]          =(isset($v0["quantity"])?$v0["quantity"]:'');
$hd["article"]           =(isset($v0["article"])?$v0["article"]:'');
$hd["article_name"]           =(isset($v0["article"])?array_search(trim($v0["article"]), $articles):'');
$hd["description"]       =(isset($v0["description"])?$v0["description"]:'');
$hd["price"]             =(isset($v0["price"])?$v0["price"]:'');
$hd["taxIeps"]           =(isset($v0["taxIeps"])?$v0["taxIeps"]:'');
$hd["taxIva"]            =(isset($v0["taxIva"])?$v0["taxIva"]:'');
$hd["taxIvaRetained"]    =(isset($v0["taxIvaRetained"])?$v0["taxIvaRetained"]:'');
$hd["taxIsr"]            =(isset($v0["taxIsr"])?$v0["taxIsr"]:'');

endif;

$stockModification_check=array(
							'name'=> 'stockModification',
							'id'=> 'stockModification',
							'title'=>'',
							'placeholder'=> 'stockModification',
							'tabindex'=> 15,
							'checked'=>(!empty($v0["stockModification"])?TRUE:''),
						);

if($MODE!="do_it")
$stockModification_check=array_merge(array('disabled' => ''),$stockModification_check);

$hd["stockModification"]=form_checkbox($stockModification_check);

if(empty($hd["article_name"]))
$hd["article_name"] ='';

$hd["article_id"] =(isset($v0["article"])?$v0["article"]:'');
$hd["id"]         =(isset($v0["id"])?trim(base64_encode($v0["id"])):'');

if(!empty($v0["quantity"]))
$hd["totalSub"] =number_format( (isset($v0["quantity"])?$v0["quantity"]*$v0["price"]:''),2,'.','' );
else
$hd["totalSub"]=0;
?>
<?php if($edit==false): ?>
<div class='item itemArticle'>
<?php endif; ?>
	<div class="form-group" style='display:none' id="hidden">
        <?php echo $hd["MODE"]; ?>
    </div>
	<div class="id">
	<?php echo $hd["id"]; ?>
	</div>
	<div class="stockModification" title="modificar inventario activo?, si está activa al procesar la compra se elimina la cantidad de artículos introducida de las existencias actuales.">
	<?php echo $hd["stockModification"]; ?>
	</div>
	<div class="quantity" title="cantidad">
		<?php echo $hd["quantity"]; ?>
	</div>
	
	<div class="article" style="<?php echo ( ($MODE=='view')?"display:none":'' ) ?>">
		<?php echo $hd["article"]; ?>
	</div>

	<div class="article_name"  style="<?php echo ( ($MODE!='view')?"display:none":'' ) ?>">
		<?php echo $hd["article_name"]; ?>
	</div>

	<div class="description">
		<?php echo $hd["description"]; ?>
	</div>

	<div class="price">
		<?php echo $hd["price"]; ?>
	</div>

	<div class="totalSub">
		<?php echo $hd["totalSub"]; ?>
	</div>

	<div class="taxIeps">
		<?php echo $hd["taxIeps"]; ?>
	</div>

	<div class="taxIva">
		<?php echo $hd["taxIva"]; ?>
	</div>

	<div class="taxIvaRetained">
		<?php echo $hd["taxIvaRetained"]; ?>
	</div>
	<div class="taxIsr">
		<?php echo $hd["taxIsr"]; ?>
	</div>

	<?php if($MODE=="do_it"): ?> <!--|Aceptar| o |Cancelar|  -->
	<div class='editionActions'>
	<button type='button' class='submit_article UStyle' tabindex='15'>aceptar</button>
	<button type='button' class='cancel UStyle' tabindex='15'>cancelar</button>
	</div>
	<?php endif; ?>	

	<?php if($MODE=="view" and $DAD_MODE=="do_it"): ?> <!--|Editar| lapiz o |Eliminar|  -->

	<div class='ops'>
	<button type='button' class='submit_article edit' tabindex='15' title='editar detalle'></button>
	<button type='button' class='delete' tabindex='15' title='eliminar detalle'></button>
	</div>
	
	<?php endif; ?>	

<?php if($edit==false): ?>
</div>
<?php endif; ?>

<?php if($MODE=="do_it"): ?>
<script>

// tienes que tener declarada la libreria del token input para que funcione
$(document).ready(function() {

	// TOKEN INPUT DEL ARTICULO 
    $("#article").tokenInput("<?php echo base_url().'article/tokeninput'; ?>", {
        queryParam:"request[name]",
		hintText:"escribe para buscar coincidencias",
		noResultsText:"no hubo coincidencias",
		searchingText:"buscando...",
		tokenLimit:1,
		onAdd:function() {
			$("#article").parent().find("ul >li.token-input-token span").prop("tabindex",15).focus();

		},
		onReady:function() {

			$("#article").parent().find("ul.token-input-list :input").prop("tabindex",15);
			
		},
		<?php if(!empty($v0["article"]) ): ?>
			prePopulate:[
				{id:<?php echo json_encode( trim($v0["article"]) ); ?>,name:<?php echo json_encode( (!empty($v0["article"])?array_search( trim($v0["article"]) , $articles ) :'' ) ); ?>,},
			],
		<?php endif; ?>

    });
});

</script>
<?php endif; ?>

<?php 	
} 
?>

<script type="text/javascript">
$(document).ready(function(){

<?php if($MODE=="do_it"){ ?>
	$("div.articleListContainer > div.area2 > div.data > div.item > div.quantity > input#quantity").focus();
	$("div.add_detail").hide();
<?php } else {?>
	$("div.add_detail").show();
<?php } ?>

});
</script>

<?php echo $this->load->view('recycled/details/detail-summary','',true); ?>