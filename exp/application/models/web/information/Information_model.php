<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Information_model extends CI_Model{

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

// <subsidiary> 
    public function record_same_information($data,$id){
        $ac=false;

        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("web_information");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }    
    
    public function get_information_amount($query_search){

    $this->db->select('id');
    $this->db->from('subsidiary');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_information($start,$end,$query_search){

    $this->db->select("id,name");
    $this->db->from("web_information");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
    $q=$this->db->get();

    return $q->result_array();

    }

    public function get_information_id($id){

        $data=array(
        "id"           =>"",
        "name"         =>"",
        "description"  =>"",
        "registred_by" =>"",
        "registred_on" =>"",
        "updated_by"   =>"",
        "updated_on"   =>"",
        );
        
    $q=$this->db->select(implode(",",array_keys($data)))
                ->where("id",$id)
                ->from("web_information")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    return $data;

    }

    public function update_information($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("web_information",$data);

    return $id;
    }

    public function insert_information($data){
        
        $this->db->insert("web_information",$data);
        
    return $this->db->insert_id();
    }

    public function information_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("web_information"))
        return true;
    }
        // select
    public function get_id_information($id=null){

    $this->db->select("id,name");
    $this->db->from("web_information");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array(),false);   

    }
// </subsidiary> 
}
?>