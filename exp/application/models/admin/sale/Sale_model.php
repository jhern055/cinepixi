<?php
class Sale_model extends CI_Model{

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

// <sale> 
    public function get_sale_amount($query_search){

    $this->db->select('id');
    $this->db->from('sale');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_sale($start,$end,$query_search){

    $data=array();
    $this->load->model("admin/payment/payment_model");
    $this->load->model("config/config_model");
    $this->load->model("config/email/email_model");
    $this->load->model("admin/client/client_model");

    $this->db->select("id,name,folio,import,client,client_subsidiary,date,type_of_currency,exchange_rate");
    $this->db->from("sale");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");

    if($q=$this->db->get())
    $data=$q->result_array();
    
    foreach ($data as $key => $row) {

    // <cliente>
    if(!empty($row["client"]) and !empty($row["client_subsidiary"]) )
    $data[$key]=array_merge($this->client_model->get_client_and_email($row["client"],$row["client_subsidiary"]),$data[$key]);
    // </cliente>

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
    $data[$key]["type_of_currency_text"]=$this->config_model->get_type_of_currency($row["type_of_currency"])[$row["type_of_currency"]];
    // </type_of_currency>

    // <email>
    $data[$key]["emails_sent"]=$this->email_model->get_dad_email("admin/sale/",$row["id"]);
    // </email>

    }

    return $data;

    }

    public function get_basic_sale_id($id){
    $data=array();               
    $q=$this->db->select("id,import,client,client_subsidiary,folio")
                ->where("id",$id)
                ->from("sale")
                ->get();
     if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    return $data;
    }

    public function get_sale_id($id){
    $this->load->helper('xml/xml_helper');

    $this->load->model("admin/stock/catalog/article/article_model");
    $this->load->model("config/email/email_model");
    $this->load->model("config/config_model");

        $data=array(
        'id'                     =>"",
        'subsidiary'             =>"",
        'name'                   =>"",
        'client'                 =>"",
        'client_subsidiary'      =>"",
        'folio'                  =>"",
        'date'                   =>"",
        'date_expires'          =>"",
        'comment'                =>"",
        'status'                 =>"",
        'type_of_currency'       =>"",
        'exchange_rate'          =>"",        
        'payment_method'         =>"",
        'payment_method_account' =>"",
        'payment_condition'      =>"",
        'method_of_payment'      =>"",
        'partiality_number'      =>"",
        'registred_by'           =>"",
        'registred_on'           =>"",
        'updated_by'             =>"",
        'updated_on'             =>"",
        'import'                 =>"",
        'sub_total'             =>"",
        'payment'                =>"",
        'tax_iva'                =>"",
        'tax_iva_retained'       =>"",
        'tax_isr'                =>"",
        'tax_ieps'               =>"",
        'sat_version'            =>"",
        'uuid'                   =>"",
        'pac'                    =>"",
        );

    // $this->db->start_cache();
    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("sale")
                ->get();
    // $this->db->stop_cache();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    if(!empty($data["type_of_currency"]))
    $data["type_of_currency_info"]=$this->config_model->type_of_currency_id($data["type_of_currency"]);
     
    //si existe timbre 
    $_INVOICE_MODE_CONFIG=invoice_mode_config("admin/sale/",$data["sat_version"]);

    if(!empty($data["id"])):

    $file_stamp=$_INVOICE_MODE_CONFIG["storage_stamp"].$data["id"]."-".$data["folio"].".xml";
    $file_cancel=$_INVOICE_MODE_CONFIG["storage_cancel"].$data["id"]."-".$data["folio"].".xml";

    $data["sat_stamp"]=(file_exists($file_stamp)?$file_stamp:"");
    $data["sat_cancel"]=(file_exists($file_cancel)?$file_cancel:"");
            
            if(!empty($data["stamp"]))    
           { $data["uuid"]=(!empty($data["uuid"])?$data["uuid"]:get_uuid($data["stamp"]));}

    endif;
    // 

    // email y sucursal client
    // <client>
    if(!empty($data["client"])){
        if(!empty($data["client_subsidiary"])):

            $this->db->select("email");
            $this->db->where("fk_client",$data["client"]);
            $this->db->from("client_subsidiary");
            $q= $this->db->get();

            if($q->result_array())
            foreach ($q->result_array() as $key => $row)
            $data["client_email"]=$row["email"];

        endif; 
    }

    // <email>
    $data["emails_sent"]=$this->email_model->get_dad_email("admin/sale/",$data["id"]);
    // </email>

// </client>
    $data["details_html"]=$this->article_model->get_details_by_id("sale",$data["id"]);

    return $data;

    }

    // mismo registro
    public function record_same_sale($data,$id){
        $ac=false;

        if(!empty($id))
        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("sale");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }

    public function update_sale($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("sale",$data);

    return $id;
    }

    public function insert_sale($data){
        
        $this->db->insert("sale",$data);
        
    return $this->db->insert_id();
    }

    public function sale_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("sale"))
        return true;
    }
// </sale>

}
?>