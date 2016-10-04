
<?php $user_id=$this->session->userdata("user_id");?>
<link href="<?php echo base_url(); ?>css/login/login.css" rel="stylesheet">


<?php if(empty($user_id)): ?>

<?php $data_email_user = array('name'=> 'email_user','id'=> 'email_user','type'=>'email_user','placeholder'=>'Email o Usuario ','class'=>'form-control','value'=> $this->input->post("email_user"),'autofocus'=>'autofocus'); ?>

<?php $attributes = array('role' => 'form','class'=>'Form5',"name"=>'Form5'); ?>

<div class="col-sm-9 col-md-8 col-lg-8">
	<div class="container well" id="sha">
		<div class="row">
					<div class="col-xs-12">
                        <img src="<?php echo base_url(); ?>css/_resources/images/interface/avatar.png" class="img-responsive" id="avatar">
					</div>
		</div>
		<?php //echo form_open(base_url().'login/in/',$attributes);?>   
				<div class="form-group">

                    <?php echo form_input($data_email_user); ?>

				</div>
				<div class="btn btn-lg btn-primary btn-block submit">recuperar mi contraseña </div>

				<div class="checkbox">
				       <p class="help-block createAccount"><a href="<?php echo base_url().'login/create_account'; ?>">Crear una cuenta</a></p>
				</div>
		<?php //echo form_close(); ?>
	</div>
</div>

<script>

function alert_danger(field,msg){
var error_html='<div class="alert alert-danger">'
                +'<a class="close" data-dismiss="alert" href="#">&times;</a>'
                +' <a href="javascript:void(0)" class="alert-link">'+field+'</a> '
                +'<p>'+msg+'.</p>'
                +'</div>'
                ;
    return error_html;
}

$(document).ready(function(){

$("input#email_user").keypress(function(e) {
    if(e.which == 13){
    $(this).blur();
    $(this).parent().parent().find("div.submit").focus().click();
    }

});

});

$(document).on("click","div.submit",function(){

    var url="<?php echo base_url(); ?>",
        form=$("div#sha").get(0),
        containerDiv=$(this).parent().parent(),
        email_user=$(this).parent().find("input#email_user").val()
        ;

    $.ajax({
        url: url+'login/recovery',
        type: 'POST',
        dataType: 'json',
        data: {email_user: email_user},
        beforeSend: function(response){

        $(form).find('input').prop("disabled","disabled"); 
        $(form).find('div.submit').hide(); 
        $(containerDiv).append('<img class="loading" src="<?php echo base_url(); ?>/images/interface/facebook_style_loader.gif" />');

        },
        success: function(response){

            if(response.status){

            $(form).append('<div class="alert alert-success">'+response.msg+'</div>');

            $(form).find('div.form-group').removeClass('borderRequired');
            $(form).find('div.alert-danger').remove();
            $(form).find("div.alert-warning").remove();

            setTimeout(function(){
                
            $(form).find('div.alert-success').remove();
            $(containerDiv).find('img.loading').remove();    
            $(form).find('input').prop("disabled",""); 
            $(form).find('div.submit').show(); 

            },5000);

            }else{

            $(form).find('div.alert-danger').remove();
            $(form).find("div.alert-warning").remove();
            
            $(form).find('input').prop("disabled",""); 
            $(form).find('div.submit').show(); 
            $(containerDiv).find('img.loading').remove();

            // validar email_user 
            if(response.email_user==1 || response.thereNickName==1){
            $(form).find('input#email_user').focus();
            $(form).find('input#email_user').parent().addClass("borderRequired");

            if(!$(form).find('input#email_user').parent().find("div.alert-danger").get(0))
            $(form).find('input#email_user').parent().append(alert_danger("",response.msg));
            
            return;
            }
            else { 
            $(form).find('input#email_user').parent().removeClass("borderRequired");
            $(form).find('input#email_user').parent().find("div.alert-danger").remove();
            }

            }  //fin else de errores

            $(containerDiv).find('img.loading').remove();
            return;

        }
    });
});
</script>

<?php endif; ?>