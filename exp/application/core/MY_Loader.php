<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Loader extends CI_Loader {

    public function template($template_name, $vars = array(), $return = FALSE) {
    
    $CI =& get_instance();
    $CI->load->model("vars_system_model");

    //validacion
    if(!empty($CI->uri->uri_string) and $CI->uri->segment(1)!="login"){
        
        // $CI->load->helper("login");
        $module_read=$vars["module"]."read";
        
        // funcion para validar los read
        $return_valid=rights_validation($module_read,"javascript");

        if(!$return_valid["status"]){

            echo $return_valid["redirect"];

            // die($return_valid["msg"]." ".$module_read);
        }
    }
    // ..


    // $menu = new menu();
    $vars["menu"]=$this->getDynamicMenu();

    $vars["sys"]=$CI->vars_system_model->_vars_system();
    $vars["jquery_1_11_1"]='<script type="text/javascript" src="'.base_url().'js/jquery-1.11.1.js"></script>';

    /// ****** TOKEN INPUT ***** ///
    $vars['tokeninput_js'] ="<script type='text/javascript' src='".base_url()."js/libraries/token_input/jquery.tokeninput.js'></script>";
    $vars['tokeninput_css'] ="<link rel='stylesheet' href='".base_url()."css/libraries/token_input/token-input.css' type='text/css' />";
    
    /// ****** jquery redirect ***** ///
    // $vars['jquery_redirect'] ="<script src='".base_url()."js/libraries/jRedirect/jquery.redirect.js'></script>";
    
    // accesos rapidos de padres
    if(!empty($vars["source_module"]) or !empty($vars["module"])):
    $source_module=(!empty($vars["source_module"])?$vars["source_module"]:$vars["module"]);
    $vars["modules_quick"]=$this->get_back_access($source_module);
    endif;
    // 

        if($return):

        $content  = $this->view('templates/header', $vars, $return);
        $content  = $this->view('templates/navBar', $vars, $return);
        $content  = $this->view('templates/leftWidget', $vars, $return);
        $content .= $this->view($template_name, $vars, $return);
        $content .= $this->view('templates/footer', $vars, $return);

        return $content;
    else:
        $this->view('templates/header', $vars);
        $this->view('templates/navBar', $vars);
        $this->view('templates/leftWidget', $vars);
        $this->view($template_name, $vars);
        $this->view('templates/footer', $vars);
    endif;

    }

// TEMPLATE DE commerce
    public function template_commerce($template_name, $vars = array(), $return = FALSE) {
    
    $CI =& get_instance();
    $CI->load->model("vars_system_model");

    $vars["menuCategory"]=$this->getDynamicMenuCategory();
    $vars["modulesNavBar"]=$this->getDynamicMenuModulesNavBar();

    $vars["sys"]=$CI->vars_system_model->_vars_system();
    $vars["jquery_1_11_1"]='<script type="text/javascript" src="'.base_url().'js/jquery-1.11.1.js"></script>';

    /// ****** TOKEN INPUT ***** ///
    $vars['tokeninput_js'] ="<script type='text/javascript' src='".base_url()."js/libraries/token_input/jquery.tokeninput.js'></script>";
    $vars['tokeninput_css'] ="<link rel='stylesheet' href='".base_url()."css/libraries/token_input/token-input.css' type='text/css' />";
    
    if($return):
        $content  = $this->view('templates/commerce/ecomerce/header', $vars, $return);
        $content  = $this->view('templates/commerce/ecomerce/header_top', $vars, $return);
        $content  = $this->view('templates/commerce/ecomerce/header_middle', $vars, $return);
        $content  = $this->view('templates/commerce/ecomerce/navBar', $vars, $return);
        $content  = $this->view('templates/commerce/ecomerce/slider', $vars, $return);
        $content  = $this->view('templates/commerce/ecomerce/openContainer', $vars, $return);
        $content  = $this->view('templates/commerce/ecomerce/leftWidget', $vars, $return);
        $content .= $this->view($template_name, $vars, $return);
        $content  = $this->view('templates/commerce/ecomerce/closeContainer', $vars, $return);
        $content .= $this->view('templates/commerce/ecomerce/footer', $vars, $return);

        return $content;
    else:
        $this->view('templates/commerce/ecomerce/header', $vars);
        $this->view('templates/commerce/ecomerce/header_top', $vars);
        $this->view('templates/commerce/ecomerce/header_middle', $vars);
        $this->view('templates/commerce/ecomerce/navBar', $vars);
        $this->view('templates/commerce/ecomerce/slider', $vars);
        $this->view('templates/commerce/ecomerce/openContainer', $vars);
        $this->view('templates/commerce/ecomerce/leftWidget', $vars);
        $this->view('templates/commerce/ecomerce/'.$template_name, $vars);
        $this->view('templates/commerce/ecomerce/closeContainer', $vars);
        $this->view('templates/commerce/ecomerce/footer', $vars);
    endif;
    }
    // Este es el MENU DEL ADMIN
    public function getDynamicMenu($input_search=null){

    $CI =& get_instance();
    $CI->load->model("dynamic_menu_model");

        $menu_html="";
        $menu_html.='<link rel="stylesheet" type="text/css" href="'.base_url().'css/dynamic_menu_favorite/styles.css" />';
        $menu_html.='<script type="text/javascript" src="'.base_url().'css/dynamic_menu_favorite/script.js"></script>';
        $menu_html.=$this->buildMenu(0, $CI->dynamic_menu_model->get_menu($input_search));

        if(!empty($_POST["ajax"]))
        return print_r(json_encode( array("status"=>1,"menu"=>$menu_html ) ) ) ;
        else
        return $menu_html;
    }

    public function buildMenu($parent, $menu) {
    $CI =& get_instance();
    $CI->load->model("vars_system_model");
    $sys=$CI->vars_system_model->_vars_system();
    $CI->load->helper('load_controller');

    $html = "";
    if (isset($menu['parent_menus'][$parent])) {
        $html .= '<ul class="nav" id="side-menu">';

        foreach ($menu['parent_menus'][$parent] as $menu_id) {
            $controller=explode("/", substr($menu['menus'][$menu_id]['link'],0, -1));

            if (!isset($menu['parent_menus'][$menu_id])) {
                $html .= "<li data-id_menu='".encode_id($menu_id) ."'>";
                $html .= "<a id='a_menu_click' class='a_menu' href=javascript:void(0); data-href=".substr($menu['menus'][$menu_id]['link'],0, -1).">";
                // $html .= "<a id='a_menu_click' class='a_menu' href='".(!empty(substr($menu['menus'][$menu_id]['link'],0, -1))?base_url().substr($menu['menus'][$menu_id]['link'],0, -1):"javascript:void(0);")."' data-href=".substr($menu['menus'][$menu_id]['link'],0, -1).">";
                $html  .= "     <span style='margin-left: -10px; margin-right: -10px;' class='icon_menu ".(!empty($menu['menus'][$menu_id]['class'])?$menu['menus'][$menu_id]['class']:"")."'></span>
                                <span class='module_text'>"
                                . $menu['menus'][$menu_id]['name'] ."
                                <span>
                            </a>";

                            if(!empty($sys["config"]["no_roulette_mouse"])):
                    $html .="<a href=".base_url().$menu['menus'][$menu_id]['link']." target='_blank' class='a_new_tab'><span style='margin-left: -10px;' class='new_tab'></span></a>";
                            else:
                                // pr(end(explode("/",substr($menu['menus'][$menu_id]['link'],0, -1) ) ));
                                // if(!empty($sys["config"]["no_click_right_mouse"]))
                                // if(controller_exists(ucwords($controller[0])) and count(explode("/", substr($menu['menus'][$menu_id]['link'],0, -1))) > 1)
                                // $html .="<a href='".base_url().$menu['menus'][$menu_id]['link']."View'  class='add_record'><span style='margin-left: -10px;' class='add_record'></span></a>";

                            endif;

                $html .="</li>";
            }

            if (isset($menu['parent_menus'][$menu_id])) {

                $html .= "<li class='has-sub' data-id_menu='".encode_id($menu_id) ."'>";
                $html .=  "<a id='a_menu_click' class='a_sub_menu' href=".base_url().$menu['menus'][$menu_id]['link']." data-href=".substr($menu['menus'][$menu_id]['link'],0, -1).">";
                // $html .=  "<a id='a_menu_click' class='a_sub_menu' href=".(!empty(substr($menu['menus'][$menu_id]['link'],0, -1))?base_url().substr($menu['menus'][$menu_id]['link'],0, -1):"javascript:void(0);")." data-href=".substr($menu['menus'][$menu_id]['link'],0, -1).">";
                $html .= "  <span style='margin-left: -10px; margin-right: -10px;' class='icon_menu ".(!empty($menu['menus'][$menu_id]['class'])?$menu['menus'][$menu_id]['class']:"")."' ></span>
                            <span class='module_text'>
                            ".$menu['menus'][$menu_id]['name']."
                            <span>
                            </a>";
                
                    if(!empty($menu['menus'][$menu_id]['name'])){
                            if(!empty($sys["config"]["no_roulette_mouse"])):
                            $html .="<a href=".base_url().$menu['menus'][$menu_id]['link']." target='_blank' class='a_new_tab'><span style='margin-left: -10px;' class='new_tab'></span></a>";
                            else: 

                                // if(controller_exists(ucwords($controller[0])) and count(explode("/", substr($menu['menus'][$menu_id]['link'],0, -1))) > 1 )
                                // $html .="<a href='".base_url().substr($menu['menus'][$menu_id]['link'],0, -1)."View'  class='add_record'><span style='margin-left: -10px;' class='add_record'></span></a>";
                            
                            endif;
                    }

                $html .= $this->buildMenu($menu_id, $menu);

                $html .= "</li>";
            }
        }

        // foreach ($menu['parent_menus'][$parent] as $menu_id) {
        //     if (!isset($menu['parent_menus'][$menu_id])) {
        //         $html .= "<li><a href='" . $menu['menus'][$menu_id]['link'] . "'>" . $menu['menus'][$menu_id]['name'] . "</a></li>";
        //     }
        //     if (isset($menu['parent_menus'][$menu_id])) {
        //         $html .= "<li class='has-sub'><a href='" . $menu['menus'][$menu_id]['link'] . "'>" . $menu['menus'][$menu_id]['name'] . "</a>";
        //         $html .= buildMenu($menu_id, $menu);
        //         $html .= "</li>";
        //     }
        // }

        $html .= "</ul>";
    }
    
    return $html;

    }
    
    // Menu de categorias
    public function getDynamicMenuCategory($input_search=null){

    $CI =& get_instance();
    $CI->load->model("dynamic_menu_model");

        $menu_html="";
        $menu_html.='<link rel="stylesheet" type="text/css" href="'.base_url().'css/template/ecommerce/dynamic_menu_category/styles.css" />';
        $menu_html.='<script type="text/javascript" src="'.base_url().'css/template/ecommerce/dynamic_menu_category/script.js"></script>';
        $menu_html.=$this->buildMenuCategory(0, $CI->dynamic_menu_model->get_menu_category($input_search));

        if(!empty($_POST["ajax"]))
        return print_r(json_encode( array("status"=>1,"menu"=>$menu_html ) ) ) ;
        else
        return $menu_html;
    }

    public function buildMenuCategory($parent, $menu) {
    $CI =& get_instance();
    $CI->load->model("vars_system_model");
    $sys=$CI->vars_system_model->_vars_system();
    $CI->load->helper('load_controller');

    $html = "";
    if (isset($menu['parent_menus'][$parent])) {
        $html .= '<ul class="nav" id="side-menu">';

        foreach ($menu['parent_menus'][$parent] as $menu_id) {
            $controller=explode("/", substr($menu['menus'][$menu_id]['link'],0, -1));

            if (!isset($menu['parent_menus'][$menu_id])) {
                $html .= "<li data-id_menu='".encode_id($menu_id) ."'>";
                // $html .= "<a id='a_menu_click' class='a_menu' href=javascript:void(0); data-href=".substr($menu['menus'][$menu_id]['link'],0, -1).">";
                $html .= "<a id='a_menu_click' class='a_menu' href='".(!empty(substr($menu['menus'][$menu_id]['link'],0, -1))?base_url().substr($menu['menus'][$menu_id]['link'],0, -1):"javascript:void(0);")."' data-href=".substr($menu['menus'][$menu_id]['link'],0, -1).">";
                $html  .= "     <span class='icon_menu ".(!empty($menu['menus'][$menu_id]['class'])?$menu['menus'][$menu_id]['class']:"")."'></span>
                                <span class='module_text'>"
                                . $menu['menus'][$menu_id]['name'] ."
                                <span>
                            </a>";

                            if(!empty($sys["config"]["no_roulette_mouse"])):
                    $html .="<a href=".base_url().$menu['menus'][$menu_id]['link']." target='_blank' class='a_new_tab'><span style='margin-left: -10px;' class='new_tab'></span></a>";
                            else:
                                // pr(end(explode("/",substr($menu['menus'][$menu_id]['link'],0, -1) ) ));
                                // if(!empty($sys["config"]["no_click_right_mouse"]))
                                // if(controller_exists(ucwords($controller[0])) and count(explode("/", substr($menu['menus'][$menu_id]['link'],0, -1))) > 1)
                                // $html .="<a href='".base_url().$menu['menus'][$menu_id]['link']."View'  class='add_record'><span style='margin-left: -10px;' class='add_record'></span></a>";

                            endif;

                $html .="</li>";
            }

            if (isset($menu['parent_menus'][$menu_id])) {

                $html .= "<li class='has-sub' data-id_menu='".encode_id($menu_id) ."'>";
                // $html .=  "<a id='a_menu_click' class='a_sub_menu' href=".base_url().$menu['menus'][$menu_id]['link']." data-href=".substr($menu['menus'][$menu_id]['link'],0, -1).">";
                $html .=  "<a id='a_menu_click' class='a_sub_menu' href=".(!empty(substr($menu['menus'][$menu_id]['link'],0, -1))?base_url().substr($menu['menus'][$menu_id]['link'],0, -1):"javascript:void(0);")." data-href=".substr($menu['menus'][$menu_id]['link'],0, -1).">";
                $html .= "  <span class='icon_menu ".(!empty($menu['menus'][$menu_id]['class'])?$menu['menus'][$menu_id]['class']:"")."' ></span>
                            <span class='module_text'>
                            ".$menu['menus'][$menu_id]['name']."
                            <span>
                            </a>";
                
                    if(!empty($menu['menus'][$menu_id]['name'])){
                            if(!empty($sys["config"]["no_roulette_mouse"])):
                            $html .="<a href=".base_url().$menu['menus'][$menu_id]['link']." target='_blank' class='a_new_tab'><span style='margin-left: -10px;' class='new_tab'></span></a>";
                            else: 

                                // if(controller_exists(ucwords($controller[0])) and count(explode("/", substr($menu['menus'][$menu_id]['link'],0, -1))) > 1 )
                                // $html .="<a href='".base_url().substr($menu['menus'][$menu_id]['link'],0, -1)."View'  class='add_record'><span style='margin-left: -10px;' class='add_record'></span></a>";
                            
                            endif;
                    }

                $html .= $this->buildMenuCategory($menu_id, $menu);

                $html .= "</li>";
            }
        }

        $html .= "</ul>";
    }
    
    return $html;

    }
// </menu categorias>

    // Menu de header top
    public function getDynamicMenuModulesNavBar($input_search=null){

    $CI =& get_instance();
    $CI->load->model("dynamic_menu_model");

        $menu_html="";
        $menu_html.='<link rel="stylesheet" type="text/css" href="'.base_url().'css/template/ecommerce/dynamic_menu_navBar/styles_navBar.css" />';
        $menu_html.='<script type="text/javascript" src="'.base_url().'css/template/ecommerce/dynamic_menu_navBar/script_navBar.js"></script>';
        $menu_html.=$this->buildMenuModulesNavBar(0, $CI->dynamic_menu_model->get_menu_category($input_search));

        if(!empty($_POST["ajax"]))
        return print_r(json_encode( array("status"=>1,"menu"=>$menu_html ) ) ) ;
        else
        return $menu_html;
    }

    public function buildMenuModulesNavBar($parent, $menu) {
    $CI =& get_instance();
    $CI->load->model("vars_system_model");
    $sys=$CI->vars_system_model->_vars_system();
    $CI->load->helper('load_controller');
   // $html = "<ul>
   //    <li><a href='#'>Home</a></li>
   //    <li><a href='#'>Products</a>
   //       <ul>
   //          <li><a href='#' class='has-sub'>Product 1</a>
   //             <ul>
   //                <li><a href='#'>Sub Product</a></li>
   //                <li><a href='#'>Sub Product</a></li>
   //             </ul>
   //          </li>
   //          <li><a href='#'>Product 2</a>
   //             <ul>
   //                <li><a href='#'>Sub Product</a></li>
   //                <li><a href='#'>Sub Product</a></li>
   //             </ul>
   //          </li>
   //       </ul>
   //    </li>
   //    <li><a href='#'>About</a></li>
   //    <li><a href='#'>Contact</a></li>
   // </ul>";
   // return $html;
    $html = "";
    if (isset($menu['parent_menus'][$parent])) {
        $html .= '<ul>';

        foreach ($menu['parent_menus'][$parent] as $menu_id) {
            $controller=explode("/", substr($menu['menus'][$menu_id]['link'],0, -1));

            if (!isset($menu['parent_menus'][$menu_id])) {
                $html .= "<li data-id_menu='".encode_id($menu_id) ."'>";
                // $html .= "<a id='a_menu_click' class='a_menu' href=javascript:void(0); data-href=".substr($menu['menus'][$menu_id]['link'],0, -1).">";
                $html .= "<a href='".(!empty($menu['menus'][$menu_id]['link'])?base_url().$menu['menus'][$menu_id]['link'].str_replace(" ", "-", $menu['menus'][$menu_id]['name']):"javascript:void(0);")."' data-href=".substr($menu['menus'][$menu_id]['link'],0, -1).">";
                     $html  .= "<span class='module_text'>"
                                . $menu['menus'][$menu_id]['name'] ."
                                <span>
                            </a>";
                $html .="</li>";
            }

            if (isset($menu['parent_menus'][$menu_id])) {

                $html .= "<li class='has-sub' data-id_menu='".encode_id($menu_id) ."'>";
                // $html .=  "<a id='a_menu_click' class='a_sub_menu' href=".base_url().$menu['menus'][$menu_id]['link']." data-href=".substr($menu['menus'][$menu_id]['link'],0, -1).">";
                $html .=  "<a href=".(!empty($menu['menus'][$menu_id]['link'])?base_url().$menu['menus'][$menu_id]['link'].str_replace(" ", "-", $menu['menus'][$menu_id]['name']):"javascript:void(0);")." data-href=".substr($menu['menus'][$menu_id]['link'],0, -1).">";
                $html .= " <span class='module_text'>
                            ".$menu['menus'][$menu_id]['name']."
                            <span>
                            </a>";

                $html .= $this->buildMenuModulesNavBar($menu_id, $menu);

                $html .= "</li>";
            }
        }

        $html .= "</ul>";
    }
    
    return $html;

    }
// </menu header top>  

    public function module_text_from_id($module) {
    
    $name="";
    $CI =& get_instance();
    $CI->load->model("dynamic_menu_model");
    $name=$CI->dynamic_menu_model->module_name($module);

    return $name;
    }

    public function get_module_childrens($module_id) {

    $module_childrens=array();
    $CI =& get_instance();
    $CI->load->model("dynamic_menu_model");
    $module_childrens=$CI->dynamic_menu_model->module_childrens($module_id);

    return $module_childrens;
    }
 
    // <LinkinModule>
    public function get_back_access($source_module,$id=null){
    $CI =& get_instance();
    $CI->load->model("config/config_model");

        $source_module_pro=explode("/", substr($source_module,0, -1)); // quitamos el ultimo caracter /
        array_pop($source_module_pro);
        $module_tmp="";
        $modules_quick="";
        foreach ($source_module_pro as $key => $module_row){

        $module_tmp.=$module_row."/";

        $modules_quick[$module_tmp]=$CI->config_model->m_name($module_tmp);
        
        @$module=end(explode("/", substr($modules_quick[$module_tmp]["link"],0, -1)));
        $modules_quick[$module_tmp]["link_sub"]=substr($modules_quick[$module_tmp]["link"],0, -1);
        $modules_quick[$module_tmp]["module"]=$module;
        $link_module=$modules_quick[$module_tmp]["link"];
        // $link_module=substr($modules_quick[$module_tmp]["link"],0, -1);

        // pr($modules_quick);
        // if(count(explode("/", $module_tmp)) > 2)
        // $modules_quick[$module_tmp]["link_view"]=$link_module."View";
        // else if(count(explode("/", $module_tmp)) >= 2)
        // $modules_quick[$module_tmp]["link_view"]=$link_module."/".$link_module."View";
        // else
        $modules_quick[$module_tmp]["link_view"]=$link_module;

        }
        return $modules_quick;
    }
    // </LinkinModule>    
}

 ?>