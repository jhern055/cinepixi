<?php
class Folio_Model extends CI_Model {

   public function current_serie($source_module,$subsidiary,$valid){
    
    $data=array();
    $this->db->select("id,serie,since,until,current,shcp_file,pac");
    $this->db->where_in('subsidiary',$subsidiary);
    
    // <sale>
    if($source_module=="admin/sale/"):
      $this->db->where_in('document_type',6);
    endif;
    // </sale>
    
    $this->db->from('config_series');

    if($q=$this->db->get())
    foreach ($q->result_array() as $k => $v):
      $data=$v;
    endforeach;

    if($valid==true):
        if($data["current"]>$data["until"])
        return array("status"=>0,"msg"=>"ha llegado al limite de folios","data"=>false);
    endif;

      return array(
        "status"=>1,
        "msg"=>"Selecion de folio correcto",
        "data"=>$data["serie"].$data["current"],
        "id_serie"=>$data["id"],
        "folio_serie"=>$data["serie"],
        "folio_number"=>$data["current"],
        "pac"=>$data["pac"],
        "shcp_file"=>$data["shcp_file"],
        );

   }
   public function current_up($source_module,$subsidiary,$id_serie){
    
    $data_serie=$this->current_get($source_module,$subsidiary);

    if($data_serie["current"]>$data_serie["until"])
    return;

    $sql="update 
          config_series set current=IF(current,(current+1),current)
          where id='$id_serie' and subsidiary in ($subsidiary) 
          ";
    // <sale>
    if($source_module=="admin/sale/"):
        $sql.=" and document_type in (6)";
    endif;
    // </sale>

    $sql.="limit 1";
    $this->db->query($sql);
   }

   public function current_get($source_module,$subsidiary){

    $data=array();
    $this->db->select("id,serie,since,until,current,shcp_file,pac");
    $this->db->where_in('subsidiary',$subsidiary);
    
        // <sale>
    if($source_module=="admin/sale/"):

      $this->db->where_in('document_type',6);
    endif;
    // </sale>

    $this->db->from('config_series');

    if($q=$this->db->get())
    foreach ($q->result_array() as $k => $v):
      $data=$v;
    endforeach;

    return $data;

   }

   public function get_data_source($source_module,$id){

    $data=array();
    $this->db->select("subsidiary,sat_version");
    $this->db->where_in('id',$id);
    
    // <sale>
    if($source_module=="admin/sale/"):
      $this->db->from('sale');
    endif;
    // </sale>

    if($q=$this->db->get())
    foreach ($q->result_array() as $k => $v):
      $data=$v;
    endforeach;

    return $data;

   }
}
?>