<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class World extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model("world_model");

	}
	public function country_tokeninput(){

		if( isset($_GET["request"]["name"]) ){

		$name =strip_tags( $this->security->xss_clean( $_GET["request"]["name"] )?:"" );
		$var_name="\$this->db->like('name', \"".$name."\");";
		$data=$this->world_model->get_country_token($var_name);

		return print_r( json_encode($data));
		}
	}

	public function state_tokeninput(){

		if( isset($_GET["request"]["name"]) ){
			
		$name =strip_tags( $this->security->xss_clean( $_GET["request"]["name"] )?:"" );
		$var_name="\$this->db->like('name', \"".$name."\");";
		$data=$this->world_model->get_state_token($var_name);

		return print_r( json_encode($data));
		}
	}

	public function city_tokeninput(){

		if( isset($_GET["request"]["name"]) ){
			
		$name =strip_tags( $this->security->xss_clean( $_GET["request"]["name"] )?:"" );
		$var_name="\$this->db->like('name', \"".$name."\");";
		$data=$this->world_model->get_city_token($var_name);

		return print_r( json_encode($data));
		}
	}	

	public function town_tokeninput(){

		if( isset($_GET["request"]["name"]) ){
			
		$name =strip_tags( $this->security->xss_clean( $_GET["request"]["name"] )?:"" );
		$var_name="\$this->db->like('name', \"".$name."\");";
		$data=$this->world_model->get_town_token($var_name);

		// return print_r( json_encode(array($this->db->last_query() ) ) );
		return print_r( json_encode($data));
		}		
	}
}
