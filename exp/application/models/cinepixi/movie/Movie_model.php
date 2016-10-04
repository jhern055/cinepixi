<?php
class Movie_model extends CI_Model{

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
    public function record_same_movie($data,$id){
        $ac=false;

        // if(!empty($id))
        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("cinepixi_movie");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }
        
    public function get_movie_amount($query_search){

    $this->db->select('id');
    $this->db->from("cinepixi_movie");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_movie($start,$end,$query_search){

    $this->db->select("id,name");
    $this->db->from("cinepixi_movie");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
	$q=$this->db->get();

    return $q->result_array();

    }

    public function get_movie_id($id){
    $this->load->model("cinepixi/movie/category/category_model");

        $data  =array(
        "id"          =>"",
        "name"        =>"",
        "category_id" =>"",
        );
        
    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("cinepixi_movie")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    $data["movie_categories"]=$this->category_model->get_cinepixi_category_to_option(false,false);

    return $data;

    }

    public function update_movie($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("cinepixi_movie",$data);

    return $id;
    }

    public function insert_movie($data){
        
        $this->db->insert("cinepixi_movie",$data);
        
    return $this->db->insert_id();
    }

    public function movie_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("cinepixi_movie"))
        return true;
    }

// <Get>
  public function select_option($array){
    $data=array("0"=>"Seleccione");
        foreach ($array as $key => $value)
        $data[$value["id"]]=$value["name"];
    return $data;    
    } 
    // <get movie>
    // Traer el arreglo para usarse con el token input 
    public function get_movies_token_search($var_name=null){

    $data=array();
    $this->db->select('id,name');
    $this->db->from("cinepixi_movie");
    $this->db->order_by('id','desc');

    if($var_name)
    eval($var_name);

    if($q=$this->db->get())
    $data =$q->result_array();

    return $data;

    }
    // <get movie>
    public function get_movies(){

    $this->db->select("id,name");
    $this->db->from("cinepixi_movie");
    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array());   

    } 
    // </get movie>
// </Get>

// <OWN>
    public function select_movie_category($name){

    $data=array("id"=>0);
    $q=$this->db->select("id")
                ->where("name",$name)
                ->from("cinepixi_movie_category")
                ->get();

        foreach ($q->result_array() as $key => $value)
        $data=$value;

    return $data["id"];
    }
    public function insert_movie_category($data){

    $this->db->insert("cinepixi_movie_category",$data);
        
    return $this->db->insert_id();;
    }

    public function insert_update_movie_category_sync($dir,$parentid=0) { 
        
        $k=array();

        $cdir = scandir($dir); 
        foreach ($cdir as $key => $value) {
            if (!in_array($value,array(".",".."))) {

                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    // pr($dir . DIRECTORY_SEPARATOR . $value);
                $before="";
                $before=explode("/",substr($dir . DIRECTORY_SEPARATOR . $value, 1));
                if(count($before)>2){
                array_pop($before);

                if( $name=end($before) and $name!="DATA");
                $parentid=$this->select_movie_category($name);
                
                }

                    $data=array("name"=>$value,"parentid" =>$parentid);
                    $parentid=$this->insert_movie_category($data);
                    $k[$value]=$this->insert_update_movie_category_sync($dir . DIRECTORY_SEPARATOR . $value,$parentid);
                }else{
                    
                    $k[] = $value; 
                    $data=array("name"     =>$value, "parentid" =>$parentid);
                    $this->insert_movie_category($data);
                }
            
            }
        }

   return $k; 

    } 


    // mismo registro
    public function record_same_file($data){
        $ac=false;

        // if(!empty($id))
        // $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("cinepixi_movie_file");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }
    public function insert_movie_file($data){

    $this->db->insert("cinepixi_movie_file",$data);
        
    return $this->db->insert_id();;
    } 

    public function delete_movie_file($id){

        $this->db->where("id",$id);
        if($this->db->delete("cinepixi_movie_file"))
        return true;
    } 
// </OWN>

}
?>