<?php 
$MODE=(empty($MODE)?"view":$MODE);
$edit=(empty($edit)?false:$edit);

$hd["MODE"]=form_hidden("MODE",$MODE);
// print_r(json_encode($link_details));
if($link_details)
foreach ($link_details as $k0 => $v0) {

$id=(!empty($v0["id"])?$v0["id"]:'');
$hd["id"] =(!empty($v0["id"])?trim(base64_encode($v0["id"])):'');
$hd["publication"]=form_hidden("publication",(!empty($v0["publication"])?encode_id($v0["publication"]):''));

if($MODE=="do_it"):

$hd["description"] =form_input("description",(!empty($v0["description"])?$v0["description"]:'')," id='description'  placeholder='descripción' tabindex='15'" );
$hd["link"]        =form_input("link",(!empty($v0["link"])?$v0["link"]:'')," id='link'  placeholder='link' tabindex='15'" );
$hd["original"]    =form_input("original",(!empty($v0["original"])?$v0["original"]:'')," id='original'  placeholder='original' tabindex='15'" );

else:

$hd["description"] =(!empty($v0["description"])?$v0["description"]:'');
$hd["link"]        =(!empty($v0["link"])?$v0["link"]:'&nbsp');
$hd["original"]    =(!empty($v0["original"])?$v0["original"]:'&nbsp');

endif;

?>
<?php if($edit==false): ?>
<div class='item itemPayment success'>
<?php endif; ?>
	<div class="form-group" style='display:none' id="hidden">
        <?php echo $hd["MODE"]."/"; ?>
		<?php echo $hd["publication"]; ?>

    </div>
	<div class="id" data-id="<?php echo (isset($hd["id"])?$hd["id"]:'') ?>">
	<?php echo $id; ?>
	</div>
<!-- 
	<div class="method" data-method_id="<?php //echo (isset($v0["method"])?$v0["method"]:'') ?>">
	<?php //echo $hd["method"]; ?>
	</div> -->
	<div class="description">
		<?php echo $hd["description"]; ?>
	</div>
	<div class="link">
		<!-- <a href="<?php //echo !empty($hd["link"])?$hd["link"]:'javascript:void(0)'; ?>"> -->
		<?php echo $hd["link"]; ?>
		<!-- </a> -->
	</div>
	<div class="original">
		<!-- <a href="<?php echo !empty($hd["original"])?$hd["original"]:'javascript:void(0)'; ?>"> -->
		<?php echo $hd["original"]; ?>
		<!-- </a> -->
	</div>



	<?php if($MODE=="do_it"): ?> <!--|Aceptar| o |Cancelar|  -->
	<div class='editionActions'>
	<button type='button' class='submit_link UStyle' tabindex='15'>aceptar</button>
	<button type='button' class='cancel UStyle' tabindex='15'>cancelar</button>
	</div>
	<?php endif; ?>	

	<?php if($MODE=="view"): ?> <!--|Editar| lapiz o |Eliminar|  -->

	<div class='ops'>
	<button type='button' class='submit_link edit' tabindex='15' title='editar detalle'></button>
	<button type='button' class='delete' tabindex='15' title='eliminar detalle'></button>
	</div>
	
	<?php endif; ?>	

<?php if($edit==false): ?>
</div>
<?php endif; ?>

	<?php if(empty($id)){ ?>

	<script>
	// // poner por default lo que nos debe 
	// var residuary   =$("label.residuary").text()
	// residuary=parseFloat(number_format(residuary,2,".",""));
	// $("div.linkListContainer > div.area2 > div.data > div.item > div.import input#import").val(residuary);
	
	</script>

	<?php } ?>

<?php 	
}
?>

<script type="text/javascript">
$(document).ready(function(){

<?php if($MODE=="do_it"){ ?>
	$("div.linkListContainer > div.area2 > div.data > div.item > div.link > input#link").focus();
	$("div.add_link").hide();
<?php } else {?>
	$("div.add_link").show();
<?php } ?>

});
</script>