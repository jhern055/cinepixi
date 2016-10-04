<?php
class Pirabook_model extends CI_Model{
    
    public $pr;

	public function __construct() {
        parent::__construct();
        $this->user_id=$this->session->userdata("user_id");
        
        $this->pr=$this->load->database("pr",TRUE);
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
    
     public function get_id_categories($id=null){

    $this->pr->select("id,name");
    $this->pr->from("publications_categories");

    if(!empty($id))
    $this->pr->where("id",$id);

    $this->pr->order_by("name","asc");
    $q= $this->pr->get();

    return $this->select_option($q->result_array(),false);   

    }   
     public function get_id_sub_categories($id=null){

    $this->pr->select("id,name");
    $this->pr->from("publications_subcategories");

    if(!empty($id))
    $this->pr->where("id",$id);

    $this->pr->order_by("name","asc");
    $q= $this->pr->get();

    return $this->select_option($q->result_array(),false);   

    }   
    
}
?>