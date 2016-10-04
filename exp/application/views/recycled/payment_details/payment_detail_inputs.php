<?php 
$MODE=(empty($MODE)?"view":$MODE);
$edit=(empty($edit)?false:$edit);

$hd["MODE"]=form_hidden("MODE",$MODE);
// print_r(json_encode($payment_details));
if($payment_details)
foreach ($payment_details as $k0 => $v0) {

$id=(isset($v0["id"])?$v0["id"]:'');
$hd["id"] =(isset($v0["id"])?trim(base64_encode($v0["id"])):'');

if($MODE=="do_it"):

$hd["method"]=form_dropdown('method',$sys["forms_fields"]["payment_method"],(isset($v0["method"])?$v0["method"]:''),"id='method' tabindex='15'");
$hd["import"]=form_input("import",(isset($v0["import"])?$v0["import"]:'')," id='import'  placeholder='importe' tabindex='15'" );
$hd["type_of_currency"]=form_dropdown('type_of_currency',$type_of_currency_array,(isset($v0["type_of_currency"])?$v0["type_of_currency"]:''),"id='type_of_currency' tabindex='15'");
$hd["exchange_rate"]=form_input("exchange_rate",(isset($v0["exchange_rate"])?$v0["exchange_rate"]:'')," id='exchange_rate'  placeholder='tipo de cambio' tabindex='15'" );
$hd["comment"]=form_input("comment",(isset($v0["comment"])?$v0["comment"]:'')," id='comment'  placeholder='Comentario' tabindex='15'" );
$hd["date"]=form_input("date",(isset($v0["date"])?$v0["date"]:'')," id='date'  placeholder='Fecha' tabindex='15'" );

else:

$hd["method"]=(isset($v0["method"])?array_search($v0["method"], array_flip($sys["forms_fields"]["payment_method"])):'');
$hd["import"]=(isset($v0["import"])?$v0["import"]:'');
$hd["type_of_currency"]=(isset($v0["type_of_currency"])?array_search($v0["type_of_currency"], array_flip($type_of_currency_array)) :'');
$hd["exchange_rate"]=(isset($v0["exchange_rate"])?$v0["exchange_rate"]:'');
$hd["comment"]=(isset($v0["comment"])?$v0["comment"]:'');
$hd["date"]=(isset($v0["date"])?$v0["date"]:'');

endif;

?>
<?php if($edit==false): ?>
<div class='item itemPayment success'>
<?php endif; ?>
	<div class="form-group" style='display:none' id="hidden">
        <?php echo $hd["MODE"]; ?>
    </div>
	<div class="id" data-id="<?php echo (isset($hd["id"])?$hd["id"]:'') ?>">
	<?php echo $id; ?>
	</div>

	<div class="method" data-method_id="<?php echo (isset($v0["method"])?$v0["method"]:'') ?>">
	<?php echo $hd["method"]; ?>
	</div>
	
	<div class="import">

    	<span class='sign_price'>$</span>
		<div class="value">
		<?php echo $hd["import"]; ?>
		</div>
	</div>

	<div class="type_of_currency" data-type_of_currency_id="<?php echo (isset($v0["type_of_currency"])?$v0["type_of_currency"]:'') ?>">
		<?php echo $hd["type_of_currency"]; ?>
	</div>

	<div class="exchange_rate">
		<?php echo $hd["exchange_rate"]; ?>
	</div>

	<div class="comment">
		<?php echo $hd["comment"]; ?>
	</div>

	<div class="date">
		<?php echo $hd["date"]; ?>
	</div>

	<?php if($MODE=="do_it"): ?> <!--|Aceptar| o |Cancelar|  -->
	<div class='editionActions'>
	<button type='button' class='submit_payment UStyle' tabindex='15'>aceptar</button>
	<button type='button' class='cancel UStyle' tabindex='15'>cancelar</button>
	</div>
	<?php endif; ?>	

	<?php if($MODE=="view"): ?> <!--|Editar| lapiz o |Eliminar|  -->

	<div class='ops'>
	<button type='button' class='submit_payment edit' tabindex='15' title='editar detalle'></button>
	<button type='button' class='delete' tabindex='15' title='eliminar detalle'></button>
	</div>
	
	<?php endif; ?>	

<?php if($edit==false): ?>
</div>
<?php endif; ?>

	<?php if(empty($id)){ ?>

	<script>
	// poner por default lo que nos debe 
	var residuary   =$("label.residuary").text()
	residuary=parseFloat(number_format(residuary,2,".",""));
	$("div.paymentListContainer > div.area2 > div.data > div.item > div.import input#import").val(residuary);
	</script>

	<?php } ?>

<?php 	
}
?>

<script type="text/javascript">
$(document).ready(function(){

<?php if($MODE=="do_it"){ ?>
	$("div.paymentListContainer > div.area2 > div.data > div.item > div.quantity > input#quantity").focus();
	$("div.add_payment").hide();
<?php } else {?>
	$("div.add_payment").show();
<?php } ?>

});
</script>

<script>

  $(function() {

   $("#date").datepicker();
  });
</script>
<?php echo $this->load->view('recycled/payment_details/payment_detail-summary','',true); ?>