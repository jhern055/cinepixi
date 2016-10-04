<?php 
require_once(APPPATH."libraries/php/lib/XML2Array/XML2Array.php");

class finkok {

	public function stamp($xml_stamped_sysTmp,$pac_user,$pac_password){

	if(!$xml_stamped_sysTmp or !$pac_user or !$pac_password)
	 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'finkok', faltan parámetros requeridos o valores inválidos.","data"=>false);

	// ... produccion

	$pac_user=htmlspecialchars($pac_user);
	$pac_password=htmlspecialchars($pac_password);
	// $zipFileEncoded=base64_encode($xml_stamped_sysTmp); // no se necesita 

	# Consuming the stamp service prueba
	// $url = "http://demo-facturacion.finkok.com/servicios/soap/stamp.wsdl";

	// PRUEBA CFDI Nueva plataforma 26  MAYO 2015
	// USUARIO Y CONTRASEÑA PARA PRUEBAS 
	// USUARIO:jhern055@gmail.com pass:preguntar por correo:jhern055@gmail.com
	// tambien existen .cer y .key para pruebas que el mismo pack te brinda
	// $url = "http://demo-phx.facturacion.finkok.com/servicios/soap/stamp.wsdl";
       $url="http://demo-facturacion.finkok.com/servicios/soap/stamp.wsdl";

	// $url = "https://facturacion.finkok.com/servicios/soap/stamp.wsdl";
	$client = new SoapClient($url);

	$params = array(
	  "xml" => $xml_stamped_sysTmp,
	  "username" => $pac_user,
	  "password" => $pac_password
	);
	$timbre = $client->__soapCall("stamp", array($params));

	if(!$timbre)
	 return array("status"=>0,"msg"=>"proceso de timbrado con finkok 'finkok', no se pudo realizar la petición debido a ".( curl_error($process)?: "error desconocido" ),"data"=>false);

	if(!empty($timbre->stampResult->Incidencias->Incidencia->CodigoError))
	 return array("status"=>0,"msg"=>var_export($timbre->stampResult->Incidencias->Incidencia->MensajeIncidencia,true),"CodigoError"=>$timbre->stampResult->Incidencias->Incidencia->CodigoError);

	// ...

	if(!empty($timbre->stampResult->xml))
	@$server_response=XML2Array::createArray($timbre->stampResult->xml); // "@" is required to avoid less importance class warnings

	if(!$server_response["cfdi:Comprobante"]["cfdi:Complemento"])
	 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'finkok', no se pudo reconocer el nodo 'tfd:TimbreFiscalDigital' en la respuesta regresada por el pac.","data"=>false);

	return array(
	"status"=>1,
	"msg"=>"proceso de timbrado con pac 'fiknkok', proceso realizado exitosamente.",
	"xml"=>$timbre->stampResult->xml,
	"xml_array"=>$server_response,
	"UUID"=>$timbre->stampResult->UUID,
	);	

	}

	public function cancel($UUID,$pac_user,$pac_password,$cer_content,$key_content,$sys){
	
	// $taxpayer_id = 'CSO050217EA1'; # The RFC of the Emisor
	$taxpayer_id = str_replace("-", "", strtoupper($sys["enterprise_fiscal"]["rfc"]) ) ;
	
	// $invoices = array("6308DF45-0D7F-4060-9121-6C8639FE1C14"); # A list of UUIDs
	if(is_array($UUID))
	$invoices = $UUID; # A list of UUIDs
	else
	$invoices = array(0=>$UUID);
	 // return array("status"=>0,"msg"=>var_export($invoices,true),"data"=>false);

	// $pac_user="integracion";
	// $pac_password="1nT36R4c!0N";
	// prueba
	$url = "http://demo-phx.facturacion.finkok.com/servicios/soap/cancel.wsdl";
	// $url = "http://demo-facturacion.finkok.com/servicios/soap/cancel.wsdl";
	// $url = "https://facturacion.finkok.com/servicios/soap/cancel.wsdl";
	$client = new SoapClient($url);
	$params = array(  
	  "UUIDS" => array('uuids' => $invoices),
	  "username" => $pac_user,
	  "password" => $pac_password,
	  "taxpayer_id" => $taxpayer_id,
	  "cer" => $cer_content,
	  "key" => $key_content,
	);

	$timbre = $client->__soapCall("cancel", array($params));
	$myArray = json_decode( json_encode($timbre), true);
	$errors=array(
      202 => 'Cancelado Previamente',
      203 => 'No corresponde el RFC del emisor y de quien solicita la cancelación',
      205 => 'No existente',
      900 => 'Error de PAC',
      708 => 'Error de conexión con el SAT'
		);
	$statusUUID=$myArray["cancelResult"]["Folios"]["Folio"]["EstatusUUID"];

	if( array_search($statusUUID,array_flip($errors) ) )
	 return array("status"=>0,"msg"=>$errors[$statusUUID],"data"=>false);

	if(!$timbre)
 	return array("status"=>0,"msg"=>"proceso de cancelacion timbrado con pac 'finkok', no se pudo realizar la petición debido a ".( curl_error($process)?: "error desconocido" ),"data"=>false);


 	// if($timbre->EstatusUUID==708)
 	// return array("status"=>0,"msg"=>"No retorno acuse","data"=>false);

			// return array("status"=>0, "msg"=>var_export($timbre,true) ); 

 		// if(!empty($timbre->cancelResult->Acuse))
			return array("status"=>1, "msg"=>"Se cancelo con exito", "acuse"=>$timbre->cancelResult->Acuse );
		// else
			// return array("status"=>0, "msg"=>$timbre->cancelResult->CodEstatus, ); 
	}

public function recovery($xml,$pac_user,$pac_password,$sys){

if(!$xml or !$pac_user or !$pac_password)
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', faltan parámetros requeridos o valores inválidos.","data"=>false);

// ... produccion

// $pac_user=htmlspecialchars("timbrado@disa1.com");
// $pac_password=htmlspecialchars("Disa2400*");

$zipFileEncoded=base64_encode($xml);
$url="http://demo-facturacion.finkok.com/servicios/soap/stamp.wsdl";

// $url = "https://facturacion.finkok.com/servicios/soap/stamp.wsdl";

$client = new SoapClient($url);

$params = array(
  "xml" => $xml,
  "username" => $pac_user,
  "password" => $pac_password
);
 
$functions = $client->__getFunctions();

$timbre = $client->__soapCall("stamped", array($params));

if(!empty($timbre->stampedResult->Incidencias->Incidencia->CodigoError))
 return array("status"=>0,"msg"=>var_export($timbre->stampedResult->Incidencias->Incidencia->MensajeIncidencia,true),"data"=>false);

// ...

@$server_response=XML2Array::createArray($timbre->stampedResult->xml); // "@" is required to avoid less importance class warnings

$server_response_result=$server_response;

if(!$server_response_result['cfdi:Comprobante']["cfdi:Complemento"]["tfd:TimbreFiscalDigital"])
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', no se pudo reconocer el nodo 'tfd:TimbreFiscalDigital' en la respuesta regresada por el pac.","data"=>false);


return array(
	"status"=>1,
	"msg"=>"proceso de timbrado con pac 'fiknkok', proceso realizado exitosamente.",
	"xml"=>$timbre->stampedResult->xml,
	"xml_array"=>$server_response,
	"UUID"=>$timbre->stampedResult->UUID,
	);	

}

}
 ?>