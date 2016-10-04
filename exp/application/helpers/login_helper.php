<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('rights_validation')){
    function rights_validation($module_right,$return=false){
   
    $CI =& get_instance();
    $CI->load->model("login/login_model");

    if($CI->session->userdata("user")):

    $user=$CI->security->xss_clean($CI->session->userdata("user"));
    $user=$CI->login_model->get_user_by_id( decode_id($user["user_id"]) );
    $user["rights"]=explode(",", $user["rights"]);
    
    endif;

    $status=false; // por default no tiene 
    $msg="No tienes acceso: ";
    $do_it="No tienes el permiso: ";
            // <script>alert('debe loguearse primero');window.location.href='".base_url()."login?redirect=".current_url()."'; </script>
    // no esta logueado
    if(empty($user)){

        $user["rights"]=array();

        $ret=array(
            "status"=>$status,
            "msg"=> $msg." debe loguearse primero",
            "redirect"=>"
            <script>alert('debe loguearse primero');window.location.href='".base_url()."login?redirect=".current_url()."'; </script>
            "
            );

        return $ret;
    }

    if(in_array($module_right, $user["rights"]))
    $status=true;

    // pr($user);

    // foreach ($user["rights"] as $key => $value) {
    // // echo $value."==".$module_right."</br>";
        
    // // pr($value."==".$module_right);

    // if($value==$module_right)
    // $status=true;

    // }

    switch ($return) {

        case $return=="javascript":
        $ret=array("status"=>$status,"msg"=>$msg,"redirect"=>"<script>alert('No tienes acceso a este modulo:{".$module_right."}');window.location.href='".base_url()."';</script>");
        break;
        
        case $return=="ajax":
        $ret=array("status"=>$status,"msg"=>$do_it."{ ".$module_right." }","redirect"=>"");
        break;

        default:
        $ret =array("status"=>$status,"msg"=>"default switch case ","redirect"=>"");
        break;
    }
        return $ret;
    }

}

            // <script type='text/javascript' src='".base_url()."js/jquery-1.11.1.js'></script>
            // <script type='text/javascript'>
            //     $(document).ready(function(){
            //             $('#dialog > p').text('');
            //             $('#dialog > p').text('debe loguearse primero');
            //             $('#dialog > p').dialog({
            //                 resizable: false,
            //                 modal: true,
            //                     buttons: {
            //                         Aceptar: function() {

            //                             $('#dialog').append('<p></p>');
            //                             $(this).dialog( 'close' );
            //                             window.location.href='".base_url()."login?redirect=".current_url()."';
            //                         }
            //                     }
            //             });
            //     $('html').remove();
            //    });

            // </script>
// /////////////////////////////////
 ?>