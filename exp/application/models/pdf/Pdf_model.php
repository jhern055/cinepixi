<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pdf_model extends CI_Model{

	public function __construct() {
        parent::__construct();
    }
    public function get_array_dad($source_module){
    
    $configs=array(
            "admin/sale/"=>array(
                "from_dad"  =>"sale",
                "from_det"  =>"sale_details",
            ),
            "admin/sale/remission/"=>array(
                "from_dad"  =>"remission",
                "from_det"  =>"remission_details",
            ),
            "admin/sale/request/"=>array(
                "from_dad"  =>"request",
                "from_det"  =>"request_details",
            ),
            "admin/sale/quatition/"=>array(
                "from_dad"  =>"quatition",
                "from_det"  =>"quatition_details",
            ),
            "admin/sale/creditNote/"=>array(
                "from_dad"  =>"credit_note",
                "from_det"  =>"credit_note_details",
            ),  
            "admin/sale/openingBalance/"=>array(
                "from_dad"  =>"opening_balance",
                "from_det"  =>"opening_balance_details",
            ),                               
            "admin/purchase/"=>array(
                "from_dad"  =>"purchase",
                "from_det"  =>"purchase_details",
            ),
            "admin/purchase/order/"=>array(
                "from_dad"  =>"purchase_order",
                "from_det"  =>"purchase_order_details",
            ),                        
        );
        // process
        $data=false;

        if($configs[$source_module])
         $data=$configs[$source_module];

        return $data;    
    }

    public function get_table_details($source_module,$id){

    $config=$this->get_array_dad($source_module);

    $this->load->model("world_model");
    $this->load->model("admin/stock/catalog/article/article_model");

    $this->load->model("vars_system_model");
    $sys=$this->vars_system_model->_vars_system();

    $data=array();
        $this->db->select("*");
        $this->db->where("id",$id);

        $this->db->from($config["from_dad"]);

    $q= $this->db->get();

        if($q->result_array())
        foreach ($q->result_array() as $key => $value):
            $data=$value;
        endforeach;

        if($source_module=="admin/sale/"):
        $data["payment_condition_text"]=(!empty($data["payment_condition"])?array_search($data["payment_condition"], array_flip($sys["forms_fields"]["payment_condition"]) ):"");
        endif;

// <details>
        $this->db->select("*");

        $this->db->where($config["from_dad"],$id);
        $this->db->from($config["from_det"]);

        $q= $this->db->get();

        if($q->result_array())
        $data["details"]=$q->result_array();

        if(!empty($data["details"])):
            foreach ($data["details"] as $key => $row)
            $data["details"][$key]["article_name"]=$this->article_model->get_article_text($row["article"]);
        
            foreach($data["details"] as $k=>$v) {

            $tmp=import_processing(null,null,$data["details"][$k]["totalSub"],(!empty($data["details"][$k]["discount"])?$data["details"][$k]["discount"]:null),$data["details"][$k]["taxIeps"],$data["details"][$k]["taxIva"],$data["details"][$k]["taxIvaRetained"],$data["details"][$k]["taxIsr"]);

            $data["details"][$k]["total_sub"]=(float) number_format($tmp["total_sub"],2,".","");
            $data["details"][$k]["discount_calculated"]=$tmp["discount"];
            $data["details"][$k]["tax_ieps_calculated"]=$tmp["tax_ieps"];
            $data["details"][$k]["tax_iva_calculated"]=$tmp["tax_iva"];
            $data["details"][$k]["tax_iva_retained_calculated"]=$tmp["tax_iva_retained"];
            $data["details"][$k]["tax_isr_calculated"]=$tmp["tax_isr"];
            $data["details"][$k]["total"]=(float) number_format($tmp["total"],2,".","");

            }

        endif;    
// </details>

// <client>
if(!empty($data["client"])){

        $this->db->select("*");
        $this->db->where("id",$data["client"]);
        $this->db->from("client");
        $q= $this->db->get();

        if($q->result_array())
        foreach ($q->result_array() as $key => $row)
        $data["client"]=$row;

    if(!empty($data["client_subsidiary"])):

        $this->db->select("*");
        $this->db->where("fk_client",$data["client"]["id"]);
        $this->db->from("client_subsidiary");
        $q= $this->db->get();

        if($q->result_array())
        foreach ($q->result_array() as $key => $row)
        $data["client"]["subsidiary"]=$row;

          $data["client"]["subsidiary"]["state_text"]=$this->world_model->get_w_state_text($data["client"]["subsidiary"]["state"]);
    endif; 

}
// </client>
// <provider>
if(!empty($data["provider"])){

        $this->db->select("*");
        $this->db->where("id",$data["provider"]);
        $this->db->from("provider");
        $q= $this->db->get();

        if($q->result_array())
        foreach ($q->result_array() as $key => $row)
        $data["provider"]=$row;

    if(!empty($data["provider_subsidiary"])):

        $this->db->select("*");
        $this->db->where("fk_provider",$data["provider"]["id"]);
        $this->db->from("provider_subsidiary");
        $q= $this->db->get();

        if($q->result_array())
        foreach ($q->result_array() as $key => $row)
        $data["provider"]["subsidiary"]=$row;

          $data["provider"]["subsidiary"]["state_text"]=$this->world_model->get_w_state_text($data["provider"]["subsidiary"]["state"]);
    endif; 

}
// </provider>
$data["payment_method_text"]=(!empty($data["payment_condition"])?array_search($data["payment_condition"], array_flip($sys["forms_fields"]["payment_condition"]) ):"");


    return $data;
    }
}
?>