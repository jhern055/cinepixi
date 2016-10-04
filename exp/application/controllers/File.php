<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File extends CI_Controller {
private $max_size = 1024;
public $base_url;
    
    // public $_INVOICE_MODE_CONFIG;
	// cargamos las librerias a usar 
	public function __construct() {

        parent:: __construct();
        // $this->load->model('file_model');
        $this->load->helper('url');
        $this->load->helper('security');
        $this->load->model("vars_system_model");
        $sys=$this->vars_system_model->_vars_system();
        // $this->_INVOICE_MODE_CONFIG=invoice_mode_config();
        $this->load->model('file_model');

        $this->load->helper('url');
        $this->load->helper('security');

        $this->base_url =  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ?  "https" : "http");
        $this->base_url .=  "://".$_SERVER['HTTP_HOST'];
        $this->base_url .=  str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);

	}

    /*public function upload(){
        //directorio de almacén de imágenes
        $uploaddir = 'images/uploads/imgPost/';

        $tmp_name = $_FILES['file']['tmp_name'];
        
        //nombre del fichero sin espacios en blanco
        $nombre_fichero_sin_espacios=str_replace(" ","",$_FILES['file']['name']);
        
        //ruta completa del fichero
        $uploadfile = $uploaddir.$nombre_fichero_sin_espacios;
               
        //nombre del fichero
        $file_name=$_FILES['file']['name'];

      
        //compruebo si existe el directorio y si no existe lo creo
        if(!is_dir($uploaddir)){ 
            @mkdir($uploaddir, 0700); 
        }
                
        //compruebo si existe el fichero y si existe le pongo _copia_ en el nombre
        if (file_exists($uploadfile)){ 
            $uploadfile = $uploaddir ."_copia_". $nombre_fichero_sin_espacios;
            $file_name="_copia_" .$nombre_fichero_sin_espacios;
        } 

         move_uploaded_file($tmp_name,$uploadfile);

         // **************************************************************************
         // **************************************************************************

        echo '{"name":"'.$uploadfile.'"}';  
    }*/

    // con el otro metodo para subir imagenes este'puede servir para los dos 
    // metodos :) porque lo configure
    
    // public function doUploadFile(){

    // $return =array('status' => 1, 'msg' => '',"data"=>false);

    // // jQueryFileUpload
    // $file_element_name = "file";

    // // $file_element_name = 'userfile';
    
    // if ($return["status"] != 0){


    //         $config['upload_path'] = FCPATH.'images/uploads/imgPost/';
    //         $config['allowed_types'] = 'gif|jpg|png|jpeg';
    //         $config['max_size']  = 1024 * 8;
    //         $config['encrypt_name'] = TRUE;
            
    //         $this->load->library('upload', $config);
        
    //     if (!$this->upload->do_upload($file_element_name))
    //         $return =array('status' => 0, 'msg' => $this->upload->display_errors('', ''),"data"=>$config['upload_path']);
    //         else{
                
    //             // informacion de la imagen subida 
    //             $data = $this->upload->data();

    //             // tratamiento de imagen
    //             $this->load->library('image_lib');
    //             $config_img = array();
 
    //             $config_img['image_library'] = 'gd2';
    //             $config_img['source_image'] = FCPATH.'images/uploads/imgPost/'.$data['file_name'];
    //             $config_img['create_thumb'] = FALSE;
    //             $config_img['maintain_ratio'] = FALSE;

    //             // calcular las dimensiones de la imagen 
    //             // alto 810
    //             // ancho 436
    //             $img_size  = getimagesize($config_img['source_image']);
    //             $img_width_new = null;
    //             $img_height_new = null;
    //             $img_w  = $img_size[0];
    //             $img_h  = $img_size[1];

    //             switch ($img_h) {
    //                 case $img_h>=1000:
    //                     $img_width_new = ( $img_w * 50 ) / 200;
    //                     $img_height_new = ( $img_h * 50 ) / 200;
    //                     break;
    //                 case $img_h>=810:
    //                     $img_width_new = ( $img_w * 50 ) / 100;
    //                     $img_height_new = ( $img_h * 50 ) / 100;
    //                     break;

    //                 default:
    //                     # code...
    //                     break;
    //             }

    //             // $config_img['width'] = $img_width_new;
    //             // $config_img['height'] = $img_height_new;

    //             // ponerle una marca de agua a la foto
    //             // $config_img['wm_text']          = 'www.pirabook.com';
    //             // $config_img['wm_type']          = 'text';
    //             // $config_img['wm_font_path']     = './system/fonts/texb.ttf';
    //             // $config_img['wm_font_size']     = '90';
    //             // $config_img['wm_font_color']    = 'ffffff';
    //             // $config_img['wm_vrt_alignment'] = 'bottom';
    //             // $config_img['wm_hor_alignment'] = 'center';
    //             // $config_img['wm_padding']       = '20';



    //             $this->image_lib->initialize($config_img);
    //             $this->image_lib->watermark();
    //             $this->image_lib->resize();
    //             $this->image_lib->clear();
    //             //... 

    //             $file_id = $this->file_model->insert_file($data['file_name']);
                
    //             if($file_id)
    //             $return =array('status' => 1, 'msg' =>'Imgane subida exitosamente',"data"=>false,"name"=>"images/uploads/imgPost/".$data['file_name'],"file_id"=>encode_id($file_id));
    //             else{ unlink($data['full_path']); $return =array('status' => 0, 'msg' =>'Algo paso y no se pudo subir la imagen,porfavor trata nuevamente.',"data"=>false);}
    //         }

    //         @unlink($_FILES[$file_element_name]);

    //     }

    //     // header('Content-Type: application/json');
    //     // jQueryFileUpload
    //     // echo '{"name":"images/uploads/imgPost/'.$data['file_name'].'"}';  

    //     echo json_encode($return);
    //      return;
    // }

/*
    public function files(){
       $files = $this->file_model->get_files();
       $this->load->view('files_view', array('files' => $files));
    }
    ******************************************************/

    public function delete_file(){

    $file_id=$this->security->xss_clean($_POST["file_id"]);
    if ($this->file_model->delete_file(decode_id($file_id)))
    $return = array('status' => 1, 'msg' => 'imagen eliminada');
    else
    $return =array('status' => 0, 'msg' => 'No se pudo borrar la imagen');

       echo json_encode($return);
    }

    public function create_gif($publication_id){

    $images_array=$this->file_model->get_files_by($publication_id);
    if(!empty($images_array) )
    foreach ($images_array as $key => $value) {
    $images_array_to_gif[$key]=FCPATH.'images/uploads/imgPost/'.$value["filename"];

    }    

    if(!empty($images_array_to_gif) and count($images_array_to_gif)>1){
    $anim = new GifCreator\AnimGif();
    $gif=$anim->create($images_array_to_gif,array(200,200));

    mt_srand();
    $filename = md5(uniqid(mt_rand())).".gif";
    $gif->save(FCPATH.'images/uploads/gifPost/'.$filename);
    return $filename;
    }

    }
    /*public function upload(){
        //directorio de almacén de imágenes
        $uploaddir = 'images/uploads/imgPost/';

        $tmp_name = $_FILES['file']['tmp_name'];
        
        //nombre del fichero sin espacios en blanco
        $nombre_fichero_sin_espacios=str_replace(" ","",$_FILES['file']['name']);
        
        //ruta completa del fichero
        $uploadfile = $uploaddir.$nombre_fichero_sin_espacios;
               
        //nombre del fichero
        $file_name=$_FILES['file']['name'];

      
        //compruebo si existe el directorio y si no existe lo creo
        if(!is_dir($uploaddir)){ 
            @mkdir($uploaddir, 0700); 
        }
                
        //compruebo si existe el fichero y si existe le pongo _copia_ en el nombre
        if (file_exists($uploadfile)){ 
            $uploadfile = $uploaddir ."_copia_". $nombre_fichero_sin_espacios;
            $file_name="_copia_" .$nombre_fichero_sin_espacios;
        } 

         move_uploaded_file($tmp_name,$uploadfile);

         // **************************************************************************
         // **************************************************************************

        echo '{"name":"'.$uploadfile.'"}';  
    }*/
    public function process(){

    /* max_size: 2 MB (or 2048 KB) */
    $this->load->model("vars_system_model");
    $sys=$this->vars_system_model->_vars_system();
    $http_params=array_merge($_GET,$_POST);
    $http_params   =array(
    "id"=>(!empty($http_params["id"])   ? strip_tags( $this->security->xss_clean( decode_id($http_params["id"]) ) ) :""),
    );

    extract($http_params);

    // esta es la carpeta que se usa para subir cualquier cosa temporal cuando no tiene registro
    // ESTO ES SI LO SUBO A UN HOSTING EL TMP EN SU MISMO DIRECTORIO
    // if(!is_dir($_SERVER['DOCUMENT_ROOT']."/".$sys["storage_tmp"]))
    // mkdir($_SERVER['DOCUMENT_ROOT']."/".$sys["storage_tmp"], 0777);

    // $tmp_dir=$_SERVER['DOCUMENT_ROOT']."/".$sys["storage_tmp"];
    // .....

    // $tmp_dir="/".$sys["storage_tmp"];
    $tmp_dir=$_SERVER['DOCUMENT_ROOT']."/".$sys["storage_tmp"];
    if(!is_dir($tmp_dir))
    mkdir($tmp_dir, 0777);

    // return print_r(json_encode($response));

    // if(!is_dir($sys["storage_tmp"]))
    // mkdir($sys["storage_tmp"], 0777);

    // $tmp_dir=$sys["storage_tmp"];
    // configuration by module

    // shcp_file
    $shcp_file_config=array(
        "path"=>$sys["storage"]["shcp_file"],
        );

    // enterprise_fiscal_config
    $enterprise_fiscal_config=array(
        "path"=>$sys["storage"]["enterprise_fiscal"],
        );

    // $publication_config=array(
    //     "path"=>$_SERVER["DOCUMENT_ROOT"]."/".$sys["storage"]["publication_config"],
    //     "path_storage"=>$sys["storage"]["publication_config"],
    //     );

    $article_image_config=array(
        "path"=>$sys["storage"]["article"]["article_image"],
        );


    if(!empty($_POST["pathFile"])){

    $pathFile_movie_id=strip_tags($this->security->xss_clean($_POST["pathFile"]));
    $this->load->model("cinepixi/pathFile/pathFile_model");
    $pathFileMovie=$this->pathFile_model->get_pathFile_id($pathFile_movie_id);

    }

    $movie_config=array(
        "path"=>$sys["storage"]["movie"],
        "pathFileMovie"=>$pathFileMovie,
        );

    $config=array(
            "shcp_file"=>array(
                "ajax"=>1, // cuidado con este porque si copias y pegas este arra() y tu proceso no es por ajax  te retornara en print_r(json_encode( array() ));
                // "upload_required_right"=>"admin/sale/cte/read",
                "path"=>$shcp_file_config["path"],
                "process"=>encode_id("shcp_file"),
                "make_dir"=>function ($id,$shcp_file_config){

                    if(!is_dir($shcp_file_config["path"]."$id"))
                     mkdir($shcp_file_config["path"]."$id", 0777, true);
                    
                    return array('status' => 1, 'msg' => 'Creado el directorio',"reload"=>true);
                },
                "additional_process"=>function ($upload_data){


                    return array('status' => 1, 'msg' => 'Se inserto en base de datos',"reload"=>true);
                },
                "upload_path"=>(!empty($id) ? $shcp_file_config["path"]."$id" :$tmp_dir ) ,
                "allowed_types"=>"cer|key",
                "max_size"=>"4000",
                "fileType"=>function($file){ return false;},
                "classSpan"=>function ($upload_data,$file){

                    // esto es para ponerle la imagen
                    if($upload_data["file_ext"]==".cer")
                    $certificate="certificate";
                    else
                    $certificate="keyFile";

                    return $certificate;
                },
                "update_record"=>function ($id,$upload_data,$shcp_file_config){

                    $file_name=strip_tags($this->security->xss_clean($upload_data["file_name"]) );
                    $this->load->model("config/invoice/invoice_model");

                        if($upload_data["file_ext"]==".cer"):
                        // si existe ya un archivo  y sube otro se elimina el que esta
                            if($file_tmp_data=$this->invoice_model->get_shcp_file_id($id)){  
                                if(!empty($file_tmp_data["file_cer"]))
                                @unlink($shcp_file_config["path"]."/$id/".$file_tmp_data["file_cer"]);
                            }
                        // 
                        $data=array("file_cer"=>$file_name); 
                        else:
                        // si existe ya un archivo  y sube otro se elimina el que esta
                            if($file_tmp_data=$this->invoice_model->get_shcp_file_id($id)){  
                                if(!empty($file_tmp_data["file_key"]))
                                @unlink($shcp_file_config["path"]."/$id/".$file_tmp_data["file_key"]);
                            }
                        // 
                        $data=array("file_key"=>$file_name); 
                        endif;

                        if(!$this->invoice_model->update_shcp_file($data,$id))
                        return array('status' => 0, 'msg' => "Hubo un error","reload"=>true);

                    return array('status' => 1, 'msg' => "Se actualizo el nombre en el registro","reload"=>true);
                },
                "return"=>array('status' => 1, 'msg' => 'Se subio el certificado exitosamente',"reload"=>true),

            ),
// ********************************************************************************************************** 
"enterprise_fiscal"=>array(
                "ajax"=>1, // cuidado con este porque si copias y pegas este arra() y tu proceso no es por ajax  te retornara en print_r(json_encode( array() ));
                "path"=>$enterprise_fiscal_config["path"],
                "make_dir"=>function ($id,$enterprise_fiscal_config){

                    if(!is_dir($enterprise_fiscal_config["path"]."$id"))
                     mkdir($enterprise_fiscal_config["path"]."$id", 0777, true);
                    
                    return array('status' => 1, 'msg' => 'Creado el directorio',"reload"=>true);
                },
                "additional_process"=>function ($upload_data){


                    return array('status' => 1, 'msg' => 'Se inserto en base de datos',"reload"=>true);
                },
                "upload_path"=>(!empty($id) ? $enterprise_fiscal_config["path"]."$id" :$tmp_dir ) ,
                "allowed_types"=>"jpg|png|bmp",
                "max_size"=>"4000",
                "fileType"=>function($file){

                    if($file=="logo")
                    $file="logo";
                    else
                    $file="cedule";

                    return $file;
                },
                "classSpan"=>function ($upload_data,$file){
                    $span="";

                    if($file=="logo")
                    $span="file_imagen";
                    else
                    $span="cedule";

                    return $span;
                },
                "update_record"=>function ($id,$upload_data,$enterprise_fiscal_config,$file){

                    $file_name=strip_tags($this->security->xss_clean($upload_data["file_name"]) );
                    $this->load->model("config/config_model");

                        if($file=="cedule"){
                            // si existe ya un archivo  y sube otro se elimina el que esta
                                if($file_tmp_data=$this->config_model->get_enterprise_id($id)){  
                                    if(!empty($file_tmp_data["cedule"]))
                                    {@unlink($enterprise_fiscal_config["path"]."/$id/".$file_tmp_data["cedule"]); }
                                }
                            // 
                            $data=array("cedule"=>$file_name); 
                        }

                        if($file=="logo"){

                            // si existe ya un archivo  y sube otro se elimina el que esta
                                if($file_tmp_data=$this->config_model->get_enterprise_id($id)){  
                                    if(!empty($file_tmp_data["logo"]))
                                    {@unlink($enterprise_fiscal_config["path"]."/$id/".$file_tmp_data["logo"]); }
                                }
                            // 
                            $data=array("logo"=>$file_name); 
                        }

                    if(!$this->config_model->update_enterprise_file($data))
                    return array('status' => 0, 'msg' => "Hubo un error","reload"=>true);

                    return array('status' => 1, 'msg' => "Se actualizo el nombre en el registro","reload"=>true,$file=array_flip($data));
                },
                "return"=>array('status' => 1, 'msg' => 'Se subio exitosamente',"reload"=>true),

            ),

// ********************************************************************************************************** 
/*
"publication"=>array(
                "ajax"=>1, // cuidado con este porque si copias y pegas este arra() y tu proceso no es por ajax  te retornara en print_r(json_encode( array() ));
                "path"=>$publication_config["path"],
                "process"=>encode_id("publication"),
                "path_storage"=>$publication_config["path_storage"],
                "image_lib"=>1, // TRATAMIENTO DE IMAGEN

                "make_dir"=>function ($id,$publication_config){

                    // if(!is_dir($publication_config["path"]."$id"))
                    //  mkdir($publication_config["path"]."$id", 0777, true);
                    
                    return array('status' => 1, 'msg' => 'Creado el directorio',"reload"=>true);
                },
                "additional_process"=>function ($upload_data){


                    return array('status' => 1, 'msg' => 'Se inserto en base de datos',"reload"=>true);
                },
                "upload_path"=>(!empty($id) ? $publication_config["path"]    :$tmp_dir ) ,
                "allowed_types"=>"jpg|png|bmp",
                "max_size"=>"4000",
                "fileType"=>function($file){
                    return true;
                },
                "classSpan"=>function ($upload_data,$file){
                    $span="";

                    $span="file_imagen";

                    return $span;
                },
                
                "insert_file_record"=>function ($id,$upload_data,$publication_config,$file){

                    $file_name=strip_tags($this->security->xss_clean($upload_data["file_name"]) );
                    $this->load->model("pirabook/publication/publication_model");

                            $data=array(
                                "filename"=>$file_name,
                                "publication_id"=>$id
                                ); 

                    if(!$file_id=$this->publication_model->insert_image_publication($data))
                    return array('status' => 0, 'msg' => "Hubo un error al insertar imagen","reload"=>true);

                    return array('status' => 1, 'msg' => "Se actualizo el nombre en el registro","reload"=>false,"file_id"=>$file_id);
                },
                "delete_record"=>function ($http_params){

                    $this->load->model("pirabook/publication/publication_model");

                    if(!$this->publication_model->delete_image_publication($http_params["file_id"],$http_params["id"]))
                    return array('status' => 0, 'msg' => "Hubo un error al eliminar imagen BD","reload"=>true);

                    return array('status' => 1, 'msg' => "Se elimino la imagen de base de datos","reload"=>false,"file_id"=>$file_id);
                },

                "update_record"=>function ($id,$upload_data,$publication_config,$file){

                    return array('status' => 1, 'msg' => "Se actualizo el nombre en el registro","reload"=>false);
                },
                "friendly_path"=>function ($id,$upload_data,$publication_config){
                     $path_explode=explode("/",substr(base_url(),0, -1 ));
                     array_pop($path_explode);
                    return implode("/",$path_explode)."/".$publication_config["path_storage"].$upload_data["file_name"];
                },

                "return"=>array('status' => 1, 'msg' => 'Se subio exitosamente',"reload"=>true),

            ),
*/
// ********************************************************************************************************** 
"article_image"=>array(
                "ajax"=>1, // cuidado con este porque si copias y pegas este arra() y tu proceso no es por ajax  te retornara en print_r(json_encode( array() ));
                "path"=>$article_image_config["path"],
                "process"=>encode_id("article_image"),
                "path_storage"=>$article_image_config["path"],
                "image_lib"=>1, // TRATAMIENTO DE IMAGEN

                "make_dir"=>function ($id,$article_image_config){

                    // if(!is_dir($article_image_config["path"]."$id"))
                    //  mkdir($article_image_config["path"]."$id", 0777, true);
                    
                    return array('status' => 1, 'msg' => 'Creado el directorio',"reload"=>true);
                },
                "additional_process"=>function ($upload_data){


                    return array('status' => 1, 'msg' => 'Se inserto en base de datos',"reload"=>true);
                },
                "upload_path"=>(!empty($id) ? $article_image_config["path"]    :$tmp_dir ) ,
                "allowed_types"=>"jpg|png|bmp",
                "max_size"=>"4000",
                "fileType"=>function($file){
                    return true;
                },
                "classSpan"=>function ($upload_data,$file){
                    $span="";

                    $span="file_imagen";

                    return $span;
                },
                
                "insert_file_record"=>function ($id,$upload_data,$article_image_config,$file){

                    $file_name=strip_tags($this->security->xss_clean($upload_data["file_name"]) );
                    $this->load->model("admin/stock/catalog/article/article_model");

                            $data=array(
                                "name"=>$file_name,
                                "article_image_id"=>$id
                                ); 

                    if(!$file_id=$this->article_model->insert_article_image($data))
                    return array('status' => 0, 'msg' => "Hubo un error al insertar imagen","reload"=>true);

                    return array('status' => 1, 'msg' => "Se actualizo el nombre en el registro","reload"=>false,"file_id"=>$file_id);
                },
                "delete_record"=>function ($http_params){

                    $this->load->model("admin/stock/catalog/article/article_model");

                    if(!$this->article_model->delete_article_image($http_params["file_id"],$http_params["id"]))
                    return array('status' => 0, 'msg' => "Hubo un error al eliminar imagen BD","reload"=>true);

                    return array('status' => 1, 'msg' => "Se elimino la imagen de base de datos","reload"=>false,"file_id"=>$file_id);
                },

                "update_record"=>function ($id,$upload_data,$article_image_config,$file){

                    return array('status' => 1, 'msg' => "Se actualizo el nombre en el registro","reload"=>false);
                },
                "friendly_path"=>function ($id,$upload_data,$article_image_config){
                     $path_explode=explode("/",substr(base_url(),0, -1 ));
                     array_pop($path_explode);
                    return implode("/",$path_explode)."/".$article_image_config["path_storage"].$upload_data["file_name"];
                },

                "return"=>array('status' => 1, 'msg' => 'Se subio exitosamente',"reload"=>true),

            ),

// ********************************************************************************************************** 
// ********************************************************************************************************** 
"movie"=>array(
                "ajax"=>1, // cuidado con este porque si copias y pegas este arra() y tu proceso no es por ajax  te retornara en print_r(json_encode( array() ));
                "path"=>$movie_config["path"],
                "process"=>encode_id("movie"),
                "path_storage"=>$movie_config["path"],
                "image_lib"=>1, // TRATAMIENTO DE IMAGEN
                "make_dir"=>function ($id,$movie_config){

                    if(!is_dir($movie_config["upload_path"]))
                     mkdir($movie_config["upload_path"], 0777, true);
                    
                    return array('status' => 1, 'msg' => 'Creado el directorio',"reload"=>true);
                },
                "additional_process"=>function ($upload_data){
                    return array('status' => 1, 'msg' => 'Se inserto en base de datos',"reload"=>true);
                },
                "upload_path"=>( (!empty($pathFileMovie["name"]) and $id )?$pathFileMovie["name"]:$tmp_dir ) ,
                "allowed_types"=>"mpg4|mkv|avi|jpg|png|mp4",
                "max_size"=>"2000000000",
                "fileType"=>function($file){
                    return true;
                },
                "classSpan"=>function ($upload_data,$file){
                    $span="";

                    $span="file_imagen";

                    return $span;
                },
                
                "insert_file_record"=>function ($id,$upload_data,$movie_config,$file){
                    $file_name=strip_tags($this->security->xss_clean($upload_data["file_name"]) );
                    $pathFile_movie_id=strip_tags($this->security->xss_clean($_POST["pathFile"]) );
                    $resolution=(!empty($_POST["resolution"])?strip_tags($this->security->xss_clean($_POST["resolution"])) :"");
                    $disk_space=(!empty($_POST["disk_space"])?strip_tags($this->security->xss_clean($_POST["disk_space"])):"");
                    
                    $this->load->model("cinepixi/movie/movie_model");

                        $data=array(
                            "file_name"  =>$file_name,
                            "resolution" =>$resolution,
                            "path"       =>$pathFile_movie_id,
                            "movie_id"   =>$id
                            ); 

                    if(!$file_id=$this->movie_model->insert_movie_file($data))
                    return array('status' => 0, 'msg' => "Hubo un error al insertar imagen","reload"=>true);

                    return array('status' => 1, 'msg' => "Se actualizo el nombre en el registro","reload"=>false,"file_id"=>$file_id);
                },
                "delete_record"=>function ($http_params){

                    $this->load->model("cinepixi/movie/movie_model");

                    if(!$this->movie_model->delete_movie_file($http_params["file_id"]))
                    return array('status' => 0, 'msg' => "Hubo un error al eliminar imagen BD","reload"=>true);

                    return array('status' => 1, 'msg' => "Se elimino la imagen de base de datos","reload"=>false,"file_id"=>$file_id);
                },

                "update_record"=>function ($id,$upload_data,$movie_config,$file){

                    return array('status' => 1, 'msg' => "Se actualizo el nombre en el registro","reload"=>false);
                },
                "friendly_path"=>function ($id,$upload_data,$movie_config){
                    return $this->base_url.$movie_config["path_storage"].$id."/".$upload_data["file_name"];
                },

                "return"=>array('status' => 1, 'msg' => 'Se subio exitosamente',"reload"=>true),

            ),
// ********************************************************************************************************** 


        );
    return  $config;

    }

    // con el otro metodo para subir imagenes este'puede servir para los dos 
    // metodos :) porque lo configure
    
    public function doUploadFile(){

    // jQueryFileUpload

    $process=""; 

    $http_params=array_merge($_GET,$_POST);
    $http_params   =array(
    "process"=>(!empty($http_params["process"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["process"]) ) ) :""),
    "id"=>(!empty($http_params["id"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["id"]) ) ) :0),
    "file_name"=>(!empty($http_params["file_name"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["file_name"]) ) ) :""),
    "file"=>(!empty($http_params["file"]) ? strip_tags( $this->security->xss_clean( decode_id( $http_params["file"]) ) ) :""),
    );

    extract($http_params);

    $functionConfig=$this->process();

    if(empty($process))
    return;    

    $file_element_name = "file";

    // $file_element_name = 'userfile';
// CREAR DIRECTORIO si el proceso lo dicta
    if(!empty($functionConfig[$process]["make_dir"]) ){

        $response=$functionConfig[$process]["make_dir"]($id,$functionConfig[$process]);

        if(!$response["status"])
        return print_r(json_encode($response));

    } 

    // print_r(json_encode($functionConfig[$process]));


// configuracion para subir imagen
    $config['upload_path']   = $functionConfig[$process]["upload_path"];
    // $config['allowed_types'] = 'xls|xlsx|csv';

    $config['allowed_types'] = $functionConfig[$process]["allowed_types"];
    $config['max_size']      = $functionConfig[$process]["max_size"];
    $config['encrypt_name']  = TRUE;
   

    $this->load->library('upload',$config);
// 
        if (!$this->upload->do_upload($file_element_name))
        {return print_r(json_encode(array('status' => 0, 'msg' => $this->upload->display_errors('', ''),"data"=>$config['upload_path']))); }
        else{
                
                // informacion de la imagen subida 
                $upload_data = $this->upload->data();
        
                if(!empty($functionConfig[$process]["additional_process"]) and !empty($upload_data)):

                        $response=$functionConfig[$process]["additional_process"]($upload_data);
                        
                        if(!$response["status"])
                        return print_r(json_encode($response));
                endif;

                // actualizar el registro el nombre de la imagen
                if(!empty($functionConfig[$process]["insert_file_record"]) and !empty($upload_data) and !empty($id)):

                        $response=$functionConfig[$process]["insert_file_record"]($id,$upload_data,$functionConfig[$process],$file);
                        
                        if(!$response["status"])
                        return print_r(json_encode($response));
                        $file_id=$response["file_id"];

                endif;

                // actualizar el registro el nombre de la imagen
                if(!empty($functionConfig[$process]["update_record"]) and !empty($upload_data) and !empty($id)):

                        $response=$functionConfig[$process]["update_record"]($id,$upload_data,$functionConfig[$process],$file);
                        
                        if(!$response["status"])
                        return print_r(json_encode($response));
                endif;

                // actualizar el registro el nombre de la imagen
                if(!empty($functionConfig[$process]["image_lib"]) and !empty($upload_data)):

                // // tratamiento de imagen
                // $this->load->library('image_lib');
                // $config_img = array();
 
                // $config_img['image_library'] = 'gd2';
                // $config_img['source_image'] = $functionConfig[$process]["path"].$upload_data['file_name'];
                // $config_img['create_thumb'] = FALSE;
                // $config_img['maintain_ratio'] = FALSE;

                // // calcular las dimensiones de la imagen 
                // // alto 810
                // // ancho 436
                // if(!empty($config_img['source_image'])){
                //     $img_size  = getimagesize($config_img['source_image']);
                //     $img_width_new = null;
                //     $img_height_new = null;
                //     $img_w  = $img_size[0];
                //     $img_h  = $img_size[1];

                //     switch ($img_h) {
                //         case $img_h>=1000:
                //             $img_width_new = ( $img_w * 50 ) / 200;
                //             $img_height_new = ( $img_h * 50 ) / 200;
                //             break;
                //         case $img_h>=810:
                //             $img_width_new = ( $img_w * 50 ) / 100;
                //             $img_height_new = ( $img_h * 50 ) / 100;
                //             break;

                //         default:
                //             # code...
                //             break;
                //     }
                // }
                endif;

                // $config_img['width'] = $img_width_new;
                // $config_img['height'] = $img_height_new;

                // ponerle una marca de agua a la foto
                // $config_img['wm_text']          = 'www.pirabook.com';
                // $config_img['wm_type']          = 'text';
                // $config_img['wm_font_path']     = './system/fonts/texb.ttf';
                // $config_img['wm_font_size']     = '90';
                // $config_img['wm_font_color']    = 'ffffff';
                // $config_img['wm_vrt_alignment'] = 'bottom';
                // $config_img['wm_hor_alignment'] = 'center';
                // $config_img['wm_padding']       = '20';
                
                // $this->image_lib->initialize($config_img);
                // $this->image_lib->watermark();
                // $this->image_lib->resize();
                // $this->image_lib->clear();
            }

        echo json_encode(
                array( 
                "status"      =>1,
                "msg"         =>$functionConfig[$process]["return"]["msg"],
                "path_file"   =>$functionConfig[$process]["upload_path"],
                "name"        =>$upload_data["file_name"],
                "name_encode" =>encode_id($upload_data["file_name"]),
                "upload_data" =>$upload_data,
                "classSpan"   =>$functionConfig[$process]["classSpan"]($upload_data,$file),
                "fileType"   =>(!empty($functionConfig[$process]["fileType"]($file))? encode_id( $functionConfig[$process]["fileType"]($file) ):""),
                "friendly_path"=>(!empty($functionConfig[$process]["friendly_path"]($id,$upload_data,$functionConfig[$process]))?$functionConfig[$process]["friendly_path"]($id,$upload_data,$functionConfig[$process]):""),
                "process"=>(!empty($functionConfig[$process]["process"])?$functionConfig[$process]["process"]:""),
                "file_id"=>(!empty($file_id)?encode_id($file_id):""),
                )
                 );
         return;
    }

    public function delete(){

    $this->load->model("vars_system_model");
    $sys=$this->vars_system_model->_vars_system();
    $tmp_dir=$_SERVER['DOCUMENT_ROOT']."/".$sys["storage_tmp"];

    $http_params=array_merge($_GET,$_POST);
    $http_params   =array(
    "process"=>(!empty($http_params["process"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["process"]) ) ) :""),
    "id"=>(!empty($http_params["id"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["id"]) ) ) :""),
    "file_name"=>(!empty($http_params["file_name"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["file_name"]) ) ) :""),
    "file"=>(!empty($http_params["file"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["file"])) ) :""),
    "file_id"=>(!empty($http_params["file_id"]) ? strip_tags( $this->security->xss_clean( decode_id($http_params["file_id"]) ) ) :""),
    );

    extract($http_params);
    
    if(empty($process))
    return;

    $functionConfig=$this->process();
    
    // ELIMINAR DEPENDIENDO SI EL REGISTRO YA ESTA GUARDADO O EL ARCHIVO UN SE ENCUENTRA EN LA CARPETA HTDOCS/TMP
    if(!empty($id)):

        $upload_data_tmp["file_name"]="";
        if(!empty($file_name)){
        $file_name_info=pathinfo($file_name);
        $upload_data_tmp["file_ext"]=".".$file_name_info["extension"];
        }
        @unlink($functionConfig[$process]["path"]."/$id/".$file_name);
        @unlink($functionConfig[$process]["path"].$file_name);

                if(!empty($functionConfig[$process]["update_record"]) and !empty($upload_data_tmp)):

                        $response=$functionConfig[$process]["update_record"]($id,$upload_data_tmp,$functionConfig[$process],$file);
                        
                        if(!$response["status"])
                        return print_r(json_encode($response));
                endif;

                if(!empty($functionConfig[$process]["delete_record"]) and !empty($upload_data_tmp)):

                        $response=$functionConfig[$process]["delete_record"]($http_params);
                        
                        if(!$response["status"])
                        return print_r(json_encode($response));
                endif;

    else:
    @unlink($tmp_dir.$file_name);
    endif;   

    return print_r(json_encode(array('status' => 1, 'msg' => 'Eliminado con exito')));

    }

    public function download_file(){

    $this->load->helper("download");
    $http_params=$_GET;
    // file_path : /opt/lampp/htdocs/proyecto/ pathcompleto
    
    $http_params   =array(
    "name_file"=>(!empty($http_params["name_file"]) ?strip_tags( $this->security->xss_clean( decode_id( $http_params["name_file"]) ) ) :""),
    "file_path"=>(!empty($http_params["file_path"]) ?strip_tags( $this->security->xss_clean( decode_id( $http_params["file_path"]) ) ) :""),
    );
        extract($http_params);
    if(!$name_file or !$file_path)
    return;

        $pathinfo=pathinfo($file_path);
        $extentions=array("php","sql");

    foreach ($extentions as $k => $exten) {
        if($pathinfo["extension"]==$exten)
        { die("No esta permitido descargar este tipo de archivos"); };
    }
    // pr($name_file);
    // pr(file_get_contents($file_path));
    force_download($name_file, file_get_contents($file_path), $set_mime = FALSE);
    echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";

    }

}
?>