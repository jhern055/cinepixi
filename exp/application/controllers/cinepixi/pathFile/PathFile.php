<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PathFIle extends CI_Controller {

	public  $config;
	public  $page;
	public 	$registred_by;
	public 	$idTemp;
	public 	$sys;

	// pathFile
	public 	$uri_pathFile;
    // ...

	public $module="";
	public $http_params="";
	
	// cargamos las librerias a usar 
	public function __construct() {

		parent:: __construct();

	$this->load->model("cinepixi/pathFile/pathFile_model");
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

    // $uri_string=array_merge($_GET,$_POST);
    // $uri_string=(!empty($uri_string["uri_string"])? $uri_string["uri_string"]:$this->uri->uri_string );

		// Peliculas <pathFile>
		$this->uri_pathFile="cinepixi/pathFile/";
		// ... </pathFile>	

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
	$module=(!empty($module)?$module:"cinepixi/pathFile/");
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
	);
	
	extract($http_params);

		if(!empty($id)):
		$data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);
		else:
		$data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now);
		endif;

	// pathFileView
		if($method=="pathFileView"){

		// Aqui almacenamos en el arreglo el cual vamos a insertar o actualizar
		$data=array(
			"name"  =>$name,
			);

		// validar que  no exista un registro identico 
		if($this->pathFile_model->record_same_pathFile($data,$id) )
		return array("status"=>0,"msg"=>"Ya existe un registro identico ","data"=>false);

		$data=array_merge($data_depend,$data);

		if($id)
		$last_id=$this->pathFile_model->update_pathFile($data,$id);
		else
		$last_id=$this->pathFile_model->insert_pathFile($data);

		return array("status"=>1,"msg"=>"Exito","data"=>$last_id);

		}

	// </> pathFileView

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

// <pathFile> 

	// carga ajax
	public function pathFile_ajax() {

	$module=$this->uri_pathFile;

	$http_params=array_merge($_GET,$_POST);
	$http_params          =array(
	"input_search_pathFile" =>(!empty($http_params["input_search_pathFile"]) ? strip_tags( $this->security->xss_clean( $http_params["input_search_pathFile"] ) ) :""),
	"show_amount_pathFile"  =>(!empty($http_params["show_amount_pathFile"]) ? strip_tags( $this->security->xss_clean( $http_params["show_amount_pathFile"])  ) :10),
	);
	extract($http_params);

	$page_amount=$show_amount_pathFile;

	$query_search=array(
		"\$this->db->like('name', \"".$input_search_pathFile."\");",
		);

	$this->pagination->initialize($this->config_pagination("cinepixi/pathFile/pathFile_ajax",$this->pathFile_model->get_pathFile_amount($query_search),$page_amount) );

	$data=array(
		"module"=>$module,
		"sys"=>$this->sys,
		"input_search_pathFile"=>$input_search_pathFile,
		"show_amount"=>$show_amount_pathFile,
		"records_array"=>$this->pathFile_model->get_pathFile($page_amount, $this->page,$query_search),
		"pagination"=>$this->pagination->create_links(),
		"module_data"=>$this->pathFile_model->m_name($module),
		);
	$data["module_data"]["module_data_method_do_it"]="cinepixi/pathFile/pathFileView/";

    $data["modules_quick"]=$this->load->get_back_access($module);

	$html=$this->load->view("cinepixi/pathFile/ajax/table-pathFile-view",$data,true);
	$data["html"]=$html;

	// $this->session->set_userdata('record_start_row_pathFile',$this->page);

	if(!empty($_GET["ajax"]))
	echo $data["html"];
	else
	return $data;

	}

	// carga normal
	public function index() {
	
	$data=$this->pathFile_ajax();

	$this->session->set_userdata("idTemp");
	$this->session->set_userdata('input_search_pathFile');
	$this->session->set_userdata("sessionMode_pathFile");

	// if(!empty($this->page) and !empty($data["records_array"]))
	// $this->session->set_userdata('record_start_row_pathFile',$this->page);

// <OWN>
// $_SESSION["paths"]="";
// function getDirectoryTree( $outerDir , $x){ 
//     $dirs = array_diff( scandir( $outerDir ), Array( ".", ".." ) ); 
//     $dir_array = Array(); 
//     foreach( $dirs as $d ){ 
//         if( is_dir($outerDir."/".$d)  ){ 
//         	$_SESSION["paths"][$d]= $outerDir."/".$d;
//         	// $_SESSION["paths"][$d]="\"".str_replace(array(" "), array("\\ ",""), $outerDir."/".$d)."\"";
//             $dir_array[ $d ] = getDirectoryTree( $outerDir."/".$d , $x); 
//         }else{ 
//          // if (($x)?preg_match($x."/\$/i",'',$d):1) 
//          //    $dir_array[ $d ] = $d; 
//             } 
//     } 
//     return $dir_array; 
// } 
// foreach ($data["sys"]["config"]["movie_path"] as $ka =>$rowa ):
// getDirectoryTree($rowa,'flv');
// endforeach;

// if(!empty($_SESSION["paths"]))
// foreach($_SESSION["paths"] as $path){

// 	$data_Sync=array("name"=>$path );

// 	if($this->pathFile_model->sync_same_pathFile($path) )
//     $this->pathFile_model->update_pathFile($data_Sync,0,$path);
// 	else
//     $this->pathFile_model->insert_pathFile($data_Sync);
	
// }


	// <OWN>
	// $this->load->helper("file");
	// foreach ($data["sys"]["config"]["movie_path"] as $ka =>$rowa ):
	//     // $paths[$rowa]=$this->movie_model->dirToArray_sync($rowa);
	//     // 
	//     // $paths[$rowa]=dirToArray($rowa);
	//     $response=$this->movie_model->insert_update_movie_category_sync($rowa);
	// endforeach;

	// foreach ($data["sys"]["config"]["movie_path"] as $ka =>$rowa ):
	    // $paths[$rowa]=$this->movie_model->dirToArray_sync($rowa);
	    // 

	    // $paths[$rowa]=dirToArray($rowa);
	    // $response=dirToArrayFiles($rowa);
	    // $response=dirToArrayFiles("/media/dell/67F18E800D673AB3/musica");

	// endforeach;
	// $this->load->model("cinepixi/pathFile/pathFile_model");
	// $path_folder=$this->pathFile_model->get_cinepixi_pathFile_to_option(null,"");

// pr($response);
	// if(!empty($path_folder))
	// foreach ($path_folder as $key => $path) {
				// insertarmos en movie o aztualizamos

		// if($pathFile_data=$this->pathFile_model->sync_same_pathFile($path)):
		//     $this->pathFile_model->update_pathFile($data_Sync,0,$path);

		//   $this->pathFile_model->insert_pathFile($data_Sync);

		// 	else:
		// 	$pathFile_data["id"]=
		// endif;

	// 	foreach ($_SESSION["FilesInPath"] as $kb => $file) {
	// 		if(file_exists($path."/".$file) ):
	// 			$data_process_file=array(
	// 				"file_name"  =>$file,
	// 				"resolution" =>"",
	// 				"pathFile_id"   =>$key,
	// 			);

	// 			// validar si ya existe la relacion con el path
	//     		if($this->pathFile_model->record_same_file($data_process_file)){
	//     			// aplicamos un update
	// 			$this->pathFile_model->record_same_file($data_process_file,$key);	    			

	//     		}else {

	//     			// aplicamos un insert
	// 				$this->pathFile_model->insert_movie_file($data_process_file);	    			
	//     		}

	// 		endif;
	// 	}
	// $_SESSION["foreach_path"]++;
	// }

// </ OWN>

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view("cinepixi/pathFile/pathFile_view",$data,true) ))) ;
	else
	return $this->load->template("cinepixi/pathFile/pathFile_view",$data);

	}

	// ver para registro
	public function pathFileView($id=null) {

	$id_affected='';

	$module=$this->uri_pathFile;
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
	$return=$this->do_it($id,"pathFileView");

		if(!$return["status"])
		return print_r(json_encode($return));		
		else
		$id=$return["data"];

	$this->session->set_userdata("idTemp",$id);

	endif;

	// traer el registro 
	$array['data']=$this->pathFile_model->get_pathFile_id($id);

	if(!empty($MODE) and $MODE=="view")
	$array['data']['MODE']="do_it";
	else if (!empty($id))
	$array['data']['MODE']="view";
	else
	$array['data']['MODE']="do_it";

	// <F5>
	$sessionMode=$this->session->userdata("sessionMode_pathFile");
	if(!empty($_POST)):
		$this->session->set_userdata("sessionMode_pathFile",$array['data']['MODE']);
	endif;
	$array['id']=$id;
	// </F5>

	// nombre del modulo
	$array['data']["module_data"]=$this->pathFile_model->m_name($module);	
	$array['data']["module_data_method_do_it"]="cinepixi/pathFile/pathFileView/";

		if(!empty($MODE)){

	    $this->load->model("vars_system_model");
		$array["data"]["sys"]=$this->vars_system_model->_vars_system();

    	$array["data"]["modules_quick"]=$this->load->get_back_access($module);

		$html=$this->load->view($module."dinamyc-inputs",$array["data"],true);
		return print_r(json_encode( array("status"=>1, "html"=>$html,"id"=>$id) ));	

		}

	$this->load->template($module.'dinamyc-view',$array);

	}


	public function pathFile_delete() {
	
	$module=$this->uri_pathFile;

    // <RightCheck> 
        $return_valid=rights_validation($module."delete","ajax");

        if(!$return_valid["status"])
        return print_r(json_encode($return_valid));       
    // </RightCheck>

	if(!empty($_POST["id"]))
    $id=decode_id( strip_tags( $this->security->xss_clean($_POST["id"]) ) );

	if($this->pathFile_model->pathFile_delete_it($id))
	return print_r(json_encode( array("status"=>1,"msg"=>"Se elimino","data"=>false ) ));

	}

	// -------------------------------------------------------------------------------------------------------
	// este metodo es para el token input 
	public function pathFile_tokeninput(){

	if( isset($_GET["request"]["name"]) ){
	$name =strip_tags( $this->security->xss_clean( $_GET["request"]["name"] )?:"" );
	if(preg_match('/[0-9]/', $_GET["request"]["name"]))
	$var_name="\$this->db->where('id', ".$name.");";
	else
	$var_name="\$this->db->like('name', \"".$name."\");";

	$data=$this->pathFile_model->get_cinepixi_pathFile_token_search($var_name);

	}

	// return print_r( json_encode(array(gettype($_GET["request"]["name"]))));
	return print_r( json_encode($data));

	}

}