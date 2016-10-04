<?php
class Purchase_model extends CI_Model{

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

// <purchase> 
    public function get_purchase_amount($query_search){

    $this->db->select('id');
    $this->db->from('purchase');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_purchase($start,$end,$query_search){

    $data=array();
    $this->load->model("admin/payment/payment_model");
    $this->load->model("config/config_model");
    $this->load->model("admin/provider/provider_model");

    $this->db->select("id,name,folio,import,provider,provider_subsidiary,date,type_of_currency,exchange_rate");
    $this->db->from("purchase");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");

    if($q=$this->db->get())
    $data=$q->result_array();

    foreach ($data as $key => $row) {
    // <provider>
    if(!empty($row["provider"]) and !empty($row["provider_subsidiary"]) )
    $data[$key]=array_merge($this->provider_model->get_provider_and_email($row["provider"],$row["provider_subsidiary"]),$data[$key]);
    // </provider>

    // <payment>
    $data[$key]["payments"]=$this->payment_model->get_payment_details_by_id("sale",null,$row["id"]);
        
        $total_payment="";
        if(!empty($data[$key]["payments"]))
        foreach ($data[$key]["payments"] as $key1 => $row1)
        $total_payment+=$row1["import"];

        $data[$key]["payment"]=$total_payment;
        $data[$key]["residuary"]=$data[$key]["import"]-$total_payment;
    // </payment>

    // <type_of_currency>
    if(!empty($row["type_of_currency"]))
    $data[$key]["type_of_currency_text"]=$this->config_model->get_type_of_currency($row["type_of_currency"])[$row["type_of_currency"]];
    // </type_of_currency>
    }
    
    return $data;

    }

    public function get_purchase_id($id){

    $this->load->model("admin/stock/catalog/article/article_model");
    $this->load->model("admin/provider/provider_model");
        
        $data                 =array(
        "id"                  =>"",
        "subsidiary"          =>"",
        "name"                =>"",
        "folio"               =>"",
        "date"                =>"",
        "comment"             =>"",
        "status"              =>"",
        "provider"            =>"",
        'provider_subsidiary' =>"",
        'import'              =>"",
        'sub_total'           =>"",
        'payment'             =>"",
        'tax_iva'             =>"",
        'tax_iva_retained'    =>"",
        'tax_isr'             =>"",
        'tax_ieps'            =>"",
        'type_of_currency'    =>"",
        'exchange_rate'       =>"",
        );

    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("purchase")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    $data["providers"]=$this->provider_model->get_providers();
    $data["details_html"]=$this->article_model->get_details_by_id("purchase",$data["id"]);

    return $data;

    }

    // mismo registro
    public function record_same_purchase($data,$id){
        $ac=false;

        if(!empty($id))
        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("purchase");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }

    public function update_purchase($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("purchase",$data);

    return $id;
    }

    public function insert_purchase($data){
        
        $this->db->insert("purchase",$data);
        
    return $this->db->insert_id();
    }

    public function purchase_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("purchase"))
        return true;
    }
// </purchase>     

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
// <Get>
    public function select_option($array){
    $data=array("0"=>"Seleccione");
        foreach ($array as $key => $value)
        $data[$value["id"]]=$value["name"];
    return $data;    
    } 
// </Get>

}
?>