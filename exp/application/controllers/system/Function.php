<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Functionsystem extends CI_Controller {

	public function __construct() {

	parent::__construct();
	$this->load->model("web/menus/main/menuMain_model");

	}

	public function main_module(){

	if( isset($_GET["request"]["name"]) ){
	$name =strip_tags( $this->security->xss_clean( $_GET["request"]["name"] )?:"" );
	$var_name="\$this->db->like('name', \"".$name."\");";
	$data=$this->client_model->get_clients_token_search($var_name);
	
	}

	// return print_r( json_encode($data));

	}
}