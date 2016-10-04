<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subsidiary_model extends CI_Model{

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
    public function record_same_subsidiary($data,$id){
        $ac=false;

        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("subsidiary");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }    
    
    public function get_subsidiary_amount($query_search){

    $this->db->select('id');
    $this->db->from('subsidiary');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_subsidiary($start,$end,$query_search){

    $this->db->select("id,name");
    $this->db->from("subsidiary");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
    $q=$this->db->get();

    return $q->result_array();

    }

    public function get_subsidiary_id($id){

        $data=array(
        "id"=>"",
        "name"=>"",
        "country"=>"",
        "state"=>"",
        "city"=>"",
        "colony"=>"",
        "location"=>"",
        "street"=>"",
        "outside_number"=>"",
        "inside_number"=>"",
        "zip_code"=>"",
        "reference"=>"",
        "website"=>"",
        "email"=>"",
        "phone"=>"",
        "contact"=>"",
        "registred_by"=>"",
        "registred_on"=>"",
        "updated_by"=>"",
        "updated_on"=>"",
        );
        
                // ->where('w_state.name as state_text', $userid);
    // $q=$this->db->select(implode(",",array_keys($data)))
    $q=$this->db->select("subsidiary.id,subsidiary.name,subsidiary.country,subsidiary.state,subsidiary.city,subsidiary.colony,subsidiary.location,subsidiary.street,subsidiary.outside_number,subsidiary.inside_number,subsidiary.zip_code,subsidiary.reference,subsidiary.website,subsidiary.email,subsidiary.phone,subsidiary.contact,subsidiary.registred_by,subsidiary.registred_on,subsidiary.updated_by,subsidiary.updated_on
")
                ->where("subsidiary.id",$id)
                ->from("subsidiary")
                ->select('w_state.name as state_text')
                ->join("w_state","w_state.id = subsidiary.state","left")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    return $data;

    }

    public function update_subsidiary($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("subsidiary",$data);

    return $id;
    }

    public function insert_subsidiary($data){
        
        $this->db->insert("subsidiary",$data);
        
    return $this->db->insert_id();
    }

    public function subsidiary_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("subsidiary"))
        return true;
    }
        // select
    public function get_id_subsidiary($id=null){

    $this->db->select("id,name");
    $this->db->from("subsidiary");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array(),false);   

    }
// </subsidiary> 
}
?>