<?php $data_nickname = array('name'=> 'nickname','id'=> 'nickname','type'=>'nickname','placeholder'=>'Nickname','class'=>'form-control','value'=> $this->input->post("nickname"),'autofocus'=>'autofocus'); ?>
<?php $data_email = array('name'=> 'email','id'=> 'email','type'=>'email','placeholder'=>'Correo electrónico','class'=>'form-control','value'=> $this->input->post("email")); ?>
<?php $data_password = array('name'=> 'password','id'=> 'password','type'=>'password','placeholder'=>'Establece tu contraseña','class'=>'form-control','value'=> $this->input->post("password") ); ?>
<?php $data_password_confirm = array('name'=> 'password_confirm','id'=> 'password_confirm','type'=>'password_confirm','placeholder'=>'Repita la contraseña','class'=>'form-control','value'=> $this->input->post("password_confirm") ); ?>
<?php $data_checkbox = array('name'=> 'remember','id'=> 'remember','type'=>'remember','value'=> $this->input->post("remember") ); ?>

<?php $attributes = array('role' => 'form','class'=>'Form6',"name"=>'Form6'); ?>

<div class="col-sm-9 col-md-8 col-lg-8">
	<div class="container well" id="sha">
		<div class="row">
					<div class="col-xs-12">
                    <p><h2>Abrir una cuenta</h2></p>
					</div>
		</div>
        <div class="mandatory-fields-notice ">
        Todos los campos marcados con <span> son obligatorios</span>
        </div>

        <?php echo form_open(base_url().'login/create_account/',$attributes);?>   
                <div class="form-group required">
                <label for="nickname" class="required">NickName</label>

                    <?php echo form_input($data_nickname); ?>

                </div>
                <div class="form-group">
                <label for="email">Email</label>

                    <?php echo form_input($data_email); ?>

                </div>
				<div class="form-group">
                <label for="password" class="required">Contraseña</label>

                    <?php echo form_password($data_password); ?>

				</div>
                <div class="form-group">
                <label for="password" class="required">Confirmar</label>

                    <?php echo form_password($data_password_confirm); ?>

                </div>

				<div class="btn btn-lg btn-success btn-block submit">Abrir una cuenta</div>
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

    $("form.Form6").keypress(function(e){

    if(e.which == 13){

    $(this).blur();
    $(this).parent().find("div.submit").focus().click();

    }

    });

});

$(document).on("click","form.Form6 > div.submit",function(){

    var url="<?php echo base_url(); ?>",
        form=$(this).parent(),
        containerDiv=$(this).parent().parent(),
        nickname=$(this).parent().find("input#nickname").val(),
        email=$(this).parent().find("input#email").val(),
        password=$(this).parent().find("input#password").val(),
        password_confirm=$(this).parent().find("input#password_confirm").val()
        ;

    // if(email==='' || password==='' || password_confirm===''){
    // $(this).parent().prepend(alert_danger("","faltan parametros requeridos"));
    // return;
    // }
    // else
    // $(this).parent().find('div.alert-danger').remove();

    // if(password != password_confirm)
    // $(this).parent().prepend(alert_danger("Contraseña","No son iguales"));
    // else
    // $(this).parent().find('div.alert-danger').remove();

    $.ajax({
        url: url+'login/create_account',
        type: 'POST',
        dataType: 'json',
        
        data: {nickname: nickname,email: email,password:password,password_confirm:password_confirm},
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

            $(form).append('<div class="alert alert-success">Tu registro fue exitoso</div>');
            $(form).find('div.form-group').removeClass('borderRequired');
            $(form).find('div.alert-danger').remove();
            $(form).removeClass('borderRequired');
            $(form).find("div.alert-warning").remove();

            setTimeout(function(){
                
            $(form).find('div.alert-success').remove();
            $(containerDiv).find('img.loading').remove();    
            $(form).find('input').prop("disabled",""); 
            $(form).find('div.submit').show(); 
            $(form).get(0).reset();
            window.location.href="<?php echo base_url(); ?>";

            },2000);


            }else{

            $(form).find('input').prop("disabled",""); 
            $(form).find('div.submit').show(); 
            $(containerDiv).find('img.loading').remove();

            if(response.nickname==1){
            $(form).find('input#nickname').focus();
            $(form).find('input#nickname').parent().addClass("borderRequired");
            $(form).find('input#nickname').tooltip();

            if(!$(form).find('input#nickname').parent().find("div.alert-danger").get(0))
            $(form).find('input#nickname').parent().append(alert_danger("",response.msg));

            return;
            }
            else { 
            $(form).find('input#nickname').parent().removeClass("borderRequired");
            $(form).find('input#nickname').parent().find("div.alert-danger").remove();
            }

            if(response.password_confirm==1){
            $(form).find('input#password_confirm').focus();
            $(form).find('input#password_confirm').parent().addClass("borderRequired");
            $(form).find('input#password_confirm').tooltip();

            if(!$(form).find('input#password_confirm').parent().find("div.alert-danger").get(0))
            $(form).find('input#password_confirm').parent().append(alert_danger("",response.msg));
            
            return;
            }
            else { 
            $(form).find('input#password_confirm').parent().removeClass("borderRequired");
            $(form).find('input#password_confirm').parent().find("div.alert-danger").remove();
            }

            if(response.password_not_equal==1){
            $(form).find('input#password').focus();
            $(form).find('input#password').parent().addClass("borderRequired");
            $(form).find('input#password_confirm').parent().addClass("borderRequired");

            if(!$(form).find('input#password_confirm').parent().find("div.alert-danger").get(0))
            $(form).find('input#password_confirm').parent().append(alert_danger("",response.msg));
            
            return;
            }
            else { 
            $(form).find('input#password_confirm').parent().removeClass("borderRequired");
            $(form).find('input#password_confirm').parent().find("div.alert-danger").remove();
            }
            
            if(response.password==1){
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

            if(response.email==1 || response.email_wrong==1){
            $(form).find('input#email').focus();
            $(form).find('input#email').parent().addClass("borderRequired");

            if(!$(form).find('input#email').parent().find("div.alert-danger").get(0))
            $(form).find('input#email').parent().append(alert_danger("",response.msg));
            
            return;
            }
            else { 
            $(form).find('input#email').parent().removeClass("borderRequired");
            $(form).find('input#email').parent().find("div.alert-danger").remove();
            }

            if(response.thereEmail==1 || response.thereNickName==1){

            $(form).focus();
            $(form).addClass("borderRequired");
            if(!$(form).find("div.alert-danger").get(0))
            $(form).append(alert_danger("",response.msg));
            $(form).append('<div class="alert alert-warning">Recuperar contraseña</div>');
            
            return;
            }
            else { 
            $(form).removeClass("borderRequired");
            $(form).find("div.alert-danger").remove();
            $(form).find("div.alert-warning").remove();
            }


            } //fin del else

            return;

        } //success }

    });
});
</script>