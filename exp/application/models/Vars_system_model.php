<?php
class Vars_system_model extends CI_Model{

	public function __construct() {
        parent::__construct();
    }

	function x_vars_value_processing($type,$value) {

		if($type=="array")
		 return !is_array( @eval(" return $value; ") ) ? array() : eval(" return $value; ") ;
		else
		 return eval(" return ($type) \$value;");

	}
    public function _vars_system(){

		$data=$this->db->select('id,category,type,name,value,description')
				->where("status",1)
				->get("_vars_system")
				->result_array();
				;
				// echo $this->db->last_query();
		$var_system=array();

		foreach ($data as $key => $row) {
			
		// regresamos el value en el formato que esta configurado
		$row["value"]=$this->x_vars_value_processing($row["type"],$row["value"]);

		// hacemos un explode de ejemplo: config/forms_fields etc.
		$at=explode("/",$row["name"]);

		$st="";

			// segundo definimos el valor del arreglo
			foreach($at AS $k=>$v) {

				$st.="['".str_replace("\'","\\'",trim($v))."']";

				if( ($k+1)!=count($at) ):
				 	$st_eval=" if( !empty(\$var_system$st) ) \$var_system$st=array(); ";
				else:
					$st_eval=" \$var_system$st=\$row['value']; ";
				endif;

			}
				eval($st_eval);
		}
		return $var_system;
    }

    function modules_html_list($modules,$html="",$hidden=false,$only_parent=false,$home_link=false) {

	if(!is_string($html))
	 $html="";

	$html_tmp="";

	// alphabetical sort (( asc ))

	$modules_tmp=array();

	foreach($modules as $v) {

		$st=module_text_from_id($sys["config"]["module"][$v]["id"]);
		$modules_tmp[$st]=$v;

	}

	ksort($modules_tmp,SORT_STRING);

	$modules=array_values($modules_tmp);

	// ...

	if($home_link) {

		$html_tmp.="<li class='item home_item'>";
		$html_tmp.="<div class='image'></div>";
		$html_tmp.="<a href='$sys[url]'>home</a>";
		$html_tmp.="</li>";

	}

	// ...

	foreach($modules as $module_id):

		$is_parent=(substr_count($module_id,"/")===0);

		if($only_parent and !$is_parent)
		 continue;

		$module_data=$sys["config"]["module"][$module_id];

		if( !$module_data["enabled"]
			or ($module_tmp["super_user_limit_access_modification"] and !$user["is_super"])
			or !rights_validation("$module_id/read","default","boolean")
				)
		 continue;

		$html_tmp.="<li class='item".( !$is_parent ? "" : " {$module_id}_item" )."'>";

		$html_tmp.="<div class='image'></div>";
		$html_tmp.="<a href='$sys[url]$module_data[path]'>$module_data[name]</a>";

		if($module_childrens=get_module_childrens($module_id) and ($html_tmp2=modules_html_list($module_childrens))):

			$html_tmp.="<div class='haveChildrensMark'>&gt;</div>";
			$html_tmp.=$html_tmp2;

		endif;

		$html_tmp.="</li>";

	endforeach;

	// ...

	if($html_tmp) {

		$html.="<ul class='menu'".( !$hidden ? "" : " style=' display:none; '" ).">";
		$html.=$html_tmp;
		$html.="</ul>";

	}

	// ...

	return $html;

	}

}
?>