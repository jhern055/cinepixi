<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Article extends CI_Controller {

	public  $config;
	public  $page;
	public 	$registred_by;
	public 	$idTemp;
	public 	$sys;

	// article
	public 	$uri_article;
    // ...

	public $module="";
	public $http_params="";
	
	// cargamos las librerias a usar 
	public function __construct() {

		parent:: __construct();

	$this->load->model("admin/stock/catalog/article/article_model");
// ------------------------

	$this->load->model("vars_system_model");
	$this->load->library("pagination");
	$segment=(int)$this->uri->segment(2);
	$this->sys=$this->vars_system_model->_vars_system();
	$this->registred_by=$this->security->xss_clean($this->session->userdata("user_id"));
	$this->now = date("Y-m-d H:i:s");
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



    $uri_string=array_merge($_GET,$_POST);
    $uri_string=(!empty($uri_string["uri_string"])? $uri_string["uri_string"]:$this->uri->uri_string );

		// Articulos <article>
		$this->uri_article="admin/stock/catalog/article/";
		// ... </article>	

		$this->http_params=array_merge($_GET,$_POST);
		$this->http_params   =array(
		"id"=>(!empty($this->http_params["id"]) ? strip_tags( $this->security->xss_clean( decode_id($this->http_params["id"]) ) ) :""),
		"source_module"=>(!empty($this->http_params["source_module"]) ? strip_tags( $this->security->xss_clean( decode_id($this->http_params["source_module"]) ) ) :""),
		"module"=>(!empty($this->http_params["module"]) ? strip_tags( $this->security->xss_clean( decode_id($this->http_params["module"]) ) ) :""),
		"id_record"=>(!empty($this->http_params["id_record"]) ? strip_tags( $this->security->xss_clean( decode_id($this->http_params["id_record"]) ) ) :""),
		"DAD_MODE"=>(!empty($this->http_params["DAD_MODE"]) ? strip_tags( $this->security->xss_clean( $this->http_params["DAD_MODE"] ) ) :"view"),
		);
	}
	public function children($module=null) {
	$module=(!empty($module)?$module:"admin/stock/catalog/article/");
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
	// ------------------------------------------------------------------------------------------------------
// insertar o actualizar
	public function do_it($id=null,$method=null) {
	$http_params=array_merge($_GET,$_POST);

	$http_params   =array(

	"MODE"  =>"do_it",
	"id"    =>(!empty($http_params["id"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["id"]) ) ) :""),
	"name"  =>(!empty($http_params["name"]) ?strip_tags( $this->security->xss_clean($http_params["name"]) ):""),
	"model" =>(!empty($http_params["model"]) ?strip_tags( $this->security->xss_clean($http_params["model"]) ):""),
	"sku"   =>(!empty($http_params["sku"]) ?strip_tags( $this->security->xss_clean($http_params["sku"]) ):""),
	"price" =>(!empty($http_params["price"]) ?strip_tags( $this->security->xss_clean($http_params["price"]) ):""),
	
	"article_category_Tmp" =>(!empty($http_params["article_category"]) ?$http_params["article_category"]:""),
	"file_name_tmp"        =>(!empty($http_params["file_name"]) ?$http_params["file_name"]:""),
	
	);
	
	extract($http_params);

	// CLEAN LIST 
	$file_name=array(); 
	if(is_array($file_name_tmp))
		foreach ($file_name_tmp as $k => $v) $file_name[$v["file_name"]]=$this->security->xss_clean($v["file_name"]);	

	$article_category=array();
	if(is_array($article_category_Tmp))
		foreach ($article_category_Tmp as $k => $v) $article_category[$v]=strip_tags( $this->security->xss_clean($v) );

		if(!empty($id)):
		$data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);
		else:
		$data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now);
		endif;

	// articleView
		if($method=="articleView"){

		// Aqui almacenamos en el arreglo el cual vamos a insertar o actualizar
		$data=array(
			"name"  =>$name,
			"model" =>$model,
			"sku"   =>$sku,
			"price" =>$price,
			);

		// validar que  no exista un registro identico 
		if($this->article_model->record_same_article($data,$id) )
		return array("status"=>0,"msg"=>"Ya existe un registro identico ","data"=>false);

		$data=array_merge($data_depend,$data);


		if($id)
		$last_id=$this->article_model->update_article($data,$id);
		else{
		$last_id=$this->article_model->insert_article($data);

			// <own>
			if(!empty($file_name)):
			// mover y insertar en el registro
				foreach ($file_name as $k => $file):

						$tmp_file=pathinfo(decode_id($file));
						$tmp_move[$k]["basename"]=$tmp_file["basename"];
						
						$data_tmp=array(
						"name"=>$tmp_file["basename"],
						"article_id"=>$last_id
						);

						$this->article_model->insert_article_image($data_tmp,$last_id);

				endforeach;
				    // <mover>

				$this->load->helper("file");

					foreach ($tmp_move as $tmp_mv_name) {

						$response=move_file("article_image",$tmp_mv_name["basename"],$last_id);
						if(!empty($response["status"]))
						return $response;

					}
			// </mover>
			endif;
			// </own>	
					
		}

		// <article_category>

			// Agregar la relacion de categoria
			$this->load->model("admin/stock/catalog/category/category_model");

			$database_article_category=$this->category_model->get_article_category($last_id);
			
			// ELMINAR
			if(!empty($database_article_category)){
				foreach ($database_article_category as $k => $row):

					// para eliminar los que ya tengo pero qe no vienen en el arreglo
					if(!in_array($row["category_id"], $article_category)){
					// return array("status"=>0,"msg"=>var_export($row["category_id"],true),"data"=>false);
					if(!$this->category_model->delete_article_category($database_article_category[$row["category_id"]]) )
					return array("status"=>0,"msg"=>"No se elimino la relacion de la categoria","data"=>false);

					}
				endforeach;
			}

				// {
				// add the last_id
				if(!empty($article_category)){
				foreach ($article_category as $k => $category_id):

						// variable para ir a insert
						$data_art_cat=array(
							"article_id"=>$last_id,
							"category_id"=>$category_id,
						);

						// si no existe lo inserta
						if(!$this->category_model->is_there_article_category($data_art_cat))
						$this->category_model->insert_article_category($data_art_cat);
							

				endforeach;

				}
		// </article_category>

		return array("status"=>1,"msg"=>"Exito","data"=>$last_id);

		}

	// </> articleView

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

// <article> 

	// carga ajax
	public function article_ajax() {

	$module=$this->uri_article;

	$http_params=array_merge($_GET,$_POST);
	$http_params          =array(
	"input_search_article" =>(!empty($http_params["input_search_article"]) ? strip_tags( $this->security->xss_clean( $http_params["input_search_article"] ) ) :""),
	"show_amount_article"  =>(!empty($http_params["show_amount_article"]) ? strip_tags( $this->security->xss_clean( $http_params["show_amount_article"])  ) :10),
	);
	extract($http_params);

	$page_amount=$show_amount_article;

	$query_search=array(
		"\$this->db->like('name', \"".$input_search_article."\");",
		);

	$this->pagination->initialize($this->config_pagination("admin/stock/catalog/article/article_ajax",$this->article_model->get_article_amount($query_search),$page_amount) );

	$data=array(
		"module"=>$module,
		"sys"=>$this->sys,
		"input_search_article"=>$input_search_article,
		"show_amount"=>$show_amount_article,
		"records_array"=>$this->article_model->get_article($page_amount, $this->page,$query_search),
		"pagination"=>$this->pagination->create_links(),
		"module_data"=>$this->article_model->m_name($module),
		);
	$data["module_data"]["module_data_method_do_it"]="admin/stock/catalog/article/articleView/";

    $data["modules_quick"]=$this->load->get_back_access($module);

	$html=$this->load->view("admin/stock/catalog/article/ajax/table-article-view",$data,true);
	$data["html"]=$html;

	// $this->session->set_userdata('record_start_row_article',$this->page);

	if(!empty($_GET["ajax"]))
	echo $data["html"];
	else
	return $data;

	}

	// carga normal
	public function index() {
		
	$data=$this->article_ajax();

	$this->session->set_userdata("idTemp");
	$this->session->set_userdata('input_search_article');
	$this->session->set_userdata("sessionMode_article");

	// if(!empty($this->page) and !empty($data["records_array"]))
	// $this->session->set_userdata('record_start_row_article',$this->page);

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view("admin/stock/catalog/article/article_view",$data,true) ))) ;
	else
	return $this->load->template("admin/stock/catalog/article/article_view",$data);

	}

	// ver para registro
	public function articleView($id=null) {

	$id_affected='';

	$module=$this->uri_article;
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
	$return=$this->do_it($id,"articleView");

		if(!$return["status"])
		return print_r(json_encode($return));		
		else
		$id=$return["data"];

	$this->session->set_userdata("idTemp",$id);

	endif;

	// traer el registro 
	$array['data']=$this->article_model->get_article_id($id);

	if(!empty($MODE) and $MODE=="view")
	$array['data']['MODE']="do_it";
	else if (!empty($id))
	$array['data']['MODE']="view";
	else
	$array['data']['MODE']="do_it";

	// <F5>
	$sessionMode=$this->session->userdata("sessionMode_article");
	if(!empty($_POST)):
		$this->session->set_userdata("sessionMode_article",$array['data']['MODE']);
	endif;
	$array['id']=$id;
	// </F5>

	// nombre del modulo
	$array['data']["module_data"]=$this->article_model->m_name($module);	
	$array['data']["module_data_method_do_it"]="admin/stock/catalog/article/articleView/";

		if(!empty($MODE)){

	    $this->load->model("vars_system_model");
		$array["data"]["sys"]=$this->vars_system_model->_vars_system();

    	$array["data"]["modules_quick"]=$this->load->get_back_access($module);

		$html=$this->load->view($module."dinamyc-inputs",$array["data"],true);
		return print_r(json_encode( array("status"=>1, "html"=>$html,"id"=>$id) ));	

		}

	$this->load->template($module.'dinamyc-view',$array);

	}


	public function article_delete() {
	
	$module=$this->uri_article;

    // <RightCheck> 
        $return_valid=rights_validation($module."delete","ajax");

        if(!$return_valid["status"])
        return print_r(json_encode($return_valid));       
    // </RightCheck>

	if(!empty($_POST["id"]))
    $id=decode_id( strip_tags( $this->security->xss_clean($_POST["id"]) ) );

	if($this->article_model->article_delete_it($id))
	return print_r(json_encode( array("status"=>1,"msg"=>"Se elimino","data"=>false ) ));

	}

	// -------------------------------------------------------------------------------------------------------
	// este metodo es para el token input 
	public function tokeninput(){

	if( isset($_GET["request"]["name"]) ){
	$name =strip_tags( $this->security->xss_clean( $_GET["request"]["name"] )?:"" );
	$var_name="\$this->db->like('name', \"".$name."\");";
	$data=$this->article_model->get_articles_token_search($var_name);
	}

	return print_r( json_encode($data));

	}

	//Aqui solo pongo el ITEM para hacer el insert  HTML
	public function add_detail(){

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
	$this->load->model("admin/stock/catalog/article/article_model");
	$data['articles'] = $this->article_model->get_articles_to_option(true);

	$data['details'] = array(0);

	$_html = $this->load->view('recycled/details/detail-article-html',$data,true);
	echo json_encode($_html);

	}	

//Aqui se inserta lo que viene en el item
	public function add_detail_do(){

	if( isset($_POST["MODE"]) )
	$MODE =strip_tags( $this->security->xss_clean( $_POST["MODE"] )?:"" );

	$data['edit']=false;
	if( isset($_POST["edit"]) )
	$data['edit']=strip_tags( $this->security->xss_clean( $_POST["edit"] )?:"" );

	if(!empty($MODE) and $MODE=="view")
	$data['MODE'] ="do_it";
	else if (!empty($MODE) and $MODE=="do_it")
	$data['MODE'] ="view";

	// para que aparezcan las de editar 
	$data['DAD_MODE']=$this->http_params["DAD_MODE"];

	$this->load->model("admin/stock/catalog/article/article_model");
	$data['articles'] = $this->article_model->get_articles_to_option(true);

	// print_r(json_encode($_POST));
	// procesar los detalles que aun no se insertar solo los insertaremos como text en el html
	if(!empty($_POST["details"]) )		
	foreach ($_POST["details"] as $k => $vdt) {

	if(empty($vdt["article"]))
	continue;	
	
	$details[$k]        =array(
	"id"         =>(!empty($vdt["id"]) )?trim(strip_tags( $this->security->xss_clean( base64_decode($vdt["id"])  ) ) ) :"",
	"quantity"           =>isset($vdt["quantity"])?trim( strip_tags( $this->security->xss_clean($vdt["quantity"]) ) ) :"",
	"stockModification" =>isset($vdt["stockModification"])?trim( strip_tags( $this->security->xss_clean($vdt["stockModification"]) ) ) :"",
	"article"           =>isset($vdt["article"])?trim( strip_tags( $this->security->xss_clean($vdt["article"]) ) ) :"",
	"description"       =>isset($vdt["description"])?trim( strip_tags( $this->security->xss_clean($vdt["description"]) ) ) :"",
	"price"             =>isset($vdt["price"])?trim( strip_tags( $this->security->xss_clean($vdt["price"]) ) ) :"",
	"totalSub"          =>isset($vdt["totalSub"])?trim( strip_tags( $this->security->xss_clean($vdt["totalSub"]) ) ) :"",
	"taxIeps"           =>isset($vdt["taxIeps"])?trim( strip_tags( $this->security->xss_clean($vdt["taxIeps"]) ) ) :"",
	"taxIva"            =>isset($vdt["taxIva"])?trim( strip_tags( $this->security->xss_clean($vdt["taxIva"]) ) ) :"",
	"taxIvaRetained"    =>isset($vdt["taxIvaRetained"])?trim( strip_tags( $this->security->xss_clean($vdt["taxIvaRetained"]) ) ) :"",
	"taxIsr"            =>isset($vdt["taxIsr"])?trim( strip_tags( $this->security->xss_clean($vdt["taxIsr"]) ) ) :"",
	);
		
	}
	$data['details'] = (!empty($details)?$details:array());

	$_html = $this->load->view('recycled/details/detail-article-html',$data,true);

	echo json_encode($_html);

	}	
	
	public function details(){

	if( isset($_POST["id_record"]) )
	$id_record =strip_tags( $this->security->xss_clean(decode_id($_POST["id_record"]) ));

	if( isset($_POST["source_module"]) )
	$source_module =strip_tags( $this->security->xss_clean( $_POST["source_module"] )?:"" );

	// else
	$data['DAD_MODE']=$this->http_params["DAD_MODE"];
	$data['MODE']="view";

	// Arreglo de los articulos 
	$this->load->model("admin/stock/catalog/article/article_model");
	$data['articles'] = $this->article_model->get_articles_to_option(true);

	$data['details'] = $this->article_model->get_details_by_id($source_module,$id_record);
	// $_html = $this->load->view('recycled/details/detail-article',$data,true);
	$_html = $this->load->view('recycled/details/detail-article-html',$data,true);

	return print_r( json_encode( array("status"=>1,"msg"=>"Html de articulos","html"=>$_html,"last"=>$this->db->last_query()) ) );
	
	}
	public function get_detail_by(){
	
	$this->load->model("admin/stock/catalog/article/article_model");
	
	if( !empty($_POST["id"]) )
	$id =isset($_POST["id"])?trim( strip_tags( $this->security->xss_clean(base64_decode($_POST["id"])) ) ) :"";

	if( isset($_POST["source_module"]) )
	$source_module =strip_tags( $this->security->xss_clean( $_POST["source_module"] )?:"" );

	$details=$this->article_model->get_sale_details($source_module,$id,$this->http_params["id_record"]);

	foreach ($details as $key => $row) 
	$details[$key]=array_merge($details[$key],array("id"=>base64_encode($row["id"])));

	return print_r(json_encode($details));
	}

}