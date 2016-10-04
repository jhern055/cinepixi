<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div id="dataTables-example_wrappesr" class="dataTables_wrapper form-inline dt-bootstrap no-footer">

<div class="row">
    <div class="col-lg-12"> 
	
    	<div class="panel panel-default moduleChildren">
        <?php echo $this->load->view("recycled/menu/panel_heading","",true); ?>

<?php 

if(!empty($module_childrens))
foreach($module_childrens as $v):
?>
		<div class="item" style='width:100%; padding:5px 0px 5px 15px; float:left;'>
			<!-- <div class="yin_yang"></div> -->
			<?php echo anchor(base_url().$v["link"]."children","<span class='illum' style='margin-top:6px;margin-left:4px;'></span>"); ?>
			<div class="text">
				<?php echo $v["name"] ? anchor($v["link"],$v["name"]) : "&nbsp;" ; ?>
			</div>
		</div>
<?php endforeach; ?>

	</div>
	<!-- /.panel-default -->
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

</div>