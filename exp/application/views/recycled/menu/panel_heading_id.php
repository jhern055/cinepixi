<div class="panel-heading">
    <?php 
    $amount_values=count($modules_quick);
    $c=0;
    foreach ($modules_quick as $key => $module_row) { $c++;
    echo anchor(base_url().( ($amount_values==$c)?$module_row["link_view"].$module_row["module"]."View/".$id:$module_row["link"]),$module_row["name"]);
    echo "<img class='yin_yang' src='".base_url()."css/_resources/images/interface/yin_yang.png'></img>";
    } ?>
        
    <?php if(!empty($module_data)) echo anchor(base_url().$module_data["link"],$module_data["name"]); ?>
    <span class="glyphicon glyphicon-plus" style="margin-left:10px;" id="add"></span>
</div>