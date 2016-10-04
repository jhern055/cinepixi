<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Controller {

	public  $config;
	public  $page;
	public 	$registred_by;
	public 	$idTemp;
	public 	$sys;

	public function __construct() {
	parent::__construct();		

	$this->load->model("config/invoice/invoice_model");
	$this->load->library("pagination");
	$this->load->model("vars_system_model");
	$segment=(int)$this->uri->segment(2);
	$this->sys=$this->vars_system_model->_vars_system();
	
	// esto es porque tuve conflicto con el segmento 3 cuando era  segmento/segmento/segmento/segmento/
	$x =3; 
	while (is_string($segment) or empty($segment) ){
		$segment=$this->uri->segment($x)?(int)$this->uri->segment($x):""; $x++;

		if($x>=20){
		$segment=0;	
		break;
		}
	}


	$this->page=( (!empty($segment))? $segment:0);

	$this->now = date("Y-m-d H:i:s");
	$this->registred_by=$this->security->xss_clean($this->session->userdata("user_id"));
	$this->idTemp=$this->session->userdata("idTemp");

    $uri_string=array_merge($_GET,$_POST);
    $uri_string=(!empty($uri_string["uri_string"])? $uri_string["uri_string"]:$this->uri->uri_string );

	}

	public function config_pagination($method,$amount,$per_page) {

	$config["base_url"] = base_url() .$method;
	$config['total_rows'] = $amount;
	$config["per_page"] = $per_page;
	// pr(str_replace("/", "_", $method));
	/* This Application Must Be Used With BootStrap 3 * esto es bootstrap 3 */
	$config['full_tag_open'] = "<ul class='pagination pagination-small pagination-centered ".str_replace("/", "_", $method)."' data-paginations_div='1'>";
	$config['full_tag_close'] ="</ul>";
	$config['num_tag_open'] = '<li>';
	$config['num_tag_close'] = '</li>';
	$config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#' class='not-active'>";
	$config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
	$config['next_tag_open'] = "<li>";
	$config['next_tag_close'] = "</li>";
	$config['prev_tag_open'] = "<li>";
	$config['prev_tag_close'] = " </li>";
	$config['first_tag_open'] = "<li>";
	$config['first_tag_close'] = "</li>";
	$config['last_tag_open'] = "<li>";
	$config['last_tag_close'] = "</li>";

	return $config;

	}
	// insertar o actualizar
	public function do_it($id=null,$method=null) {
	$http_params=$_POST;

	$http_params   =array(
	"MODE"         =>"do_it",
	"id"           =>(!empty($http_params["id"]) ?decode_id( strip_tags( $this->security->xss_clean($http_params["id"]) ) ) :""),
	"name"         =>(!empty($http_params["name"]) ?strip_tags( $this->security->xss_clean($http_params["name"]) ):""),
	"subsidiary_tmp"=>(!empty($http_params["subsidiary"]) ?$http_params["subsidiary"]:""),
	"document_type_tmp"=>(!empty($http_params["document_type"]) ?$http_params["document_type"]:""),
	"serie"    =>(!empty($http_params["serie"]) ?strip_tags( $this->security->xss_clean($http_params["serie"]) ):""),
	"since"    =>(!empty($http_params["since"]) ?strip_tags( $this->security->xss_clean($http_params["since"]) ):""),
	"until"    =>(!empty($http_params["until"]) ?strip_tags( $this->security->xss_clean($http_params["until"]) ):""),
	"current"  =>(!empty($http_params["current"]) ?strip_tags( $this->security->xss_clean($http_params["current"]) ):""),
	"user"     =>(!empty($http_params["user"]) ?strip_tags( $this->security->xss_clean($http_params["user"]) ):""),
	"password" =>(!empty($http_params["password"]) ?strip_tags( $this->security->xss_clean($http_params["password"]) ):""),
	"pac" =>(!empty($http_params["pac"]) ?strip_tags( $this->security->xss_clean($http_params["pac"]) ):""),
	"shcp_file" =>(!empty($http_params["shcp_file"]) ?strip_tags( $this->security->xss_clean($http_params["shcp_file"]) ):""),
	"status" =>(!empty($http_params["status"]) ?strip_tags( $this->security->xss_clean($http_params["status"]) ):""),
	"date_expires" =>(!empty($http_params["date_expires"]) ?strip_tags( $this->security->xss_clean($http_params["date_expires"]) ):""),
	"file_name_tmp"=>(!empty($http_params["files"]) ?$http_params["files"]:""),

	"rfc"            =>(!empty($http_params["rfc"]) ?strip_tags( $this->security->xss_clean($http_params["rfc"]) ):""),
	"country"        =>(!empty($http_params["country"]) ?strip_tags( $this->security->xss_clean($http_params["country"]) ):""),
	"state"          =>(!empty($http_params["state"]) ?strip_tags( $this->security->xss_clean($http_params["state"]) ):""),
	"city"           =>(!empty($http_params["city"]) ?strip_tags( $this->security->xss_clean($http_params["city"]) ):""),
	"town"           =>(!empty($http_params["town"]) ?strip_tags( $this->security->xss_clean($http_params["town"]) ):""),
	"colony"         =>(!empty($http_params["colony"]) ?strip_tags( $this->security->xss_clean($http_params["colony"]) ):""),
	"street"         =>(!empty($http_params["street"]) ?strip_tags( $this->security->xss_clean($http_params["street"]) ):""),
	"inside_number"  =>(!empty($http_params["inside_number"]) ?strip_tags( $this->security->xss_clean($http_params["inside_number"]) ):""),
	"outside_number" =>(!empty($http_params["outside_number"]) ?strip_tags( $this->security->xss_clean($http_params["outside_number"]) ):""),
	"zip_code"       =>(!empty($http_params["zip_code"]) ?strip_tags( $this->security->xss_clean($http_params["zip_code"]) ):""),
	"cedule"         =>(!empty($http_params["cedule"]) ?strip_tags( $this->security->xss_clean($http_params["cedule"]) ):""),
	"logo"           =>(!empty($http_params["logo"]) ?strip_tags( $this->security->xss_clean($http_params["logo"]) ):""),
	"email"          =>(!empty($http_params["email"]) ?strip_tags( $this->security->xss_clean($http_params["email"]) ):""),
	"phone"          =>(!empty($http_params["phone"]) ?strip_tags( $this->security->xss_clean($http_params["phone"]) ):""),
	"tax_regime"     =>(!empty($http_params["tax_regime"]) ?strip_tags( $this->security->xss_clean($http_params["tax_regime"]) ):""),

	);

	extract($http_params);

	// limpiar arreglos
	
	$document_type=array();
	if(is_array($document_type_tmp))
		foreach ($document_type_tmp as $k => $v) $document_type[$v]=strip_tags( $this->security->xss_clean($v) );

	$subsidiary=array(); 
	if(is_array($subsidiary_tmp))
		foreach ($subsidiary_tmp as $k => $v) $subsidiary[$v]=strip_tags( $this->security->xss_clean($v) );	

	$file_name=array(); 
	if(is_array($file_name_tmp))
		foreach ($file_name_tmp as $k => $v) $file_name[$v["file_name"]]=$this->security->xss_clean($v["file_name"]);	

	// </limpiar>

		if(!empty($id)):
		$data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);
		else:
		$data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now);
		endif;

	}	

// <invoice>
	public function index() {$this->children("config/invoice/"); }

	public function children($module) {

	if(empty($module))
	$module=$this->children("config/invoice/");

	$data["module"]=$module;	

	$data["module_data"]=$this->load->module_text_from_id($module);
	$data["module_childrens"]=$this->load->get_module_childrens($data["module_data"]["id"]);

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view('recycled/menu/Module_children',$data,true) ))) ;
	else
	return $this->load->template('recycled/menu/Module_children',$data);

	}
// </invoice>
}
