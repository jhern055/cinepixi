<?php
class Dynamic_menu_model extends CI_Model{

    public $user_id;

	public function __construct() {
        parent::__construct();
        $this->user_id=$this->session->userdata("user_id");
    }

    function get_menu($input_search){

    $menu = array(
        'menus' => array(),
        'parent_menus' => array()
    );

                // ->where("registred_by",$this->user_id)
    $this->db->select("id,name,link,parentid,class");

    if(!empty($input_search))
    $this->db->where("name",$input_search);

    $this->db->from("modules");
    $this->db->order_by("id","asc");
    $q=$this->db->get();
    
    foreach ($q->result_array() as $key => $row) {

        //creates entry into menus array with current menu id ie. $menus['menus'][1]
        $menu['menus'][$row['id']] = $row;
        //creates entry into parent_menus array. parent_menus array contains a list of all menus with children
        $menu['parent_menus'][$row['parentid']][] = $row['id'];
    }

    return $menu;
    }


    function module_name($module){
    $data=array();
        $this->db->select('id,name,link,class');
        $this->db->where('link',$module);
        $this->db->from('modules');
        $q=$this->db->get();

        if(!empty($q))
        foreach ($q->result_array() as $key => $row):
        $data["name"]=$row["name"];
        $data["id"]=$row["id"];
        $data["link"]=$row["link"];
        endforeach;
     return $data;
    }

    function module_childrens($module_id){
    $data=array();
        $this->db->select('id,name,link,class');
        $this->db->where('parentid',$module_id);
        $this->db->from('modules');
        $q=$this->db->get();
        // pr($this->db->last_query());
        if(!empty($q))
        foreach ($q->result_array() as $key => $row):

        // validation de permisos
        $con=rights_validation($row["link"]."read");

            if($con["status"]):
            $data[$row["id"]]["name"]=$row["name"];
            $data[$row["id"]]["id"]=$row["id"];
            $data[$row["id"]]["link"]=$row["link"];
            else:
               print_r($con);
            endif;

        endforeach;

     return $data;
    }
 //    // traer los links de favoritos
 //    function get_links($limit=null, $start=null,$id_menu){        

 //    $data=array();

 //        $this->db->select("id,text,image,name,canonicalUrl,url,description,iframe,users_favorites");
 //        $this->db->from("users_favorites_url");

 //        $this->db->where("users_favorites",$id_menu);

 //        $this->db->limit($limit, $start);

 //        $this->db->where("registred_by",$this->user_id);
 //        $q=$this->db->get();
        
 //        $c=1;
 //        if($q->result_array())
 //        foreach ($q->result_array as $k => $v):
 //         $data[$v["users_favorites"]]["folder"]=$v["users_favorites"];
 //         $data[$v["users_favorites"]]["links"][$v["id"]]=$v;
 //         // $data[$v["users_favorites"]]["links_amount"]=$c++;
 //        endforeach;

 //    // amount
 //     $data[$id_menu]["links_amount"]=$this->get_links_amount($id_menu);
 //    //nombre del folder 
 //    if(!empty($data[$id_menu]["folder"]))
 //    foreach ($data as $k => &$v):
 //       $this->db->select("id,name")
 //                ->from("users_favorites")
 //                ->where("registred_by",$this->user_id)
 //                ->where("id",$v["folder"]);
 //    $q=$this->db->get();

 //    if($q->result_array()[0]["name"])
 //    $v["folder_name"]=$q->result_array()[0]["name"];
 //    endforeach; 
 //    // .............
    
 //    return  $data;

 //    }

 //    // contar los registros  con lo que venga de POST
 //    function get_links_amount($id_menu){

 //        $this->db->where("users_favorites",$id_menu);
 //        $this->db->where("registred_by",$this->user_id);
        
 //        $q=$this->db->get("users_favorites_url");
 //        return $q->num_rows();
 //    }

    function get_menu_category($input_search){

    $menu = array(
        'menus' => array(),
        'parent_menus' => array()
    );

    $this->db->select("id,name,link,parentid,class");

    if(!empty($input_search))
    $this->db->where("name",$input_search);

    $this->db->from("category");
    $this->db->order_by("id","asc");
    $q=$this->db->get();
    
    foreach ($q->result_array() as $key => $row) {

        //creates entry into menus array with current menu id ie. $menus['menus'][1]
        $menu['menus'][$row['id']] = $row;
        //creates entry into parent_menus array. parent_menus array contains a list of all menus with children
        $menu['parent_menus'][$row['parentid']][] = $row['id'];
    }

    return $menu;
    }
}
?>