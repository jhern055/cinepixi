    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <?php echo $this->load->view("recycled/menu/panel_heading","",true); ?>
                <div class="panel-body">

<?php $attributes_form = array('class' => 'formAdminSaleAjax'); ?>
<?php  echo form_open("form",$attributes_form);?>

                    <div class="row">
                        <div class="col-sm-6">
                        <div class="dataTables_length" id="dataTables-example_length">
                        <label>Show 
                            <?php echo form_dropdown('show_amount_payment',$sys["forms_fields"]["show_amount"], '10',"id='show_amount_payment'"); ?>
                        </div>
                        </div>

                        <div class="col-sm-6">
                        <div id="dataTables-example_filter" class="dataTables_filter">
                        <label>Nombre:<?php echo form_input("input_search_payment",$input_search_payment," id=input_search_payment"); ?></label>
                        </div>
                        </div>

                        <div class="col-sm-6">
                        <div id="dataTables-example_filter" class="dataTables_filter">
                        <label>
                        <?php 
                        $modules=array(
                            "admin/sale/"=>"Ventas",
                            "admin/sale/remission/"=>"Remisiones"
                            );
                         ?>
                        Modulo
                        <?php echo form_dropdown('source_module',$modules,!empty($source_module)?$source_module:"","id='source_module'"); ?>
                        </label>
                        </div>
                        </div>

                    </div>

<?php  echo form_close();?>

                    <div class="dataTable_wrapper" id="div_table">

                            <?php echo $this->load->view("admin/payment/ajax/table-payment-view",$records_array,true); ?>
                    </div>
                    
                </div>
                <!-- /.panel-body -->
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
        
    </div>
<!-- /.row -->

<!-- esto es la paginacion  -->
<script>
$(document).on("change", "select#source_module", function() {

var formSearch=$("form.formAdminSaleAjax").serialize()+"&ajax=1";

    $.ajax({

        type: "GET",
        data: formSearch,
        url: "<?php echo base_url().$this->uri->uri_string."/payment_ajax/"; ?>",
        beforeSend:function (html) {
        // ajax
        $("div#ajax_loading").addClass("ajax_loading");
        // ...
        console.log(html);
        },
        success: function(html) {
        $('div#div_table').html(html);

        // ajax
        $("div#ajax_loading").removeClass("ajax_loading");
        // ...

        }

     });

});

// // paginacion de ajax 
$(document).on("change", "select#show_amount_payment", function() {

var formSearch=$("form.formAdminSaleAjax").serialize()+"&ajax=1";

    $.ajax({

        type: "GET",
        data: formSearch,
        url: "<?php echo base_url().$this->uri->uri_string."/payment_ajax/"; ?>",
        beforeSend:function (html) {
        // ajax
        $("div#ajax_loading").addClass("ajax_loading");
        // ...

        },
        success: function(html) {
        $('div#div_table').html(html);

        // ajax
        $("div#ajax_loading").removeClass("ajax_loading");
        // ...

        }

     });

});

$(document).on("keyup", "input#input_search_payment", function() {

var formSearch=$("form.formAdminSaleAjax").serialize()+"&ajax=1";

    $.ajax({

        type: "GET",
        data:formSearch,
        url: "<?php echo base_url().$this->uri->uri_string."/payment_ajax/"; ?>",
        beforeSend:function (html) {
        // ajax
        $("div#ajax_loading").addClass("ajax_loading");
        // ...

        },
        success: function(html) {

        $('div#div_table').html(html);

        // ajax
        $("div#ajax_loading").removeClass("ajax_loading");
        // ...
        
        }

     });

});

// cargar los registros por ajax
// $(document).ready(function(){

//     $.ajax({

//         type: "GET",
//         data:{ajax:1},
//         url: "<?php echo base_url().$this->uri->uri_string."/payment_ajax/".(!empty($this->session->userdata('record_start_row_payment'))?$this->session->userdata('record_start_row_payment'):0 ); ?>",
//         beforeSend:function(html){
//         },
//         success: function(html) {

//         $('div#div_table').html(html);
//         $('div#div_table').focus();

//         }

//      });

// });

// paginacion de ajax 
$(document).on("click", "ul.admin_payment_payment_ajax > li > a", function() {

var formSearch=$("form.formAdminSaleAjax").serialize()+"&ajax=1";

    $.ajax({

        type: "GET",
        data:formSearch,
        url: $(this).prop("href"),
        beforeSend: function(html) {

        // ajax
        $("input").prop("disabled",true);
        $("button").prop("disabled",true);
        $("div#ajax_loading").addClass("ajax_loading");
        // ...

        },
        success: function(html) {

        $('div#div_table').html(html);
        $('div#div_table').focus();

        // ajax
        $("input").prop("disabled",false);
        $("button").prop("disabled",false);
        $("div#ajax_loading").removeClass("ajax_loading");
        // ...

        }

     });
    
    return false;

});

$('form.formAdminSaleAjax').submit(false);
</script>