<?php
class File_Model extends CI_Model {
 
   public function insert_file($filename){
      $data = array( 'filename' => $filename);
      $this->db->insert('publications_file', $data);
      return $this->db->insert_id();
   }

   public function get_files(){
   return $this->db->select()
         ->from('publications_file')
         ->order_by('id','desc')
         ->get()->result()
         ;
   }

   public function delete_file($file_id){
      $file = $this->get_file($file_id);

      if (!$this->db->where('id', $file_id)->delete('publications_file'))
      return FALSE;

      @unlink(FCPATH.'images/uploads/imgPost/'. $file->filename);  
      return TRUE;
   }
 
   public function get_file($file_id){
      return $this->db->select()
            ->from('publications_file')
            ->where('id', $file_id)
            ->get()
            ->row();
   }
 
   public function update_img($publication_id,$ids_publications_file){

      $update = array('publication_id' => $publication_id);
      $this->db->where_in('id',$ids_publications_file)
               ->update('publications_file',$update)
                  ;
   }

   public function get_files_by($publication_id){
      $q=$this->db->select('id,filename')
            ->from('publications_file')
            ->where('publication_id', $publication_id)
            ->get();
      $data=$this->get_data($q);   
         
      return $data;      
   }

   function get_data($q){
        $data=array();
        if ($q->num_rows() > 0) {
            foreach ($q->result_array() as $row) {
                $data[$row["id"]] = $row;
            }
            return $data;
        }
        return false;
    }

    public function delete_gif($id_publication){

    $q=$this->db->select("gif")
        ->from("publications")
        ->where('id', $id_publication)
        ->limit(1)
        ->get()
        ->result();

        if(!empty($q[0]->gif))
         @unlink(FCPATH.'images/uploads/gifPost/'.$q[0]->gif);
    }
}
?>