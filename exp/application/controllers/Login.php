<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	// cargamos las librerias a usar 
	public function __construct() {

		parent:: __construct();
		$this->load->library('bcrypt');
		$this->load->model('login/login_model');
		$this->load->helper('security');
		$this->load->helper('email');

	}
	// metodo de inicio 
	public function index(){

		$data=array();
		$this->load->template('login/login_view',$data);

	}

	public function out(){

		$user["user"]=array();
		$this->session->set_userdata("user","");
		$this->session->set_userdata("user_id","");
		$this->session->set_userdata("");
		redirect("login/");
	}

	public function in(){

		if(empty($_POST["nickname"]))
		$return=array("status"=>0,"msg"=>"No especificaste el nickname","nickname"=>1);

		if(empty($_POST["password"]))
		$return=array("status"=>0,"msg"=>"No especificaste el password","password"=>1);

		if(!empty($_POST["nickname"]))
		$pass_hash=$this->login_model->check_if_there_nickname( $this->security->xss_clean($_POST["nickname"]));
		
		if(empty($pass_hash))
		$return=array("status"=>0,"msg"=>"Este nickname no esta registrado si gustas puedes crear una cuenta","thereNickName"=>1);

		$password = $this->security->xss_clean($_POST["password"]);
		$nickname = $this->security->xss_clean($_POST["nickname"]);
		
		if(!empty($pass_hash)){
		if(!$this->bcrypt->check_password($password,$pass_hash[0]["pass_hash"]))
		$return=array("status"=>0,"msg"=>"Vuelve a introducir tu contraseña","passwordBad"=>1);
		}

		if(!empty($return) )
		return print_r(json_encode($return) );	
		else{

	
			$this->create_session($pass_hash[0]["id"]);

			return rt(true,"Bienvenido :)",false);

		}

	}

	public function create_account(){

		if($_POST){

			if(!$_POST["nickname"])
			$return=array("status"=>0,"msg"=>"Nick name es necesario","nickname"=>1);
			
			// if(!$_POST["email"])
			// $return=array("status"=>0,"msg"=>"No especificaste el correo","email"=>1);

			if(!empty($_POST["email"]) and !valid_email($_POST["email"]))
			$return=array("status"=>0,"msg"=>"El email no es correcto","email_wrong"=>1);

			if(!$_POST["password"])
			$return=array("status"=>0,"msg"=>"No especificaste el password","password"=>1);

			if(!$_POST["password_confirm"])
			$return=array("status"=>0,"msg"=>"la confirmacion es requerida","password_confirm"=>1);

			if($_POST["password"] != $_POST["password_confirm"])
			$return=array("status"=>0,"msg"=>"No son iguales","password_not_equal"=>1);

			if(!empty($_POST["email"]) and $this->login_model->check_if_there( $this->security->xss_clean($_POST["email"]) ) )
			$return=array("status"=>0,"msg"=>"Este correo ya esta registrado si gustas puedes usar","thereEmail"=>1);

			if(!empty($_POST["nickname"]) and $this->login_model->check_if_there_nickname( $this->security->xss_clean($_POST["nickname"]) ) )
			$return=array("status"=>0,"msg"=>"Este nickname ya esta registrado si gustas puedes usar","thereNickName"=>1);

			if(!empty($return) )
			return print_r(json_encode($return) );	
			else{

			$now          = date("Y-m-d H:i:s");
			if(!empty($_POST["email"])){

			$email    = $this->security->xss_clean($_POST["email"]);
			preg_match_all('/^[a-zA-Z0-9.]+@/', $email, $matches);
				
			}else
			$email="";

			$nickname = $this->security->xss_clean($_POST["nickname"]);
			$password = $this->security->xss_clean($_POST["password"]);
			if($password)
			$passwordHash = $this->bcrypt->hash_password($password); 


			$ip           = $this->input->ip_address();
			$registred_by = $this->security->xss_clean($this->session->userdata("user_id"))?:"";
			$registred_on = $now;

			$data=array(
			"nickname"     =>$nickname,
			"email"        =>$email,
			"pass_hash"    =>$passwordHash,
			"pass_php"     =>md5($passwordHash),
			"status"       =>1,
			"rights"       =>"",
			"ip"           =>$ip,
			"registred_by" =>$registred_by,
			"registred_on" =>$now
			);	


			if($last_id_insert=$this->login_model->create_account_model($data) ){

			// $nickname=$matches[0][0]?$matches[0][0]:$email;

			$this->create_session($last_id_insert);

			return rt(true,"Se creo tu cuenta exitosamente",false);
			
			}

			}
		} //POST }

		$data=array();
		$this->load->template('login/create_account_view',$data);
	}

	public function recovery(){

		if($_POST){

			if(!$data_user=$this->login_model->check_if_there_nickname($_POST["email_user"]))
			$return=array("status"=>0,"msg"=>"No existe este usuario","email_user"=>1,"data"=>$this->login_model->check_if_there_nickname($_POST["email_user"]));
			else{

			$data_us=$data_user[0];

			// error si no tiene email este usuario
			if(empty($data_us["email"]))
			$return=array("status"=>0,"msg"=>"Cuando creaste tu cuenta no especificaste un correo comunícate con el admin para mas ayuda","email_user"=>1);

			if(!empty($return))
			return print_r(json_encode($return) );

			// password temporal 
			$pass_tmp = do_hash(random_string('alnum', 32), 'md5');
   			$data=array("pass_tmp"=>$pass_tmp);
			$this->login_model->pass_tmp($data_us["id"],$data);
			$data_us["pass_tmp"]=$pass_tmp;
			// ...

			// send 
			$html=$this->load->view("login/recovery_view_html",$data_us,true);
			$return=send_it("recuperar contraseña",$html,$data_us["email"]);

			if(!$return["status"])
			return print_r(json_encode($return) );

			$return=array("status"=>1,"msg"=>"Te enviamos un correo","email_user"=>1,"data"=>$data_us);
			if( !empty($return) )
			return print_r(json_encode($return) );
				
			}

			if(empty($_POST["email_user"]))
			$return=array("status"=>0,"msg"=>"Necesitas especificar email o usuario","email_user"=>1);

			if( !empty($return) )
			return print_r(json_encode($return) );
		}

		$data=array();
		$this->load->template('login/recovery_view',$data);
	}

	public function change_passwrd(){

		$data=array();
		$data['javascript_1_11_0'] = "<script src=".base_url()."js/jquery-1.11.0.js></script>";
		$data['publications_categories'] = $this->home_model->get_categories();
		$data['main_content']='change_passwd_view';
		$this->load->template('login/change_passwd_view',$data);

	}

	public function reset_passwrd(){

		if($_POST):

		$return="";

		if(!empty($_POST["pass_tmp"]))
		$pass_tmp=$_POST["pass_tmp"];

		if(!empty($_POST["id_user"]))
		$id_user=decode_url($_POST["id_user"]);

// ////////////////////////////////////////////////

		if(!$_POST["password"])
		$return=array("status"=>0,"msg"=>"No especificaste el password","password"=>1);

		if(!empty($return))
		return print_r(json_encode($return) );
// ////////////////////////////////////////////////

		if(!$_POST["password_confirm"])
		$return=array("status"=>0,"msg"=>"la confirmacion es requerida","password_confirm"=>1);

		if(!empty($return))
		return print_r(json_encode($return) );

// ////////////////////////////////////////////////

		if($_POST["password"] != $_POST["password_confirm"])
		$return=array("status"=>0,"msg"=>"No son iguales","password_not_equal"=>1);

		if(!empty($return))
		return print_r(json_encode($return) );

// ////////////////////////////////////////////////
	
		$password = $this->security->xss_clean($_POST["password"]);
		
			if($password){
				$passwordHash = $this->bcrypt->hash_password($password); 

				$data=array(
					"pass_hash"  =>$passwordHash,
					"pass_php"   =>md5($passwordHash),
					"updated_by" =>$id_user,
					"updated_on" =>date("Y-m-d H:m:s")
					);
			}

		// actualizar el password
		if($this->login_model->change_passwrd($data,$id_user,$pass_tmp)!=true)
		$return=array("status"=>0,"msg"=>"No se pudo actualizar porque falto un dato","password"=>1);

		if(!empty($return))
		return print_r(json_encode($return) );
		// ...

		// cargar las variables del usuario  en sessiones
		$this->create_session($id_user);
		// ...

		// Eliminar el pass_tmp para que ya no pueda ser usado
		$data=array("pass_tmp"  =>'');
		if($this->login_model->clear_passwrd($data,$id_user)!=true)
		$return=array("status"=>0,"msg"=>"No se pudo limpiar el password temporal","password"=>1);

		if(!empty($return))
		return print_r(json_encode($return) );
		// ...

		return rt(true,"se cambio la contraseña con exito",false);
		
		endif;

	}

	public function create_session($id_user){

		$data_get=$this->login_model->get_user_by_id($id_user);
		
		$user["user"]=array(
			"nickname"=>$data_get["nickname"],
			"user_id"=>encode_id($data_get["id"]),
			"user_email"=>$data_get["email"],
			"rights"=>(!empty($data_get["rights"])?explode(",", $data_get["rights"]):"") ,
			);	
		$this->session->set_userdata($user);
		$this->session->set_userdata("user_id",$data_get["id"]);
	}
}