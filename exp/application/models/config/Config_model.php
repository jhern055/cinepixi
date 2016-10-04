<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_model extends CI_Model{

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
    
    public function get_id_subsidiary($id=null){

    $this->db->select("id,name");
    $this->db->from("subsidiary");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array(),false);   

    }

    public function get_type_of_currency($id=null){

    $this->db->select("id,currency as name,concept,reference");
    $this->db->from("type_of_currency");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("id","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array(),false);   

    }

    public function type_of_currency_id($id){

        $data=array(
            "id"=>"",
            "currency"=>"",
            "concept"=>"",
            "reference"=>"",
            "registred_by"=>"",
            "registred_on"=>"",
            "updated_by"=>"",
            "updated_on"=>"",
            );
        $this->db->select(implode(",", array_keys($data)));
        $this->db->where("id",$id);
        $this->db->from("type_of_currency");
        $q=$this->db->get();

        if($q->result_array())
        foreach ($q->result_array() as $key => $value)
        $data=$value;

    return $data;
    }
    public function get_user_id($id=null){

    $data=array();
    $this->db->select("id,nickname");
    $this->db->from("users");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("id","asc");

    if($q= $this->db->get())
    foreach ($q->result_array() as $key => $row)
    $data=$row;
    
    if(!empty($data["nickname"]))
    return $data["nickname"];   

    }
   
    public function get_id_shcp_file($id=null){

    $this->db->select("id,name");
    $this->db->from("shcp_file");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array(),true);   

    } 
        // select
    public function get_id_pac($id=null){

    $this->db->select("id,name");
    $this->db->from("pac");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array(),true);   

    }
}
?>