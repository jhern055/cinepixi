<?php
class Provider_model extends CI_Model{

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

    return $data;
    }

// <provider> 
    // Traer el arreglo para usarse con el token input 
    // <own>
    public function get_providers(){

    $this->db->select("id,name");
    $this->db->from("provider");
    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array());   

    } 
    public function get_providers_token_search($var_name=null){

    $data=array();
    $this->db->select('id,name');
    $this->db->from('provider');
    $this->db->order_by('id','desc');

    if($var_name)
    eval($var_name);

    if($q=$this->db->get())
    $data=$q->result_array();

        foreach ($data as $key => $row) {
            # code...
        if(!empty($row["id"]))
        $data[$key]["subsidiaries"]=$this->get_provider_subsidiaries($row["id"]);

        }


    return $data;
    }
    // </own>
    public function get_provider_amount($query_search){

    $this->db->select('id');
    $this->db->from('provider');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_provider($start,$end,$query_search){

    $this->db->select("id,name");
    $this->db->from("provider");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
    $q=$this->db->get();

    return $q->result_array();

    }

    public function get_provider_id($id,$provider_subsidiary=null){

    $data=array(
                "id"=>"",
                "name"=>"",
                "rfc"=>""
                );

    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("provider")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;

    if(!empty($provider_subsidiary)):
    $data["subsidiary"]=$this->get_provider_subsidiary_id($provider_subsidiary);
    else:
    $data["provider_subsidiaries"]=$this->get_provider_subsidiaries($data["id"]);
    endif;

    return $data;

    }

    // mismo registro
    public function record_same_provider($data,$id){
        $ac=false;

        // if(!empty($id))
        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("provider");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }

    public function update_provider($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("provider",$data);

    return $id;
    }

    public function insert_provider($data){
        
        $this->db->insert("provider",$data);
        
    return $this->db->insert_id();
    }

    public function provider_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("provider"))
        return true;
    }
// </provider>     

// <Get>
    public function select_option($array){
    $data=array("0"=>"Seleccione");
        foreach ($array as $key => $value)
        $data[$value["id"]]=$value["name"];
    return $data;    
    } 
// </Get>

 public function get_provider_subsidiaries($provider,$select=null){

    $q=$this->db->select("*")
                ->where("fk_provider",$provider)
                ->from("provider_subsidiary")
                ->get();

        if($q->result_array())
        $data=$q->result_array();
        else
        $data=array();

    if(!empty($select))
    return $this->select_option($q->result_array());   

    return $data;

    }

    public function get_provider_subsidiary_id($provider_subsidiary){
        
    $q=$this->db->select("*")
                ->where("id",$provider_subsidiary)
                ->from("provider_subsidiary")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    $this->load->model("world_model");
    if(!empty($data["state"]))
    $data["state_text"]=$this->world_model->get_w_state_text($data["state"]);  

    if(!empty($data["country"]))
    $data["country_text"]=$this->world_model->get_w_country_text($data["country"]);  
         
    return $data;
    }
      // <OWN>
    public function get_provider_and_email($provider,$provider_subsidiary){

    $data=array();
        $this->db->select("id as provider_id,name as provider_name");
        $this->db->where("id",$provider);
        $this->db->from("provider");
        ;

        if($q= $this->db->get())
        foreach ($q->result_array() as $key => $row)
        $data=$row;

    if(!empty($provider_subsidiary)):

        $this->db->select("id,email");
        $this->db->where("id",$provider_subsidiary);
        $this->db->from("provider_subsidiary");
        // $q= $this->db->get();

        if($q= $this->db->get())
        foreach ($q->result_array() as $key => $row)
        {$data=array_merge(array("provider_email"=>$row["email"]),$data); }

    endif; 

    return $data;
    }
    // </OWN>
 // <providerSubsidiary> 
    public function get_fk_providerSubsidiary($fk_provider){

    $this->db->select("id,name");
    $this->db->where("fk_provider",$fk_operative_system);
    $this->db->from("provider_subsidiary");
    $q= $this->db->get();

    return $q->result_array();   

    }

    // para editar 
    public function get_providerSubsidiary_id($id=null){

        $data=array(
        "id"              =>"",
        "name"            =>"",
        "fk_provider"       =>"",
        "country"         =>"",
        "state"           =>"",
        "town"            =>"",
        "city"            =>"",
        "colony"          =>"",
        "street"          =>"",
        "inside_number"   =>"",
        "outside_number"  =>"",
        "zip_code"        =>"",
        "reference"       =>"",
        "working_hours"   =>"",
        "reception_days"  =>"",
        "reception_hours" =>"",
        "website"        =>"",
        "email"           =>"",
        "phone"           =>"",
        "contact"         =>"",
        "paydays"         =>"",
        "payhours"        =>"",
        );

    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("provider_subsidiary")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;

    return $data;

    }

    // mismo registro
    public function record_same_providerSubsidiary($data,$id,$idRecordRelation=null){
        $ac=false;

        if(!empty($id))
        $this->db->where_not_in("id",$id);

        // if(!empty($idRecordRelation))
        // $this->db->where_in("fk_operative_system",$idRecordRelation);

        $this->db->where($data);
        $row=$this->db->get("provider_subsidiary");
        

        if($row->num_rows())
        $ac=true;    

        return $ac;
    }

    public function update_providerSubsidiary($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("provider_subsidiary",$data);

    return $id;
    }

    public function insert_providerSubsidiary($data){
        
        $this->db->insert("provider_subsidiary",$data);
        
    return $this->db->insert_id();
    }

    public function providerSubsidiary_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("provider_subsidiary"))
        return true;
    }
// </providerSubsidiary>    
}
?>