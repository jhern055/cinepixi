<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MenuPrimary_model extends CI_Model{

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
    public function record_same_menuPrimary($data,$id){
        $ac=false;

        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("web_menu_primary");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }    
    
    public function get_menuPrimary_amount($query_search){

    $this->db->select('id');
    $this->db->from('subsidiary');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_menuPrimary($start,$end,$query_search){

    $this->db->select("id,name");
    $this->db->from("web_menu_primary");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
    $q=$this->db->get();

    return $q->result_array();

    }

    public function get_menuPrimary_id($id){

        $data=array(
        "id"=>"",
        "name"=>"",
        "registred_by"=>"",
        "registred_on"=>"",
        "updated_by"=>"",
        "updated_on"=>"",
        );
        
    $q=$this->db->select(implode(",",array_keys($data)))
                ->where("id",$id)
                ->from("web_menu_primary")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    return $data;

    }

    public function update_menuPrimary($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("web_menu_primary",$data);

    return $id;
    }

    public function insert_menuPrimary($data){
        
        $this->db->insert("web_menu_primary",$data);
        
    return $this->db->insert_id();
    }

    public function menuPrimary_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("web_menu_primary"))
        return true;
    }
        // select
    public function get_id_menuPrimary($id=null){

    $this->db->select("id,name");
    $this->db->from("web_menu_primary");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array(),false);   

    }
// </subsidiary> 
}
?>