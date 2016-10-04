<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase extends CI_Controller {
	public  $config;
	public  $page;
	public 	$registred_by;
	public 	$idTemp;
	public 	$sys;

    // purchase
	public 	$uri_purchase;
    // ...

	public function __construct() {

	parent::__construct();

	$this->load->model("admin/provider/provider_model");
	$this->load->model("admin/purchase/purchase_model");

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

		// Compras <purchase>
		$this->uri_purchase="admin/purchase/";
		// </purchase>
	}
	
	// insertar o actualizar
	public function do_it($id=null,$method=null) {
	$http_params=array_merge($_GET,$_POST);

	$http_params   =array(
	"MODE"         =>"do_it",
	"id"           =>(!empty($http_params["id"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["id"]) ) ) :""),
	"name"         =>(!empty($http_params["name"]) ?strip_tags( $this->security->xss_clean($http_params["name"]) ):""),
	"capacity"     =>(!empty($http_params["capacity"]) ?strip_tags( $this->security->xss_clean($http_params["capacity"]) ):""),
	"storage_unit" =>(!empty($http_params["storage_unit"]) ?strip_tags( $this->security->xss_clean($http_params["storage_unit"]) ):""),
	"provider"      =>(!empty($http_params["provider"]) ?strip_tags( $this->security->xss_clean($http_params["provider"]) ):""),
	"provider_subsidiary"      =>(!empty($http_params["provider_subsidiary"]) ?strip_tags( $this->security->xss_clean($http_params["provider_subsidiary"]) ):""),
	"folio"       =>(!empty($http_params["folio"]) ?strip_tags( $this->security->xss_clean($http_params["folio"]) ):""),
	"status"      =>(!empty($http_params["status"]) ?strip_tags( $this->security->xss_clean($http_params["status"]) ):""),
	"comment"     =>(!empty($http_params["comment"]) ?strip_tags( $this->security->xss_clean($http_params["comment"]) ):""),
	"date"        =>(!empty($http_params["date"]) ?strip_tags( $this->security->xss_clean($http_params["date"]) ):""),
	"details"     =>(!empty($http_params["details"]) ?$http_params["details"]:array()),
	"subsidiary"  =>(!empty($http_params["subsidiary"]) ?strip_tags( $this->security->xss_clean($http_params["subsidiary"]) ):""),
	"method_of_payment"  =>(!empty($http_params["method_of_payment"]) ?strip_tags( $this->security->xss_clean($http_params["method_of_payment"]) ):""),
	"payment_condition"  =>(!empty($http_params["payment_condition"]) ?strip_tags( $this->security->xss_clean($http_params["payment_condition"]) ):""),

	"type_of_currency" =>(!empty($http_params["type_of_currency"]) ?strip_tags( $this->security->xss_clean($http_params["type_of_currency"]) ):""),
	"exchange_rate"    =>(!empty($http_params["exchange_rate"]) ?strip_tags( $this->security->xss_clean($http_params["exchange_rate"]) ):""),
	);


		extract($http_params);

		if(!empty($id)):
		$data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);
		else:
		$data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now);
		endif;

	// purchaseView
		if($method=="purchaseView"){ 

    	// no vacios
    		$response_required=$this->required_fields("purchase",$http_params);
    		if(!$response_required["status"])
			return $response_required;
    	// ...

		// Aqui almacenamos en el arreglo el cual vamos a insertar o actualizar
		$data                 =array(
		"name"                =>$name,
		"folio"               =>$folio,
		"provider"            =>$provider,
		"provider_subsidiary" =>$provider_subsidiary,
		"status"              =>$status,
		"comment"             =>$comment,
		"date"                =>$date,
		"subsidiary"          =>$subsidiary,
		"type_of_currency"    =>$type_of_currency,
		"exchange_rate"       =>$exchange_rate,
		);

		// validar que  no exista un registro identico 
		if($this->purchase_model->record_same_purchase($data,$id) )
		return array("status"=>0,"msg"=>"Ya existe un registro identico ","data"=>false);

		$data=array_merge($data_depend,$data);

		if($id)
		$last_id=$this->purchase_model->update_purchase($data,$id);
		else
		$last_id=$this->purchase_model->insert_purchase($data);

		// processar details
		$response_detail=$this->processDetail("purchase",$last_id);

		if(!$response_detail["status"])
		return $response_detail;
		// 

		return array("status"=>1,"msg"=>"Exito","data"=>$last_id);

		}

	// </> purchaseView

		return $last_id;
	}

	public function required_fields($module,$http_params){

		if($module=="purchase"){
		if(empty($http_params["provider"]))
		return array("status"=>0,"msg"=>"Selecciona un proveedor","provider"=>1);

		if(empty($http_params["provider_subsidiary"]))
		return array("status"=>0,"msg"=>"Selecciona una sucursal de proveedor","provider_subsidiary"=>1);
		}

		if(empty($http_params["subsidiary"]))
		return array("status"=>0,"msg"=>"Selecciona una sucursal","subsidiary"=>1);

		if(empty($http_params["folio"]))
		return array("status"=>0,"msg"=>"Especifica un folio","folio"=>1);

		if(empty($http_params["date"]))
		return array("status"=>0,"msg"=>"Especifica un fecha","date"=>1);

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

// <purchase> 

	// carga ajax
	public function purchase_ajax() {

	$module=$this->uri_purchase;

	$http_params=array_merge($_GET,$_POST);
	$http_params          =array(
	"input_search_purchase" =>(!empty($http_params["input_search_purchase"]) ? strip_tags( $this->security->xss_clean( $http_params["input_search_purchase"] ) ) :""),
	"show_amount_purchase"  =>(!empty($http_params["show_amount_purchase"]) ? strip_tags( $this->security->xss_clean( $http_params["show_amount_purchase"])  ) :10),
	);
	extract($http_params);

	$page_amount=$show_amount_purchase;

	$query_search=array(
		"\$this->db->like('folio', \"".$input_search_purchase."\");",
		);

	$this->pagination->initialize($this->config_pagination("admin/purchase/purchase_ajax",$this->purchase_model->get_purchase_amount($query_search),$page_amount) );

	$data=array(
		"module"=>$module,
		"sys"=>$this->sys,
		"input_search_purchase"=>$input_search_purchase,
		"show_amount"=>$show_amount_purchase,
		"records_array"=>$this->purchase_model->get_purchase($page_amount, $this->page,$query_search),
		"pagination"=>$this->pagination->create_links(),
		"module_data"=>$this->purchase_model->m_name($module),
		);
	$data["module_data"]["module_data_method_do_it"]="admin/purchase/purchaseView/";

    $data["modules_quick"]=$this->load->get_back_access($module);

	$html=$this->load->view("admin/purchase/ajax/table-purchase-view",$data,true);
	$data["html"]=$html;

	// $this->session->set_userdata('record_start_row_purchase',$this->page);

	if(!empty($_GET["ajax"]))
	echo $data["html"];
	else
	return $data;

	}

	// carga normal
	public function index() {
		
	$data=$this->purchase_ajax();

	$this->session->set_userdata("idTemp");
	$this->session->set_userdata('input_search_purchase');
	$this->session->set_userdata("sessionMode_purchase");

	// if(!empty($this->page) and !empty($data["records_array"]))
	// $this->session->set_userdata('record_start_row_purchase',$this->page);

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view("admin/purchase/purchase_view",$data,true) ))) ;
	else
	return $this->load->template("admin/purchase/purchase_view",$data);

	}

	public function purchaseView($id=null) {

	$id_affected='';

	$module=$this->uri_purchase;
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
	$return=$this->do_it($id,"purchaseView");

		if(!$return["status"])
		return print_r(json_encode($return));		
		else
		$id=$return["data"];

	$this->session->set_userdata("idTemp",$id);

	endif;

	// traer el registro 
	$array['data']=$this->purchase_model->get_purchase_id($id);

	if(!empty($MODE) and $MODE=="view")
	$array['data']['MODE']="do_it";
	else if (!empty($id))
	$array['data']['MODE']="view";
	else
	$array['data']['MODE']="do_it";

	// <F5>
	$sessionMode=$this->session->userdata("sessionMode_purchase");
	if(!empty($_POST)):
		$this->session->set_userdata("sessionMode_purchase",$array['data']['MODE']);
	endif;
	$array['id']=$id;
	// </F5>

	// nombre del modulo
	$array['data']["module_data"]=$this->purchase_model->m_name($module);	
	$array['data']["module_data_method_do_it"]="admin/purchase/purchaseView/";

	// <OWN>
	$this->load->model("config/config_model");
	$array['data']['subsidiaries']=$this->config_model->get_id_subsidiary();
    $array['data']["providers"]=$this->provider_model->get_providers();
    $array['data']["provider_subsidiaries"]=$this->provider_model->get_provider_subsidiaries($array['data']["provider"],true);
    $array['data']["type_of_currencies"]=$this->config_model->get_type_of_currency();
	// </OWN>
		if(!empty($_POST["MODE"])){
		$this->load->model("vars_system_model");
		$array["data"]["sys"]=$this->vars_system_model->_vars_system();

   	 	$array["data"]["modules_quick"]=$this->load->get_back_access($module);

		$html=$this->load->view($module."dinamyc-inputs",$array["data"],true);
		return print_r(json_encode( array("status"=>1, "html"=>$html,"id"=>$id,"MODE"=>$array['data']['MODE'],"MODE_POST"=>$MODE,"data_inform"=>$array['data']) ));	

		}

	$this->load->template($module.'dinamyc-view',$array);

	}

	public function purchase_delete() {

	$module=$this->uri_purchase;

    // <RightCheck> 
        $return_valid=rights_validation($module."delete","ajax");

        if(!$return_valid["status"])
        return print_r(json_encode($return_valid));       
    // </RightCheck>

	if(!empty($_POST["id"]))
    $id=strip_tags( $this->security->xss_clean( decode_id($_POST["id"]) ) );

	if($this->purchase_model->purchase_delete_it($id))
	return print_r(json_encode( array("status"=>1,"msg"=>"Se elimino","data"=>false ) ));

	}
//  <./>  purchase

	public function children() {


	$module="admin/purchase/";
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

// <detail>
	public function processDetail($source_module,$id_record){
	/* source_module: de que modulo viene 
   id_record: id que afectara el detalle */
		/*
	 	REVISAR antes de hacer el UPDATE o el INSERT checamos si tenemos existencias de los articulos 
		INVENTARIO
	 */
	$this->load->model("admin/stock/catalog/article/article_model");

	// if(!empty($_POST["details"]) ){

	// $tmp_stock=function($details){return $this->stock_movement_output_details_availability_check($details);};
	// $return=$tmp_stock($_POST["details"]);

	// // MENSAJE SI NO TIENES DISPONIBLES EN EL INVENTARIO
	// if(empty($return["status"])):
	// 	return print_r(
	// 	json_encode(array(
	// 		"status"=>false,
	// 		"msg"=>$return["msg"],
	// 		"data"=>$this->stock_movement_output_details_availability_check($_POST["details"]),
	// 		"modal"=>1
	// 	)));	
	// endif;
	// }
	// Insertar Detalle  o actualizar
$sub_total        ="";
$tax_ieps         ="";
$tax_iva          ="";
$tax_iva_retained ="";
$tax_isr          ="";
$total            ="";
	// if(empty($_POST["details"]) )
	// {return array("status"=>0,"msg"=>"Tiene que especificar almenos un articulo"); }

	if(!empty($_POST["details"]) )		
		foreach ($_POST["details"] as $k => $vdt) {

		if(empty($vdt["article"]))
		continue;	

		if(!empty($vdt["id"])):
		$id = strip_tags( $this->security->xss_clean(base64_decode($vdt["id"]))?:"");
		else:
		$id =0;
		endif;	

		$data               =array(
		$source_module      =>$id_record,
		"stockModification" =>isset($vdt["stockModification"])?trim(strip_tags( $this->security->xss_clean($vdt["stockModification"]) ) ):"",
		"quantity"          =>isset($vdt["quantity"])?trim(strip_tags( $this->security->xss_clean($vdt["quantity"]) ) ):"",
		"article"           =>isset($vdt["article"])?trim(strip_tags( $this->security->xss_clean($vdt["article"]) )) :"",
		"description"       =>isset($vdt["description"])?trim(strip_tags( $this->security->xss_clean($vdt["description"]) ) ):"",
		"price"             =>isset($vdt["price"])?trim(strip_tags( $this->security->xss_clean($vdt["price"]) ) ) :"",
		"totalSub"          =>isset($vdt["totalSub"])?trim(strip_tags( $this->security->xss_clean($vdt["totalSub"]) ) ):"",
		"taxIeps"           =>isset($vdt["taxIeps"])?trim(strip_tags( $this->security->xss_clean($vdt["taxIeps"]) ) ):"",
		"taxIva"            =>isset($vdt["taxIva"])?trim(strip_tags( $this->security->xss_clean($vdt["taxIva"]) ) ):"",
		"taxIvaRetained"    =>isset($vdt["taxIvaRetained"])?trim(strip_tags( $this->security->xss_clean($vdt["taxIvaRetained"]) ) ):"",
		"taxIsr"            =>isset($vdt["taxIsr"])?trim(strip_tags( $this->security->xss_clean($vdt["taxIsr"]) ) ):""
		);
	
	// procesar columnas de  la tabla padre
	$sub_total        +=$data["totalSub"];
	$tax_ieps         +=$data["taxIeps"];
	$tax_iva          +=$data["taxIva"];
	$tax_iva_retained +=$data["taxIvaRetained"];
	$tax_isr          +=$data["taxIsr"];
	// ...................

		// revisar si existe el registro si no insertarlo
		if($this->article_model->record_details_there($source_module,$id,$id_record)){
		
		$data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);

		$data=array_merge($data_depend,$data);	
		
			if(!$this->article_model->update_details($source_module,$data,$id_record,$id))
			{ return array("status"=>0,"msg"=>"No se pudo actualizar el articulo"); }

		}

		else{

		$data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now,$source_module =>$id_record);
		
		$data=array_merge($data_depend,$data);	

			if(!$this->article_model->insert_details($source_module,$data) )
			{ return array("status"=>0,"msg"=>"No se pudo insertar el articulo"); }	

		}
		
		// INVENTARIO SI LLEGO HASTA AQUI ES PORQUE PASO LA VALIDACION DE LAS ENTRADAS
		// Grabar en un arreglo las cantidades de los articulos que vas a restar en la tabla de entrada
		// if(!empty($vdt["stockModification"]) ){
		// $articles_to_subtract[$vdt["article"]]["article"]=$vdt["article"];
		// $articles_to_subtract[$vdt["article"]]["quantity"][]=$vdt["quantity"];
		// }

		}

		// <actualizarPapa>
		if(!empty($sub_total)){
			$total=(($sub_total+$tax_ieps+$tax_iva)-$tax_iva_retained)-$tax_isr;
			$data_update=array(
				"sub_total"        =>number_format($sub_total,2,".",""),
				"tax_ieps"         =>number_format($tax_ieps,2,".",""),
				"tax_iva"          =>number_format($tax_iva,2,".",""),
				"tax_iva_retained" =>number_format($tax_iva_retained,2,".",""),
				"tax_isr"          =>number_format($tax_isr,2,".",""),
				"import"          =>number_format($total,2,".",""),
			);

			if(!$this->article_model->update_dad_details($source_module,$data_update,$id_record) )
			{ return array("status"=>0,"msg"=>"No se pudo actualizar los importes del papa"); }	
		}
		// </actualizarPapa>

	// Borrar los articulos que  el elimino en el update
	if(!empty($id))
	$this->article_model->delete_details($source_module,$id_record,$this->now);
	// ...............................................................................

	// INVENTARIO
	// if(!empty($articles_to_subtract) ):

	// $tmp_stock=function($id_record,$timestamp,$articles_to_subtract){return $this->stock_movement_output_details_processing($id_record,$timestamp,$articles_to_subtract);};
	// $return=$tmp_stock($id_record,$this->now,$articles_to_subtract);

	// if(empty($return["status"]))
	// return print_r( json_encode( array("status"=>0,"msg"=>$return["msg"],"data"=>false,"modal"=>1) ));

	// endif;

	return array("status"=>1,"msg"=>"Se processo correctamente los detalles","data"=>false);

	}
	
	// </details>

}
