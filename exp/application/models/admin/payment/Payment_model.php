<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payment_model extends CI_Model{

	public function __construct() {
        parent::__construct();
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

    public function get_simple_dad($source_module){

    switch ($source_module) {
        case $source_module=='sale':
        $source_module="admin/sale/";
        break;

        case $source_module=='remission':
        $source_module="admin/sale/remission/";
        break;

        case $source_module=='opening_balance':
        $source_module="admin/sale/openingBalance/";
        break;

        default:
        # code...
        break;
    }
    $configs=array(
            "admin/sale/"=>array(
                "where_dad"  =>"sale",
                "from_dad"  =>"sale_payments",
                // "from_det"  =>"purchase_details",
            ),
            "admin/sale/remission/"=>array(
                "where_dad"  =>"remission",
                "from_dad"  =>"remission_payments",
                // "from_det"  =>"purchase_details",
            ),   
            "admin/sale/openingBalance/"=>array(
                "where_dad"  =>"opening_balance",
                "from_dad"  =>"opening_balance_payments",
                // "from_det"  =>"purchase_details",
            ),                      
            "admin/purchase/"=>array(
                "where_dad"  =>"purchase",
                "from_dad"  =>"purchase_payments",
                // "from_det"  =>"purchase_details",
            ),
        );

        // process
        $data=false;

        if($configs[$source_module])
         $data=$configs[$source_module];

        return $data;             
    }

    public function get_payments_amount($query_search,$source_module){

        if(empty($source_module)) return;
        $config=$this->get_simple_dad($source_module);

        $data=array();

        $this->db->select('id');
        $this->db->from($config["from_dad"]);

        if(!empty($query_search))
        foreach ($query_search as $k => $row)
        eval($row);

        $q=$this->db->get();


        return  $q->num_rows();
    }

    public function get_array_dad($source_module){

    $this->load->model('email/email_model');
    
    $this->load->model('admin/provider/provider_model');
    $this->load->model('admin/purchase/purchase_model');

        $configs=array(
            "admin/sale/"=>array(
            "source_module"=>"admin/sale/",
            "record_view"=>"admin/sale/saleView/",
            "beneficiary_view"=>"admin/client/clientView/",
            "data"=>function($payment_data){
                // $CI=&get_instance();
                $data_record=array();
                $this->load->model('admin/client/client_model');
                $this->load->model('admin/sale/sale_model');
                $this->load->model('admin/payment/payment_model');

                    if(!empty($payment_data["sale"])):

                    $data_record=$this->sale_model->get_basic_sale_id($payment_data["sale"]);
                    
                    $client_tmp="";
                    $client_tmp=$this->client_model->get_client_and_email($data_record["client"],$data_record["client_subsidiary"]);
                    $data_record["folio"]=$data_record["folio"];
                    $data_record["beneficiary_id"]=$data_record["client"];
                    $data_record["beneficiary_name"]=$client_tmp["client_name"];
                    $data_record["beneficiary_email"]=$client_tmp["client_email"];

                    endif;

                return $data_record;    
                },
            ),

            "admin/sale/remission/"=>array(
            "source_module"=>"admin/sale/remission/",
            "record_view"=>"admin/sale/remission/remissionView/",
            "beneficiary_view"=>"admin/client/clientView/",
            "data"=>function($payment_data){
                // $CI=&get_instance();
                $data_record=array();
                $this->load->model('admin/client/client_model');
                $this->load->model('admin/sale/remission/remission_model');
                $this->load->model('admin/payment/payment_model');

                    if(!empty($payment_data["remission"])):

                    $data_record=$this->remission_model->get_basic_remission_id($payment_data["remission"]);
                    
                    $client_tmp="";
                    $client_tmp=$this->client_model->get_client_and_email($data_record["client"],$data_record["client_subsidiary"]);
                    $data_record["folio"]=$data_record["folio"];
                    $data_record["beneficiary_id"]=$data_record["client"];
                    $data_record["beneficiary_name"]=$client_tmp["client_name"];
                    $data_record["beneficiary_email"]=$client_tmp["client_email"];

                    endif;

                return $data_record;    
                },
            ),



        );

        // process
        $data=false;

        if($configs[$source_module])
         $data=$configs[$source_module];

        return $data;
    }

    public function get_payments($start,$end,$vars_array,$source_module){

    $data_tmp=array();
    $data=array();
    $this->load->model('config/config_model');
    $config=$this->get_simple_dad($source_module);

    $module_explode= explode("/",substr($source_module,0, -1) );
    $module= end($module_explode);
    
    $this->db->select('*');

    $this->db->from($config["from_dad"]);

    $this->db->order_by('id','desc');
    $this->db->limit($start,$end);

    if($vars_array)
    foreach ($vars_array as $k => $v)
    eval($v);

    if($q=$this->db->get())
    $data_tmp=$q->result_array();
    
    $config_process=$this->get_array_dad($source_module);

    // <email>
    foreach ($data_tmp as $ĸ => $row) {

    $row["emails_sent"]=$this->email_model->get_dad_email($config_process["source_module"]."payment/",$row[$module]);
    $response=$config_process["data"]($row);

    $row["id_record"]         =$response["id"];
    $row["folio"]             =$response["folio"];
    $row["module"]            =$module;
    $row["beneficiary_id"]    =$response["beneficiary_id"];
    $row["beneficiary_name"]  =$response["beneficiary_name"];
    $row["beneficiary_email"] =$response["beneficiary_email"];
    $row["record_view"]       =$config_process["record_view"];
    $row["beneficiary_view"]  =$config_process["beneficiary_view"];

    // <type_of_currency>
    $row["type_of_currency_text"]=$this->config_model->get_type_of_currency($row["type_of_currency"])[$row["type_of_currency"]];
    // </type_of_currency>

    $data[]=$row;

    }
    // </email>
    return $data;
    }

    // Traer el arreglo para usarse con el token input 
    public function get_payments_token_search($var_name=null){

    $this->db->select('id,name');
    $this->db->from('article');
    $this->db->order_by('id','desc');

    if($var_name)
    eval($var_name);

    if($q=$this->db->get())
    return  $q->result_array();

    }

// <details>

    // trear el detalle de registro
    public function get_payment_details_by_id($module,$id,$id_record){

        if(empty($module)) return;
        $data=array();
        $config=$this->get_simple_dad($module);

        $this->db->select('*');

        if(!empty($id))
        $this->db->where_not_in("id",$id);

        $this->db->where($config["where_dad"],$id_record);
        $this->db->from($config["from_dad"]);

        $q=$this->db->get();

        $data = $q->result_array();

        return $data;
    }

    public function record_payments_there($module,$id){

        $config=$this->get_simple_dad($module);

        $this->db->select('id');
        $this->db->where('id',$id);
        
        $q=$this->db->get($config["from_dad"]);

        return $q->num_rows();
    }

    public function update_payments($module,$data,$id_record,$id){

        $config=$this->get_simple_dad($module);

        $this->db->where('id',$id);

        $this->db->update($config["from_dad"],$data);

        return $id;
    }

    public function insert_payments($module,$data){
        $config=$this->get_simple_dad($module);

        $this->db->insert($config["from_dad"],$data);

        return $this->db->insert_id();
    }

    public function delete_payments($module,$id_record,$timestamp){
        
        $config=$this->get_simple_dad($module);

        $this->db->where($config["where_dad"],$id_record);
            
        $this->db->where('registred_on !=',$timestamp);
        $this->db->where('updated_on !=',$timestamp);

        $this->db->delete($config["from_dad"]);

    }

    public function delete_payment_it($module,$id,$id_record){

        $config=$this->get_simple_dad($module);

        $this->db->where('id',$id);
        
        $this->db->where($config["where_dad"],$id_record);

        $this->db->delete($config["from_dad"]);

        return true;
    }

    public function get_admin_payments($module,$id,$id_record){

        $config=$this->get_simple_dad($module);

        $this->db->where('id',$id);
        $this->db->select('*');

        $this->db->where($config["where_dad"],$id_record);

        $this->db->from($config["from_dad"]);

        $q=$this->db->get();
        return $q->result_array();
    }

    public function select_dad_payments($module,$id_record){

        $data=array();
        $config=$this->get_simple_dad($module);

        $this->db->select("id,import,payment");
        $this->db->where('id',$id_record);

        $this->db->from($config["where_dad"]);

        $q=$this->db->get();

        foreach ($q->result_array() as $k => $v)
        $data=$v;

        return $data;
    }

    public function update_dad_payments($module,$data,$id_record){

        $config=$this->get_simple_dad($module);

        $this->db->where('id',$id_record);

        $this->db->update($config["where_dad"],$data);

        return $id_record;
    }
// </details>
}