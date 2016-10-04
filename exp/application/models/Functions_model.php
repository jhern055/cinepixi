<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Functions_model extends CI_Model{

	public function __construct() {
        parent::__construct();
        $this->user_id=$this->session->userdata("user_id");
    }

    public function get_table($source_module,$id){

    if(empty($source_module))
        return;
    $data=array();
        $this->db->select("*");
        $this->db->where("id",$id);

        if($source_module=="admin/sale/")
        $this->db->from("sale");

        if($source_module=="admin/sale/remission/")
        $this->db->from("remission");

        if($source_module=="admin/sale/request/")
        $this->db->from("request");
    
        if($source_module=="admin/sale/quatition/")
        $this->db->from("quatition");

        if($source_module=="admin/sale/openingBalance/")
        $this->db->from("opening_balance");

        if($source_module=="admin/sale/creditNote/")
        $this->db->from("credit_note");

        if($source_module=="admin/purchase/")
        $this->db->from("purchase");

        if($source_module=="admin/purchase/order/")
        $this->db->from("purchase_order");

        $q=$this->db->get();

        if($q->result_array())
        foreach ($q->result_array() as $key => $value):
            $data=$value;
        endforeach;

    return $data;
    }
    
}
?>