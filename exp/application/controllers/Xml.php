<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once("classes/satxmlsv32.php");
require_once(APPPATH."libraries/php/pacs/finkok.php");
require_once(APPPATH."libraries/php/pacs/ateb.php");
require_once(APPPATH."libraries/php/lib/Sanear-string/sanear-string.php");

class Xml extends CI_Controller {

public $invoice_electronic_xml_file_path;
public $file_cer_path;
public $file_key_path;
public $file_key_password;
public $sys;
public $folio_response;
public $_INVOICE_MODE_CONFIG;
public $source_module;

public $pac_user;
public $pac_password;

public function __construct() {

	parent::__construct();	
	$this->load->model("vars_system_model");
	$this->load->model("config/invoice/series/folio_model");
	$this->load->model("config/config_model");
	$this->load->model("config/invoice/invoice_model");
	$this->load->model("admin/sale/sale_model");
	$this->load->model("admin/client/client_model");
	$this->load->model("admin/stock/catalog/article/article_model");
	$this->load->helper('xml/xml_helper');

    $sys=$this->vars_system_model->_vars_system();

	$http_params=$_POST;
	$http_params      =array(
	"id"              =>(!empty($http_params["id"]) ?decode_id( strip_tags( $this->security->xss_clean($http_params["id"]) ) ) :""),
	"source_module" =>(!empty($http_params["source_module"]) ?decode_id( strip_tags( $this->security->xss_clean($http_params["source_module"]) ) ) :""),
	);

	extract($http_params);
	// $source_module="sale";
	// $id="2";
	$this->source_module=$source_module;
	// get data source
	$data_source=$this->folio_model->get_data_source($source_module,$id);
	// 
	$this->_INVOICE_MODE_CONFIG=invoice_mode_config($source_module,$data_source["sat_version"]);

	// get the pass and pac
	if(!empty($sys["config"]["invoice_folio_automatic"]) and !empty($data_source['subsidiary']) ){
		$this->folio_response=$this->folio_model->current_serie($source_module,$data_source['subsidiary'],true); 
		
		if(!$this->folio_response["status"])
		return $this->folio_response;
	}

		// get pac info
		$pac_data=$this->invoice_model->get_pac_id($this->folio_response["pac"]);
		// get shcp
		$shcp_file_data=$this->invoice_model->get_shcp_file_id($this->folio_response["shcp_file"]);
	// 
	$this->invoice_electronic_xml_file_path="";
	// $this->file_cer_path=APPPATH.$this->_INVOICE_MODE_CONFIG["shcp_file_upload_storage_path"]$sys["storage"]["shcp_file"].$shcp_file_data['id']."/".$shcp_file_data['file_cer'];
	$this->file_cer_path=$this->_INVOICE_MODE_CONFIG["shcp_file_upload_storage_path"].$shcp_file_data['id']."/".$shcp_file_data['file_cer'];
	$this->file_key_path=$this->_INVOICE_MODE_CONFIG["shcp_file_upload_storage_path"].$shcp_file_data['id']."/".$shcp_file_data['file_key'];
	$this->file_key_password=(!empty($shcp_file_data['password'])?$shcp_file_data['password']:"");

	$this->sys=$this->vars_system_model->_vars_system();

	$this->pac_user=$pac_data["user"];
	$this->pac_password=decode_id($pac_data["password"]);

	date_default_timezone_set('America/Mexico_City');	

}
public static function xml_fech($fech) {

$ano = substr($fech,0,4);
$mes = substr($fech,4,2);
$dia = substr($fech,6,2);
$hor = substr($fech,8,2);
$min = substr($fech,10,2);
$seg = substr($fech,12,2);
$aux = $ano."-".$mes."-".$dia."T".$hor.":".$min.":".$seg;
return ($aux);

}
public function saveXmlIn($xml,$path_save,$uuid) {

	// datos del record
	$data=$this->get_dataToXml();
	$date = date('YmdHis');
	$folio=$data["bill_data"]["id"]."-".$data["bill_data"]["folio_serie"].$data["bill_data"]["folio_number"];

	if(isset($xml)):
	$ndir=$path_save;

	if($ndir===false)
	 { $file=null;  break; }

	if(chdir($ndir)===false)
	 { $file=null;  break; }

	if(file_exists($ndir.$folio.".xml"))
	unlink($ndir.$folio.".xml");

	@$handler=fopen($folio.".xml","a");

	if(@fwrite($handler,$xml)===false)
	 { $file=null;  break; }
	endif;

		if($uuid==true):
			$data_uuid=array("uuid"=>get_uuid($ndir.$folio.".xml"));
			//ir actualizar el registro y ponerle su uuid
			switch ($this->source_module) {
				case $this->source_module=="admin/sale/":
				$this->sale_model->update_sale($data_uuid,$data["bill_data"]["id"]);
					break;
				
				default:
					# code...
				break;
			}
		endif;
return array("status"=>1,"msg"=>"Se guardo");
}

public function get_dataToXmlStatic(){
	$data["sale"]["partiality_number"]="1";
	$data["sale"]["folio_serie"]="A";
	$data["sale"]["folio_number"]="455";
	// $data["sale"]["registred_on"]=date("Y-m-d H:m:s");
	$data["sale"]["registred_on"]="2015-11-16 07:00:00";

	$data["sale"]["method_of_payment"]="2";
	$data["sale"]["payment_condition"]="6";
	$data["sale"]["sub_total"]="1";
	$data["sale"]["discount"]="0";
	$data["sale"]["discount_reason"]="0";
	$data["sale"]["import"]="1";
	$data["sale"]["sale_payment_method"]="1";
	$data["sale"]["payment_method"]="1";
	$data["sale"]["moneda"]="MXN";
	$data["sale"]["tipoCambio"]="1.0000";
	$data["sale"]["payment_method_account"]="1234";

	$data["sale"]["details"][1]["article"]          ="066";
	$data["sale"]["details"][1]["article_name"]     ="Playera";
	$data["sale"]["details"][1]["quantity"]         ="1";
	$data["sale"]["details"][1]["unit"]             ="PIEZA";
	$data["sale"]["details"][1]["description"]      ="Playera roja con .....";
	$data["sale"]["details"][1]["price"]            ="10";
	$data["sale"]["details"][1]["total_sub"]        ="10";	
	$data["sale"]["details"][1]["tax_iva_retained"] ="0";
	$data["sale"]["details"][1]["tax_iva"]          ="1.6";
	$data["sale"]["details"][1]["tax_isr"]          ="0";
	$data["sale"]["details"][1]["tax_ieps"]           ="0";
	$data["sale"]["details"][1]["tax_iva_calculated"] ="1.6";


	$data["subsidiary"]=array();
	$data["subsidiary"]["state_text"]                        ="Monterrey,Nuevo Leon";
	$data["subsidiary"]["street"]                            ="General cepeda";
	$data["subsidiary"]["outside_number"]                    ="102";
	$data["subsidiary"]["inside_number"]                     ="";
	$data["subsidiary"]["colony"]                            ="Coahuila";
	$data["subsidiary"]["location"]                          ="Juarez";
	$data["subsidiary"]["reference"]                         ="Entre calles nava";
	$data["subsidiary"]["city"]                              ="Monterrey";
	$data["subsidiary"]["state_text"]                        ="Nuevo Leon";
	$data["sale"]["enterprise_fiscal_country_text"] ="Mexico";
	$data["sale"]["subsidiary"]["zip_code"]                          ="67257";

	$data["client"]=array();
	$data["client"]["rfc"]="XXXXXX";
	$data["client"]["name"]="Isai Adalberto Garcia Gutierrez";
	$data["client"]["subsidiary"]["country"]        ="Mexico";
	$data["client"]["subsidiary"]["street"]         ="Las villas";
	$data["client"]["subsidiary"]["outside_number"] ="102";
	$data["client"]["subsidiary"]["inside_number"]  ="102";
	$data["client"]["subsidiary"]["colony"]         ="Anaguac";
	$data["client"]["subsidiary"]["location"]       ="tepeyac";
	$data["client"]["subsidiary"]["reference"]      ="Por sanluis";
	$data["client"]["subsidiary"]["city"]           ="Monterrey";
	$data["client"]["subsidiary"]["state_text"]     ="Nuevo Leon";
	$data["client"]["subsidiary"]["country_text"]   ="Mexico";
	$data["client"]["subsidiary"]["zip_code"]       ="67525";

	return $data;
}

public function get_dataToXml(){

	$http_params=$_POST;
	$http_params=array(
	"id"              =>(!empty($http_params["id"]) ?decode_id( strip_tags( $this->security->xss_clean($http_params["id"]) ) ) :""),
	"source_module" =>(!empty($http_params["source_module"]) ?decode_id( strip_tags( $this->security->xss_clean($http_params["source_module"]) ) ) :""),
	);

	extract($http_params);
	// $source_module="sale";
	// $id="2";
	if($source_module=="admin/sale/"):
	$data["bill_data"]=$this->sale_model->get_sale_id($id);
	$data["bill_data"]["details"]=$this->article_model->get_details_by_id("sale",$data["bill_data"]["id"]);
	endif;	

	// details 
	foreach($data["bill_data"]["details"] as $k=>$v) {

	$tmp=import_processing(null,null,$data["bill_data"]["details"][$k]["totalSub"],$data["bill_data"]["details"][$k]["discount"],$data["bill_data"]["details"][$k]["taxIeps"],$data["bill_data"]["details"][$k]["taxIva"],$data["bill_data"]["details"][$k]["taxIvaRetained"],$data["bill_data"]["details"][$k]["taxIsr"]);

	$data["bill_data"]["details"][$k]["total_sub"]=(float) number_format($tmp["total_sub"],2,".","");
	$data["bill_data"]["details"][$k]["discount_calculated"]=$tmp["discount"];
	$data["bill_data"]["details"][$k]["tax_ieps_calculated"]=$tmp["tax_ieps"];
	$data["bill_data"]["details"][$k]["tax_iva_calculated"]=$tmp["tax_iva"];
	$data["bill_data"]["details"][$k]["tax_iva_retained_calculated"]=$tmp["tax_iva_retained"];
	$data["bill_data"]["details"][$k]["tax_isr_calculated"]=$tmp["tax_isr"];
	$data["bill_data"]["details"][$k]["total"]=(float) number_format($tmp["total"],2,".","");

	}
	// …
	// folios 
	$data["bill_data"]["folio_serie"]=$this->folio_response["folio_serie"];
	$data["bill_data"]["folio_number"]=$this->folio_response["folio_number"];
	// …
	// sucursal donde se emitio la factura
	$data["subsidiary"]=$this->config_model->get_subsidiary_id($data["bill_data"]["subsidiary"]);
	// …

	$data["client"]=$this->client_model->get_client_id($data["bill_data"]["client"],$data["bill_data"]["client_subsidiary"]);

	return $data;
}

public function createXmlSign(){

$obj=new Satxml();

// el xml
$xml=$obj->getSaleXmlArray($this->get_dataToXml(),$this->sys);	

if(!$xml["status"])
return $xml; // retorna un ejempl:array("status"=>1,"msg"=>"Se creo el XML sellado","data"=>$xml_stamped_sysTmp["data"])

// sellado del servidor 

$xml_stamped_sysTmp=$this->stampSystem($xml["data"]);

if(!$xml_stamped_sysTmp["status"])
return $xml_stamped_sysTmp;
// ..

// retorna el XML para timbrar o recuperar ya con el sello 
return array("status"=>1,"msg"=>"Se creo el XML sellado","data"=>$xml_stamped_sysTmp["data"]);

}

public function stampXmlPac(){
$xml=$this->createXmlSign();

if(!$xml["status"])
return print_r(json_encode($xml));

	// <timbre>
	$obj_finkok=new finkok();
	$tim_stamp=$obj_finkok->stamp($xml,$this->pac_user,$this->pac_password);

	if(empty($tim_stamp["status"])):

		// si timbre previo
		if( !empty($tim_stamp["CodigoError"]) and $tim_stamp["CodigoError"]==307):

			$response_tmp=$this->recoveryXmlStamped();

		else:
			return print_r(json_encode($tim_stamp));
		endif;



	else:
		// generar timbre
		if(!empty($tim_stamp["xml"])):
		$saveIt=$this->saveXmlIn( $tim_stamp["xml"],$this->_INVOICE_MODE_CONFIG["storage_stamp"],true );

		if(empty($saveIt["status"]))
		return print_r(json_encode($saveIt));

		return print_r(json_encode( array("status"=>1,"msg"=>"Se creo exitosamente el XML") ));

		endif;

	endif;
	// </timbre>

}

public function recoveryXmlStamped(){

	// <recovery> Finkok
	$xml=$this->createXmlSign();
	$obj_recovery=new finkok();
	$tim_recovery=$obj_recovery->recovery($xml,$this->pac_user,$this->pac_password,$this->sys);
	// </recovery>

	if(!$tim_recovery["status"])
	return print_r(json_encode($tim_recovery));

	// grabar el XML QUE RETORNA
	$saveIt=$this->saveXmlIn( $tim_recovery["xml"],$this->_INVOICE_MODE_CONFIG["storage_stamp"],true );

	if(!$saveIt["status"])
	return print_r(json_encode( $saveIt ));

	return print_r(json_encode( array("status"=>1,"msg"=>"Se recupero el xml") ));
}

public function stampSystem($xml_specific){

	$version="3.2";
	// creacion del sellado 
	$myCFDXobj=new myCFDX("CFDI",$version);
	$msg_title="procesamiento de xml según versión CFDI $version de facturación electrónica";

	if( $myCFDXobj->initError )
	 return array("status"=>0,"msg"=>"$msg_title, ".$myCFDXobj->initError,"data"=>false);

	// data cleaning

	$xml_specific=$myCFDXobj->dataCleaning($xml_specific);

	// json schema validation

	if( is_string( $error=$myCFDXobj->jsonSchemaValidation($xml_specific) ) )
	 return array("status"=>0,"msg"=>"$msg_title, error al realizar la validaciòn json schema: $error.","data"=>false) ;

	// get xml ( partial, it no includes "noCertificado", "certificado", "sello", etc )

	if( !($xml=$myCFDXobj->getXML($xml_specific)) )
	 return array("status"=>0,"msg"=>"$msg_title, no se pudo generar el xml.","data"=>false);

	// original string

	if( ($original_string=$myCFDXobj->getOriginalString($xml))===false )
	 return array("status"=>0,"msg"=>"$msg_title, no se pudo obtener la cadena original.","data"=>false);

	// check certificate expiration

	if( ($date_expires=$myCFDXobj->checkCertificateExpiration($this->file_cer_path,date("Y-m-d H:i:s",time())))!==true )
	 return array("status"=>0,"msg"=>" el certificado ya expiró ( $date_expires ).","data"=>false);

	// get noCertificado

	if( !($xml_specific["noCertificado"]=$myCFDXobj->getSerialFromCertificate($this->file_cer_path)) )
	 return array("status"=>0,"msg"=>"$msg_title, no se pudo obtener el número de certificado.","data"=>false);

	// get certificado

	if( !($xml_specific["certificado"]=$myCFDXobj->getCertificate($this->file_cer_path,false)) )
	 return array("status"=>0,"msg"=>"$msg_title, no se pudo obtener el certificado.","data"=>false);
	 
	// get private key

	if( !($private_key=$myCFDXobj->getPrivateKey($this->file_key_path,$this->file_key_password)) )
	 return array("status"=>0,"msg"=>"$msg_title, no se pudo obtener la llave privada ( archivo .key corrupto? password incorrecto? ).","data"=>false);
	 
	// get sello

	if( !($xml_specific["sello"]=$myCFDXobj->signData($private_key,$original_string)) )
	 return array("status"=>0,"msg"=>"$msg_title, error al generar el sello.","data"=>false);

	// get xml ( partial )

	if( !($data["xml"]=$myCFDXobj->getXML($xml_specific)) )
	 return array("status"=>0,"msg"=>"$msg_title, fallo en la conversión del xml formato array a cadena.","data"=>false);

	return array("status"=>1,"msg"=>"sellado del sistema exitoso","data"=>$data["xml"],"xml_specific"=>$xml_specific) ;
}

public function xmlToCancel(){
	$data=$this->get_dataToXml();
	$folio=$data["bill_data"]["id"]."-".$data["bill_data"]["folio_serie"].$data["bill_data"]["folio_number"];
	$path_xml =$this->_INVOICE_MODE_CONFIG["storage_stamp"].$folio.".xml";

	if(!empty($data["bill_data"]["uuid"]))
	$UUID=$data["bill_data"]["uuid"];
	else if(file_get_contents($path_xml))
	$UUID=get_uuid($path_xml);
		
	$version="3.2";
	$myCFDXobj=new myCFDX("CFDI",$version);

	// contenido del certificado
	if( !($cer_content=$myCFDXobj->getCertificate($this->file_cer_path,$to_string=true)) )
 	return print(json_encode(array("status"=>0,"msg"=>"$msg_title, no se pudo obtener el certificado.","data"=>false) ) );
 	// contenido de la key
	if( !($key_content=$myCFDXobj->getPrivateKey($this->file_key_path,$this->file_key_password,$to_string=true)) )
 	return print(json_encode(array("status"=>0,"msg"=>"$msg_title, no se pudo obtener la llave privada ( archivo .key corrupto? password incorrecto? ).","data"=>false) ) );
	  
	// cancel con finkok
	$obj=new finkok();
	$tim_cancel=$obj->cancel($UUID,$this->pac_user,$this->pac_password,$cer_content,$key_content,$this->sys);

	if(!$tim_cancel["status"]):
	return print_r(json_encode($tim_cancel));
	else:
		// print_r($tim_cancel);
		$saveIt=$this->saveXmlIn( $tim_cancel["acuse"],$this->_INVOICE_MODE_CONFIG["storage_cancel"],false );

		if(empty($saveIt["status"]))
		return print_r(json_encode($saveIt));

	endif;

	// 
	return print(json_encode(array("status"=>1,"msg"=>"Se cancelo con exito en sat") ));
}

public function signXmlToCancel(){

$sys=$this->vars_system_model->_vars_system();

$version="3.2";
// Array2XML::init("1.0","UTF-8");
$obj =new myCFDX("CFDI",$version);

$date = date('YmdHis');
$date = $obj->xml_fech($date);

// uso un metodo de la clase myCFDX para traer la key en formato .pem
$key_pem = $obj->getPrivateKey($this->file_key_path,$this->file_key_password,$to_string=true);

// traer el cerfificado
$X509Certificate=$obj->getCertificate($this->file_cer_path,$to_string=false);
// esto el lo que trae $X509Certificate
/*MIIEazCCA1OgAwIBAgIUMDAwMDEwMDAwMDAyMDI2ODY1MzEwDQYJKoZIhvcNAQEFBQAwggGVMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMSEwHwYJKoZIhvcNAQkBFhJhc2lzbmV0QHNhdC5nb2IubXgxJjAkBgNVBAkMHUF2LiBIaWRhbGdvIDc3LCBDb2wuIEd1ZXJyZXJvMQ4wDAYDVQQRDAUwNjMwMDELMAkGA1UEBhMCTVgxGTAXBgNVBAgMEERpc3RyaXRvIEZlZGVyYWwxFDASBgNVBAcMC0N1YXVodMOpbW9jMRUwEwYDVQQtEwxTQVQ5NzA3MDFOTjMxPjA8BgkqhkiG9w0BCQIML1Jlc3BvbnNhYmxlOiBDZWNpbGlhIEd1aWxsZXJtaW5hIEdhcmPDrWEgR3VlcnJhMB4XDTEyMTIyMTIxNDgwOVoXDTE2MTIyMTIxNDgwOVowgawxIDAeBgNVBAMTF0FMRk9OU08gUk9TQVMgQ0VSVkFOVEVTMSAwHgYDVQQpExdBTEZPTlNPIFJPU0FTIENFUlZBTlRFUzEgMB4GA1UEChMXQUxGT05TTyBST1NBUyBDRVJWQU5URVMxFjAUBgNVBC0TDVJPQ0E3NDA4MDZUTjAxGzAZBgNVBAUTElJPQ0E3NDA4MDZITkxTUkwwNDEPMA0GA1UECxMGTUFUUklaMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDBRoF5/e/OfVggAG2rjCNigBPYWkt7362NNqTG3kVJXL4moBwspxcgrJ3q0PkVe0XNxruuylgiBpoOmlaWJ6YupWvwUD5IQyXA4QiIJcMGwHEABG8aXdd85py3vOgiJgjxDDaOWZ4zkaKbC+aj9XVrfwvgRabuE5DPm7fWbKTcNQIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQUFAAOCAQEAkIgLbS0mgU6JJtbxqjY8hvnQZy4BUgVkWM71PvoylxCmINp6FyLiE/pt80qjTLDEmdgS+O6ervVpnNbIDjhtrWZuEYte5OgGUP1rgbe2smrmL2ksqela2aarRp3odArBtJdrFxjqGOYG+/I8fI/eCQy1+bxXGvLMV8vue3oQ+ssl1qSjSmZ9d9XVr9qyHDtAMrN/RQ1/MSpXqe7O6z/buGh/TzpWmZF2H3T3+An74XedaGlUH3whupjg6MyPcZgVRUW2PwnsE52rXB3QuGfHouQCDUoTIX0Q53KHBakmS5At5Vw/w3FqVJsAO/BshX6v0xfgRUvUFYEM3r8UK1gx5A==*/

$cert_array = openssl_x509_parse("-----BEGIN CERTIFICATE-----\n".chunk_split($X509Certificate, 64, "\n")."-----END CERTIFICATE-----\n");
$X509IssuerName_implode =sanear_string(implode(",",$cert_array["issuer"]));
$X509SerialNumber = $cert_array["serialNumber"];

$RfcEmisor=str_replace("-", "", strtoupper(LineToCheckOutput($sys["enterprise_fiscal"]["rfc"])) ) ;
$Cancelacion["@attributes"]=array(
	"xmlns"=>"http://cancelacfd.sat.gob.mx",
	"xmlns:xsd"=>"http://www.w3.org/2001/XMLSchema",
	"xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance",
	"Fecha"=>$date,
	"RfcEmisor"=>$RfcEmisor
	);
	// "Fecha"=>"2012-09-30T14:14:40",
	// "RfcEmisor"=>"AAA010101AAA")
$Cancelacion["Folios"]=array();
$Cancelacion["Folios"]["UUID"]=$UUID;
// $Cancelacion["Folios"]["UUID"]="AA97B177-9383-4934-8543-0F91A7A02836";
// echo sha1('<Cancelacion xmlns="http://cancelacfd.sat.gob.mx" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Fecha="2012-09-30T14:14:40" RfcEmisor="AAA010101AAA"><Folios><UUID>AA97B177-9383-4934-8543-0F91A7A02836</UUID></Folios></Cancelacion>');
// 4e660ed992bec29aa1e8e2e2add6e7b5db3d7458

$Header_Cancelacion_xml = Array2XML::createXML('Cancelacion',$Cancelacion);
$Header_Cancelacion_xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>','', $Header_Cancelacion_xml->saveXML() );
$Header_Cancelacion_xml_out_space=LineToCheckOutput($Header_Cancelacion_xml);

// echo$version_out;
// echo sha1(trim($version_out ));
// 4e660ed992bec29aa1e8e2e2add6e7b5db3d7458

$digest = base64_encode( pack("H*", sha1(trim($Header_Cancelacion_xml_out_space ))) );
// echo $digest;
// TmYO2ZK+wpqh6OLirdbntds9dFg=
// Create a canonicalized version of the SignedInfo element
$SignedInfo=array(
	"@attributes"=>array(
	 "xmlns"=>"http://www.w3.org/2000/09/xmldsig#", 
	 "xmlns:xsd"=>"http://www.w3.org/2001/XMLSchema",
	 "xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance"
	 ),
	"CanonicalizationMethod"=>array(
		"@attributes"=>array(
					"Algorithm"=>"http://www.w3.org/TR/2001/REC-xml-c14n-20010315"
		),
		"@value"=>""
		),
	"SignatureMethod"=>array(
		"@attributes"=>array(
					"Algorithm"=>"http://www.w3.org/2000/09/xmldsig#rsa-sha1"
		),
		"@value"=>""
		),
	"Reference"=>array(
		"@attributes"=>array(
					"URI"=>""
		),
		"Transforms"=>array(
				"Transform"=>array(		
					"@attributes"=>array(
					"Algorithm"=>"http://www.w3.org/2000/09/xmldsig#enveloped-signature"
						),
					"@value"=>""
					),

				),
		"DigestMethod"=>array(
					"@attributes"=>array(
					"Algorithm"=>"http://www.w3.org/2000/09/xmldsig#sha1"
						),
					"@value"=>""
					),
		"DigestValue"=>$digest
	)
 );
	
$SignedInfo_xml = Array2XML::createXML('SignedInfo',$SignedInfo);

$SignedInfo_xml=str_replace('<?xml version="1.0" encoding="UTF-8"?>','', $SignedInfo_xml->saveXML());
$SignedInfo_xml_out_space=LineToCheckOutput($SignedInfo_xml);

// 1dccb689cf418bbb0ee55bd0231b8de0c608d57f
// echo sha1(trim($SignedInfo_xml_out_space ));

// Compute the rsa-sha1 signature of the SignedInfo element using the private key
// echo $obj->signData($key_pem,$SignedInfo_xml_out_space);
 $SignedInfo_signData = $obj->signData($key_pem,$SignedInfo_xml_out_space);

$Signature=array(
	"@attributes"    =>array( "xmlns"=>"http://www.w3.org/2000/09/xmldsig#"),
	"SignedInfo"     =>$SignedInfo,
	"SignatureValue" =>$SignedInfo_signData,
	"KeyInfo"        =>array(
						"X509Data"=>array(
								"X509IssuerSerial"=>array(
										"X509IssuerName"   =>$X509IssuerName_implode,
										"X509SerialNumber" =>$X509SerialNumber,
										),
								"X509Certificate"=>$X509Certificate
									),
						),
	);

$Signature_xml = Array2XML::createXML('Signature',$Signature);
$Signature_xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>','', $Signature_xml->saveXML());
$Signature_xml_out_space=LineToCheckOutput($Signature_xml);

// metemos el signature al header de cancelacion
$Cancelacion["Signature"]=$Signature;
$Cancelacion_xml_complete = Array2XML::createXML('Cancelacion',$Cancelacion);
/*
$Cancelacion_xml_complete_out_version = str_replace('<?xml version="1.0" encoding="UTF-8"?>','', $Cancelacion_xml_complete->saveXML());
*/
// echo $Cancelacion_xml_complete_out_version;

$Cancelacion_xml_complete_out_space=LineToCheckOutput($Cancelacion_xml_complete->saveXML());
// echo $Cancelacion_xml_complete_out_space;

$this->file_key_path_out=$this->file_key_path.".pem";
$this->file_cer_path_out=$this->file_cer_path.".pem";

$templ_subst=_UWBS_RRP.$_INVOICE_MODE_CONFIG["shcp_file_upload_storage_path"]."tmp/can_tim_".$user["id"].".subst.xml";

$xml_out=_UWBS_RRP.$_INVOICE_MODE_CONFIG["shcp_file_upload_storage_path"]."tmp/can_tim_".$user["id"].".petic.xml";

// XML A CANCELAR sin firma y sin espacios
$xml_tmp= $Cancelacion_xml_complete_out_space;

file_put_contents($templ_subst, $xml_tmp);

shell_exec('openssl x509 -inform der -in '.$this->file_cer_path.' -outform pem -out '.$this->file_cer_path_out);
shell_exec('openssl pkcs8 -inform der -in '.$this->file_key_path.' -outform pem -out '.$this->file_key_path_out.' -passin pass:'.$this->file_key_password);

//firma
$cmd = 'xmlsec1 --sign --privkey-pem '.$this->file_key_path_out.','.$this->file_cer_path_out.' --output '.$xml_out.' '.$templ_subst;
shell_exec($cmd);

$xml = file_get_contents($xml_out);

return $xml;	
}

public function cancelXml(){

$UUID=get_uuid($this->invoice_electronic_xml_file_path);
// <Finkok>
$obj_finkok=new finkok();

	$tim_cancel=$obj_finkok->cancel($UUID,$this->sys);
// </Finkok>

// <ateb>
$obj_ateb_pac=new ateb();

	$UUID=get_uuid($this->invoice_electronic_xml_file_path);
	$tim_cancel=$obj_ateb_pac->cancel($UUID,$this->sys);
// </ateb>

}

}