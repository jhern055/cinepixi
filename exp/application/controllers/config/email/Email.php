<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// require_once(dirname(__FILE__)."/Pdf.php");
class Email extends CI_Controller {

public $source_module;
public $data_source;
public $sys;
public $http_params;
	public  $config;
	public  $page;
	public 	$registred_by;
	public 	$now;
	public 	$idTemp;
	public 	$pagination;

    // email
	public 	$uri_email;
    // ..

	// cargamos las librerias a usar 
	public function __construct() {

		parent:: __construct();

		$this->load->helper('date');
		$this->load->library("email");
		$this->load->helper('email');
		$this->load->helper('pdf/pdf_helper');
		$this->load->model("config/email/email_model");

		$this->load->model("vars_system_model");
		$this->load->model("functions_model");
		$this->load->model("admin/sale/sale_model");

    $this->sys=$this->vars_system_model->_vars_system();
	$this->uri_email="config/email/";

	$this->load->model("vars_system_model");
	$segment=(int)$this->uri->segment(2);
	$this->sys=$this->vars_system_model->_vars_system();
	$this->now = date("Y-m-d H:i:s");
	$this->registred_by=$this->security->xss_clean($this->session->userdata("user_id"));
	$this->idTemp=$this->session->userdata("idTemp");
	
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

	$this->http_params=$_POST;
	$this->http_params      =array(
	"id"            =>(!empty($this->http_params["id"]) ?strip_tags( $this->security->xss_clean( decode_id($this->http_params["id"]) ) ) :""),
	"source_module" =>(!empty($this->http_params["source_module"]) ?strip_tags( $this->security->xss_clean( decode_id($this->http_params["source_module"]) ) ) :""),
	"email"         =>(!empty($this->http_params["email"]) ? strip_tags( $this->security->xss_clean( $this->http_params["email"] ) ) :""),
	"document_type" =>(!empty($this->http_params["document_type"]) ? (int) $this->security->xss_clean( $this->http_params["document_type"] )  :""),
	
	);

	extract($this->http_params);


	// $email="jhern055@gmail.com";
	$this->source_module=$source_module;

	if(!$this->data_record=$this->functions_model->get_table($source_module,$id))
	return array("status"=>0,"msg"=>"No se ha seleccionado el modulo en functions model");

	$this->data_source=source_emailConfig($source_module);

	$this->data_source["toEmail"]=$email;
	$this->data_source["subject"]=$this->data_source["subject"].(!empty($this->data_record["folio"])?" Folio:".$this->data_record["folio"]:"");
	
	if(!empty($this->data_record["sat_version"])):
    $_INVOICE_MODE_CONFIG=invoice_mode_config($source_module,$this->data_record["sat_version"]);

    $file_stamp=$_INVOICE_MODE_CONFIG["storage_stamp"].$this->data_record["id"]."-".$this->data_record["folio"].".xml";
    $file_cancel=$_INVOICE_MODE_CONFIG["storage_cancel"].$this->data_record["id"]."-".$this->data_record["folio"].".xml";

    if(file_exists($file_stamp)){
    	$this->data_source["attachments"][$this->data_record["status"]]["folio"]=$this->data_record["folio"].".xml";
    	$this->data_source["attachments"][$this->data_record["status"]]["file_stamp"]=(file_exists($file_stamp)?$file_stamp:"");
    	$this->data_source["attachments"][$this->data_record["status"]]["status"]=$this->data_record["status"];
	}
    
    if(file_exists($file_cancel)){
    	$this->data_source["attachments"][$this->data_record["status"]]["folio"]=$this->data_record["folio"]."-C.xml";
    	$this->data_source["attachments"][$this->data_record["status"]]["file_cancel"]=(file_exists($file_cancel)?$file_cancel:"");
    	$this->data_source["attachments"][$this->data_record["status"]]["status"]=$this->data_record["status"];
	}

	endif;
	
	// <pdf>
	$pdf=printGet($this->source_module,$this->data_record["id"],null,null);

	$dir="/tmp/";
	$filenamePath=$dir.$pdf["data"]["pdf"]["filename"];

	@$handler=fopen($filenamePath,"a");
	@fwrite($handler,$pdf["data"]["pdf"]["filecontent"]);

    $this->data_source["attachments"][500]["pdf_name"]=$pdf["data"]["pdf"]["filename"];
    $this->data_source["attachments"][500]["pdf_content"]=$pdf["data"]["pdf"]["filecontent"];
    $this->data_source["attachments"][500]["pdf_path"]=$filenamePath;

	// </pdf>


	// Sucursal <email>
	// ... </email>

	}

	// metodo de inicio 
	// public function index(){

	// 	redirect("home/");

	// }

	// traer el annuncio a ver por id
	public function send(){

	// $pdf=printGet("admin/sale/",2,null,null);
    // return print_r(json_encode($pdf) );
		
		// return print_r(json_encode($this->data_source));	
		// $fn_return=function($data_source){return $this->do_it_send($data_source); };

		// $return==$fn_return($this->data_source);

		// if(empty($return["status"]))
		// return print_r(json_encode($return));	
		$msg=$this->do_it_send($this->data_source);

		return print_r(json_encode(array("status"=>$msg["status"], "msg"=>$msg["msg"] ) ) );

	}

	/* esto es para enviar el HTML */
	public function do_it_send($data_source){

		// validar el email
		if(empty($data_source["toEmail"]))
		return array("status"=>0,"msg"=>"Los emails a mandar son necesarios");

		if(!empty($data_source["toEmail"])):

			$emailExplode=explode(",", $data_source["toEmail"]);
			foreach ($emailExplode as $key => $value):

				if(!valid_email($value))
				return array("status"=>0,"msg"=>"Un email que proporcionaste no es valido","emailToSendFrom"=>1,"emailToSendFromBad"=>1);

			endforeach;
				
		endif;
		
		if(!empty($data_source["from"]))
		$emailOwn=$this->security->xss_clean($data_source["from"]) ;
		else
		$emailOwn=$this->sys["enterprise_fiscal"]["email"];


		$config_email=$this->email_model->get_email_apply_to($this->http_params["document_type"]);

		if(empty($config_email["host"]) or empty($config_email["username"]) or empty($config_email["userpassword"]))
		return array("status"=>0,"msg"=>" faltan campos en la configuracion del email o no has seleccionado el servicio");

		$config       = array( 
		'protocol'    => 'smtp', 
		'smtp_host'   => $config_email["host"], 
		'smtp_port'   => 587, 
		'smtp_user'   => $config_email["username"],
		'smtp_pass'   => $config_email["userpassword"],
		'charset'     => 'utf-8',
		'mailtype'    => 'html',
		'smtp_crypto' => 'tls',
		); 

	$this->load->library('email');

	$this->email->initialize($config);
	$this->email->set_newline("\r\n");  

	$this->email->from($emailOwn, $this->sys["enterprise_fiscal"]["name"]);
	// $this->email->to("jhern055@gmail.com");
	$this->email->to($emailExplode);
	$this->email->subject($data_source["subject"]);
	$this->email->message($data_source["message"]);

	if(!empty($data_source["attachments"]))
	foreach ($data_source["attachments"] as $ĸ => $attachment):

		if(!empty($attachment["file_stamp"]))
		$this->email->attach($attachment["file_stamp"],null,$attachment["folio"]);
	
		if(!empty($attachment["file_cancel"]))
		$this->email->attach($attachment["file_cancel"],null,$attachment["folio"]);

		if(!empty($attachment["pdf_path"]))
		$this->email->attach($attachment["pdf_path"],null,$attachment["pdf_name"]);

	endforeach;

	if(!$this->email->send())
	return array("status"=>0,"msg"=>"No se envio");	

	// hacer el insert de que se envio 
	if(!$this->email_model->emails_sent($this->http_params["source_module"],$this->http_params["id"],$emailExplode))
	return array("status"=>0,"msg"=>"Hubo un error al querer actualizar la tabla de emails");

	if(!empty($data_source["attachments"]))
	foreach ($data_source["attachments"] as $ĸ => $attachment):

		if(!empty($attachment["pdf_path"])):
    	@unlink($attachment["pdf_path"]);
    	endif;

	endforeach;
	// sumar las veces que se ha enviado esta publicacion
	// if(!empty($emailToSendFrom_explode)){
	// $num_send=count($emailToSendFrom_explode);	
	// $num_send_before_update=$this->email_model->send_increment($id_publication,$num_send);

	// }



	return array("status"=>1,"msg"=>"Se envio correctamente");
	}

	public function get_emails_source_module(){

	$data=array();

	// traer los datos de los correos 
    $data["emails_sent"]=$this->email_model->get_dad_email($this->http_params["source_module"],$this->http_params["id"]);
	
	if(empty($data["emails_sent"]))
	return print_r(json_encode(array("status"=>0,"msg"=>"Hubo un error al reflejar la informacion de los correos","data"=>false)));
	
	$_html=$this->load->view("email/email_view_info",$data,true);

	return print_r(json_encode(array("status"=>1,"msg"=>"Los emails","data"=>$data,"html"=>$_html))) ;

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
	"host"         =>(!empty($http_params["host"]) ?strip_tags( $this->security->xss_clean($http_params["host"]) ):""),
	"port"         =>(!empty($http_params["port"]) ?strip_tags( $this->security->xss_clean($http_params["port"]) ):""),
	"username"     =>(!empty($http_params["username"]) ?strip_tags( $this->security->xss_clean($http_params["username"]) ):""),
	"userpassword" =>(!empty($http_params["userpassword"]) ?strip_tags( $this->security->xss_clean($http_params["userpassword"]) ):""),
	"comment"      =>(!empty($http_params["comment"]) ?strip_tags( $this->security->xss_clean($http_params["comment"]) ):""),
	"quantity"     =>(!empty($http_params["quantity"]) ?strip_tags( $this->security->xss_clean($http_params["quantity"]) ):""),
	"massive"      =>(!empty($http_params["massive"]) ?strip_tags( $this->security->xss_clean($http_params["massive"]) ):""),
	
	"ssl_enabled"  =>(!empty($http_params["ssl_enabled"]) ?1:0),
	"apply_to_tmp"=>(!empty($http_params["apply_to"]) ?$http_params["apply_to"]:""),
	);

	extract($http_params);

	// limpiar arreglos
	
	$apply_to=array();
	if(is_array($apply_to_tmp))
		foreach ($apply_to_tmp as $k => $v) $apply_to[$v]=strip_tags( $this->security->xss_clean($v) );
	// ...

		if(!empty($id)):
		$data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);
		else:
		$data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now);
		endif;
// return print_r(json_encode($http_params));
	// emailView
		if($method=="emailView"){
		
		// Aqui almacenamos en el arreglo el cual vamos a insertar o actualizar
			$data          =array(
			"name"         =>$name,
			"apply_to"     =>$apply_to_tmp,
			"apply_to" =>implode(",", $apply_to),			
			"host"         =>$host,
			"port"         =>$port,
			"username"     =>$username,
			"userpassword" =>$userpassword,
			"ssl_enabled"  =>$ssl_enabled,
			"comment"      =>$comment,
			"quantity"     =>$quantity,
			"massive"      =>$massive,
			);

		// validar que  no exista un registro identico 
		if($this->email_model->record_same_email($data,$id) )
		return array("status"=>0,"msg"=>"Ya existe un registro identico ","data"=>false);

		$data=array_merge($data_depend,$data);

				if($id)
				$last_id=$this->email_model->update_email($data,$id);
				else
				$last_id=$this->email_model->insert_email($data);

		return array("status"=>1,"msg"=>"Exito","data"=>$last_id);

		}
	// </emailView> 		
	}	

	public function children($module) {

	$module=(!empty($module)?$module:"config/email/");

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

	// <email>
	// carga ajax
	public function email_ajax() {

	$module=$this->uri_email;
	$this->load->library("pagination");

	$http_params=array_merge($_GET,$_POST);
	$http_params          =array(
	"input_search_email" =>(!empty($http_params["input_search_email"]) ? strip_tags( $this->security->xss_clean( $http_params["input_search_email"] ) ) :""),
	"show_amount_email"  =>(!empty($http_params["show_amount_email"]) ? strip_tags( $this->security->xss_clean( $http_params["show_amount_email"])  ) :10),
	);
	extract($http_params);

	$page_amount=$show_amount_email;

	$query_search=array(
		"\$this->db->like('name', \"".$input_search_email."\");",
		);

	$this->pagination->initialize($this->config_pagination("config/email/email_ajax",$this->email_model->get_email_amount($query_search),$page_amount) );

	$data=array(
		"module"=>$module,
		"sys"=>$this->sys,
		"input_search_email"=>$input_search_email,
		"show_amount"=>$show_amount_email,
		"records_array"=>$this->email_model->get_email($page_amount, $this->page,$query_search),
		"pagination"=>$this->pagination->create_links(),
		"module_data"=>$this->email_model->m_name($module),
		);
	$data["module_data"]["module_data_method_do_it"]="config/email/emailView/";

    $data["modules_quick"]=$this->load->get_back_access($module);

	$html=$this->load->view("config/email/ajax/table-email-view",$data,true);
	$data["html"]=$html;

	// $this->session->set_userdata('record_start_row_email',$this->page);

	if(!empty($_GET["ajax"]))
	echo $data["html"];
	else
	return $data;

	}

	// carga normal
	public function index() {
		
	$data=$this->email_ajax();
	$this->session->set_userdata("idTemp");
	$this->session->set_userdata('input_search_email');
	$this->session->set_userdata("sessionMode_email");

	// if(!empty($this->page) and !empty($data["records_array"]))
	// $this->session->set_userdata('record_start_row_email',$this->page);

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view('config/email/email_view',$data,true) ))) ;
	else
	return $this->load->template('config/email/email_view',$data);

	}
	// ver para registro
	public function emailView($id=null) {

	$id_affected='';
	$module=$this->uri_email;
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
	$return=$this->do_it($id,"emailView");

		if(!$return["status"])
		return print_r(json_encode($return));		
		else
		$id=$return["data"];

		$this->session->set_userdata("idTemp",$id);

	endif;

	// traer el registro 
	$array['data']=$this->email_model->get_email_id($id);

	if(!empty($MODE) and $MODE=="view")
	$array['data']['MODE']="do_it";
	else if (!empty($id))
	$array['data']['MODE']="view";
	else
	$array['data']['MODE']="do_it";

	// <F5>
	$sessionMode=$this->session->userdata("sessionMode_email");
	if(!empty($_POST)):
		$this->session->set_userdata("sessionMode_email",$array['data']['MODE']);
	endif;
	$array['id']=$id;
	// </F5>
	// nombre del modulo
	$array['data']["module_data"]=$this->email_model->m_name($module);	
	$array['data']["module_data_method_do_it"]="config/email/emailView/";

		if(!empty($MODE)){

    	$this->load->model("vars_system_model");
		$array["data"]["sys"]=$this->vars_system_model->_vars_system();

	    $array['data']["modules_quick"]=$this->load->get_back_access($module);

		$html=$this->load->view($module."dinamyc-inputs",$array["data"],true);
		return print_r(json_encode( array("status"=>1,"html"=>$html,"id"=>$id) ));	

		}

	$this->load->template($module.'dinamyc-view',$array);

	}
	public function email_delete() {

	$module=$this->uri_email;

    // <RightCheck> 
        $return_valid=rights_validation($module."delete","ajax");

        if(!$return_valid["status"])
        return print_r(json_encode($return_valid));       
    // </RightCheck>

	if(!empty($_POST["id"]))
    $id=decode_id( strip_tags( $this->security->xss_clean($_POST["id"]) ) );

	if($this->email_model->email_delete_it($id))
	return print_r(json_encode( array("status"=>1,"msg"=>"Se elimino","data"=>false ) ));

	}		
// </email>
}