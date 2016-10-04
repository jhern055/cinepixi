<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pac_model extends CI_Model{

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
// <pac> 
    public function record_same_pac($data,$id){
        $ac=false;

        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("pac");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }    
    
    public function get_pac_amount($query_search){

    $this->db->select('id');
    $this->db->from('pac');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_pac($start,$end,$query_search){

    $this->db->select("id,name");
    $this->db->from("pac");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
    $q=$this->db->get();

    return $q->result_array();

    }

    public function get_pac_id($id){

        $data=array(
        "id"=>"",
        "name"=>"",
        "user"=>"",
        "password"=>"",
        );
        
    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("pac")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    return $data;

    }

    public function update_pac($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("pac",$data);

    return $id;
    }

    public function insert_pac($data){
        
        $this->db->insert("pac",$data);
        
    return $this->db->insert_id();
    }

    public function pac_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("pac"))
        return true;
    }
// </pac> 
    
}
?>