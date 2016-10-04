<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."libraries/php/pdf/tcpdf/5_9_172/config/lang/eng.php");
require_once(APPPATH."libraries/php/pdf/tcpdf/5_9_172/tcpdf.php");
require_once(APPPATH."libraries/php/pdf/tcpdf-class-extend.php");
require_once(APPPATH."libraries/php/pdf/tcpdf-data-printing-lib.php");
require_once(APPPATH."libraries/php/myCFDX/MyCFDX.php");

require_once(APPPATH."libraries/php/numberLetter/numberLetter.php");
require_once(APPPATH."libraries/php/pdftk/pdftk.php");

if ( ! function_exists('printGet'))
{
	function printGet($source_module,$id,$http_params,$options) {


	$CI=& get_instance();
	$CI->load->helper('xml/xml_helper');

	$_CONFIG_MODULE=config_module_pdf($source_module);

    $CI->load->model("world_model");

	$data["enterprise_country_text"] =$CI->world_model->get_w_country_text($CI->sys["enterprise_fiscal"]["country"]);
	$data["enterprise_state_text"]   =$CI->world_model->get_w_state_text($CI->sys["enterprise_fiscal"]["country"]);

	$CI->load->model('pdf/pdf_model');

	$data["data_records"]=$CI->pdf_model->get_table_details($source_module,$id);

	// CONFIGURACION DE LA FACTURA Y SUS CARPETAS PARA GUARDAR XML
if(!(empty($data["data_records"]["sat_version"])) ){
	$_INVOICE_MODE_CONFIG=invoice_mode_config($source_module,$data["data_records"]["sat_version"]);
	
	$xmlPath=$_INVOICE_MODE_CONFIG["storage_stamp"].$data["data_records"]["id"]."-".$data["data_records"]["folio"].".xml";
	
	if(file_exists($xmlPath)){

	$xml_array =xmlstr_to_array(file_get_contents($xmlPath));
	$data["xml_array"]=$xml_array;
// $_INVOICE_MODE_CONFIG["name"]." ".
	$data["document_version_text"] =$xml_array["@attributes"]["version"];
	$data["payment_method_text"]   =$xml_array["@attributes"]["metodoDePago"];

	if(!empty($xml_array["@attributes"]["NumCtaPago"]))
	$data["account_payment_text"]=$xml_array["@attributes"]["NumCtaPago"];

	if($xml_array["@attributes"]["condicionesDePago"])
	$data["payment_condition_text"]=$xml_array["@attributes"]["condicionesDePago"];

	$data["cert_number"]  =$xml_array["@attributes"]["noCertificado"];
	$data["digital_seal"] =$xml_array["@attributes"]["sello"];

// CADENA
if($data["data_records"]["sat_version"]=="cfdi"){
	$data["uuid"]=$xml_array["cfdi:Complemento"]["tfd:TimbreFiscalDigital"]["@attributes"]["UUID"];
	$data["cert_date"]=$xml_array["cfdi:Complemento"]["tfd:TimbreFiscalDigital"]["@attributes"]["FechaTimbrado"];
	$data["cert_number_sat"]=$xml_array["cfdi:Complemento"]["tfd:TimbreFiscalDigital"]["@attributes"]["noCertificadoSAT"];
	$data["sat_seal"]=$xml_array["cfdi:Complemento"]["tfd:TimbreFiscalDigital"]["@attributes"]["selloSAT"];
	
	$tmp=array();
	$tmp[]=$xml_array["cfdi:Complemento"]["tfd:TimbreFiscalDigital"]["@attributes"]["version"];
	$tmp[]=$xml_array["cfdi:Complemento"]["tfd:TimbreFiscalDigital"]["@attributes"]["UUID"];
	$tmp[]=$xml_array["cfdi:Complemento"]["tfd:TimbreFiscalDigital"]["@attributes"]["FechaTimbrado"];
	$tmp[]=$xml_array["cfdi:Complemento"]["tfd:TimbreFiscalDigital"]["@attributes"]["selloCFD"];
	$tmp[]=$xml_array["cfdi:Complemento"]["tfd:TimbreFiscalDigital"]["@attributes"]["noCertificadoSAT"];
	$tmp="||".implode("|",$tmp)."||";

	$data["original_string"]=$tmp;
	$data["original_string_name"]="cadena original del complemento de certificación digital del SAT";

	// ...

	$tmp=number_format($xml_array["@attributes"]["total"],2);
	$tmp=explode(".",$tmp);
	$tmp[0]=str_pad($tmp[0],10,"0",STR_PAD_LEFT);
	$tmp[1]=str_pad($tmp[1],6,"0",STR_PAD_RIGHT);
	$tmp=implode(".",$tmp);

	$tmp2=array();
	$tmp2[]="re=".$xml_array["cfdi:Emisor"]["@attributes"]["rfc"];
	$tmp2[]="rr=".$xml_array["cfdi:Receptor"]["@attributes"]["rfc"];
	$tmp2[]="tt=".$tmp;
	$tmp2[]="id=".$xml_array["cfdi:Complemento"]["tfd:TimbreFiscalDigital"]["@attributes"]["UUID"];
	$tmp2="?".implode("&",$tmp2);

	$data["qrcode_cbb"]=$tmp2;
	}

	} // file exists
}
	// NUMERO CON LETRA
		$tmp=new CNumeroaletra;
		$tmp->setMayusculas(1);
		$tmp->setPrefijo("==");
		// $tmp->setSufijo("$currency[reference] ==");
		$tmp->setSufijo("MXN");
		if(!empty($xml_array["@attributes"]["total"])):
			$tmp->setMoneda($xml_array["@attributes"]["Moneda"]);
			$tmp->setNumero($xml_array["@attributes"]["total"]);
		else:
			$tmp->setMoneda("MXN");
			$tmp->setNumero($data["data_records"]["import"]);			
		endif;

		$data["total_with_letter"]=$tmp->letra();
	// …

	if(!empty($http_params["template"]))
	$data["template"]=config_template($http_params["template"]);
	else
	$data["template"]=config_template($_CONFIG_MODULE["DEFAULT_TEMPLATE"]);

	$data["sys"]=$CI->sys;
	$data["_CONFIG_MODULE"]=$_CONFIG_MODULE;

	$data["options"]=(!empty($options)?$options:$_CONFIG_MODULE["DEFAULT_TEMPLATE_OPTIONS"]);

	// $response_pdf=$CI->load->view("pdf/getPdf/".$_CONFIG_MODULE["DEFAULT_GET_PDF"],$data,true);
	$tmp_pdf=function($data,$_CONFIG_MODULE){
	extract($data);
	extract($_CONFIG_MODULE);
	return require(VIEWPATH."pdf/getPdf/".$_CONFIG_MODULE["DEFAULT_GET_PDF"].".php");
	};
	
	$response_pdf=$tmp_pdf($data,$_CONFIG_MODULE);

	return $response_pdf;

	};
}		

if ( ! function_exists('config_module_pdf'))
{
	function config_module_pdf($source_module) {
		$CI=& get_instance();
		$CI->load->model("vars_system_model");
		$sys=$CI->vars_system_model->_vars_system();
		$configs=array(
			"admin/sale/"=>array(
			'TEMPLATES_DIR'=>APPPATH."helpers/pdf/templates/",
			'DEFAULT_TEMPLATE'=>$sys["config"]["sale_print_default_template"],
			'DEFAULT_TEMPLATE_OPTIONS'=>$sys["config"]["sale_print_default_template_options"],
			'DEFAULT_TEMPLATE_WATERMARK'=>$sys["config"]["sale_print_default_template_watermark"],
			'DOC_TYPE_NAME'=>"Factura",
			'DEFAULT_GET_PDF'=>"get_pdf",
			'DEFAULT_TCPDFCONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfConfigDefault.php",
			'TCPDF_DETAILS_HEADER'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfDetailsDataPreparing.php",
			'TCPDF_MULTI_PAGE'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfMultiPage.php",
			'DETAILS_CONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/detailsConfig.php",
			'PROMISSORY_ENABLED'=>true,
			),

			"admin/sale/remission/"=>array(
			'TEMPLATES_DIR'=>APPPATH."helpers/pdf/templates/",
			'DEFAULT_TEMPLATE'=>$sys["config"]["print_default_template"],
			'DEFAULT_TEMPLATE_OPTIONS'=>$sys["config"]["print_default_template_options"],
			'DEFAULT_TEMPLATE_WATERMARK'=>$sys["config"]["print_default_template_watermark"],
			'DOC_TYPE_NAME'=>"Remision",
			'DEFAULT_GET_PDF'=>"get_pdf",
			'DEFAULT_TCPDFCONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfConfigDefault.php",
			'TCPDF_DETAILS_HEADER'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfDetailsDataPreparing.php",
			'TCPDF_MULTI_PAGE'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfMultiPage.php",
			'DETAILS_CONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/detailsConfig.php",
			'PROMISSORY_ENABLED'=>true,
			),

			"admin/sale/request/"=>array(
			'TEMPLATES_DIR'=>APPPATH."helpers/pdf/templates/",
			'DEFAULT_TEMPLATE'=>$sys["config"]["print_default_template"],
			'DEFAULT_TEMPLATE_OPTIONS'=>$sys["config"]["print_default_template_options"],
			'DEFAULT_TEMPLATE_WATERMARK'=>"",
			'DOC_TYPE_NAME'=>"Pedido",
			'DEFAULT_GET_PDF'=>"get_pdf",
			'DEFAULT_TCPDFCONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfConfigDefault.php",
			'TCPDF_DETAILS_HEADER'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfDetailsDataPreparing.php",
			'TCPDF_MULTI_PAGE'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfMultiPage.php",
			'DETAILS_CONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/detailsConfig.php",
			'PROMISSORY_ENABLED'=>true,
			),

			"admin/sale/quatition/"=>array(
			'TEMPLATES_DIR'=>APPPATH."helpers/pdf/templates/",
			'DEFAULT_TEMPLATE'=>$sys["config"]["print_default_template"],
			'DEFAULT_TEMPLATE_OPTIONS'=>$sys["config"]["print_default_template_options"],
			'DEFAULT_TEMPLATE_WATERMARK'=>"",
			'DOC_TYPE_NAME'=>"Cotización",
			'DEFAULT_GET_PDF'=>"get_pdf",
			'DEFAULT_TCPDFCONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfConfigDefault.php",
			'TCPDF_DETAILS_HEADER'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfDetailsDataPreparing.php",
			'TCPDF_MULTI_PAGE'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfMultiPage.php",
			'DETAILS_CONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/detailsConfig.php",
			'PROMISSORY_ENABLED'=>true,
			),

			"admin/sale/creditNote/"=>array(
			'TEMPLATES_DIR'=>APPPATH."helpers/pdf/templates/",
			'DEFAULT_TEMPLATE'=>$sys["config"]["print_default_template"],
			'DEFAULT_TEMPLATE_OPTIONS'=>$sys["config"]["print_default_template_options"],
			'DEFAULT_TEMPLATE_WATERMARK'=>"",
			'DOC_TYPE_NAME'=>"Nota de credito",
			'DEFAULT_GET_PDF'=>"get_pdf",
			'DEFAULT_TCPDFCONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfConfigDefault.php",
			'TCPDF_DETAILS_HEADER'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfDetailsDataPreparing.php",
			'TCPDF_MULTI_PAGE'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfMultiPage.php",
			'DETAILS_CONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/detailsConfig.php",
			'PROMISSORY_ENABLED'=>true,
			),

			"admin/sale/openingBalance/"=>array(
			'TEMPLATES_DIR'=>APPPATH."helpers/pdf/templates/",
			'DEFAULT_TEMPLATE'=>$sys["config"]["print_default_template"],
			'DEFAULT_TEMPLATE_OPTIONS'=>$sys["config"]["print_default_template_options"],
			'DEFAULT_TEMPLATE_WATERMARK'=>"",
			'DOC_TYPE_NAME'=>"Nota de credito",
			'DEFAULT_GET_PDF'=>"get_pdf",
			'DEFAULT_TCPDFCONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfConfigDefault.php",
			'TCPDF_DETAILS_HEADER'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfDetailsDataPreparing.php",
			'TCPDF_MULTI_PAGE'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfMultiPage.php",
			'DETAILS_CONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/detailsConfig.php",
			'PROMISSORY_ENABLED'=>true,
			),
			"admin/purchase/"=>array(
			'TEMPLATES_DIR'=>APPPATH."helpers/pdf/templates/",
			'DEFAULT_TEMPLATE'=>$sys["config"]["print_default_template"],
			'DEFAULT_TEMPLATE_OPTIONS'=>$sys["config"]["print_default_template_options"],
			'DEFAULT_TEMPLATE_WATERMARK'=>"",
			'DOC_TYPE_NAME'=>"Compra",
			'DEFAULT_GET_PDF'=>"get_pdf",
			'DEFAULT_TCPDFCONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfConfigDefault.php",
			'TCPDF_DETAILS_HEADER'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfDetailsDataPreparing.php",
			'TCPDF_MULTI_PAGE'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfMultiPage.php",
			'DETAILS_CONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/detailsConfig.php",
			'PROMISSORY_ENABLED'=>true,
			),

			"admin/purchase/order/"=>array(
			'TEMPLATES_DIR'=>APPPATH."helpers/pdf/templates/",
			'DEFAULT_TEMPLATE'=>$sys["config"]["print_default_template"],
			'DEFAULT_TEMPLATE_OPTIONS'=>$sys["config"]["print_default_template_options"],
			'DEFAULT_TEMPLATE_WATERMARK'=>"",
			'DOC_TYPE_NAME'=>"Ordenes de compra",
			'DEFAULT_GET_PDF'=>"get_pdf",
			'DEFAULT_TCPDFCONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfConfigDefault.php",
			'TCPDF_DETAILS_HEADER'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfDetailsDataPreparing.php",
			'TCPDF_MULTI_PAGE'=>APPPATH."helpers/pdf/tcpdfDocConfig/tcpdfMultiPage.php",
			'DETAILS_CONFIG'=>APPPATH."helpers/pdf/tcpdfDocConfig/detailsConfig.php",
			'PROMISSORY_ENABLED'=>true,
			),
			
		);

		// process
		$data=false;

		if($configs[$source_module])
		 $data=$configs[$source_module];

		return $data;

	};

}

if ( ! function_exists('config_template'))
{
	function config_template($template) {

		$configs=array(
			"basic"=>array(

			'template_image_page_main'=>APPPATH."/helpers/pdf/images/basic.png",
			'template_image_page_main_dpi'=>72,
			'template_image_page_generic'=>APPPATH."/helpers/pdf/images/generic.png",
			'template_image_page_generic_dpi'=>72,
			'template_image_page_details'=>APPPATH."/helpers/pdf/images/page_bar.png",
			'template_image_page_details_dpi'=>72,

			),

		);		

				// process
		$data=false;

		if($configs[$template])
		 $data=$configs[$template];

		return $data;
	}
}
?>	