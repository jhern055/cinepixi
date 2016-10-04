    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <?php echo $this->load->view("recycled/menu/panel_heading","",true); ?>
                <!-- /.panel-heading -->
                <div class="panel-body">

<?php $attributes_form = array('class' => 'formAdminProviderAjax'); ?>
<?php  echo form_open("form",$attributes_form);?>

                    <div class="row">
                        <div class="col-sm-6">
                        <div class="dataTables_length" id="dataTables-example_length">
                        <label>Show 
                            <?php echo form_dropdown('show_amount_provider',$sys["forms_fields"]["show_amount"], '10',"id='show_amount_provider'"); ?>
                        </div>
                        </div>

                        <div class="col-sm-6">
                        <div id="dataTables-example_filter" class="dataTables_filter">
                        <label>Nombre:<?php echo form_input("input_search_provider",$input_search_provider," id=input_search_provider"); ?></label>
                        </div>
                        </div>
                    </div>

<?php  echo form_close();?>

                    <div class="dataTable_wrapper" id="div_table">

                            <?php echo $this->load->view("admin/provider/ajax/table-provider-view",$records_array,true); ?>
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

// // paginacion de ajax 
$(document).on("change", "select#show_amount_provider", function() {

var formSearch=$("form.formAdminProviderAjax").serialize()+"&ajax=1";

    $.ajax({

        type: "GET",
        data: formSearch,
        url: "<?php echo base_url().$this->uri->uri_string."/provider_ajax/"; ?>",
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

$(document).on("keyup", "input#input_search_provider", function() {

var formSearch=$("form.formAdminProviderAjax").serialize()+"&ajax=1";

    $.ajax({

        type: "GET",
        data:formSearch,
        url: "<?php echo base_url().$this->uri->uri_string."/provider_ajax/"; ?>",
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
//         url: "<?php echo base_url().$this->uri->uri_string."/provider_ajax/".(!empty($this->session->userdata('record_start_row_provider'))?$this->session->userdata('record_start_row_provider'):0 ); ?>",
//         beforeSend:function(html){
//         },
//         success: function(html) {

//         $('div#div_table').html(html);
//         $('div#div_table').focus();

//         }

//      });

// });

// paginacion de ajax 
$(document).on("click", "ul.admin_provider_provider_ajax > li > a", function() {

var formSearch=$("form.formAdminProviderAjax").serialize()+"&ajax=1";

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

$('form.formAdminProviderAjax').submit(false);
</script>