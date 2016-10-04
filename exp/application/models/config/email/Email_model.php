<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_model extends CI_Model{

    public $registred_by;
    public $now;

    public function __construct() {
        parent::__construct();
        $this->registred_by=$this->security->xss_clean($this->session->userdata("user_id"));
        $this->now = date("Y-m-d H:i:s");
        $this->user_id=$this->session->userdata("user_id");


    }

    public function emails_sent($source_module,$id_record,$emailExplode){

        $data["number_of_times"]="";
        $this->db->select("number_of_times");
        $this->db->where('id_record', $id_record);
        $this->db->where('source_module', $source_module);

        $this->db->where('registred_by', $this->registred_by);

        $this->db->from("emails_sent");
        $this->db->limit(1);
        $q=$this->db->get();

        // return $this->db->last_query();

        if($q->num_rows()){

        foreach ($q->result_array() as $key => $row)
        $data=$row;

        $data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);
        }
        else
        $data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now);
        
        $number_of_times=array(
            'number_of_times' => (!empty($data["number_of_times"])?$data["number_of_times"]+1:1),
            'source_module' => $source_module,
            'id_record' => $id_record,
            'emails' => (!empty($emailExplode)?implode(",", $emailExplode):""),
             );
        $number_of_times=array_merge($data_depend,$number_of_times);

        if($q->num_rows()){

        $this->db->where('registred_by', $this->registred_by);

        $this->db->where('source_module', $source_module);
        $this->db->where('id_record', $id_record);
        $this->db->update('emails_sent', $number_of_times); 

        }else
        $this->db->insert('emails_sent', $number_of_times); 

        return true;
    }


    public function get_dad_email($source_module,$id_record){

        $data=array();
        $this->load->model("config/config_model");

        $this->db->where('source_module',$source_module);
        $this->db->where('id_record',$id_record);
        $this->db->from('emails_sent');

        if($q=$this->db->get())
        $data=$q->result_array();
        
        if($data)
        foreach ($data as $key => $row) {
            if(!empty($row["updated_by"]))
            $data[$key]["registred_name"]=$this->config_model->get_user_id($row["updated_by"]);
            else
            $data[$key]["registred_name"]=$this->config_model->get_user_id($row["registred_by"]);
        }
        
        return $data;
    }

    public function get_data($q){
        $data=array();
        if ($q->num_rows() > 0) {
            foreach ($q->result_array() as $row) {
                $data[$row["id"]] = $row;
            }
            return $data;
        }
        return false;
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

// <email> 
    public function record_same_email($data,$id){
        $ac=false;

        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("config_email");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }    
    
    public function get_email_amount($query_search){

    $this->db->select('id');
    $this->db->from("config_email");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_email($start,$end,$query_search){

    $this->db->select("id,name");
    $this->db->from("config_email");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
    $q=$this->db->get();

    return $q->result_array();

    }

    public function get_email_id($id){

        $data=array(
        "id"           =>"",
        "name"         =>"",
        "apply_to"     =>"",
        "host"         =>"",
        "port"         =>"",
        "username"     =>"",
        "userpassword" =>"",
        "ssl_enabled"  =>"",
        "comment"      =>"",
        "quantity"     =>"",
        "massive"      =>"",
        "registred_by" =>"",
        "registred_on" =>"",
        "updated_by"   =>"",
        "updated_on"   =>"",
        );

    $q=$this->db->select(implode(",",array_keys($data)))
                ->where("id",$id)
                ->from("config_email")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    return $data;

    }

    public function update_email($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("config_email",$data);

    return $id;
    }

    public function insert_email($data){
        
        $this->db->insert("config_email",$data);
        
    return $this->db->insert_id();
    }

    public function email_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("config_email"))
        return true;
    }
        // select
    public function get_id_email($id=null){

    $this->db->select("id,name");
    $this->db->from("config_email");

    if(!empty($id))
    $this->db->where("id",$id);

    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array(),false);   

    }
// </email> 

    // traer la configuracion del emaik
    public function get_email_apply_to($document_type){

        $data=array(
        "id"           =>"",
        "name"         =>"",
        "apply_to"     =>"",
        "host"         =>"",
        "port"         =>"",
        "username"     =>"",
        "userpassword" =>"",
        "ssl_enabled"  =>"",
        "comment"      =>"",
        "quantity"     =>"",
        "massive"      =>"",
        "registred_by" =>"",
        "registred_on" =>"",
        "updated_by"   =>"",
        "updated_on"   =>"",
        );

    $q=$this->db->select(implode(",",array_keys($data)))
                ->like("apply_to",$document_type)
                ->from("config_email")
                ->get();

    // return print_r($this->db->last_query());

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    return $data;

    }
}
?>