<?php
class Openingbalance_model extends CI_Model{

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
// <openingBalance>
    public function get_openingBalance_amount($query_search){

    $this->db->select('id');
    $this->db->from("opening_balance");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_openingBalance($start,$end,$query_search){
    $this->load->model("config/email/email_model");
    $this->load->model("admin/client/client_model");
    $this->load->model("config/config_model");

    $data=array();
    $this->db->select("id,name,folio,import,client,client_subsidiary,type_of_currency,exchange_rate");
    $this->db->from("opening_balance");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
    if($q=$this->db->get())
    $data=$q->result_array();

    if(!empty($data))
    foreach ($data as $key => $row) {

    // <cliente>
    if(!empty($row["client"]) and !empty($row["client_subsidiary"]) )
    $data[$key]=array_merge($this->client_model->get_client_and_email($row["client"],$row["client_subsidiary"]),$data[$key]);
    // </cliente>

    // <type_of_currency>
    if(!empty($row["type_of_currency"]))
    $data[$key]["type_of_currency_text"]=$this->config_model->get_type_of_currency($row["type_of_currency"])[$row["type_of_currency"]];
    // </type_of_currency>

    // <email>
    $data[$key]["emails_sent"]=$this->email_model->get_dad_email("admin/sale/openingBalance/",$row["id"]);
    // </email>

    }
    return $data;

    }

    public function get_openingBalance_id($id){

    $this->load->model("admin/stock/catalog/article/article_model");
    $this->load->model("config/config_model");

        $data=array(
        'id'                     =>"",
        'subsidiary'             =>"",
        'name'                   =>"",
        'client'                 =>"",
        'client_subsidiary'      =>"",
        'folio'                  =>"",
        'comment'                =>"",
        'status'                 =>"",
        'type_of_currency'       =>"",
        'registred_by'           =>"",
        'registred_on'           =>"",
        'updated_by'             =>"",
        'updated_on'             =>"",
        'import'                 =>"",
        'sub_total'             =>"",
        'tax_iva'                =>"",
        'tax_iva_retained'       =>"",
        'tax_isr'                =>"",
        'tax_ieps'               =>"",
        'exchange_rate'          =>"",        
        );

    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("opening_balance")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;
    
    if(!empty($data["type_of_currency"]))
    $data["type_of_currency_info"]=$this->config_model->type_of_currency_id($data["type_of_currency"]);

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
// </client>

    $data["details_html"]=$this->article_model->get_details_by_id("opening_balance",$data["id"]);

    return $data;

    }

    // mismo registro
    public function record_same_openingBalance($data,$id){
        $ac=false;

        if(!empty($id))
        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("opening_balance");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }

    public function update_openingBalance($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("opening_balance",$data);

    return $id;
    }

    public function insert_openingBalance($data){
        
        $this->db->insert("opening_balance",$data);
        
    return $this->db->insert_id();
    }

    public function openingBalance_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("opening_balance"))
        return true;
    }
// </openingBalance>

}
?>