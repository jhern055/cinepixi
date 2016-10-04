<?php
class Category_model extends CI_Model{

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
    public function record_same_category($data,$id){
        $ac=false;

        // if(!empty($id))
        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("cinepixi_movie_category");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }
        
    public function get_category_amount($query_search){

    $this->db->select('id');
    $this->db->from('cinepixi_movie_category');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_category($start,$end,$query_search){

    $this->db->select("id,name");
    $this->db->from("cinepixi_movie_category");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
	$q=$this->db->get();

    return $q->result_array();

    }

    public function get_category_id($id,$category_subsidiary=null){

        $data=array(
        "id"=>"",
        "name"=>"",
        );
        
    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("cinepixi_movie_category")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    return $data;

    }

    public function update_category($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("cinepixi_movie_category",$data);

    return $id;
    }

    public function insert_category($data){
        
        $this->db->insert("cinepixi_movie_category",$data);
        
    return $this->db->insert_id();
    }

    public function category_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("cinepixi_movie_category"))
        return true;
    }

// <Get>
  public function select_option($array){
    $data=array("0"=>"Seleccione");
        foreach ($array as $key => $value)
        $data[$value["id"]]=$value["name"];
    return $data;    
    } 
    // <get category>
    // Traer el arreglo para usarse con el token input 
    public function get_cinepixi_category_token_search($var_name=null){

    $data=array();
    $this->db->select('*');
    $this->db->from('cinepixi_movie_category');
    $this->db->order_by('id','desc');

    if($var_name)
    eval($var_name);

    if($q=$this->db->get())
    $data =$q->result_array();

    return $data;

    }
// </Get>

    public function get_cinepixi_category_to_option($flip=null,$id){

    $data=array(0=>"Seleccione un Padre");
    $this->db->select('id,name');

    if(!empty($id))
    $this->db->where_not_in("id",$id);

    $this->db->from('cinepixi_movie_category');
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

}
?>