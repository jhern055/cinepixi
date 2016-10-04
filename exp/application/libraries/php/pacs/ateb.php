<?php 
require_once(APPPATH."libraries/php/lib/XML2Array/XML2Array.php");

class Ateb {

public function stamp($xml_stamped_sysTmp){

$pac_user=da_xcess("fill",$pac_user);
$pac_password=da_xcess("fill",$pac_password);

$data=array();

if(!$xml or !$pac_user or !$pac_password)
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', faltan parámetros requeridos o valores inválidos.","data"=>false);

// ... produccion

$pac_user=htmlspecialchars($pac_user);
$pac_password=htmlspecialchars($pac_password);
$zipFileEncoded=base64_encode($xml_stamped_sysTmp);

// $pac_user=htmlspecialchars(0000000001);
// $pac_password=htmlspecialchars("pwd");
// $zipFileEncoded=base64_encode($xml);

// ATEB request

/*

para realizar un test se debe poner...
-- en $cfdi_xml el siguiente valor :: xmlns:cfdi="https://test.timbrado.com.mx/cfdi/"
-- $process=curl_init("https://test.timbrado.com.mx/cfdi/wsTimbrado.asmx?WSDL");
-- quitar el parámetro de la cabecera http 'SOAPAction:"https://cfdi.timbrado.com.mx/cfdi/GeneraTimbre"'

*/

// produccion 

$cfdi_xml =<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cfdi="https://cfdi.timbrado.com.mx/cfdi/">
   <soapenv:Header>
      <cfdi:AuthenticationHeader>
         <cfdi:UserName>$pac_user</cfdi:UserName>
         <cfdi:Password>$pac_password</cfdi:Password>
      </cfdi:AuthenticationHeader>
   </soapenv:Header>
   <soapenv:Body>
      <cfdi:GeneraTimbre>
         <cfdi:xmlBytes>$zipFileEncoded</cfdi:xmlBytes>
      </cfdi:GeneraTimbre>
   </soapenv:Body>
</soapenv:Envelope>

XML;


// para recuperar el timbre descomenta esto ↓ y comenta lo de arriba
/*
$cfdi_xml =<<<XML
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <AuthenticationHeader xmlns="https://cfdi.timbrado.com.mx/cfdi/">
      <UserName>$pac_user</UserName>
      <Password>$pac_password</Password>
    </AuthenticationHeader>
  </soap:Header>
  <soap:Body>
    <GeneraTimbreRec xmlns="https://cfdi.timbrado.com.mx/cfdi/">
      <xmlBytes>$zipFileEncoded</xmlBytes>
    </GeneraTimbreRec>
  </soap:Body>
</soap:Envelope>

XML;
*/

$process=curl_init("https://cfdi.timbrado.com.mx/cfdi/wstimbrado.asmx?WSDL"); // producción

// para recuperar el timbre descomenta esto ↓ y comenta lo de arriba
// $process=curl_init("https://cfdi.timbrado.com.mx/cfdi/wstimbradorec.asmx?WSDL"); // producción

$params=array(
	'SOAPAction:"https://cfdi.timbrado.com.mx/cfdi/GeneraTimbre"', /* es importante encerrar la url en comillas dobles, dado que las comillas simples no son reconocidas por el webservice PRODUCCION*/
	// para recuperar el timbre descomenta esto ↓ y comenta lo de arriba
	// 'SOAPAction: "https://cfdi.timbrado.com.mx/cfdi/GeneraTimbreRec"',
	"Content-Type: text/xml",
	"charset=utf-8"
);

curl_setopt($process,CURLOPT_HTTPHEADER,$params);
curl_setopt($process,CURLOPT_POSTFIELDS,$cfdi_xml);
#curl_setopt($process,CURLOPT_SSLCERT,'file.pem');
curl_setopt($process,CURLOPT_RETURNTRANSFER,true);
curl_setopt($process,CURLOPT_POST,true);
curl_setopt($process,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($process,CURLOPT_SSL_VERIFYHOST,false);

$timbre=curl_exec($process);

// if(1)
// return array("status"=>0,"msg"=>$cfdi_xml,"data"=>false);

if(!$timbre)
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', no se pudo realizar la petición debido a ".( curl_error($process)?: "error desconocido" ),"data"=>false);

curl_close($process);

// ...

$timbre=str_replace('&lt;','<',$timbre);
$timbre=str_replace('&gt;','>',$timbre);

@$server_response=XML2Array::createArray($timbre); // "@" is required to avoid less importance class warnings

if(!is_array($server_response) 
	or !$server_response["soap:Envelope"] 
	or !$server_response["soap:Envelope"]["soap:Body"] 
	or !$server_response["soap:Envelope"]["soap:Body"]["GeneraTimbreResponse"]
	or !$server_response["soap:Envelope"]["soap:Body"]["GeneraTimbreResponse"]["GeneraTimbreResult"]
	)
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', el pac regresó una respuesta no válida. :: $timbre","data"=>false);

$server_response_result=$server_response["soap:Envelope"]["soap:Body"]["GeneraTimbreResponse"]["GeneraTimbreResult"];

if($server_response_result["Error"]) {

	$error=array();

	if($server_response_result["Error"]["@attributes"] and $server_response_result["Error"]["@attributes"]["Codigo"])
	 $error[]="código ".$server_response_result["Error"]["@attributes"]["Codigo"];

	if($server_response_result["Error"]["DescripcionError"])
	 $error[]=$server_response_result["Error"]["DescripcionError"];

	if($error)
	 $error=implode(", ",$error);
	else
	 $error="erorr desconocido";

	return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', $error.","data"=>false);

}

if(!$server_response_result["tfd:TimbreFiscalDigital"])
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', no se pudo reconocer el nodo 'tfd:TimbreFiscalDigital' en la respuesta regresada por el pac.","data"=>false);

	return array(
	"status"=>1,
	"msg"=>"proceso de timbrado con pac 'ateb', proceso realizado exitosamente.",
	"xml"=>$timbre->stampResult->xml,
	"xml_array"=>$server_response,
	"UUID"=>$timbre->stampResult->UUID,
	);	
	
}

public function recovery(){

$data=array();

if(!$xml or !$pac_user or !$pac_password)
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', faltan parámetros requeridos o valores inválidos.","data"=>false);

// ... produccion

$pac_user=htmlspecialchars($pac_user);
$pac_password=htmlspecialchars($pac_password);
$zipFileEncoded=base64_encode($xml);

// $pac_user=htmlspecialchars(0000000001);
// $pac_password=htmlspecialchars("pwd");
// $zipFileEncoded=base64_encode($xml);

// ATEB request

/*

para realizar un test se debe poner...
-- en $cfdi_xml el siguiente valor :: xmlns:cfdi="https://test.timbrado.com.mx/cfdi/"
-- $process=curl_init("https://test.timbrado.com.mx/cfdi/wsTimbrado.asmx?WSDL");
-- quitar el parámetro de la cabecera http 'SOAPAction:"https://cfdi.timbrado.com.mx/cfdi/GeneraTimbre"'

*/

// produccion 
/*
$cfdi_xml =<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cfdi="https://cfdi.timbrado.com.mx/cfdi/">
   <soapenv:Header>
      <cfdi:AuthenticationHeader>
         <cfdi:UserName>$pac_user</cfdi:UserName>
         <cfdi:Password>$pac_password</cfdi:Password>
      </cfdi:AuthenticationHeader>
   </soapenv:Header>
   <soapenv:Body>
      <cfdi:GeneraTimbre>
         <cfdi:xmlBytes>$zipFileEncoded</cfdi:xmlBytes>
      </cfdi:GeneraTimbre>
   </soapenv:Body>
</soapenv:Envelope>

XML;
*/

// para recuperar el timbre descomenta esto ↓ y comenta lo de arriba

$cfdi_xml =<<<XML
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <AuthenticationHeader xmlns="https://cfdi.timbrado.com.mx/cfdi/">
      <UserName>$pac_user</UserName>
      <Password>$pac_password</Password>
    </AuthenticationHeader>
  </soap:Header>
  <soap:Body>
    <GeneraTimbreRec xmlns="https://cfdi.timbrado.com.mx/cfdi/">
      <xmlBytes>$zipFileEncoded</xmlBytes>
    </GeneraTimbreRec>
  </soap:Body>
</soap:Envelope>

XML;


// $process=curl_init("https://cfdi.timbrado.com.mx/cfdi/wstimbrado.asmx?WSDL"); // producción

// para recuperar el timbre descomenta esto ↓ y comenta lo de arriba
$process=curl_init("https://cfdi.timbrado.com.mx/cfdi/wstimbradorec.asmx?WSDL"); // producción

$params=array(
	// 'SOAPAction:"https://cfdi.timbrado.com.mx/cfdi/GeneraTimbre"', /* es importante encerrar la url en comillas dobles, dado que las comillas simples no son reconocidas por el webservice PRODUCCION*/
	// para recuperar el timbre descomenta esto ↓ y comenta lo de arriba
	'SOAPAction: "https://cfdi.timbrado.com.mx/cfdi/GeneraTimbreRec"',
	"Content-Type: text/xml",
	"charset=utf-8"
);

curl_setopt($process,CURLOPT_HTTPHEADER,$params);
curl_setopt($process,CURLOPT_POSTFIELDS,$cfdi_xml);
#curl_setopt($process,CURLOPT_SSLCERT,'file.pem');
curl_setopt($process,CURLOPT_RETURNTRANSFER,true);
curl_setopt($process,CURLOPT_POST,true);
curl_setopt($process,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($process,CURLOPT_SSL_VERIFYHOST,false);

$timbre=curl_exec($process);

// if(1)
// return array("status"=>0,"msg"=>$cfdi_xml,"data"=>false);

if(!$timbre)
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', no se pudo realizar la petición debido a ".( curl_error($process)?: "error desconocido" ),"data"=>false);

curl_close($process);

// ...

$timbre=str_replace('&lt;','<',$timbre);
$timbre=str_replace('&gt;','>',$timbre);

@$server_response=XML2Array::createArray($timbre); // "@" is required to avoid less importance class warnings

if(!is_array($server_response) 
	or !$server_response["soap:Envelope"] 
	or !$server_response["soap:Envelope"]["soap:Body"] 
	or !$server_response["soap:Envelope"]["soap:Body"]["GeneraTimbreRecResponse"]
	or !$server_response["soap:Envelope"]["soap:Body"]["GeneraTimbreRecResponse"]["GeneraTimbreRecResult"]
	)
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', el pac regresó una respuesta no válida. :: $timbre","data"=>false);

$server_response_result=$server_response["soap:Envelope"]["soap:Body"]["GeneraTimbreRecResponse"]["GeneraTimbreRecResult"];

if($server_response_result["Error"]) {

	$error=array();

	if($server_response_result["Error"]["@attributes"] and $server_response_result["Error"]["@attributes"]["Codigo"])
	 $error[]="código ".$server_response_result["Error"]["@attributes"]["Codigo"];

	if($server_response_result["Error"]["DescripcionError"])
	 $error[]=$server_response_result["Error"]["DescripcionError"];

	if($error)
	 $error=implode(", ",$error);
	else
	 $error="erorr desconocido";

	return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', $error.","data"=>false);

}

if(!$server_response_result["tfd:TimbreFiscalDigital"])
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', no se pudo reconocer el nodo 'tfd:TimbreFiscalDigital' en la respuesta regresada por el pac.","data"=>false);

	return array(
	"status"=>1,
	"msg"=>"proceso de timbrado con pac 'ateb', proceso realizado exitosamente.",
	"xml"=>$timbre->stampResult->xml,
	"xml_array"=>$server_response,
	"UUID"=>$timbre->stampResult->UUID,
	);	

}


public function cancel(){

$data=array();

if(!$xml or !$pac_user or !$pac_password)
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', faltan parámetros requeridos o valores inválidos.","data"=>false);

// ... produccion

$pac_user=htmlspecialchars($pac_user);
$pac_password=htmlspecialchars($pac_password);
$zipFileEncoded=base64_encode($xml);

$cfdi_xml =<<<XML
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <AuthenticationHeader xmlns="https://cfdi.timbrado.com.mx/cancelacfdi/">
      <UserName>$pac_user</UserName>
      <Password>$pac_password</Password>
    </AuthenticationHeader>
  </soap:Header>
  <soap:Body>
    <CancelaCFDIs xmlns="https://cfdi.timbrado.com.mx/cancelacfdi/">
      <xmlBytes>$zipFileEncoded</xmlBytes>
    </CancelaCFDIs>
  </soap:Body>
</soap:Envelope>
XML;

//cancelar
$process=curl_init("https://cfdi.timbrado.com.mx/cancelacfdi/WS_Cancela.asmx?wsdl");
$params=array(

  "POST /cancelacfdi/WS_Cancela.asmx HTTP/1.1",
  "Content-Type: text/xml",
  "charset=utf-8",
  'SOAPAction:"https://cfdi.timbrado.com.mx/cancelacfdi/CancelaCFDIs"'  //es importante encerrar la url en comillas dobles, dado que las comillas simples no son reconocidas por el webservice 

);


curl_setopt($process,CURLOPT_HTTPHEADER,$params);
curl_setopt($process,CURLOPT_POSTFIELDS,$cfdi_xml);
#curl_setopt($process,CURLOPT_SSLCERT,'file.pem');
curl_setopt($process,CURLOPT_RETURNTRANSFER,true);
curl_setopt($process,CURLOPT_POST,true);
curl_setopt($process,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($process,CURLOPT_SSL_VERIFYHOST,false);

$timbre=curl_exec($process);

// if(1)
// return array("status"=>0,"msg"=>$cfdi_xml,"data"=>false);

if(!$timbre)
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', no se pudo realizar la petición debido a ".( curl_error($process)?: "error desconocido" ),"data"=>false);

// esto es para guardar la respuesta de cancelacion 
// cuando lo pidan los de ateb

// @$server_response_save_CancelaCFDIsResult=XML2Array::createArray($timbre); // "@" is required to avoid less importance class warnings
// file_put_contents('respuestaCancelar.xml', $server_response_save_CancelaCFDIsResult["soap:Envelope"]["soap:Body"]["CancelaCFDIsResponse"]["CancelaCFDIsResult"]);
 
curl_close($process);

// $timbre=str_replace('&lt;','<',$timbre);
// $timbre=str_replace('&gt;','>',$timbre);
@$server_response=XML2Array::createArray($timbre); // "@" is required to avoid less importance class warnings

if(!is_array($server_response) 
	or !$server_response["soap:Envelope"] 
	or !$server_response["soap:Envelope"]["soap:Body"] 
	or !$server_response["soap:Envelope"]["soap:Body"]["CancelaCFDIsResponse"]
	or !$server_response["soap:Envelope"]["soap:Body"]["CancelaCFDIsResponse"]["CancelaCFDIsResult"]
	)
 return array("status"=>0,"msg"=>"proceso de timbrado con pac 'ateb', el pac regresó una respuesta no válida. :: $timbre","data"=>false);

$server_response_result=$server_response["soap:Envelope"]["soap:Body"]["CancelaCFDIsResponse"]["CancelaCFDIsResult"];

return $server_response_result;

}

}
 ?>