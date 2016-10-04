<div class="panel-heading">

<?php 
$c=0;
if(!empty($modules_quick)):
$amount_values=count($modules_quick);
foreach ($modules_quick as $key => $module_row) { $c++;
echo anchor(base_url().$module_row["link"]."children/","<img class='yin_yang' src='".base_url()."css/_resources/images/interface/yin_yang.png'></img>");
echo anchor(base_url().$module_row["link"],$module_row["name"]);
// echo anchor(base_url().( ($amount_values==$c)?$module_row["link_view"]."/".$id:$module_row["link"]),$module_row["name"]);
// echo "<img class='yin_yang' src='".base_url()."css/_resources/images/interface/yin_yang.png'></img>";

} 
endif;
?>

<?php 
if(!empty($module_data)):
echo anchor(base_url().$module_data["link"]."children/","<img class='yin_yang' src='".base_url()."css/_resources/images/interface/yin_yang.png'></img>");
echo anchor(base_url().$module_data["link"],$module_data["name"]);
endif;
 ?>

<?php if(!empty($module_data["module_data_method_do_it"])): ?>
<?php echo anchor(base_url().$module_data["module_data_method_do_it"],'<span class="glyphicon glyphicon-plus" style="margin-left:10px;"></span>'); ?>
<?php else: ?>
<span class="glyphicon glyphicon-plus" style="margin-left:10px;" id="add"></span>
<?php endif; ?>
</div>