<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Series extends CI_Controller {

	public  $config;
	public  $page;
	public 	$registred_by;
	public 	$idTemp;
	public 	$sys;

    // series
	public 	$uri_series;
    // ..

	public function __construct() {
	parent::__construct();		

	$this->load->model("config/invoice/series/series_model");
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

	// Folios <series>
	$this->uri_series="config/invoice/series/";
	// ... </series>

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

	// seriesView
		if($method=="seriesView"){
		// <OWN>
			/* procesar los rangos */
			if($since>$until)
			return array("status"=>0,"msg"=>"Rango desde debe ser menor a los otros 2 rangos","data"=>false);

			if($until<$since or $until<$current)
			return array("status"=>0,"msg"=>"Rango hasta debe ser mayor que rango desde o mayor igual a siguiente","data"=>false);
			
			if($current>$until)
			return array("status"=>0,"msg"=>"Folio siguiente debe ser menor o igual a rango hasta","data"=>false);

		// </OWN>
		
		// Aqui almacenamos en el arreglo el cual vamos a insertar o actualizar
			$data=array(
			"name"          =>$name,
			"subsidiary"    =>implode(",", $subsidiary),
			"document_type" =>implode(",", $document_type),			
			"serie"     =>$serie,
			"since"     =>$since,
			"until"     =>$until,
			"current"   =>$current,
			"shcp_file" =>$shcp_file,
			"pac"       =>$pac,
			"status"       =>$status,
			"date_expires" =>$date_expires,
			);

		// validar que  no exista un registro identico 
		if($this->series_model->record_same_series($data,$id) )
		return array("status"=>0,"msg"=>"Ya existe un registro identico ","data"=>false);

		$data=array_merge($data_depend,$data);

				if($id)
				$last_id=$this->series_model->update_series($data,$id);
				else
				$last_id=$this->series_model->insert_series($data);

		return array("status"=>1,"msg"=>"Exito","data"=>$last_id);

		}
	// </seriesView>		

	}	

	public function children() {
	$module="config/invoice/series/";
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

	// <series>
	// carga ajax
	public function series_ajax() {

	$module=$this->uri_series;

	$http_params=array_merge($_GET,$_POST);
	$http_params          =array(
	"input_search_series" =>(!empty($http_params["input_search_series"]) ? strip_tags( $this->security->xss_clean( $http_params["input_search_series"] ) ) :""),
	"show_amount_series"  =>(!empty($http_params["show_amount_series"]) ? strip_tags( $this->security->xss_clean( $http_params["show_amount_series"])  ) :10),
	);
	extract($http_params);

	$page_amount=$show_amount_series;

	$query_search=array(
		"\$this->db->like('name', \"".$input_search_series."\");",
		);

	$this->pagination->initialize($this->config_pagination("config/invoice/series/series_ajax",$this->series_model->get_series_amount($query_search),$page_amount) );

	$data=array(
		"module"=>$module,
		"sys"=>$this->sys,
		"input_search_series"=>$input_search_series,
		"show_amount"=>$show_amount_series,
		"records_array"=>$this->series_model->get_series($page_amount, $this->page,$query_search),
		"pagination"=>$this->pagination->create_links(),
		"module_data"=>$this->series_model->m_name($module),
		);
	$data["module_data"]["module_data_method_do_it"]="config/invoice/series/seriesView/";
    
    $data["modules_quick"]=$this->load->get_back_access($module);

	$html=$this->load->view("config/invoice/series/ajax/table-series-view",$data,true);
	$data["html"]=$html;

	// $this->session->set_userdata('record_start_row_series',$this->page);

	if(!empty($_GET["ajax"]))
	echo $data["html"];
	else
	return $data;

	}

	// carga normal
	public function index() {
		
	$data=$this->series_ajax();

	$this->session->set_userdata("idTemp");
	$this->session->set_userdata('input_search_series');
	$this->session->set_userdata("sessionMode_series");

	// if(!empty($this->page) and !empty($data["records_array"]))
	// $this->session->set_userdata('record_start_row_series',$this->page);

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view('config/invoice/series/series_view',$data,true) ))) ;
	else
	return $this->load->template('config/invoice/series/series_view',$data);

	}
	// ver para registro
	public function seriesView($id=null) {
	$this->load->model("config/config_model");	
	$id_affected='';
	$module=$this->uri_series;
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
	$return=$this->do_it($id,"seriesView");

		if(!$return["status"])
		return print_r(json_encode($return));		
		else
		$id=$return["data"];

		$this->session->set_userdata("idTemp",$id);

	endif;

	// traer el registro 
	$array['data']=$this->series_model->get_series_id($id);

	if(!empty($MODE) and $MODE=="view")
	$array['data']['MODE']="do_it";
	else if (!empty($id))
	$array['data']['MODE']="view";
	else
	$array['data']['MODE']="do_it";

	// <F5>
	$sessionMode=$this->session->userdata("sessionMode_series");
	if(!empty($_POST)):
		$this->session->set_userdata("sessionMode_series",$array['data']['MODE']);
	endif;
	$array['id']=$id;
	// </F5>
	// nombre del modulo
	$array['data']["module_data"]=$this->series_model->m_name($module);	
	$array['data']["module_data_method_do_it"]="config/invoice/series/seriesView/";

	// <OWN>
	$array['data']['subsidiaries']=$this->config_model->get_id_subsidiary();
	$array['data']['shcp_files']=$this->config_model->get_id_shcp_file();
	$array['data']['pacs']=$this->config_model->get_id_pac();
	// </OWN>

		if(!empty($MODE)){

    	$this->load->model("vars_system_model");
		$array["data"]["sys"]=$this->vars_system_model->_vars_system();

    	$array["data"]["modules_quick"]=$this->load->get_back_access($module);

		$html=$this->load->view($module."dinamyc-inputs",$array["data"],true);
		return print_r(json_encode( array("status"=>1,"html"=>$html,"id"=>$id) ));	

		}

	$this->load->template($module.'dinamyc-view',$array);

	}

	public function series_delete() {

	$module=$this->uri_series;

    // <RightCheck> 
        $return_valid=rights_validation($module."delete","ajax");

        if(!$return_valid["status"])
        return print_r(json_encode($return_valid));       
    // </RightCheck>

	if(!empty($_POST["id"]))
    $id=decode_id( strip_tags( $this->security->xss_clean($_POST["id"]) ) );

	if($this->series_model->series_delete_it($id))
	return print_r(json_encode( array("status"=>1,"msg"=>"Se elimino","data"=>false ) ));

	}
// </series>
}
