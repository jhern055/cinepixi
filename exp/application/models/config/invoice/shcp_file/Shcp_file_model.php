<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shcp_file_model extends CI_Model{

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

    if(!empty($data))
    return $data;
    }

    public function select_option($array,$select){
    if(!empty($select))   
    $data=array("0"=>"Seleccione");
        
        if(!empty($array))   
        foreach ($array as $key => $value)
        $data[$value["id"]]=$value["name"];

    if(!empty($data))   
    return $data; 

    }
    
// <shcp_file> 
    public function record_same_shcp_file($data,$id){
        $ac=false;

        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("shcp_file");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }    
    
    public function get_shcp_file_amount($query_search){

    $this->db->select('id');
    $this->db->from('shcp_file');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_shcp_file($start,$end,$query_search){

    $this->db->select("id,name");
    $this->db->from("shcp_file");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
    $q=$this->db->get();

    return $q->result_array();

    }

    public function get_shcp_file_id($id){

        $data=array(
        "id"=>"",
        "name"=>"",
        "file_cer"=>"",
        "file_key"=>"",
        "password"=>"",
        );
        
    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("shcp_file")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    return $data;

    }

    public function update_shcp_file($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("shcp_file",$data);

    return $id;
    }

    public function insert_shcp_file($data){
        
        $this->db->insert("shcp_file",$data);
        
    return $this->db->insert_id();
    }

    public function shcp_file_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("shcp_file"))
        return true;
    }
// </shcp_file> 
    
}
?>