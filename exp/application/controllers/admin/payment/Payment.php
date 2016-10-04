<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

public $id;
public $http_params;
public $sys;
public $source_module;
public $now;
public $registred_by;

	// client
	public 	$uri_client;
	// ...

	public function __construct(){
		parent::__construct();

		$this->load->model("admin/payment/payment_model");
		$this->load->model("admin/client/client_model");
		$this->load->library("pagination");

		$this->load->model("vars_system_model");
		$this->sys=$this->vars_system_model->_vars_system();
		$this->now = date("Y-m-d H:i:s");
		$this->registred_by=$this->security->xss_clean($this->session->userdata("user_id"));


			$segment=(int)$this->uri->segment(2);

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
				  
		// $this->load->model('functions_model');
		$this->http_params=array_merge($_GET,$_POST);
		// $this->load->helper('xml/xml_helper');


		$this->http_params   =array(
		"id"=>(!empty($this->http_params["id"]) ? strip_tags( $this->security->xss_clean( decode_id($this->http_params["id"]) ) ) :""),
		"source_module"=>(!empty($this->http_params["source_module"]) ? strip_tags( $this->security->xss_clean( decode_id($this->http_params["source_module"]) ) ) :""),
		"module"=>(!empty($this->http_params["module"]) ? strip_tags( $this->security->xss_clean( decode_id($this->http_params["module"]) ) ) :""),
		"id_record"=>(!empty($this->http_params["id_record"]) ? strip_tags( $this->security->xss_clean( decode_id($this->http_params["id_record"]) ) ) :""),
		"pay_import_sum"=>(!empty($this->http_params["pay_import_sum"]) ? strip_tags( $this->security->xss_clean($this->http_params["pay_import_sum"] ) ) :""),
		);
	
		// clientes <payment>
		$this->uri_payment="admin/payment/";
		// ... </payment>

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
	public function get_information($http_params){
	$data["data"]=array();
	$this->load->model("admin/sale/sale_model");
	$this->load->model("admin/sale/remission/remission_model");
	$this->load->model("admin/sale/openingBalance/openingBalance_model");
	$this->load->model("admin/purchase/purchase_model");
	$this->load->model("admin/provider/provider_model");
	$this->load->model("config/config_model");

	$data["module_data"]=$this->sale_model->m_name($this->uri->uri_string."/");

    if($http_params["source_module"]=="admin/sale/payment/")
	 	$data["data"]=$this->sale_model->get_sale_id($http_params["id"]);

    if($http_params["source_module"]=="admin/sale/remission/payment/")
		$data["data"]=$this->remission_model->get_remission_id($http_params["id"]);

    if($http_params["source_module"]=="admin/purchase/payment/")
	 	$data["data"]=$this->purchase_model->get_purchase_id($http_params["id"]);

    if($http_params["source_module"]=="admin/sale/openingBalance/payment/")
	 	$data["data"]=$this->openingBalance_model->get_openingBalance_id($http_params["id"]);

    if(!empty($data['data']["client"]) ):
	 $data["data"]=array_merge(
	 	array(
	 		"client_data"=>$this->client_model->get_client_id($data['data']["client"],null),
	 		"client_subsidiaries"=>$this->client_model->get_client_subsidiaries($data['data']["client"],true),
	 		),
	 	$data["data"]);
	 endif;

    if(!empty($data['data']["provider"]) ):
	 $data["data"]=array_merge(
	 	array(
	 		"provider_data"=>$this->provider_model->get_provider_id($data['data']["provider"],null),
	 		"provider_subsidiaries"=>$this->provider_model->get_provider_subsidiaries($data['data']["provider"],true),
	 		),
	 	$data["data"]);
	 endif;

   	$data["data"]=array_merge(
	 	array(
	 		"subsidiaries"=>$this->config_model->get_id_subsidiary(),
	 		"type_of_currency_array"=>$this->config_model->get_type_of_currency(),
	 		),
	 	$data["data"]);

		return $data;
	}
	
	public function paymentView() {
	}

	public function admin() {

	$data=array(
		"id"=>$this->http_params["id"],
		"module"=>$this->http_params["source_module"],
		"data"=>$this->get_information($this->http_params)["data"],
		"module_data"=>$this->payment_model->m_name("admin/payment/"),
		);

	$data["data"]=array_merge(
					array("sys"=>$this->sys),
					array("module"=>$this->http_params["module"]),
					array("modules_quick"=>$this->load->get_back_access($this->http_params["source_module"])),
					$data["data"]
					);
	$this->load->template('admin/payment/dinamyc-view',$data);

	}

	// functions payments
		// este metodo es para el token input 
	public function tokeninput(){

	if( isset($_GET["request"]["name"]) ){
	$name =strip_tags( $this->security->xss_clean( $_GET["request"]["name"] )?:"" );
	$var_name="\$this->db->like('name', \"".$name."\");";
	$data=$this->payment_model->get_articles_token_search($var_name);
	}

	return print_r( json_encode($data));

	}

	//Aqui solo pongo el ITEM para hacer el insert  HTML
	public function add_payment(){

	if( isset($_POST["MODE"]) )
	$MODE =strip_tags( $this->security->xss_clean( $_POST["MODE"] )?:"" );

	$data['edit']=false;
	if( isset($_POST["edit"]) )
	$data['edit']=strip_tags( $this->security->xss_clean( $_POST["edit"] )?:"" );

	if(!empty($MODE) and $MODE=="view")
	$data['MODE'] ="do_it";
	else if (!empty($id))
	$data['MODE'] ="view";
	else
	$data['MODE'] ="do_it";

	// Arreglo de los articulos 
	$this->load->model("config/config_model");

	$data=array_merge(
		array(
		"payment_details"=>array(0),
		"sys"=>$this->sys,
		"type_of_currency_array"=>$this->config_model->get_type_of_currency(),
		),
		$data
		); 

	$_html = $this->load->view('recycled/payment_details/payment_detail_inputs',$data,true);
	echo json_encode($_html);

	}	

//Aqui se inserta lo que viene en el item
	public function add_payment_do(){

	$module=$this->http_params["module"];
	$id_record=$this->http_params["id_record"];

	if( isset($_POST["MODE"]) )
	$MODE =strip_tags( $this->security->xss_clean( $_POST["MODE"] )?:"" );

	$data['edit']=false;
	if( isset($_POST["edit"]) )
	$data['edit']=strip_tags( $this->security->xss_clean( $_POST["edit"] )?:"" );

	if(!empty($MODE) and $MODE=="view")
	$data['MODE'] ="do_it";
	else if (!empty($MODE) and $MODE=="do_it")
	$data['MODE'] ="view";

	$this->load->model("config/config_model");

	// <validacion>
	if(!empty($id_record))
	$dad_data=$this->payment_model->select_dad_payments($module,$id_record);
	// </validacion>

	// procesar los detalles que aun no se insertar solo los insertaremos como text en el html
	if(!empty($_POST["payment_details"]) )		
	foreach ($_POST["payment_details"] as $k => $vdt) {

	if(empty($vdt["type_of_currency"]))
	continue;	

	// <RightCheck> 
    $module_do_it=(!empty($vdt["id"])?"update":"insert");
	$return_valid=rights_validation($this->http_params["source_module"].$module_do_it,"ajax");

	if(!$return_valid["status"])
	return print_r(json_encode($return_valid));       
    // </RightCheck>

	// validar inputs que no vengan vacios
	if(empty($vdt["import"]) )
	return print_r( json_encode( array("status"=>0,"msg"=>"Necesitas especificar el importe") ) );

	if(!empty($vdt["id"])):
	$id=(!empty($vdt["id"]) )?strip_tags( $this->security->xss_clean( base64_decode($vdt["id"]) ) ) :"";
	else:
	$id =0;
	endif;

	// pagos de el papa id_record menos el que estoy modificando
	$payments_made=$this->payment_model->get_payment_details_by_id($module,$id,$id_record);
	$pay_import_sum="";

	if(!empty($payments_made)):

		foreach ($payments_made as $key => $payment_row)
		$pay_import_sum+=$payment_row["import"];

	endif;
	// ... 

	$pay_import_sum+=$vdt["import"];

		if($pay_import_sum>$dad_data["import"] )
		return print_r( json_encode( array("status"=>0,"msg"=>"La suma de todos los pagos es mayor al importe") ) );

		if($vdt["import"]>$dad_data["import"])
		return print_r( json_encode( array("status"=>0,"msg"=>"Pago mayor al importe de factura") ) );
	

	$payment_details[$k]        =array(
	$module            =>$id_record,
	"id"               =>(!empty($id)?$id:null),
	"method"           =>isset($vdt["method"])?trim( strip_tags( $this->security->xss_clean($vdt["method"]) ) ) :"",
	"import"           =>isset($vdt["import"])?trim( strip_tags( $this->security->xss_clean($vdt["import"]) ) ) :"",
	"type_of_currency" =>isset($vdt["type_of_currency"])?trim( strip_tags( $this->security->xss_clean($vdt["type_of_currency"]) ) ) :"",
	"exchange_rate"    =>isset($vdt["exchange_rate"])?trim( strip_tags( $this->security->xss_clean($vdt["exchange_rate"]) ) ) :"",
	"comment"          =>isset($vdt["comment"])?trim( strip_tags( $this->security->xss_clean($vdt["comment"]) ) ) :"",
	"date"         =>!empty($vdt["date"])?trim( strip_tags( $this->security->xss_clean($vdt["date"]) ) ) :"",	
	);

		// revisar si existe el registro si no insertarlo
		if($this->payment_model->get_admin_payments($module,$id,$id_record)){
		
		$data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);

		$data_process=array_merge($data_depend,$payment_details[$k]);	
		
			if(!$this->payment_model->update_payments($module,$data_process,$id_record,$id))
			{ return array("status"=>0,"msg"=>"No se pudo actualizar el pago"); }

		}

		else{

		$data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now,$module =>$id_record);
		
		$data_process=array_merge($data_depend,$payment_details[$k]);	

			if(!$last_reg_id=$this->payment_model->insert_payments($module,$data_process) )
			{ return array("status"=>0,"msg"=>"No se pudo insertar el pago"); }
			else
			$payment_details=$this->payment_model->get_admin_payments($module,$last_reg_id,$id_record);

		}


	}

	$data=array_merge(
		array(
		"payment_details"=>(!empty($payment_details)?$payment_details:array()),
		"sys"=>$this->sys,
		"type_of_currency_array"=>$this->config_model->get_type_of_currency(),
		),
		$data
	); 

	// cambiar de estatus el dad si ya pago toda la 
	if(!empty($pay_import_sum)){
		if($pay_import_sum==$dad_data["import"]){
		if(!$this->payment_model->update_dad_payments($module,array("status"=>2),$id_record) )
		return print_r( json_encode( array("status"=>0,"msg"=>"No se pudo actualizar".$module) ) );
		}else{

		if(!$this->payment_model->update_dad_payments($module,array("status"=>1),$id_record) )
		return print_r( json_encode( array("status"=>0,"msg"=>"No se pudo actualizar".$module) ) );
		}
	}
	// ...

	$_html = $this->load->view('recycled/payment_details/payment_detail_inputs',$data,true);

	return print_r( json_encode( array("status"=>1,"msg"=>"Exito","html"=>$_html) ) );

	}	
	
	public function payment_details(){

	$id_record =(!empty($_POST["id_record"])?strip_tags( $this->security->xss_clean( decode_id($_POST["id_record"]) )) :"");
	$source_module =(!empty($_POST["source_module"])?strip_tags( $this->security->xss_clean( decode_id($_POST["source_module"]) ) ):"");

	// else
	$data['MODE']="view";

	$this->load->model("config/config_model");
	$data=array_merge(
		array(
		"payment_details"=>$this->payment_model->get_payment_details_by_id($source_module,null,$id_record),
		"sys"=>$this->sys,
		"type_of_currency_array"=>$this->config_model->get_type_of_currency(),
		),
		$data
	); 

	$_html = $this->load->view('recycled/payment_details/payment_detail_inputs',$data,true);

	return print_r( json_encode( array("status"=>1,"msg"=>"Html de articulos","html"=>$_html) ) );
	
	}
	public function get_payment_by(){
	
	$id =(!empty($_POST["id"])?trim( strip_tags( $this->security->xss_clean(base64_decode($_POST["id"])) ) ) :"");
	$source_module =(!empty($_POST["source_module"])?strip_tags( $this->security->xss_clean( decode_id($_POST["source_module"]) ) ):"");

	$details=$this->payment_model->get_admin_payments($source_module,$id,$this->http_params["id_record"]);
	
	foreach ($details as $key => $row) 
	$details[$key]=array_merge($details[$key],array("id"=>base64_encode($row["id"])));

	return print_r(json_encode($details));
	}

	public function delete_payment(){
	
	$id =(!empty($_POST["id"])?strip_tags( $this->security->xss_clean(base64_decode($_POST["id"])) ) :"");
	$id_record=$this->http_params["id_record"];
	$module=$this->http_params["module"];
    // <RightCheck> 
        $return_valid=rights_validation($this->http_params["source_module"]."delete","ajax");

        if(!$return_valid["status"])
        return print_r(json_encode($return_valid));       
    // </RightCheck>

	if(!empty($id_record))
	$dad_data=$this->payment_model->select_dad_payments($module,$id_record);

	// pagos de el papa id_record menos el que estoy modificando
	$payments_made=$this->payment_model->get_payment_details_by_id($module,$id,$id_record);
	$pay_import_sum="";

	if(!empty($payments_made)):

		foreach ($payments_made as $key => $payment_row)
		$pay_import_sum+=$payment_row["import"];

	endif;

	// cambiar de estatus el dad si ya pago toda la 
		if($pay_import_sum==$dad_data["import"]){
		if(!$this->payment_model->update_dad_payments($module,array("status"=>2),$id_record) )
		return print_r( json_encode( array("status"=>0,"msg"=>"No se pudo actualizar".$module) ) );
		}else{

		if(!$this->payment_model->update_dad_payments($module,array("status"=>1),$id_record) )
		return print_r( json_encode( array("status"=>0,"msg"=>"No se pudo actualizar".$module) ) );
		}
	// ... 

	if($this->payment_model->delete_payment_it($this->http_params["module"],$id,$this->http_params["id_record"]) )
	return print_r( json_encode( array("status"=>1,"msg"=>"Se elimino") ) );
	else
	return print_r( json_encode( array("status"=>0,"msg"=>"Hubo un error al eliminar el pago") ) );

	}
// ----------------------

	// carga ajax
	public function payment_ajax() {

	$module=$this->uri_payment;

	$http_params=array_merge($_GET,$_POST);
	$http_params          =array(
	"input_search_payment" =>(!empty($http_params["input_search_payment"]) ? strip_tags( $this->security->xss_clean( $http_params["input_search_payment"] ) ) :""),
	"show_amount_payment"  =>(!empty($http_params["show_amount_payment"]) ? strip_tags( $this->security->xss_clean( $http_params["show_amount_payment"])  ) :10),
	"source_module"  =>(!empty($http_params["source_module"]) ? strip_tags( $this->security->xss_clean( $http_params["source_module"])  ) :"admin/sale/"),
	);
	extract($http_params);

	$page_amount=$show_amount_payment;

	$query_search="";
	if(!empty($input_search_payment)){
	$query_search=array(
		"\$this->db->like('name', \"".$input_search_payment."\");",
		);
	}

	$this->pagination->initialize($this->config_pagination("admin/payment/payment_ajax",$this->payment_model->get_payments_amount($query_search,$source_module),$page_amount) );

	$data=array(
		"module"=>$module,
		"sys"=>$this->sys,
		"input_search_payment"=>$input_search_payment,
		"show_amount"=>$show_amount_payment,
		"records_array"=>$this->payment_model->get_payments($page_amount, $this->page,$query_search,$source_module),
		"pagination"=>$this->pagination->create_links(),
		"module_data"=>$this->payment_model->m_name($module),
		);
	$data["module_data"]["module_data_method_do_it"]="admin/payment/paymentView/";
    
    $data["modules_quick"]=$this->load->get_back_access($module);

	$html=$this->load->view("admin/payment/ajax/table-payment-view",$data,true);
	$data["html"]=$html;

	// $this->session->set_userdata('record_start_row_client',$this->page);

	if(!empty($_GET["ajax"]))
	echo $data["html"];
	else
	return $data;

	}
	// carga normal
	public function index() {
		
	$data=$this->payment_ajax();

	$this->session->set_userdata("idTemp");
	$this->session->set_userdata('input_search_payment');
	$this->session->set_userdata("sessionMode_payment");

	// if(!empty($this->page) and !empty($data["records_array"]))
	// $this->session->set_userdata('record_start_row_client',$this->page);

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view('admin/payment/payment_view',$data,true) ))) ;
	else
	return $this->load->template('admin/payment/payment_view',$data);

	}
}
?>