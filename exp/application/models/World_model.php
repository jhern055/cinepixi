<?php
class World_Model extends CI_Model {
 
     public function select_option($array,$select){
    if(!empty($select))   
    $data=array("0"=>"Seleccione");
        
        if(!empty($array))   
        foreach ($array as $key => $value)
        $data[$value["id"]]=$value["name"];

    if(!empty($data))   
    return $data; 

    }

    public function get_city_token($var_name=null){

    $this->db->select('id,name');
    $this->db->from('w_city');
    $this->db->order_by('id','desc');

    if($var_name)
    eval($var_name);

    if($q=$this->db->get())
    return  $q->result_array();

    }
    public function get_w_city($id=null){

    $this->db->select("id,name");
    $this->db->from("w_city");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array(),false);   

    }

    public function get_country_token($var_name=null){

    $this->db->select('id,name');
    $this->db->from('w_country');
    $this->db->order_by('id','desc');

    if($var_name)
    eval($var_name);

    if($q=$this->db->get())
    return  $q->result_array();

    }

    public function get_w_country($id=null){

    $this->db->select("id,name");
    $this->db->from("w_country");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array(),false);   

    }

    public function get_w_country_text($id=null){

    $this->db->select("id,name");
    $this->db->from("w_country");

    if(!empty($id))
    $this->db->where("id",$id);

    $q=$this->db->get();

        if($q->result_array())
        foreach ($q->result_array() as $key => $value):
            $data=$value;
        endforeach;

    return $data["name"];   

    }

    public function get_state_token($var_name=null){

    $this->db->select('id,name');
    $this->db->from('w_state');
    $this->db->order_by('id','desc');

    if($var_name)
    eval($var_name);

    if($q=$this->db->get())
    return  $q->result_array();

    }

    public function get_w_state($id=null){

    $this->db->select("id,name");
    $this->db->from("w_state");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array(),false);   

    }

    public function get_w_state_text($id=null){

    $this->db->select("id,name");
    $this->db->from("w_state");

    if(!empty($id))
    $this->db->where("id",$id);
    
    $q=$this->db->get();

        if($q->result_array())
        foreach ($q->result_array() as $key => $value):
            $data=$value;
        endforeach;

    return $data["name"];   

    }
    public function get_town_token($var_name=null){

    $this->db->select('id,name');
    $this->db->from('w_town');
    $this->db->order_by('id','desc');

    if($var_name)
    eval($var_name);


    if($q=$this->db->get())
    return  $q->result_array();

    }
    public function get_w_town($id=null){

    $this->db->select("id,name");
    $this->db->from("w_town");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array(),false);   

    }
    
}
?>