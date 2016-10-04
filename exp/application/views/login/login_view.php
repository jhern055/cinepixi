<link href="<?php echo base_url(); ?>css/login/login.css" rel="stylesheet">
<?php $user_id=$this->session->userdata("user_id");?>


<?php if(empty($user_id)): ?>

<?php $data_nickname = array('name'=> 'nickname','id'=> 'nickname','type'=>'nickname','placeholder'=>'Nickname','class'=>'form-control','value'=> $this->input->post("nickname"),'autofocus'=>'autofocus'); ?>
<?php $data_password = array('name'=> 'password','id'=> 'password','type'=>'password','placeholder'=>'Contraseña','class'=>'form-control','value'=> $this->input->post("password") ); ?>

<?php $attributes = array('role' => 'form','class'=>'Form5',"name"=>'Form5'); ?>

<div class="col-sm-9 col-md-8 col-lg-8">
	<div class="container well" id="sha">
		<div class="row">
					<div class="col-xs-12">
						<img src="<?php echo base_url(); ?>css/_resources/images/interface/avatar.png" class="img-responsive" id="avatar">
					</div>
		</div>
		<?php echo form_open(base_url().'login/in/',$attributes);?>   
				<div class="form-group">

                    <?php echo form_input($data_nickname); ?>

				</div>
				<div class="form-group">

                    <?php echo form_password($data_password); ?>

				</div>

				<div class="btn btn-lg btn-primary btn-block submit">iniciar sesión</div>

		<?php echo form_close(); ?>
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

$("form.Form5").keypress(function(e) {
    if(e.which == 13){
    $(this).blur();
    $(this).find("div.submit").focus().click();
    }

});

});

$(document).on("click","form.Form5 > div.submit",function(){

    var url="<?php echo base_url(); ?>",
        form="form.Form5",
        containerDiv=$(this).parent().parent(),
        nickname=$(this).parent().find("input#nickname").val(),
        password=$(this).parent().find("input#password").val(),
        registred_by="<?php echo $this->session->userdata('user_id'); ?>"
        ;

    $.ajax({
        url: url+'login/in',
        type: 'POST',
        dataType: 'json',
        data: {nickname: nickname,password:password,registred_by:registred_by},
        beforeSend: function(response){
// ajax
            $("input").prop("disabled",true);
            $("button").prop("disabled",true);
            $("div#ajax_loading").addClass("ajax_loading");
// ...              
        },
        success: function(response){
// ajax
            $("input").prop("disabled",false);
            $("button").prop("disabled",false);
            $("div#ajax_loading").removeClass("ajax_loading");
// ...
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
            $(form).get(0).reset();

            <?php if(!empty($_GET["redirect"])): ?>
            window.location.href="<?php echo decode_url($_GET['redirect']); ?>";
            <?php else: ?>
                <?php if(!empty($redirect)): ?>
            window.location.href="<?php echo decode_url($redirect); ?>";
                <?php else: ?>
            window.location.href="<?php echo base_url(); ?>";
                 <?php endif; ?>
            <?php endif; ?>

            },10);

            }else{


            $(form).find('input').prop("disabled",""); 
            $(form).find('div.submit').show(); 
            $(containerDiv).find('img.loading').remove();

            // validar nickname 
            if(response.nickname==1 || response.thereNickName==1){
            $(form).find('input#nickname').focus();
            $(form).find('input#nickname').parent().addClass("borderRequired");

            if(!$(form).find('input#nickname').parent().find("div.alert-danger").get(0))
            $(form).find('input#nickname').parent().append(alert_danger("",response.msg));
            
            return;
            }
            else { 
            $(form).find('input#nickname').parent().removeClass("borderRequired");
            $(form).find('input#nickname').parent().find("div.alert-danger").remove();
            }

            // validar password 
            if(response.password==1 || response.passwordBad==1){
            $(form).find('input#password').focus();
            $(form).find('input#password').parent().addClass("borderRequired");

            if(!$(form).find('input#password').parent().find("div.alert-danger").get(0))
            $(form).find('input#password').parent().append(alert_danger("",response.msg));
            
            return;
            }
            else { 
            $(form).find('input#password').parent().removeClass("borderRequired");
            $(form).find('input#password').parent().find("div.alert-danger").remove();
            }

            }  //fin else de errores

            return;

        }
    });
});
</script>

<?php endif; ?>