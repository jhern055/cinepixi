<?php
class Pathfile_model extends CI_Model{

	public function __construct() {
        parent::__construct();
        $this->user_id=$this->session->userdata("user_id");
    }

    public function m_name($uri_string){

    $data=array();
    $q=$this->db->select("id,name,link")
                ->where("link",$uri_string)
                ->from("modules")
                ->get();

        if($q->result_array())
        foreach ($q->result_array() as $key => $value):
            $data=$value;
        endforeach;

    return $data;
    }

    // mismo registro
    public function record_same_pathFile($data,$id){
        $ac=false;

        // if(!empty($id))
        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("cinepixi_pathFile");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }
        
    public function get_pathFile_amount($query_search){

    $this->db->select('id');
    $this->db->from('cinepixi_pathFile');

    $this->db->where("parentid",0);

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_pathFile($start,$end,$query_search){

    $this->db->select("id,name,link,parentid,real_path,file");
    $this->db->from("cinepixi_pathFile");

    $this->db->where("parentid",0);
    // $this->db->where("id",1);

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
	$q=$this->db->get();

    $data=$q->result_array();

        function detect_son($son){
            $CI=& get_instance();
            // $response[]=$rb;
            $response=array();

            foreach ($son as $kb => $rb) {
                if(!empty($rb["file"])){

            //     // if($compare["id"] and $compare["id"]==$rb["parentid"]){
                    
            //         // $this->db->select("id,name,link,parentid,real_path,file");
            //         // $this->db->from("cinepixi_pathFile");
            //         // $this->db->where("parentid",$rb["parentid"]);     
            //         // $q=$this->db->get();

            //         // detect_son($q->result_array(),$son);
            //         // pr($rb);
            //         // $CI->db->select("id,name,link,parentid,real_path,file");
            //         // $CI->db->from("cinepixi_pathFile");
            //         // $CI->db->where("parentid",$rb["id"]);     
            //         // $q=$CI->db->get();
            //         // $_SESSION["SE"]="";
            //         // $_SESSION["SE"][$compare["id"]][]=$rb;
            //             // detect_son($q->result_array(),null);
            //         // $son[]=$rb;
                    $response[]=$rb;
                }
                else{
                    $response[]=$rb;

                    $CI->db->select("id,name,link,parentid,real_path,file");
                    $CI->db->from("cinepixi_pathFile");
                    $CI->db->where("parentid",$rb["id"]);     
                    $q=$CI->db->get();
                    // $response=array_merge($q->result_array(),$response);
                    $response=array_merge(detect_son($q->result_array()),$response);
// 
                }

            }

            return $response;
        }


    $this->load->model("dynamic_menu_model");

    foreach ($data as $key => &$value) {
        
        $this->db->select("id,name,link,parentid,real_path,file");
        $this->db->from("cinepixi_pathFile");
        $this->db->where("parentid",$value["id"]);     
        $q=$this->db->get();
        $son=$q->result_array();
        $data[$key]["pre_menu"]="";
        $data[$key]["pre_menu"]=detect_son($son);
        // pr(detect_son($son,$value));
            $data[$key]["pre_menu"]=array_merge(
                    array(
                        0=>array("id"=>$value["id"] ,
                                "name"=>$value["name"] ,
                                "link"=>$value["link"] ,
                                "parentid"=>$value["parentid"] ,
                                "real_path"=>$value["real_path"] ,
                                "file"=>$value["file"] ,
                                )
                        ),
                    $data[$key]["pre_menu"]);

        $menu = array('menus' => array(), 'parent_menus' => array() );

        if($data[$key]["pre_menu"])
        foreach ($data[$key]["pre_menu"] as $keyb => $valueb) {
            if(!empty($valueb['id'])){
            $menu['menus'][$valueb['id']] = $valueb;
            $menu['parent_menus'][$valueb['parentid']][] = $valueb['id'];    
            }        
        }

        // $menu_html="";
        // $menu_html.='<link rel="stylesheet" type="text/css" href="'.base_url().'css/dynamic_menu_favorite/styles.css" />';
        // $menu_html.='<script type="text/javascript" src="'.base_url().'css/dynamic_menu_favorite/script.js"></script>';
        // $menu_html.=$this->load->buildMenu(0, $menu);

        // $data[$key]["menu_html"]=$this->load->buildMenu(0, $menu);
        $data[$key]["menu_html"]=$this->load->buildMenu($value["parentid"], $menu);
           
    }

// <own >

    // pr($menu_html);
// </own >
    return $data;

    }

    public function get_pathFile_id($id,$pathFile_subsidiary=null){

        $data=array(
        "id"=>"",
        "name"=>"",
        );
        
    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("cinepixi_pathFile")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    return $data;

    }

    public function update_pathFile($data,$id,$path){
        
        if(!empty($id))
        $this->db->where("id",$id);

        if(!empty($path))
        $this->db->where("name",$path);

        $this->db->update("cinepixi_pathFile",$data);

    return $id;
    }

    public function insert_pathFile($data){
        
        $this->db->insert("cinepixi_pathFile",$data);
        
    return $this->db->insert_id();
    }

    public function pathFile_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("cinepixi_pathFile"))
        return true;
    }

// <Get>
  public function select_option($array){
    $data=array("0"=>"Seleccione");
        foreach ($array as $key => $value)
        $data[$value["id"]]=$value["name"];
    return $data;    
    } 
    // <get pathFile>
    // Traer el arreglo para usarse con el token input 
    public function get_cinepixi_pathFile_token_search($var_name=null){

    $data=array();
    $this->db->select('id,name');
    $this->db->from('cinepixi_pathFile');
    $this->db->order_by('id','desc');

    if($var_name)
    eval($var_name);

    if($q=$this->db->get())
    $data =$q->result_array();
    // $data_pre =$q->result_array();
    
    if(!empty($data))
    foreach ($data as $k=>&$value):
        $data[$k]["name"]=str_replace(array("\\ ","\\"), " ", $value["name"]);
        // $data[$k]["name"]=str_replace(array("\\ ","\\","\(","\)"), " ", $value["name"]);
    endforeach;
    
    return $data;

    }
// </Get>

    public function get_cinepixi_pathFile_to_option($flip=null,$id){

    // $data=array(0=>"Seleccione un Padre");
    $this->db->select('id,name');

    if(!empty($id))
    $this->db->where_not_in("id",$id);

    $this->db->from('cinepixi_pathFile');
    $this->db->order_by('id','desc');

    if($q=$this->db->get())
    foreach ($q->result_array() as $row) {
        if($flip)
        $data[$row["name"]]=$row["id"];
        else
        $data[$row["id"]]=$row["name"];
    }
    return $data;

    }
 
 // <OWN>
    public function pathFile_PATHS($dir,$file=null) { 
        
        $k=array();

        $cdir = scandir($dir); 
        foreach ($cdir as $key => $value) {
            if (!in_array($value,array(".",".."))):

                if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
                $k[$value]=$this->pathFile_PATHS($dir . DIRECTORY_SEPARATOR . $value);
                else if($file)
                    $k[] = $value; 
                
            
            endif;
        }

   return $k; 

    } 
    public function sync_same_pathFile($pathFile){
        $ac=false;
        $data=array();

        $this->db->where("name",$pathFile);

        $row=$this->db->get("cinepixi_pathFile");
        
        if($row->num_rows()){
        $ac=true;    
         
            foreach ($row->result_array() as $key => $value):
                $data=$value;
            endforeach;
        }


        return $data;
    }  

    // mismo registro
    public function record_same_file($data){
        $ac=false;

        // if(!empty($id))
        // $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("cinepixi_file");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }

    public function update_movie_file($data,$id){

    $this->db->where($id);
    $this->db->update("cinepixi_file",$data);
    
    return $id;
    } 

    public function insert_movie_file($data){

    $this->db->insert("cinepixi_file",$data);
        
    return $this->db->insert_id();;
    } 

    public function delete_movie_file($id){

        $this->db->where("id",$id);
        if($this->db->delete("cinepixi_file"))
        return true;
    } 
 // </ OWN>

}
?>