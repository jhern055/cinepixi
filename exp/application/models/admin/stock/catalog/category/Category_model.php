<?php

class Category_model extends CI_Model{

    public  $registred_by;
    public  $now;

	public function __construct() {
        parent::__construct();

    $this->now = date("Y-m-d H:i:s");
    $this->registred_by=$this->security->xss_clean($this->session->userdata("user_id"));

    }

    public function get_id_category_link($name_category){

    $data=array("id"=>"");
    $this->db->select('id,name');
    $this->db->from('category');
    $this->db->where('name',str_replace("-"," ", $name_category));

    if($q=$this->db->get())
    foreach ($q->result_array() as $key => $value) 
    $data=$value;
        
    return $data["id"];

    }

    public function get_categories_token_search($var_name=null){

    $this->db->select('id,name');
    $this->db->from('category');
    $this->db->where('link!=',"");
    $this->db->order_by('id','desc');

    if($var_name)
    eval($var_name);

    if($q=$this->db->get())
    return  $q->result_array();

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

// <category> 
    public function get_category_amount($query_search){

    $this->db->select('id');
    $this->db->from('category');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_category($start,$end,$query_search){

    $this->db->select("id,name");
    $this->db->from("category");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
    $q=$this->db->get();

    return $q->result_array();

    }

    public function get_category_id($id){

    $data=array(
    "id"    =>"",
    "name"  =>"",
    "parentid"  =>"",
    );

    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("category")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value):
    $value["category_parentid"]=$this->get_category_to_option(false,$value["id"]);
        


    $data=$value;
    endforeach;
    
        if(empty($data["category_parentid"]))
        $data["category_parentid"]=$this->get_category_to_option(false,false);

    return $data;

    }

    // mismo registro
    public function record_same_category($data,$id){
        $ac=false;

        // if(!empty($id))
        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("category");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }

    public function update_category($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("category",$data);

    return $id;
    }

    public function insert_category($data){
        
        $this->db->insert("category",$data);
        
    return $this->db->insert_id();
    }

    public function category_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("category"))
        return true;
    }
// </category>  

    public function get_category_text($id){

    $data["name"]="";
    $this->db->select('name');
    $this->db->where('id',$id);
    $this->db->from('category');

    $q=$this->db->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $row)
    $data=$row;

    return $data["name"];
    }

    public function get_category_to_option($flip=null,$id){

    $data=array(0=>"Seleccione un Padre");
    $this->db->select('id,name');

    if(!empty($id))
    $this->db->where_not_in("id",$id);

    $this->db->from('category');
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

    // trear el registro
    public function get_category_by_id($id){

        $data=array();
        $this->db->select('*');
        $this->db->from('category');
        $this->db->where('id',$id);
        $this->db->limit(1);
        $q=$this->db->get();
        $data = $q->result_array();

        if($data)
        return $data[0];
    }

// <article_category>

    // mismo registro
    public function is_there_article_category($data){
        $ac=false;

        $this->db->where($data);
        $row=$this->db->get("article_category");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }

    public function get_article_category($id){

    $data_r=array();
    $q=$this->db->select("article_id,category_id")
                ->where("article_id",$id)
                ->from("article_category")
                ->get();

    if($data=$q->result_array())
    foreach ($data as $key => $value):
        $data_r[$value["category_id"]]["article_id"]=$value["article_id"];
        $data_r[$value["category_id"]]["category_id"]=$value["category_id"];
    endforeach;

    return $data_r;

    }

    public function insert_article_category($data){

    $this->db->insert("article_category",$data);
    
    }

    public function delete_article_category($data){

        $this->db->where($data);
        if($this->db->delete("article_category"))
        return true;

    }

// </article_category>

}