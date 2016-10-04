<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class getMenu extends CI_Controller{

    public function index(){

	$input_search=(!empty($_POST["input_search"]) ?strip_tags( $this->security->xss_clean($_POST["input_search"]) ):"");
    $this->load->getDynamicMenu($input_search);
    
    }
   
}
