<?php
class Client_model extends CI_Model{

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

// <client> 
    // <OWN>
    public function get_client_and_email($client,$client_subsidiary){

    $data=array();
        $this->db->select("id as client_id,name as client_name");
        $this->db->where("id",$client);
        $this->db->from("client");
        ;

        if($q= $this->db->get())
        foreach ($q->result_array() as $key => $row)
        $data=$row;

    if(!empty($client_subsidiary)):

        $this->db->select("id,email");
        $this->db->where("id",$client_subsidiary);
        $this->db->from("client_subsidiary");
        // $q= $this->db->get();

        if($q= $this->db->get())
        foreach ($q->result_array() as $key => $row)
        {$data=array_merge(array("client_email"=>$row["email"]),$data); }

    endif; 

    return $data;
    }
    // </OWN>
    // mismo registro
    public function record_same_client($data,$id){
        $ac=false;

        // if(!empty($id))
        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("client");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }
        
    public function get_client_amount($query_search){

    $this->db->select('id');
    $this->db->from('client');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_client($start,$end,$query_search){

    $this->db->select("id,name");
    $this->db->from("client");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
	$q=$this->db->get();

    return $q->result_array();

    }

    public function get_client_id($id,$client_subsidiary=null){

        $data=array(
        "id"=>"",
        "name"=>"",
        "rfc"=>""
        );
        
    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("client")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    if(!empty($client_subsidiary)):
    $data["subsidiary"]=$this->get_client_subsidiary_id($client_subsidiary);
    else:
    $data["client_subsidiaries"]=$this->get_client_subsidiaries($data["id"]);
    endif;

    return $data;

    }

    public function update_client($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("client",$data);

    return $id;
    }

    public function insert_client($data){
        
        $this->db->insert("client",$data);
        
    return $this->db->insert_id();
    }

    public function client_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("client"))
        return true;
    }

    public function get_client_subsidiaries($client,$select=null){

    $q=$this->db->select("*")
                ->where("fk_client",$client)
                ->from("client_subsidiary")
                ->get();

        if($q->result_array())
        $data=$q->result_array();
        else
        $data=array();

    if(!empty($select))
    return $this->select_option($q->result_array());   

    return $data;

    }

    public function get_client_subsidiary_id($client_subsidiary){
        
    $q=$this->db->select("*")
                ->where("id",$client_subsidiary)
                ->from("client_subsidiary")
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
 // <clientSubsidiary> 
    public function get_fk_clientSubsidiary($fk_client){

    $this->db->select("id,name");
    $this->db->where("fk_client",$fk_operative_system);
    $this->db->from("client_subsidiary");
    $q= $this->db->get();

    return $q->result_array();   

    }

    // para editar 
    public function get_clientSubsidiary_id($id=null){

        $data=array(
        "id"              =>"",
        "name"            =>"",
        "fk_client"       =>"",
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
                ->from("client_subsidiary")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;

    return $data;

    }

    // mismo registro
    public function record_same_clientSubsidiary($data,$id,$idRecordRelation=null){
        $ac=false;

        if(!empty($id))
        $this->db->where_not_in("id",$id);

        // if(!empty($idRecordRelation))
        // $this->db->where_in("fk_operative_system",$idRecordRelation);

        $this->db->where($data);
        $row=$this->db->get("client_subsidiary");
        

        if($row->num_rows())
        $ac=true;    

        return $ac;
    }

    public function update_clientSubsidiary($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("client_subsidiary",$data);

    return $id;
    }

    public function insert_clientSubsidiary($data){
        
        $this->db->insert("client_subsidiary",$data);
        
    return $this->db->insert_id();
    }

    public function clientSubsidiary_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("client_subsidiary"))
        return true;
    }
// </clientSubsidiary>    


// </client> 

// <Get>
  public function select_option($array){
    $data=array("0"=>"Seleccione");
        foreach ($array as $key => $value)
        $data[$value["id"]]=$value["name"];
    return $data;    
    } 
    // <get client>
    // Traer el arreglo para usarse con el token input 
    public function get_clients_token_search($var_name=null){

    $data=array();
    $this->db->select('id,name');
    $this->db->from('client');
    $this->db->order_by('id','desc');

    if($var_name)
    eval($var_name);

    if($q=$this->db->get())
    $data =$q->result_array();

    foreach ($data as $key => $row) {
        # code...
    if(!empty($row["id"]))
    $data[$key]["subsidiaries"]=$this->get_client_subsidiaries($row["id"]);

    }

    return $data;

    }
    // <get client>
    public function get_clients(){

    $this->db->select("id,name");
    $this->db->from("client");
    $this->db->order_by("name","asc");
    $q= $this->db->get();

    return $this->select_option($q->result_array());   

    } 
    // </get client>
// </Get>

}
?>