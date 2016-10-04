<?php

// NOTE :: this test was done successfully to generate a CFD 2.2 xml document.

// required libraries

require(realpath(__DIR__."/MyCFDX.php"));

// to realize this test you need specifiy this values ( including sample-data.php modification )

$schema="CFD";
$version="2.2";

$file_key_path=realpath(__DIR__."/path-to-file.key");
$file_key_password="";
$file_cer_path=realpath(__DIR__."/path-to-file.cer");
$data=require(realpath(__DIR__."/sample-data.php"));

// ...

if(!$file_key_path)
 die("no se encontrò el archivo .key");
else if(!$file_key_password)
 die("no se especifico el password del archivo .key");
else if(!$file_cer_path)
 die("no se encontrò el archivo .cer");

// ...

$myCFDXobj=new myCFDX($schema,$version);
 
if( $myCFDXobj->initError )
 die($myCFDXobj->initError);

// data cleaning

$data=$myCFDXobj->dataCleaning($data);

// json schema validation

if( is_string( $error=$myCFDXobj->jsonSchemaValidation($data) ) )
 die("error al realizar la validaciòn json schema: $error.");

// get xml ( partial, it no includes "noCertificado", "certificado", "sello", etc )

if( !($xml=$myCFDXobj->getXML($data)) )
 die("no se pudo generar el xml (parcial).");

// original string

if( ($original_string=$myCFDXobj->getOriginalString($xml))===false )
 die("no se pudo obtener la cadena original.");

// check certificate expiration

if( ($date_expires=$myCFDXobj->checkCertificateExpiration($file_cer_path,date("Y-m-d H:i:s",time())))!==true )
 die("el certificado ya expiró ( $date_expires )");

// add "noCertificado"

if( !($data["noCertificado"]=$myCFDXobj->getSerialFromCertificate($file_cer_path)) )
 die("no se pudo obtener el número de certificado.");

// add "certificado"

if( !($data["certificado"]=$myCFDXobj->getCertificate($file_cer_path,false)) )
 die("no se pudo obtener el certificado.");

// get private key
 
if( !($private_key=$myCFDXobj->getPrivateKey($file_key_path,$file_key_password)) )
 die("no se pudo obtener la llave privada ( archivo .key corrupto? password incorrecto? ).");
 
// add "sello"

if( !($data["sello"]=$myCFDXobj->signData($private_key,$original_string)) )
 die("error al generar el sello.");

// get xml ( complete )

if( !($xml=$myCFDXobj->getXML($data)) )
 die("no se pudo generar el xml (final).");

// ...

header('Content-type: text/xml; charset=utf-8');
echo $xml;

?>