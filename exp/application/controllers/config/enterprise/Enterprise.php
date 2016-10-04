<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enterprise extends CI_Controller {

	public  $config;
	public  $page;
	public 	$registred_by;
	public 	$idTemp;
	public 	$sys;

    // enterprise
	public 	$uri_enterprise;
    // ..

	public function __construct() {
	parent::__construct();		

	$this->load->model("config/enterprise/enterprise_model");
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

	// Empresa <enterprise>
	$this->uri_enterprise="config/enterprise/";
	// ... </enterprise>	
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

		if(!empty($id)):
		$data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);
		else:
		$data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now);
		endif;

// enterpriseView
		if($method=="enterpriseView"){
		
		// Aqui almacenamos en el arreglo el cual vamos a insertar o actualizar
			$data=array(
			"name"           =>$name,
			'rfc'            =>$rfc,
			'country'        =>$country,
			'state'          =>$state,
			'city'           =>$city,
			'town'           =>$town,
			'colony'         =>$colony,
			'street'         =>$street,
			'inside_number'  =>$inside_number,
			'outside_number' =>$outside_number,
			'zip_code'       =>$zip_code,
			'cedule'         =>$cedule,
			'logo'           =>$logo,
			'email'           =>$email,
			'phone'           =>$phone,
			'tax_regime'     =>$tax_regime,
			
			);

		$data=array_merge($data_depend,$data);

				$last_id=$this->enterprise_model->update_enterprise($data,$id);

		return array("status"=>1,"msg"=>"Exito","data"=>$last_id);

		}
	// </enterpriseView> 

	}	

	public function children() {

	$module=(!empty($module)?$module:"config/enterprise/");

	$data["module"]=$module;	

	$data["module_data"]=$this->load->module_text_from_id($module);
	$data["module_childrens"]=$this->load->get_module_childrens($data["module_data"]["id"]);

	if(empty($data["module_childrens"]))
	redirect($module);

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view('recycled/menu/Module_children',$data,true) ))) ;
	else
	return $this->load->template('recycled/menu/Module_children',$data);

	}	

	// <enterprise>
	// carga ajax
	public function enterprise_ajax() {

	$module=$this->uri_enterprise;

	$http_params=array_merge($_GET,$_POST);
	$http_params          =array(
	"input_search_enterprise" =>(!empty($http_params["input_search_enterprise"]) ? strip_tags( $this->security->xss_clean( $http_params["input_search_enterprise"] ) ) :""),
	"show_amount_enterprise"  =>(!empty($http_params["show_amount_enterprise"]) ? strip_tags( $this->security->xss_clean( $http_params["show_amount_enterprise"])  ) :10),
	);
	extract($http_params);

	$page_amount=$show_amount_enterprise;

	$query_search=array(
		"\$this->db->like('name', \"".$input_search_enterprise."\");",
		);

	$this->pagination->initialize($this->config_pagination("config/enterprise/enterprise_ajax",$this->enterprise_model->get_enterprise_amount($query_search),$page_amount) );

	$data=array(
		"module"=>$module,
		"sys"=>$this->sys,
		"input_search_enterprise"=>$input_search_enterprise,
		"show_amount"=>$show_amount_enterprise,
		"records_array"=>$this->enterprise_model->get_enterprise($page_amount, $this->page,$query_search),
		"pagination"=>$this->pagination->create_links(),
		"module_data"=>$this->enterprise_model->m_name($module),
		);
	$data["module_data"]["module_data_method_do_it"]="config/enterprise/enterpriseView/";

    $data["modules_quick"]=$this->load->get_back_access($module);

	$html=$this->load->view("config/enterprise/ajax/table-enterprise-view",$data,true);
	$data["html"]=$html;

	// $this->session->set_userdata('record_start_row_enterprise',$this->page);

	if(!empty($_GET["ajax"]))
	echo $data["html"];
	else
	return $data;

	}

	// carga normal
	public function index() {
		
	$data=$this->enterprise_ajax();

	$this->session->set_userdata("idTemp");
	$this->session->set_userdata('input_search_enterprise');
	$this->session->set_userdata("sessionMode_enterprise");

	// if(!empty($this->page) and !empty($data["records_array"]))
	// $this->session->set_userdata('record_start_row_enterprise',$this->page);

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view('config/enterprise/enterprise_view',$data,true) ))) ;
	else
	return $this->load->template('config/enterprise/enterprise_view',$data);

	}
	// ver para registro
	public function enterpriseView($id=null) {

	$id_affected='';
	$module=$this->uri_enterprise;
	$array["module"]=$module;

	if(!empty($_POST["id"]))
    $id=decode_id( strip_tags( $this->security->xss_clean($_POST["id"]) ) );

    $MODE=(!empty($_POST["MODE"])?strip_tags( $this->security->xss_clean($_POST["MODE"]) ) : "");

	if($this->idTemp and $MODE!="add")
	$id=$this->idTemp;

    // <RightCheck> 
    if(!empty($MODE)):

        $module_do_it=(!empty($id)?"update":"insert");
        $return_valid=rights_validation($array["module"].$module_do_it,"ajax");

        if(!$return_valid["status"])
        return print_r(json_encode($return_valid));       

    endif;
    // </RightCheck> 

	if(!empty($MODE) and $MODE=="do_it"):

	// retorna el id que se vio afectado asi sea UPDATE o INSERT o si existe un registro identico
	$return=$this->do_it($id,"enterpriseView");

		if(!$return["status"])
		return print_r(json_encode($return));		
		else
		$id=$return["data"];

		$this->session->set_userdata("idTemp",$id);

	endif;

	// traer el registro 
	$array['data']=$this->enterprise_model->get_enterprise_id($id);

	if(!empty($MODE) and $MODE=="view")
	$array['data']['MODE']="do_it";
	else if (empty($id))
	$array['data']['MODE']="view";
	else
	$array['data']['MODE']="do_it";

	// nombre del modulo
	$array['data']["module_data"]=$this->enterprise_model->m_name($module);	
	$array['data']["module_data_method_do_it"]="config/enterprise/enterpriseView/";

	// <own>
	$this->load->model("world_model");
	// $array['data']['cities']=$this->world_model->get_w_city();
	$array['data']['countries']=$this->world_model->get_w_country();
	$array['data']['states']=$this->world_model->get_w_state();
	$array['data']['towns']=$this->world_model->get_w_town();
	// </own>
		if(!empty($MODE)){

    	$this->load->model("vars_system_model");
		$array["data"]["sys"]=$this->vars_system_model->_vars_system();

	    $array['data']["modules_quick"]=$this->load->get_back_access($module);

		$html=$this->load->view($module."dinamyc-inputs",$array["data"],true);
		return print_r(json_encode( array("status"=>1,"html"=>$html,"id"=>$id) ));	

		}

	$this->load->template($module.'dinamyc-view',$array);

	}
	public function enterprise_delete() {

	$module=$this->uri_enterprise;

    // <RightCheck> 
        $return_valid=rights_validation($module."delete","ajax");

        if(!$return_valid["status"])
        return print_r(json_encode($return_valid));       
    // </RightCheck>

	if(!empty($_POST["id"]))
    $id=decode_id( strip_tags( $this->security->xss_clean($_POST["id"]) ) );

	if($this->enterprise_model->enterprise_delete_it($id))
	return print_r(json_encode( array("status"=>1,"msg"=>"Se elimino","data"=>false ) ));

	}		
// </enterprise>		
}
