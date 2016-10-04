<?php echo $this->load->view("email/email_view_info","",true); ?>
<script>
function get_emails(source_module,id){

	$.ajax({
        type: "POST",
        url: "<?php echo base_url().'email/get_emails_source_module';?>",
        async:true,
        dataType:"json",
        data:{  
        id:id, // deben de llegar encriptados con encode_id()
        source_module:source_module, // deben de llegar encriptados con encode_id()
        }, 
        beforeSend:  function(response) {

        },
        success: function(response){

		$("div#emailInfoContainer").html(response.html);
	        		
        }

	});

}
</script>							