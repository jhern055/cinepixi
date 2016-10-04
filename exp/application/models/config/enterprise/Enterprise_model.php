<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enterprise_model extends CI_Model{

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

// <enterprise> 
    public function record_same_enterprise($data,$id){
        $ac=false;

        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("enterprise");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }    
    
    public function get_enterprise_amount($query_search){

    $this->db->select('id');
    $this->db->from('enterprise');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_enterprise($start,$end,$query_search){

        // <own>
    $this->load->model("vars_system_model");
    $sys =$this->vars_system_model->_vars_system();
        // </own>

        $data[0]=array(
        "id"           =>"1",
        "name"           =>$sys["enterprise_fiscal"]["name"],
        "rfc"            =>$sys["enterprise_fiscal"]["rfc"],
        "country"        =>$sys["enterprise_fiscal"]["country"],
        "state"          =>$sys["enterprise_fiscal"]["state"],
        "city"           =>$sys["enterprise_fiscal"]["city"],
        "town"           =>$sys["enterprise_fiscal"]["town"],
        "colony"         =>$sys["enterprise_fiscal"]["colony"],
        "street"         =>$sys["enterprise_fiscal"]["street"],
        "inside_number"  =>$sys["enterprise_fiscal"]["inside_number"],
        "outside_number" =>$sys["enterprise_fiscal"]["outside_number"],
        "zip_code"       =>$sys["enterprise_fiscal"]["zip_code"],
        "cedule"         =>$sys["enterprise_fiscal"]["cedule"],
        "logo"           =>$sys["enterprise_fiscal"]["logo"],
        "tax_regime"     =>$sys["enterprise_fiscal"]["tax_regime"],
        "email"           =>$sys["enterprise_fiscal"]["email"],
        "phone"           =>$sys["enterprise_fiscal"]["phone"],

        );
        return $data;

    }

    public function get_enterprise_id(){
        // <own>
    $this->load->model("vars_system_model");
    $sys =$this->vars_system_model->_vars_system();
        // </own>

        $data=array(
        "name"           =>(!empty($sys["enterprise_fiscal"]["name"])?$sys["enterprise_fiscal"]["name"]:""),
        "rfc"            =>(!empty($sys["enterprise_fiscal"]["rfc"])?$sys["enterprise_fiscal"]["rfc"]:""),
        "country"        =>(!empty($sys["enterprise_fiscal"]["country"])?$sys["enterprise_fiscal"]["country"]:""),
        "state"          =>(!empty($sys["enterprise_fiscal"]["state"])?$sys["enterprise_fiscal"]["state"]:""),
        "city"           =>(!empty($sys["enterprise_fiscal"]["city"])?$sys["enterprise_fiscal"]["city"]:""),
        "town"           =>(!empty($sys["enterprise_fiscal"]["town"])?$sys["enterprise_fiscal"]["town"]:""),
        "colony"         =>(!empty($sys["enterprise_fiscal"]["colony"])?$sys["enterprise_fiscal"]["colony"]:""),
        "street"         =>(!empty($sys["enterprise_fiscal"]["street"])?$sys["enterprise_fiscal"]["street"]:""),
        "inside_number"  =>(!empty($sys["enterprise_fiscal"]["inside_number"])?$sys["enterprise_fiscal"]["inside_number"]:""),
        "outside_number" =>(!empty($sys["enterprise_fiscal"]["outside_number"])?$sys["enterprise_fiscal"]["outside_number"]:""),
        "zip_code"       =>(!empty($sys["enterprise_fiscal"]["zip_code"])?$sys["enterprise_fiscal"]["zip_code"]:""),
        "cedule"         =>(!empty($sys["enterprise_fiscal"]["cedule"])?$sys["enterprise_fiscal"]["cedule"]:""),
        "logo"           =>(!empty($sys["enterprise_fiscal"]["logo"])?$sys["enterprise_fiscal"]["logo"]:""),
        "tax_regime"     =>(!empty($sys["enterprise_fiscal"]["tax_regime"])?$sys["enterprise_fiscal"]["tax_regime"]:""),
        "email"          =>(!empty($sys["enterprise_fiscal"]["email"])?$sys["enterprise_fiscal"]["email"]:""),
        "emailtax_regime"=>(!empty($sys["enterprise_fiscal"]["emailtax_regime"])?$sys["enterprise_fiscal"]["emailtax_regime"]:""),
        "phone"          =>(!empty($sys["enterprise_fiscal"]["phone"])?$sys["enterprise_fiscal"]["phone"]:""),

        );
        
    return $data;

    }

    public function update_enterprise($data){
        
    $data_var=$this->get_enterprise_id();
        
        $textProc=(string) "array(
            'name'=>'".$data["name"]."',
            'rfc'=>'".$data["rfc"]."',
            'country'=>'".$data["country"]."',
            'state'=>'".$data["state"]."',
            'city'=>'".$data["city"]."',
            'town'=>'".$data["town"]."',
            'colony'=>'".$data["colony"]."',
            'street'=>'".$data["street"]."',
            'inside_number'=>'".$data["inside_number"]."',
            'outside_number'=>'".$data["outside_number"]."',
            'zip_code'=>'".$data["zip_code"]."',
            'logo'=>'".(!empty($data_var["logo"])?$data_var["logo"]:$data["logo"])."',
            'cedule'=>'".(!empty($data_var["cedule"])?$data_var["cedule"]:$data["cedule"])."',
            'email'=>'".$data["email"]."',
            'tax_regime'=>'".$data["tax_regime"]."',
            'phone'=>'".$data["phone"]."',
            )";

        $this->db->where("name","enterprise_fiscal");
        $this->db->set('value', $textProc); 
        $this->db->update("_vars_system");
    }
    public function update_enterprise_file($data){

    $data_var=$this->get_enterprise_id();
    $this->load->model("vars_system_model");
    $sys=$this->vars_system_model->_vars_system();
    $file_name_cedule="";
    $file_name_logo="";
    $filePath=APPPATH.$sys["storage"]["enterprise_fiscal"];

    if(empty($data["cedule"]))
    $data["cedule"]="";

    if(empty($data["logo"]))
    $data["logo"]="";

    if(!empty($data_var["cedule"])):
    $file_name_cedule=file_exists($filePath."/enterprise_fiscal/".$data_var["cedule"]);
    endif;

    if(!empty($data_var["logo"])):
    $file_name_logo=file_exists($filePath."/enterprise_fiscal/".$data_var["logo"]);
    endif;


        $textProc=(string) "array(
            'name'=>'".$data_var["name"]."',
            'rfc'=>'".$data_var["rfc"]."',
            'country'=>'".$data_var["country"]."',
            'state'=>'".$data_var["state"]."',
            'city'=>'".$data_var["city"]."',
            'town'=>'".$data_var["town"]."',
            'colony'=>'".$data_var["colony"]."',
            'street'=>'".$data_var["street"]."',
            'inside_number'=>'".$data_var["inside_number"]."',
            'outside_number'=>'".$data_var["outside_number"]."',
            'zip_code'=>'".$data_var["zip_code"]."',
            'logo'=>'".((!empty($data["logo"]) or !$file_name_logo)?$data["logo"]:$data_var["logo"])."',
            'cedule'=>'".((!empty($data["cedule"]) or !$file_name_cedule)?$data["cedule"]:$data_var["cedule"])."',
            'email'=>'".$data_var["email"]."',
            'tax_regime'=>'".$data_var["tax_regime"]."',
            'phone'=>'".$data_var["phone"]."',
            )";

        $this->db->where("name","enterprise_fiscal");
        $this->db->set('value', $textProc); 
        if($this->db->update("_vars_system"))
        return true;
    }

// </enterprise> 
}
?>