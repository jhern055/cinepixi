<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('trim_shell_exec'))
{
	function  trim_shell_exec($command){
		
		$string_explote=explode("\n", delete_format(trim(shell_exec_function($command))));
		// if(!empty($string_explote))
		// foreach($string_explote as $k=>$val): 
		// $string_explote[$k]=explode(" ", delete_format($val)); 
		// endforeach; 
	    
	    return $string_explote;

	}
}

if ( ! function_exists('delete_format'))
{
	function  delete_format($string){
	     $string = trim($string);
	     $string = str_replace('&nbsp;', ' ', $string);
	     $string = preg_replace('/\s\s+/', ' ', $string);
	     return $string;
	}
}

if ( ! function_exists('shell_exec_function')){
	function shell_exec_function($cmd) {

	    if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");

	    // log in at server1.example.com on port 2222
	    if(!($con = ssh2_connect("localhost", 2222))){
	        echo "fail: unable to establish connection\n";
	    } else {
	        // try to authenticate with username root, password secretpassword
	        if(!ssh2_auth_password($con, "root", "Plex2016.,")) {
	            echo "fail: unable to authenticate\n";
	        } else {
	            // allright, we're in!
	    // fdisk -l
	            // execute a command

	            if (!($stream = ssh2_exec($con, $cmd ))) {
	                echo "fail: unable to execute command\n";
	            } else {
	                // collect returning data from command
	                
	                // stream_set_blocking($errorStream, true);
	                stream_set_blocking($stream, true);
	                $data = "";
	                while ($buf = fread($stream,4096)) {
	                    $data .= $buf;
	                }

	                fclose($stream);
	                return $data;
	            }
	        }
	    }
	}
}
if ( ! function_exists('array_sort_by_column'))
{
function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    if($arr)
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    if($sort_col)
    array_multisort($sort_col, $dir, $arr);
 }

}

if ( ! function_exists('source_emailConfig'))
{
	function source_emailConfig($source_module) {
		$configs=array(

			"admin/sale/"=>array(
				"from"=>"sales@regiosweb.com",
				"subject"=>"Factura",
				"message"=>"",

			),
			"admin/sale/remission/"=>array(
				"from"=>"sales@regiosweb.com",
				"subject"=>"Remision",
				"message"=>"",

			),
			"admin/sale/request/"=>array(
				"from"=>"pedidos@regiosweb.com",
				"subject"=>"Pedido",
				"message"=>"",

			),
			"admin/sale/quatition/"=>array(
				"from"=>"cotizaciones@regiosweb.com",
				"subject"=>"Cotizaciones",
				"message"=>"",

			),
			"admin/sale/openingBalance/"=>array(
				"from"=>"saldoinicial@regiosweb.com",
				"subject"=>"Saldo inicial",
				"message"=>"",

			),			
			"admin/sale/creditNote/"=>array(
				"from"=>"creditNote@regiosweb.com",
				"subject"=>"Nota de credito",
				"message"=>"",

			),								
			"admin/purchase/order/"=>array(
				"from"=>"purchase@regiosweb.com",
				"subject"=>"Compra",
				"message"=>"",

			),

			"admin/purchase/order/"=>array(
				"from"=>"order@regiosweb.com",
				"subject"=>"Orden de compra",
				"message"=>"",

			),				
				
		);

		$data=false;
		if($configs[$source_module])
		 $data=$configs[$source_module];
		
		return $data;
	}
}

if ( ! function_exists('path_mode_config'))
{
	function path_mode_config() {

	}
}	

if ( ! function_exists('invoice_mode_config'))
{
function invoice_mode_config($source_module,$cfdi_version=null) {
	$CI=& get_instance();
	$CI->load->model("vars_system_model");
	$sys=$CI->vars_system_model->_vars_system();

	$configs=array(

		// "GENERAL"=>array(

		// 	"name"=>"GENERAL",
		// 	"parent_module"=>"config/invoice",
		// 	"series_module"=>"invoice/series",
		// 	"series_db_table"=>"invoice_general_series",
		// 	"shcp_file_module"=>"",
		// 	"shcp_file_db_table"=>"",
		// 	"shcp_file_upload_storage_path"=>"",
		// 	"shcp_file_upload_allowed_ext"=>"",
		// 	"shcp_file_upload_max_size"=>"",			
		// 	"text"=>"facturación general",
		// 	"storage_path"=>"",

		// ),
		// "CFD"=>array(

		// 	"name"=>"CFD",
		// 	"parent_module"=>"invoice",
		// 	"series_module"=>"invoice/series",
		// 	"series_db_table"=>"invoice_electronic_cfd_series",
		// 	"shcp_file_module"=>"invoice/shcp-file",
		// 	"shcp_file_db_table"=>"invoice_electronic_cfd_shcp_file",
		// 	"shcp_file_upload_storage_path"=>$sys["config"]["invoice_electronic_cfd_shcp_file_upload_storage_path"],
		// 	"shcp_file_upload_allowed_ext"=>$sys["config"]["invoice_electronic_cfd_shcp_file_upload_allowed_ext"],
		// 	"shcp_file_upload_max_size"=>$sys["config"]["invoice_electronic_cfd_shcp_file_upload_max_size"],
		// 	"text"=>"facturación electrónica cfd",
		// 	"storage_path"=>$sys["config"]["invoice_electronic_cfd_file_xml_storage_path"],

		// ),
		"admin/sale/"=>array(
			"cfdi"=>array(

				"name"=>"CFDI",
				"parent_module"=>"invoice/",
				"series_module"=>"invoice/series",
				"series_db_table"=>"invoice_electronic_cfdi_series",
				// "series_accounting_db_table"=>"accounting_cfdi_series",
				"shcp_file_module"=>"invoice/shcp-file",
				"shcp_file_db_table"=>"shcp_file",
				"shcp_file_upload_storage_path"=>APPPATH.$sys["storage"]["shcp_file"],
				"shcp_file_upload_allowed_ext"=>4000,
				"shcp_file_upload_max_size"=>4000,
				"text"=>"facturación electrónica cfdi",
				"text_accounting"=>APPPATH."contabilidad electrónica",
				"storage_stamp"=>APPPATH.$sys["storage"]["invoice_cfdi_file_xml_storage_path"],
				"url_friendly_stamp"=>base_url()."application/".$sys["storage"]["invoice_cfdi_file_xml_storage_path"],
				"storage_cancel"=>APPPATH.$sys["storage"]["invoice_cfdi_file_xml_storage_path_canceled"],
				"url_friendly_cancel"=>base_url()."application/".$sys["storage"]["invoice_cfdi_file_xml_storage_path"],
				"storage_attachments"=>APPPATH.$sys["storage"]["sale_attachment_upload_storage_path"],

			),
		),

	);

	// process
	$data=false;

	// if($configs[$source_module][$sys["config"]["invoice_electronic_mode"]])
	//  $data=$configs[$source_module][$sys["config"]["invoice_electronic_mode"]];

	if(!empty($cfdi_version))
	if($configs[$source_module][$cfdi_version])
	 $data=$configs[$source_module][$cfdi_version];

	return $data;

};

}
if ( ! function_exists('import_processing'))
{
function import_processing($quantity,$price,$total_sub,$discount_percent,$tax_ieps_percent,$tax_iva_percent,$tax_iva_retained_percent,$tax_isr_percent,$list_prices_by_provider=null) {

// cambia los decimales en cantidad de articulo ejemplo 392.365 a 392.370 si lo pones a 2 decimales
	$decimals=4;

	// decimals have this value cuz data like taxes, discounts, etc should preserve 4 decimals, consider next sample ::
	//
	// 10.5099 	+	10.5099 =21.0198	- ok!
	// 10.50	+	10.50	=21		- rounding to 2 decimals cause "lost" of cents, this is much more notorious on big numbers

	// $quantity and $price can be optionals (( $total_sub should be defined ))

	if((is_numeric($quantity) and $quantity>0) and (is_numeric($price) and $price>0)) {

		$quantity=(float) number_format($quantity,$decimals,".","");
		$price=(float) number_format($price,$decimals,".","");
		$list_prices_by_provider=($list_prices_by_provider ?(float) number_format($list_prices_by_provider,$decimals,".",""):0);
		$total_sub=($quantity*$price);

	}

	// ...

	$total_sub=(float) number_format($total_sub,$decimals,".","");
	$total=$total_sub;

	$discount_percent=(float) $discount_percent;
	$discount=$discount_percent/100;
	$discount=$total_sub*$discount;
	$discount=(float) number_format($discount,$decimals,".","");

	$total-=$discount;

	$tax_ieps_percent=(float) $tax_ieps_percent;
	$tax_ieps=$tax_ieps_percent/100;
	$tax_ieps=$total*$tax_ieps;
	$tax_ieps=(float) number_format($tax_ieps,$decimals,".","");

	$tax_iva_percent=(float) $tax_iva_percent;
	$tax_iva=$tax_iva_percent/100;
	$tax_iva=$total*$tax_iva;
	$tax_iva=(float) number_format($tax_iva,$decimals,".","");

	$tax_iva_retained_percent=(float) $tax_iva_retained_percent;
	$tax_iva_retained=$tax_iva_retained_percent/100;
	$tax_iva_retained=$total*$tax_iva_retained;
	$tax_iva_retained=(float) number_format($tax_iva_retained,$decimals,".","");

	$tax_isr_percent=(float) $tax_isr_percent;
	$tax_isr=$tax_isr_percent/100;
	$tax_isr=$total*$tax_isr;
	$tax_isr=(float) number_format($tax_isr,$decimals,".","");

	$total+=$tax_ieps;
	$total+=$tax_iva;
	$total-=$tax_iva_retained;
	$total-=$tax_isr;

	$total=(float) number_format($total,$decimals,".","");

	return array(

		"quantity"=>$quantity,
		"list_prices_by_provider"=>$list_prices_by_provider,
		"price"=>$price,
		"total_sub"=>$total_sub,
		"discount_percent"=>$discount_percent,
		"discount"=>$discount,
		"tax_ieps_percent"=>$tax_ieps_percent,
		"tax_ieps"=>$tax_ieps,
		"tax_iva_percent"=>$tax_iva_percent,
		"tax_iva"=>$tax_iva,
		"tax_iva_retained_percent"=>$tax_iva_retained_percent,
		"tax_iva_retained"=>$tax_iva_retained,
		"tax_isr_percent"=>$tax_isr_percent,
		"tax_isr"=>$tax_isr,
		"total"=>$total,

	);

}

}
?>