<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Provider extends CI_Controller {
	public  $config;
	public  $page;
	public 	$registred_by;
	public 	$idTemp;
	public 	$sys;

	// provider
	public 	$uri_provider;
    // ...

	public function __construct() {

	parent::__construct();

	$this->load->model("admin/provider/provider_model");

	$this->load->model("vars_system_model");
	$this->load->library("pagination");
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

		// Provedores <provider>
		$this->uri_provider="admin/provider/";
		// ... </provider>
	}
	
	// insertar o actualizar
	public function do_it($id=null,$method=null) {
	$http_params=array_merge($_GET,$_POST);

	$http_params   =array(
	"MODE"         =>"do_it",
	"id"           =>(!empty($http_params["id"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["id"]) ) ) :""),
	"name"         =>(!empty($http_params["name"]) ?strip_tags( $this->security->xss_clean($http_params["name"]) ):""),
	"rfc"          =>(!empty($http_params["rfc"]) ?strip_tags( $this->security->xss_clean( strtoupper($http_params["rfc"]) ) ):""),
	"comment"     =>(!empty($http_params["comment"]) ?strip_tags( $this->security->xss_clean($http_params["comment"]) ):""),

	// provider subsidiary
	"fk_provider"       =>(!empty($http_params["fk_provider"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["fk_provider"]) ) ) :""),
	"country"         =>(!empty($http_params["country"]) ?strip_tags( $this->security->xss_clean($http_params["country"]) ):""),
	"state"           =>(!empty($http_params["state"]) ?strip_tags( $this->security->xss_clean($http_params["state"]) ):""),
	"town"            =>(!empty($http_params["town"]) ?strip_tags( $this->security->xss_clean($http_params["town"]) ):""),
	"city"            =>(!empty($http_params["city"]) ?strip_tags( $this->security->xss_clean($http_params["city"]) ):""),
	"colony"          =>(!empty($http_params["colony"]) ?strip_tags( $this->security->xss_clean($http_params["colony"]) ):""),
	"street"          =>(!empty($http_params["street"]) ?strip_tags( $this->security->xss_clean($http_params["street"]) ):""),
	"inside_number"   =>(!empty($http_params["inside_number"]) ?strip_tags( $this->security->xss_clean($http_params["inside_number"]) ):""),
	"outside_number"  =>(!empty($http_params["outside_number"]) ?strip_tags( $this->security->xss_clean($http_params["outside_number"]) ):""),
	"zip_code"        =>(!empty($http_params["zip_code"]) ?strip_tags( $this->security->xss_clean($http_params["zip_code"]) ):""),
	"reference"       =>(!empty($http_params["reference"]) ?strip_tags( $this->security->xss_clean($http_params["reference"]) ):""),
	"working_hours"   =>(!empty($http_params["working_hours"]) ?strip_tags( $this->security->xss_clean($http_params["working_hours"]) ):""),
	"reception_days"  =>(!empty($http_params["reception_days"]) ?strip_tags( $this->security->xss_clean($http_params["reception_days"]) ):""),
	"reception_hours" =>(!empty($http_params["reception_hours"]) ?strip_tags( $this->security->xss_clean($http_params["reception_hours"]) ):""),
	"website"         =>(!empty($http_params["website"]) ?strip_tags( $this->security->xss_clean($http_params["website"]) ):""),
	"email"           =>(!empty($http_params["email"]) ?strip_tags( $this->security->xss_clean($http_params["email"]) ):""),
	"phone"           =>(!empty($http_params["phone"]) ?strip_tags( $this->security->xss_clean($http_params["phone"]) ):""),
	"contact"         =>(!empty($http_params["contact"]) ?strip_tags( $this->security->xss_clean($http_params["contact"]) ):""),
	"paydays"         =>(!empty($http_params["paydays"]) ?strip_tags( $this->security->xss_clean($http_params["paydays"]) ):""),
	"payhours"        =>(!empty($http_params["payhours"]) ?strip_tags( $this->security->xss_clean($http_params["payhours"]) ):""),
	
	"type_of_currency" =>(!empty($http_params["type_of_currency"]) ?strip_tags( $this->security->xss_clean($http_params["type_of_currency"]) ):""),
	"exchange_rate"    =>(!empty($http_params["exchange_rate"]) ?strip_tags( $this->security->xss_clean($http_params["exchange_rate"]) ):""),
	);


		extract($http_params);

		if(!empty($id)):
		$data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);
		else:
		$data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now);
		endif;

	// providerView
		if($method=="providerView"){
	
		// Aqui almacenamos en el arreglo el cual vamos a insertar o actualizar
			$data=array(
			"name"=>$name,
			"rfc"=>$rfc
			);		

		// validar que  no exista un registro identico 
		if($this->provider_model->record_same_provider($data,$id) )
		return array("status"=>0,"msg"=>"Ya existe un registro identico ","data"=>false);

		$data=array_merge($data_depend,$data);


		if($id)
		$last_id=$this->provider_model->update_provider($data,$id);
		else
		$last_id=$this->provider_model->insert_provider($data);

		return array("status"=>1,"msg"=>"Exito","data"=>$last_id);

		}
		// </providerSubsidiaryView>

		if($method=="providerSubsidiaryView"){

		// Aqui almacenamos en el arreglo el cual vamos a insertar o actualizar
			$data             =array(
			"fk_provider"       =>$fk_provider,
			"name"            =>$name,
			"country"         =>$country,
			"state"           =>$state,
			"town"            =>$town,
			"city"            =>$city,
			"colony"          =>$colony,
			"street"          =>$street,
			"inside_number"   =>$inside_number,
			"outside_number"  =>$outside_number,
			"zip_code"        =>$zip_code,
			"reference"       =>$reference,
			"working_hours"   =>$working_hours,
			"reception_days"  =>$reception_days,
			"reception_hours" =>$reception_hours,
			"website"         =>$website,
			"email"           =>$email,
			"phone"           =>$phone,
			"contact"         =>$contact,
			"paydays"         =>$paydays,
			"payhours"        =>$payhours,
			);

		// validar que  no exista un registro identico 
		if($this->provider_model->record_same_providerSubsidiary($data,$id,$fk_provider) )
		return array("status"=>0,"msg"=>"Ya existe un registro identico ","data"=>false);

		$data=array_merge($data_depend,$data);

				if($id)
				$last_id=$this->provider_model->update_providerSubsidiary($data,$id);
				else
				$last_id=$this->provider_model->insert_providerSubsidiary($data);

		return array("status"=>1,"msg"=>"Exito","data"=>$last_id);

		}
		
		// </providerSubsidiaryView>
	// </> providerView

		return $last_id;
	}

	public function required_fields($module,$http_params){

		if(!empty($http_params["type_of_currency"]) and $http_params["type_of_currency"]!=1 and empty($http_params["exchange_rate"]))
		return array("status"=>0,"msg"=>"Debe de especificar el tipo de cambio","exchange_rate"=>1);
		
		return array("status"=>1,"msg"=>"Todos los campos ok");

	}

	public function config_pagination($method,$amount,$per_page) {

	$config["base_url"] = base_url() .$method;
	// $config["base_url"] = base_url() .$method;
	$config['total_rows'] = $amount;
	$config["per_page"] = $per_page;
	// pr(str_replace("/", "_", $method));

	/* This Application Must Be Used With BootStrap 3 * esto es bootstrap 3 */
	$config['full_tag_open'] = "<ul class='pagination pagination-small pagination-centered ".str_replace("/", "_", $method)."' data-paginations_div='1'>";
	$config['full_tag_close'] ="</ul>";
	$config['num_tag_open'] = '<li>';
	$config['num_tag_close'] = '</li>';
	$config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='javascript:void(0)' class='not-active'>";
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

// <provider>  
	// este metodo es para el token input 
	public function provider_tokeninput(){

	if( isset($_GET["request"]["name"]) ){
	$name =strip_tags( $this->security->xss_clean( $_GET["request"]["name"] )?:"" );
	$var_name="\$this->db->like('name', \"".$name."\");";
	$data=$this->provider_model->get_providers_token_search($var_name);
	}

	return print_r( json_encode($data));

	}

	// carga ajax
	public function provider_ajax() {

	$module=$this->uri_provider;

	$http_params=array_merge($_GET,$_POST);
	$http_params          =array(
	"input_search_provider" =>(!empty($http_params["input_search_provider"]) ? strip_tags( $this->security->xss_clean( $http_params["input_search_provider"] ) ) :""),
	"show_amount_provider"  =>(!empty($http_params["show_amount_provider"]) ? strip_tags( $this->security->xss_clean( $http_params["show_amount_provider"])  ) :10),
	);
	extract($http_params);

	$page_amount=$show_amount_provider;

	$query_search=array(
		"\$this->db->like('name', \"".$input_search_provider."\");",
		);

	$this->pagination->initialize($this->config_pagination("admin/provider/provider_ajax",$this->provider_model->get_provider_amount($query_search),$page_amount) );

	$data=array(
		"module"=>$module,
		"sys"=>$this->sys,
		"input_search_provider"=>$input_search_provider,
		"show_amount"=>$show_amount_provider,
		"records_array"=>$this->provider_model->get_provider($page_amount, $this->page,$query_search),
		"pagination"=>$this->pagination->create_links(),
		"module_data"=>$this->provider_model->m_name($module),
		);
	$data["module_data"]["module_data_method_do_it"]="admin/provider/providerView/";

    $data["modules_quick"]=$this->load->get_back_access($module);
	
	$html=$this->load->view("admin/provider/ajax/table-provider-view",$data,true);
	$data["html"]=$html;

	// $this->session->set_userdata('record_start_row_provider',$this->page);

	if(!empty($_GET["ajax"]))
	echo $data["html"];
	else
	return $data;

	}

	// carga normal
	public function index() {
		
	$data=$this->provider_ajax();

	$this->session->set_userdata("idTemp");
	$this->session->set_userdata('input_search_provider');

	// if(!empty($this->page) and !empty($data["records_array"]))
	// $this->session->set_userdata('record_start_row_provider',$this->page);

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view('admin/provider/provider_view',$data,true) ))) ;
	else
	return $this->load->template("admin/provider/provider_view",$data);

	}

	// ver para registro
	public function providerView($id=null) {

	$id_affected='';

	$module=$this->uri_provider;
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
	$return=$this->do_it($id,"providerView");

		if(!$return["status"])
		return print_r(json_encode($return));		
		else
		$id=$return["data"];

	$this->session->set_userdata("idTemp",$id);

	endif;

	// traer el registro 
	$array['data']=$this->provider_model->get_provider_id($id);

	if(!empty($MODE) and $MODE=="view")
	$array['data']['MODE']="do_it";
	else if (!empty($id))
	$array['data']['MODE']="view";
	else
	$array['data']['MODE']="do_it";

	// <F5>
	$sessionMode=$this->session->userdata("sessionMode_provider");
	if(!empty($_POST)):
		$this->session->set_userdata("sessionMode_provider",$array['data']['MODE']);
	endif;
	$array['id']=$id;
	// </F5>

	// <OWN>
	$this->load->model("world_model");
	$array['countries']         =$this->world_model->get_w_country();
	$array['states']            =$this->world_model->get_w_state();
	$array['towns']             =$this->world_model->get_w_town();
	$array['data']['fk_provider'] =$id;

	// para las sucursales
	$array['data']['countries'] =$array['countries'];
	$array['data']['states'] =$array['states'];
	$array['data']['towns'] =$array['towns'];
	// </OWN>

	// nombre del modulo
	$array['data']["module_data"]=$this->provider_model->m_name($module);	
	$array['data']["module_data_method_do_it"]="admin/provider/providerView/";

		if(!empty($_POST["MODE"])){

	    $this->load->model("vars_system_model");
		$array["data"]["sys"]=$this->vars_system_model->_vars_system();

    	$array["data"]["modules_quick"]=$this->load->get_back_access($module);

		$html=$this->load->view($module."dinamyc-inputs",$array["data"],true);
		return print_r(json_encode( array("status"=>1, "html"=>$html,"id"=>$id) ));	

		}

	$this->load->template($module.'dinamyc-view',$array);

	}

	public function provider_delete() {

	$module=$this->uri_provider;

    // <RightCheck> 
        $return_valid=rights_validation($module."delete","ajax");

        if(!$return_valid["status"])
        return print_r(json_encode($return_valid));       
    // </RightCheck>

	if(!empty($_POST["id"]))
    $id=decode_id( strip_tags( $this->security->xss_clean($_POST["id"]) ) );

	if($this->provider_model->provider_delete_it($id))
	return print_r(json_encode( array("status"=>1,"msg"=>"Se elimino","data"=>false ) ));

	}
//  <./>  provider

// <provider> 
	public function children() {


	$module="admin/provider/";
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
// </provider> 
// AJAX <providerSubsidiary> 

	public function provider_subsidiary_info() {

		if(!empty($_POST["id"])){
		$id= strip_tags( $this->security->xss_clean($_POST["id"]) );

		if($data=$this->provider_model->get_providerSubsidiary_id($id))
		return print_r(json_encode( array("status"=>1,"msg"=>"Informacion de sucursal","data"=>$data )));
		}
	}

	public function providerSubsidiary_delete() {

	if(!empty($_POST["id"]))
    $id=decode_id( strip_tags( $this->security->xss_clean($_POST["id"]) ) );

	if($this->provider_model->providerSubsidiary_delete_it($id))
	return print_r(json_encode( array("status"=>1,"msg"=>"Se elimino","data"=>false ) ));

	}

	public function providerSubsidiaryAjaxDoIt(){

		$idRecordRelation=(!empty($_POST["idRecordRelation"]) ? decode_id( strip_tags( $this->security->xss_clean($_POST["idRecordRelation"]) ) ):"");
		
		$providerSubsidiaryArray=(!empty($_POST["providerSubsidiaryArray"]) ? $this->security->xss_clean($_POST["providerSubsidiaryArray"]):"");
        
        // dproviderSubsidiaryView
		
		if($_POST AND !empty($providerSubsidiaryArray) ):
		
			foreach ($providerSubsidiaryArray as $key => $row) {

				$id_providerSubsidiary=(!empty($row["id"]) ?decode_id(strip_tags( $this->security->xss_clean($row["id"]) ) ) :"");

				// Aqui almacenamos en el arreglo el cual vamos a insertar o actualizar
				$data=array(
					"name"=>$row['name'],
					"fk_provider"=>$idRecordRelation,
					);

				// validar que  no exista un registro identico 
				if($this->provider_model->record_same_providerSubsidiary($data,$id_providerSubsidiary,$idRecordRelation) )
				return print_r(json_encode(array("status"=>0,"msg"=>"Ya existe un registro identico ","data"=>false))) ;

				if(!empty($id_providerSubsidiary)):
				$data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);
				else:
				$data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now);
				endif;

				$data=array_merge($data_depend,$data);

				if($id_providerSubsidiary)
				$last_id=$this->provider_model->update_providerSubsidiary($data,$id_providerSubsidiary);
				else
				$last_id=$this->provider_model->insert_providerSubsidiary($data);

			$providerSubsidiaryArrayToHtml[$last_id]["id"]=encode_id($last_id);
			$providerSubsidiaryArrayToHtml[$last_id]["name"]=$row['name'];
			$providerSubsidiaryArrayToHtml[$last_id]["fk_provider"]=encode_id($idRecordRelation);

			}
				return print_r(json_encode(array("status"=>1,"msg"=>"Exito","data_id"=>$last_id,"providerSubsidiaryArrayToHtml"=>$providerSubsidiaryArrayToHtml ) )) ;

		endif;

	}

	// ver para registro
	public function providerSubsidiaryView($id=null) {

	$module="admin/provider/providerSubsidiary/";
	$array["module"]=$module;

	$http_params=array_merge($_GET,$_POST);
	
	$http_params =array(
	"id"         => ( !empty($http_params["id"]) ?strip_tags( $this->security->xss_clean( decode_id( $http_params["id"]) ) ) : ""),
	"fk_provider"  => ( !empty($http_params["fk_provider"]) ?strip_tags( $this->security->xss_clean( decode_id( $http_params["fk_provider"]) ) ) : ""),
	"MODE"       => ( !empty($http_params["MODE"]) ?strip_tags( $this->security->xss_clean(  $http_params["MODE"] ) ) : ""),
	);
	extract($http_params);

	// if($this->idTemp and $MODE!="add")
	// $id=$this->idTemp;

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
		$return=$this->do_it($id,"providerSubsidiaryView");

			if(!$return["status"])
			return print_r(json_encode($return));		
			else
			$id=$return["data"];

	// $this->session->set_userdata("idTemp",$id);

	endif;

	// traer el registro 
	$array=$this->provider_model->get_providerSubsidiary_id($id);

	if(!empty($MODE) and $MODE=="view")
	$array['MODE']="do_it";
	else if (!empty($id))
	$array['MODE']="view";
	else
	$array['MODE']="do_it";

	// <F5>
	// $sessionMode=$this->session->userdata("sessionMode_provider");
	// if(!empty($_POST)):
	// 	$this->session->set_userdata("sessionMode_provider",$array['data']['MODE']);
	// endif;
	// $array['id']=$id;
	// </F5>

// <OWN>
		$this->load->model("world_model");
		$array['countries']=$this->world_model->get_w_country();
		$array['states']=$this->world_model->get_w_state();
		$array['towns']=$this->world_model->get_w_town();
// </OWN>

	// nombre del modulo
	$array["module_data"]=$this->provider_model->m_name($module);

	$html=$this->load->view($module."dinamyc-inputs",$array,true);

	return print_r(json_encode( array("status"=>1,"html"=>$html,"id"=>$id) ));	

	}	
}
