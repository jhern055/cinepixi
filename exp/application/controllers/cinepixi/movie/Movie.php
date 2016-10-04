<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Movie extends CI_Controller {

	public  $config;
	public  $page;
	public 	$registred_by;
	public 	$idTemp;
	public 	$sys;

	// movie
	public 	$uri_movie;
    // ...

	public $module="";
	public $http_params="";
    public $disk_space="";
	
	// cargamos las librerias a usar 
	public function __construct() {

		parent:: __construct();

	$this->load->model("cinepixi/movie/movie_model");
// ------------------------

	$this->load->model("vars_system_model");
	$this->load->library("pagination");
	$segment=(int)$this->uri->segment(2);
	$this->sys=$this->vars_system_model->_vars_system();
	$this->registred_by=$this->security->xss_clean($this->session->userdata("user_id"));
	$this->now = date("Y-m-d H:i:s");
	$this->idTemp=$this->session->userdata("idTemp");
	// <OWN>
	$this->disk_space=explode("\n", trim(shell_exec_function("df -h | grep /dev/")));
	if(!empty($this->disk_space))
	foreach($this->disk_space as $k=>$val): 
	$this->disk_space[$k]=explode(" ", delete_format($val)); 
	endforeach; 
 	// </OWN>

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

		// Peliculas <movie>
		$this->uri_movie="cinepixi/movie/";
		// ... </movie>	

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
	$module=(!empty($module)?$module:"cinepixi/movie/");
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

	"MODE"        =>"do_it",
	"id"          =>(!empty($http_params["id"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["id"]) ) ) :""),
	"name"        =>(!empty($http_params["name"]) ?strip_tags( $this->security->xss_clean($http_params["name"]) ):""),
	"pathFile"    =>(!empty($http_params["pathFile"]) ?strip_tags( $this->security->xss_clean($http_params["pathFile"]) ):""),
	"resolution"  =>(!empty($http_params["resolution"]) ?strip_tags( $this->security->xss_clean($http_params["resolution"]) ):""),
	"category_id" =>(!empty($http_params["category_id"]) ?strip_tags( $this->security->xss_clean($http_params["category_id"]) ):""),
	
	);
	
	extract($http_params);

		if(!empty($id)):
		$data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);
		else:
		$data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now);
		endif;

	// movieView
		if($method=="movieView"){

    	// no vacios
    		$response_required=$this->required_fields("movie",$http_params);
    		if(!$response_required["status"])
			return $response_required;
    	// ...

		// Aqui almacenamos en el arreglo el cual vamos a insertar o actualizar
			$data         =array(
			"name"        =>$name,
			"pathFile"    =>$pathFile,
			"category_id" =>$category_id,
			"resolution"  =>$resolution,
			);

		// validar que  no exista un registro identico 
		if($this->movie_model->record_same_movie($data,$id) )
		return array("status"=>0,"msg"=>"Ya existe un registro identico ","data"=>false);

		$data=array_merge($data_depend,$data);


		if($id)
		$last_id=$this->movie_model->update_movie($data,$id);
		else{
		$last_id=$this->movie_model->insert_movie($data);

			// <own>
			if(!empty($file_name)):
			// mover y insertar en el registro
				foreach ($file_name as $k => $file):

						$tmp_file=pathinfo(decode_id($file));
						$tmp_move[$k]["basename"]=$tmp_file["basename"];
						
						$data_tmp=array(
						"name"     =>$tmp_file["basename"],
						"movie_id" =>$last_id,
						"pathFile" =>$pathFile
						);

						$this->movie_model->insert_movie_image($data_tmp,$last_id);

				endforeach;
				    // <mover>

				$this->load->helper("file");

					foreach ($tmp_move as $tmp_mv_name) {

						$response=move_file("movie",$tmp_mv_name["basename"],$last_id,$pathFile);
						if(!empty($response["status"]))
						return $response;

					}
			// </mover>
			endif;
			// </own>	
					
		}

		// <movie_category>

			// // Agregar la relacion de categoria
			// $this->load->model("cinepixi/stock/catalog/category/category_model");

			// $database_movie_category=$this->category_model->get_movie_category($last_id);
			
			// // ELMINAR
			// if(!empty($database_movie_category)){
			// 	foreach ($database_movie_category as $k => $row):

			// 		// para eliminar los que ya tengo pero qe no vienen en el arreglo
			// 		if(!in_array($row["category_id"], $movie_category)){
			// 		// return array("status"=>0,"msg"=>var_export($row["category_id"],true),"data"=>false);
			// 		if(!$this->category_model->delete_movie_category($database_movie_category[$row["category_id"]]) )
			// 		return array("status"=>0,"msg"=>"No se elimino la relacion de la categoria","data"=>false);

			// 		}
			// 	endforeach;
			// }

			// 	// {
			// 	// add the last_id
			// 	if(!empty($movie_category)){
			// 	foreach ($movie_category as $k => $category_id):

			// 			// variable para ir a insert
			// 			$data_art_cat=array(
			// 				"movie_id"=>$last_id,
			// 				"category_id"=>$category_id,
			// 			);

			// 			// si no existe lo inserta
			// 			if(!$this->category_model->is_there_movie_category($data_art_cat))
			// 			$this->category_model->insert_movie_category($data_art_cat);
							

			// 	endforeach;

			// 	}
		// </movie_category>

		return array("status"=>1,"msg"=>"Exito","data"=>$last_id);

		}

	// </> movieView

		return $last_id;
	}

	public function required_fields($module,$http_params){

		if(empty($http_params["pathFile"]))
		return array("status"=>0,"msg"=>"Debes especificar el path","exchange_rate"=>1);
		
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

// <movie> 

	// carga ajax
	public function movie_ajax() {

	$module=$this->uri_movie;

	$http_params=array_merge($_GET,$_POST);
	$http_params          =array(
	"input_search_movie" =>(!empty($http_params["input_search_movie"]) ? strip_tags( $this->security->xss_clean( $http_params["input_search_movie"] ) ) :""),
	"show_amount_movie"  =>(!empty($http_params["show_amount_movie"]) ? strip_tags( $this->security->xss_clean( $http_params["show_amount_movie"])  ) :10),
	);
	extract($http_params);

	$page_amount=$show_amount_movie;

	$query_search=array(
		"\$this->db->like('name', \"".$input_search_movie."\");",
		);

	$this->pagination->initialize($this->config_pagination("cinepixi/movie/movie_ajax",$this->movie_model->get_movie_amount($query_search),$page_amount) );
// <OWN>
		$array_option_dev=array();
		foreach($this->disk_space as $k=>$val): 
		$array_option_dev[$val[0]]=$val[0]; 
		endforeach; 

	    if(!empty($array_option_dev))
		sort($array_option_dev); 

		foreach($array_option_dev as $k=>$val): 
		$array_option_dev_[$val]=$val; 
		endforeach;

		$array['data']["disk_space"]=$array_option_dev_;
// </OWN>
	$data=array(
		"module"=>$module,
		"sys"=>$this->sys,
		"input_search_movie"=>$input_search_movie,
		// <OWN>
		"disk_space"=>$this->disk_space,
		"disk_space_path"=>$array_option_dev_,
		// </OWN>
		"show_amount"=>$show_amount_movie,
		"records_array"=>$this->movie_model->get_movie($page_amount, $this->page,$query_search),
		"pagination"=>$this->pagination->create_links(),
		"module_data"=>$this->movie_model->m_name($module),
		);
	$data["module_data"]["module_data_method_do_it"]="cinepixi/movie/movieView/";

    $data["modules_quick"]=$this->load->get_back_access($module);

	$html=$this->load->view("cinepixi/movie/ajax/table-movie-view",$data,true);
	$data["html"]=$html;

	// $this->session->set_userdata('record_start_row_movie',$this->page);

	if(!empty($_GET["ajax"]))
	echo $data["html"];
	else
	return $data;

	}

	// carga normal
	public function index() {
		
	$data=$this->movie_ajax();

	$this->session->set_userdata("idTemp");
	$this->session->set_userdata('input_search_movie');
	$this->session->set_userdata("sessionMode_movie");

	// if(!empty($this->page) and !empty($data["records_array"]))
	// $this->session->set_userdata('record_start_row_movie',$this->page);

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view("cinepixi/movie/movie_view",$data,true) ))) ;
	else
	return $this->load->template("cinepixi/movie/movie_view",$data);

	}

	// ver para registro
	public function movieView($id=null) {

	$id_affected='';

	$module=$this->uri_movie;
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
	$return=$this->do_it($id,"movieView");

		if(!$return["status"])
		return print_r(json_encode($return));		
		else
		$id=$return["data"];

	$this->session->set_userdata("idTemp",$id);

	endif;

	// traer el registro 
	$array['data']=$this->movie_model->get_movie_id($id);

	if(!empty($MODE) and $MODE=="view")
	$array['data']['MODE']="do_it";
	else if (!empty($id))
	$array['data']['MODE']="view";
	else
	$array['data']['MODE']="do_it";

	// <F5>
	$sessionMode=$this->session->userdata("sessionMode_movie");
	if(!empty($_POST)):
		$this->session->set_userdata("sessionMode_movie",$array['data']['MODE']);
	endif;
	$array['id']=$id;
	// </F5>

	// nombre del modulo
	$array['data']["module_data"]=$this->movie_model->m_name($module);	
	$array['data']["module_data_method_do_it"]="cinepixi/movie/movieView/";

// <OWN>

		$pathFile_array=array();
		$this->load->model("cinepixi/pathFile/pathFile_model");

		$pathFile_array=$this->pathFile_model->get_cinepixi_pathFile_to_option(false,null);
		// foreach($this->disk_space as $k=>$val): 
		// $array_option_dev[$val[0]]=$val[0]; 
		// endforeach; 

	 //    if(!empty($array_option_dev))
		// sort($array_option_dev); 

		// foreach($array_option_dev as $k=>$val): 
		// $array_option_dev_[$val."/"]=$val."/"; 
		// endforeach;

		$array['data']["pathFile_array"]=$pathFile_array;
// </OWN>
		if(!empty($MODE)){

	    $this->load->model("vars_system_model");
		$array["data"]["sys"]=$this->vars_system_model->_vars_system();

    	$array["data"]["modules_quick"]=$this->load->get_back_access($module);

		$html=$this->load->view($module."dinamyc-inputs",$array["data"],true);
		return print_r(json_encode( array("status"=>1, "html"=>$html,"id"=>$id) ));	

		}

	$this->load->template($module.'dinamyc-view',$array);

	}


	public function movie_delete() {
	
	$module=$this->uri_movie;

    // <RightCheck> 
        $return_valid=rights_validation($module."delete","ajax");

        if(!$return_valid["status"])
        return print_r(json_encode($return_valid));       
    // </RightCheck>

	if(!empty($_POST["id"]))
    $id=decode_id( strip_tags( $this->security->xss_clean($_POST["id"]) ) );

	if($this->movie_model->movie_delete_it($id))
	return print_r(json_encode( array("status"=>1,"msg"=>"Se elimino","data"=>false ) ));

	}

	// -------------------------------------------------------------------------------------------------------
	// este metodo es para el token input 
	public function tokeninput(){

	if( isset($_GET["request"]["name"]) ){
	$name =strip_tags( $this->security->xss_clean( $_GET["request"]["name"] )?:"" );
	$var_name="\$this->db->like('name', \"".$name."\");";
	$data=$this->movie_model->get_movies_token_search($var_name);
	}

	return print_r( json_encode($data));

	}


}