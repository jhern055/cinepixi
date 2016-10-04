<?php

class Login_model extends CI_Model{

	public function __construct() {
        parent::__construct();
    }

	function create_account_model($data){

        $this->db->insert('users',$data);
        return $this->db->insert_id();
    }

    function check_if_there($email){

        $q=$this->db->select('email')
        			->from('users')
        			->where('email',$email)
        			->get();
		if($q->num_rows())	
		return true;
		else
		return false;
    }

    function get_user_by_id($id){

    if(empty($id))
    return false;
        
    $this->db->select('id,nickname,email,pass_hash,rights');
    $this->db->from('users');
    $this->db->where('id',$id);
    $this->db->limit(1);
    $q=$this->db->get();

        if(!empty($q)){
        foreach ($q->result_array() as $k => $row)
        $data=$row;

        return  $data;
        }
    }

    function check_if_there_nickname($nickname){

    if(empty($nickname))
    return false;
        
    $this->load->helper('email');
    
    $this->db->select('id,nickname,email,pass_hash');
    $this->db->from('users');
    
    if( valid_email($nickname) )
    $this->db->where('email',$nickname);
    else
    $this->db->where('nickname',$nickname);

    $this->db->limit(1);
    $q=$this->db->get();

		if(!empty($q))
		return  $q->result_array();
		else
		return false;
    }

    function pass_tmp($id_user, $data){

    $this->db->where('id',$id_user);
    $this->db->update('users',$data);
        
    }

    function change_passwrd($data,$id_user,$pass_tmp){

    $this->db->where('id',$id_user);
    $this->db->where('pass_tmp',$pass_tmp);

    if($this->db->update('users',$data))
    return true;

    }

    function clear_passwrd($data,$id_user){

    $this->db->where('id',$id_user);
    
    if($this->db->update('users',$data))
    return true;

    }

    function is_logged_in($actually_place){
        
            $is_logged_in = $this->session->userdata('user_id');
           
            if(!isset($is_logged_in) || $is_logged_in != true):
            redirect('login/?redirect='.encode_url($actually_place) );
            endif;
    }
}