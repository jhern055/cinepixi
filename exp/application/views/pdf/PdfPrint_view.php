<?php 

if(!empty($_CONFIG_MODULE))
    extract($_CONFIG_MODULE);

$templates=glob($TEMPLATES_DIR."/*",GLOB_ONLYDIR);
$templates=array_map(function($v) { $v=explode("/",$v);  $v=array_slice($v,-1,1);  return $v[0]; },$templates);
sort($templates);

// ...options
$i=0;
foreach($templates as $k=>$v) {

    $templates[$v]=array("index"=>$i);  
    $templates[$v]["options"]=!file_exists($TEMPLATES_DIR."/options.php") ? array () : include($TEMPLATES_DIR."/options.php") ;

    unset($templates[$k]);
    $i++;

}

$hidden=array(
        "id"=>encode_id($id),
        "source_module"=>encode_id($module),
        );
    
$form["templates"]=array();

    foreach($templates as $k=>$v)
    $form["templates"][]=form_radio("template",$k,"template_{$v['index']}","tabindex=11 data-templateindex='{$v['index']}'".( $DEFAULT_TEMPLATE!=$k ? "" : " checked=checked" ))."<label for='template_{$v['index']}'>".$k."</label>";
 
$form["buttons"]["submit"]=form_submit("submit","enviar","tabindex=11");

$form["buttons"]["cancel"]=form_button("cancel","cancelar","tabindex=11 onclick=' window.close(); '");

 ?>
    <div class="row">
        <div class="col-lg-12">
        
        <div class="panel panel-default">
        <?php echo $this->load->view("recycled/menu/panel_heading_id","",true); ?>

            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">

<?php $attributes_form = array('class' => 'dinamic','id' => 'dinamic'); ?>
<?php  echo form_open("pdf/printGet",$attributes_form,$hidden);?>

                <?php if($form["templates"]): ?>
                <div class="form-group">
                    <div class="area1">
                        plantilla
                    </div>
                    <div class="area2">
                    <?php foreach($form["templates"] as $v): ?>
                        <div class="item"><?php echo $v; ?></div>
                    <?php endforeach; ?>
                    </div>                    
                </div>

<!-- style=" display:none; " -->
                <?php foreach($templates as $k=>$v): $i=-1; ?>
                <?php foreach($v["options"] as $k2=>$v2):  $i++;  $tmp_id="template_{$v['index']}_option_{$k2}"; ?>
                <div class="form-group template<?php echo $v["index"]; ?>OptionContainer" >
                        <div class="area1">
                            opción <?php echo ($i+1); ?>
                        </div>
                        <div class="area2">
                            <?php echo form_checkbox("options[]",$k2,"",$tmp_id." tabindex='11'".( ($DEFAULT_TEMPLATE!=$k or !$DEFAULT_TEMPLATE_OPTIONS or !in_array($k2,$DEFAULT_TEMPLATE_OPTIONS)) ? "" : " checked=checked" )); ?> <label for="<?php echo $tmp_id; ?>"><?php echo $v2; ?></label>
                        </div>
                </div>
                <?php endforeach; ?>
                <?php endforeach; ?>

                <?php if(!empty($DEFAULT_TEMPLATE_WATERMARK)): ?>
                <div class="form-group">
                        <div class="area1">
                            marca de agua
                        </div>
                        <div class="area2">
                            <?php echo form_input("watermark",$DEFAULT_TEMPLATE_WATERMARK,"tabindex='11'"); ?>
                            <span class="note">opcional :: útil si deseas imprimir el mismo documento varias veces, donde en c/u se añada una marca de agua (texto), separar múltiples valores con comas.</span>
                        </div>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <div class="area1">
                        archivo
                    </div>
                    <div class="area2">
                        <?php $action="display"; ?>

                        <div class="item">
                            <?php echo form_radio("action","download",( $action!="download" ? null : " checked" ),"tabindex='11'"); ?>
                            <label for="action_download">descargar</label>
                        </div>

                        <div class="item">
                            <?php echo form_radio("action","display",( $action!="display" ? null : " checked" ),"tabindex='11'"); ?>
                            <label for="action_display">mostrar</label>
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <div class="area1">
                        &nbsp;
                    </div>
                    <div class="area2">
                        <?php echo implode("",$form["buttons"]); ?>
                    </div>
                </div>

                <?php else: ?>
                    <div class="noTemplatesMsg">
                        <span>no existen plantillas de impresión!</span>
                    </div>
                <?php endif; ?>
<?php  echo form_close();?>

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

    <script>
    <!--

    // browser pdf reader plugin exists?, CODEID "BPRPE012633"

    // if(!simplePDFReaderBrowserPluginDetection())
     // $("form#dinamic > div.pdfPluginDetectionAdviseContainer").show();

    // dinamic print template options, CODEID "DPTO08414"

    $("form#dinamic > div.templateContainer > div.area2 > div.item > :input").click(function() {

        $("form#dinamic > div.templateOptionContainer").hide();
        $("form#dinamic > div.template"+this.dataset.templateindex+"OptionContainer").show();

    });

    $("form#dinamic").submit(function() {

        if(!$("form#dinamic :input[name='template']:checked").length)
         { alert("selecciona una plantilla de impresión!");  return false; }

    });

    // ...

    $(document).ready(function() {
<?php if($DEFAULT_TEMPLATE): ?>

        $("form#dinamic > div.templateContainer > div.area2 > div.item > :input:checked").click();

<?php endif; ?>

<?php if(!in_array($DEFAULT_TEMPLATE,array_keys($templates))): ?>
        $("form#dinamic > div.templateContainer > div.area2 > div.item:eq(0) > input").focus();
<?php else: ?>
        $("form#dinamic > div.buttonsContainer > div.area2 > input[type=submit]").focus();
<?php endif; ?>

    });

    -->
    </script>