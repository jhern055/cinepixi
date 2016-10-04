<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Link_model extends CI_Model{

    // public $db;

	public function __construct() {
        parent::__construct();
        $this->db=$this->load->database("pr",true);

    }

    public function m_name($uri_string){
    // $s=$this->load->database("default");

    // $data=array();
    // $q=$s->select("id,name,link")
    //             ->where("link",$uri_string)
    //             ->from("modules")
    //             ->get();

    //     if($q->result_array())
    //     foreach ($q->result_array() as $key => $value):
    //         $data=$value;
    //     endforeach;

    // return $data;
    }


    // trear el detalle de registro
    public function get_server_link_id($module,$id,$id_record){

        if(empty($module)) return;
        $data=array();
        $this->db->select('*');

        if(!empty($id))
        $this->db->where("id",$id);

        if($module=="publication"):
        $this->db->where("publication_id",$id_record);
        $this->db->from("publications_hosting_server");
        endif;

        if($q=$this->db->get())
        foreach ($q->result_array() as $key => $value) {
        $data =$value ;
        }

        return $data;
    }

    public function get_link_details_by_id($module,$id,$id_record){

        if(empty($module)) return;
        $data=array();
        $this->db->select('*');

        if(!empty($id))
        $this->db->where_not_in("id",$id);

        if($module=="publication"):
        $this->db->where("publication",$id_record);
        $this->db->from("publications_hosting_server_link");
        endif;

        $q=$this->db->get();

        $data = $q->result_array();

        if($module=="publication"):
            foreach ($data as $ĸ => $row) {
                $server_dad_tmp=$this->get_server_link_id($module,$row["publications_hosting_server_id"],$row["publication"]);
                $data[$ĸ]=array_merge(
                    array(
                        "publications_hosting_server_name"=>$server_dad_tmp["description"],
                        ),$data[$ĸ]);
            }
            array_sort_by_column($data,"publications_hosting_server_id",SORT_ASC);
        endif;

        return $data;
    }

    public function record_links_there($module,$id){

        $this->db->select('id');
        $this->db->where('id',$id);
        
        if($module=="publication")
        $q=$this->db->get("publications_hosting_server_link");

        return $q->num_rows();
    }

    public function update_links($module,$data,$id_record,$id){

        $this->db->where('id',$id);

        if($module=="publication")
        $this->db->update("publications_hosting_server_link",$data);

        return $id;
    }

    public function insert_links($module,$data){

        if($module=="publication")
        $this->db->insert("publications_hosting_server_link",$data);

        return $this->db->insert_id();
    }

    public function delete_links($module,$id_record,$timestamp){

        if($module=="publication")
        $this->db->where("publication",$id_record);
            
        $this->db->where('registred_on !=',$timestamp);
        $this->db->where('updated_on !=',$timestamp);

        if($module=="publication")
        $this->db->delete("publications_hosting_server_link");

    }

    public function delete_link_it($module,$id,$id_record){

        $this->db->where('id',$id);
        
        if($module=="publication")
        $this->db->where("publication",$id_record);

        if($module=="publication")
        $this->db->delete("publications_hosting_server_link");

        return true;
    }

    public function get_pirabook_links($module,$id,$id_record){

        $this->db->where('id',$id);
        $this->db->select('*');

        if($module=="publication")
        $this->db->where("publication",$id_record);

        if($module=="publication")
        $this->db->from("publications_hosting_server_link");

        $q=$this->db->get();
        // return $this->db->last_query();
        return $q->result_array();
    }

    public function select_dad_links($module,$id_record){

        $data=array();
        $this->db->select("id,import,link");
        $this->db->where('id',$id_record);

        if($module=="publication")
        $this->db->from("publications");

        $q=$this->db->get();

        foreach ($q->result_array() as $k => $v)
        $data=$v;

        return $data;
    }

    public function update_dad_links($module,$data,$id_record){

        $this->db->where('id',$id_record);

        if($module=="publication")
        $this->db->update("publication",$data);

        return $id_record;
    }
// </details>
}