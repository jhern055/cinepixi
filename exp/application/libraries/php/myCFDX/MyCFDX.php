<?php
class myCFDX {
// require_once("XMLSecurityDSig.php"));
// require_once(realpath(__DIR__."/../../Xmlseclib-master/XMLSecurityKey.php"));

	/*
	*
	* author :: 
	*
	*
	* description ::
	*
	* esta clase fuè creada con el propòsito de usar una misma metodologìa estàndar para la creaciòn de comprobantes fiscales digitales (CFD)
	* y comprobantes fiscales digitales por internet (CFDI) tomando en cuenta la adiciòn de nuevas versiones de los documentos segùn el paso del tiempo de una
	* manera sencilla, asì mismo se reconoce que la misma no podrìa haber sido realizada sin la adiciòn de còdigo de terceros.
	*
	* checar funcionamiento en sample.php
	*
	* en el caso del CFDI esta clase no incluye soporte para el timbrado, no obstante es capaz de generar el xml y añadirle los atributos de timbrado
	*
	* esta clase fuè testeada en su totalidad en ambiente linux, no obstante en windows tambien sirve, sòlo que los mètodos getPrivateKey(), getSerialFromCertificate() y
	* getCertificate() hacen uso de la funciòn shell_exec, en donde la librerìa openssl debe estar instalada en el servidor y debe ser posible llamarla 
	* mediante la lìnea de comandos desde cualquier àrea del sistema.
	*
	* key features ::
	*
	* - realiza la validación de datos introducidos por el usuario ((programador)) mediante el uso de un json schema (( archivos .json ))
	* - genera la cadena original apartir del archivo .xslt
	* - incluye todos los archivos del sat ( .xslt ) para una ejecución más rápida
	*
	* third parties :: (( thanks! ))
	*
	* Array2XML			- lalit.org/lab/convert-php-array-to-xml-with-attributes/
	*
	* SimpleCFD			- github.com/bbh/SimpleCFD/blob/master/SimpleCFD.php
	*
	* php-json-schema	- github.com/hasbridge/php-json-schema
	*
	*/

	/*
	*
	* construct: on error $this->$initError will contain the error message
	*
	*/

	public $schema;
	public $version;
	public $initError;
	public $jsonSchemaFile;
	public $xsltFile;
	public $xmlRootElementName;
	public $xmlDefaultData;

    public function __construct($schema,$version) {

		$schema=(string) $schema;
		$version=(string) $version;

		if( is_string($tmp=$this->schemaVersionConfig($schema,$version)) )
		 { $this->initError="error de inicializaciòn de objeto ".get_class($this).", ".$tmp;  return; }

        $this->schema=$schema;
        $this->version=$version;
		$this->initError=false;

		$this->jsonSchemaFile=$tmp["jsonSchemaFile"];
		$this->xsltFile=$tmp["xsltFile"];
		$this->xmlRootElementName=$tmp["xmlRootElementName"];
		$this->xmlDefaultData=$tmp["xmlDefaultData"];

     }

	/*
	*
	* return related config according to schema/version
	*
	* returns string on error
	*
	* return associative array on success
	*
	*/

	public static function schemaVersionConfig($schema,$version) {

		if(!$schema or !is_string($schema))
		 return "no se ha definido el valor del parámetro 'schema' o bien no es del tipo string";
		else if(!$version or !is_string($version))
		 return "no se ha definido el valor del parámetro 'version' o bien no es del tipo string";

		$data=array();

		if($schema==="CFD" and $version==="2.0") {

			$data["jsonSchemaFile"]=realpath(__DIR__."/files/cfd/2/json-schema/cfd2.json");
			$data["xsltFile"]=realpath(__DIR__."/files/cfd/2/sat/cadenaoriginal_2_0.xslt");
			$data["xmlRootElementName"]="Comprobante";
			$data["xmlDefaultData"]=require(realpath(__DIR__."/files/cfd/2/config/xml-default-data.php"));

		}
		else if($schema==="CFD" and $version==="2.2") {

			$data["jsonSchemaFile"]=realpath(__DIR__."/files/cfd/2_2/json-schema/cfd2_2.json");
			$data["xsltFile"]=realpath(__DIR__."/files/cfd/2_2/sat/cadenaoriginal_2_2.xslt");
			$data["xmlRootElementName"]="Comprobante";
			$data["xmlDefaultData"]=require(realpath(__DIR__."/files/cfd/2_2/config/xml-default-data.php"));

		}
		else if($schema==="CFDI" and $version==="3.2") {

			$data["jsonSchemaFile"]=realpath(__DIR__."/files/cfdi/3_2/json-schema/cfd3_2.json");
			$data["xsltFile"]=realpath(__DIR__."/files/cfdi/3_2/sat/cadenaoriginal_3_2.xslt");
			$data["xmlRootElementName"]="cfdi:Comprobante";
			$data["xmlDefaultData"]=require(realpath(__DIR__."/files/cfdi/3_2/config/xml-default-data.php"));

		}
		else if($schema==="CFDI_Retentions" and $version==="1.0") {

			$data["jsonSchemaFile"]=realpath(__DIR__."/files/cfdi_retentions/1.1/json-schema/cfd3_2.json");
			$data["xsltFile"]=realpath(__DIR__."/files/cfdi_retentions/1.1/sat/reten_original_1_1.xslt");
			// $data["xsltFile"]=realpath(__DIR__."/files/cfdi/3_2/sat/cadenaoriginal_3_2.xslt");
			
			$data["xmlRootElementName"]="retenciones:Retenciones";
			$data["xmlDefaultData"]=require(realpath(__DIR__."/files/cfdi_retentions/1.1/config/xml-default-data.php"));

		}
		else
		 return "no se ha definido la configuración de el esquema '$schema' versión '$version'.";

		if(!$data["jsonSchemaFile"])
		 return "no se encontró el archivo json schema.";

		if(!$data["xsltFile"])
		 return "no se encontró el archivo xslt.";

		if(!$data["xmlRootElementName"])
		 return "no se definió el nombre del elemento 'root' para el xml.";

		if(!is_array($data["xmlDefaultData"]))
		 return "el archivo xml-default-data.php no regreso un valor del tipo array.";

		return $data;

	}

	/*
	*
	* array cleaning
	*
	*/

	public static function dataCleaning($var) {

		if(!is_array($var))
		 return array();

		foreach($var as $k=>&$v) {

			// delete empty values

			if(is_array($v))
			 { $v=self::dataCleaning($v);  continue; }

			if($v==="" or $v===null)
			 { unset($var[$k]);  continue; }

			// characters escaping

			// note :: 	nexts characters are escaped automatically by getXML() function 'on line $xml->saveXML'
			//			"&", "\"", "<", ">", "'"

			$v=str_replace(array("\r","\n","\t","|",),"",$v);

			$v=preg_replace('/\s+/',' ',trim($v));

		}

		unset($v);

		return $var;

	}

	/*
	*
	* array structure validation against to json schema file
	*
	*/

	public function jsonSchemaValidation($data) {

		// required library

		require_once(realpath(__DIR__."/lib/hasbridge-php-json-schema/0.1.0-4/src/Json/Validator.php"));

		// ...

		if(!is_array($data))
		 return "el parámetro 'data' no es del tipo array.";

		// ...

		$json_data=json_encode($data);
		$json_object=json_decode($json_data);

		try {

			$jsonSchemaValidator=new JsonValidator($this->jsonSchemaFile);

		}
		catch(SchemaException $e) {

			$e=(string) $e;

			preg_match('/exception \'SchemaException\' with message \'(.*?)\'/',$e,$matches);

			return !$matches[1] ? $e : $matches[1] ;

		}

		try {

			$jsonSchemaValidator->validate($json_object);

		}
		catch(ValidationException $e) {

			$e=(string) $e;

			preg_match('/exception \'ValidationException\' with message \'(.*?)\'/',$e,$matches);

			return !$matches[1] ? $e : $matches[1] ;

		}

		return true;

	}

	/*
	*
	* change array format according to Array2XML requirements
	*
	*/

	public static function dataSetSpecificFormat($var) {
	
		if(!is_array($var))
		 return array();

		$all_array=true;

		foreach($var as $v)
		 { if(!is_array($v)) { $all_array=false;  break; } }

		if(!$all_array)
		 $var["@attributes"]=array();

		foreach($var as $k=>&$v) {

			if($k==="@attributes") // prevents infinite recursion
			 continue;

			if(is_array($v))
			 { $v=self::dataSetSpecificFormat($v);  continue; }

			$var["@attributes"][$k]=$v;
			unset($var[$k]);

		}

		unset($v);

		return $var;

	}

	/*
	*
	* appends default data to an array and transform to xml
	*
	*/

	public function getXML($data) {
		// required library

		require_once(realpath(__DIR__."/lib/Array2XML.php"));

		// append default data

		if($this->xmlDefaultData)
		 $data=array_merge($this->xmlDefaultData,$data);

		// ...

		$data=$this->dataSetSpecificFormat($data);

		// create xml through an array

		Array2XML::init("1.0","UTF-8");

		$xml=Array2XML::createXML($this->xmlRootElementName,$data);

		// ...

		return $xml->saveXML();

	}

	public function getXMLRetentions($data) {

		// required library
		require_once(realpath(__DIR__."/lib/Array2XML.php"));

		// append default data

		if($this->xmlDefaultData)
		 $data=array_merge($this->xmlDefaultData,$data);

		// ...

		$data=$this->dataSetSpecificFormat($data);

		// create xml through an array

		Array2XML::init("1.0","UTF-8");

		$xml=Array2XML::createXML($this->xmlRootElementName,$data);

		// ...

		return $xml->saveXML();

	}

	/*
	*
	* Validates and transforma an array of data to a | (pipe) separated string
	*
	* @param array contains the FEA data
	* @return string separated by | (pipe)
	*
	*/

	public function getOriginalString($xml) {

		// NOTE :: is much more fast when call to local files instead to load from external server.
		// ...

		if (!$xml)
		 return false;

		// ...

		$dom=new DOMDocument();
		@$dom->loadXML($xml);

		if($dom===false)
		 return false;

		$xsl=new DOMDocument;
		@$xsl->load($this->xsltFile);

		if($xsl===false)
		 return false;

		$proc=new XSLTProcessor;

		@$proc->importStyleSheet($xsl);
		@$originalString=$proc->transformToXML($dom); // returns false on error

		return $originalString;

	}

	/*
	*
	* Returns the private key from DER to PEM format, uses openssl from shell
	*
	* @param string $key_path the path of the private key in DER format
	* @param string $password the private key password
	* @return string the private key in a PEM format
	*
	*/

	// public static function getPrivateKey($key_path,$password) {

	// 	$cmd='openssl pkcs8 -inform DER -in '.$key_path.' -passin pass:'.$password;

	// 	if($result=shell_exec($cmd)) {

	// 		unset($cmd);
	// 		return $result;

	// 	}

	// 	return false;

	// }
	public static function getPrivateKey($key_path,$password,$to_string=true) {

	$cmd='openssl pkcs8 -inform DER -in '.$key_path.' -passin pass:'.$password;

	if($result=shell_exec($cmd)) {
	unset($cmd);

	if($to_string)
	return $result;

	$split=preg_split('/-*(END|\sKEY)-*\s/',$result);
	unset($result);

	 return preg_replace('/\n/','',$split[1]);

	}
	}

	/*
	*
	* Returns the serial number from a DER certificate, uses openssl from shell
	*
	* @param string $cer_path the certificate path in DER format
	* @return string the serial number of the certificate in ASCII
	*
	*/

	public static function getSerialFromCertificate ($cer_path) {

		$cmd ='openssl x509 -inform DER -outform PEM -in '.$cer_path.' -pubkey | '.
			   'openssl x509 -serial -noout';

		if($serial=shell_exec($cmd)) {

			unset($cmd);

			if ( preg_match("/([0-9]{40})/",$serial,$match) ) {

				unset($serial);

				return implode('', array_map( 'chr', array_map('hexdec',str_split($match[1],2)) ));

			}

		}

		return false;

	}

	/*
	*
	* Return the certificate from DER to PEM on two formats, uses openssl from shell
	* if to_string is true resutns the certificate in a string as is (multiline)
	* but if set to false returns only the certificate in a one line string.
	*
	* @param string $cer_path the path of the certificate in DER format
	* @param boolean $to_string a flag to set the format required
	* @return string the certificate in PEM format
	*
	*/

	public static function getCertificate($cer_path,$to_string=true) {

		$cmd='openssl x509 -inform DER -outform PEM -in '.$cer_path.' -pubkey';

		if($result=shell_exec($cmd)) {

			unset($cmd);

			if($to_string)
			 return $result;

			$split=preg_split('/\n(-*(BEGIN|END)\sCERTIFICATE-*\n)/',$result);
			unset($result);

			return preg_replace('/\n/','',$split[1]);

		}

		return false;

	}

	/*
	*
	* Return certificate date (( Y-m-d H:i:s ))
	*
	* @param string cer_path param, should be .cer file path (( according to php date function ))
	* @param string type, can be "start" or "end"
	*
	* on success, returns string date.
	*
	* on error, returns false.
	*
	*/

	public static function getCertificateDate($cer_path,$type) {

		if(!$cer_path)
		 return false;

		if($type==="start")
		 $tmp="startdate";
		else if($type==="end")
		 $tmp="enddate";
		else
		 return false;
		
		if (PHP_OS=="WINNT")
		$cmd='openssl x509 -in '.$cer_path.' -inform DER -noout -'.$tmp;
		else
		$cmd='openssl x509 -in '.$cer_path.' -inform DER -noout -'.$tmp.' | cut -f2 -d=';

		$result=shell_exec($cmd);

		if (PHP_OS=="WINNT")
		$result=str_replace("notAfter=", "", $result);

		if(!$result)
		 return false;

		$time=strtotime($result);

		if(!$time or $time===-1)
		 return false;

		$date=date("Y-m-d H:i:s",$time);

		if(!$date)
		 return false;

		return $date;

	}

	/*
	*
	* check expiration of certificate
	*
	* @param string cer_path param, should be .cer file path
	* @param string timestamp, should have next format "Y-m-d H:i:s" (( according to php date function ))
	*
	* on success, returns true.
	*
	* on error, returns false or string date expiration.
	*
	*/

	public static function checkCertificateExpiration($cer_path,$timestamp) {

		if( mb_strlen($timestamp)!=19 )
		 return false;

		if( !($timestamp_expires=self::getCertificateDate($cer_path,"end")) )
		 return false;

		$timestamp_uet=self::timestamp2unixepochtime($timestamp);

		if($timestamp_uet===0)
		 return false;

		$timestamp_expires_uet=self::timestamp2unixepochtime($timestamp_expires);

		if($timestamp_expires_uet===0)
		 return false;

		// $time=strtotime($result);

		if($timestamp_uet<$timestamp_expires_uet)
		 return true;
		else
		 return $timestamp_expires;

	}

	/*
	*
	* converts timestamp date to timestamp unix epoch time
	*
	* @param string $timestamp, should have next format "Y-m-d H:i:s" (( according to php date function ))
	*
	* on success, returns seconds.
	*
	* on error, returns 0.
	*
	*/

	public static function timestamp2unixepochtime($timestamp) {

		if( mb_strlen($timestamp)!=19 )
		 return 0;

		return mktime(mb_substr($timestamp,11,2),mb_substr($timestamp,14,2),mb_substr($timestamp,17,2),mb_substr($timestamp,5,2),mb_substr($timestamp,8,2),mb_substr($timestamp,0,4));

	}

	/*
	* 
	* Signs data with the key and returns it in a base64 string
	*
	* @param string $key string containing the key in PEM format
	* @param string $data data to sign
	* @return string the signed data in base64
	*
	*/

	public static function signData($key,$data) {

		$pkeyid=openssl_get_privatekey($key);

		if(openssl_sign($data,$cryptedata,$pkeyid,OPENSSL_ALGO_SHA1)) {

			openssl_free_key($pkeyid);

			return base64_encode($cryptedata);

		}

	}

	// crear el DIGEST values
    public static function digestValue($xml) {

        $cmd='openssl dgst -binary -sha1 '.$xml.' | openssl enc -base64';

        if($result=shell_exec($cmd)) {
            unset($cmd);

            if($to_string)
             return $result;

            $split=preg_split('/\n(-*(BEGIN|END)\sCERTIFICATE-*\n)/',$result);
            unset($result);

            return preg_replace('/\n/','',$split[1]);

        }

        return false;

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

	// este metodo esta cancelado no se usa
	/*
	public static function generarcancelacion($UUID,$file_key_path,$file_cer_path,$file_key_password,$invoice_electronic_xml_file_path,$cer_content,$key_content,$rfc){
        $obj=new myCFDX("CFDI","3.2");

        $date = date('YmdHis');
        $date = $obj->xml_fech($date);
        $objCancelacion = new DOMDocument('1.0');
        $cancelacion = $objCancelacion->createElementNS('http://cancelacfd.sat.gob.mx','Cancelacion');
        $cancelacion = $objCancelacion->appendChild($cancelacion);
        $cancelacion->textContent = 'http://www.w3.org/2001/XMLSchema';
        $cancelacion->setAttribute('xmlns:xsd', 'http://www.w3.org/2000/xmlns');
        $cancelacion->textContent = 'http://www.w3.org/2001/XMLSchema-instance';
        $cancelacion->setAttribute('xmlns:xsi', 'http://www.w3.org/2000/xmlns');
        $cancelacion->setAttribute('RfcEmisor',$rfc);

        $cancelacion->setAttribute('Fecha', $date);
        $folios = $objCancelacion->createElementNS('http://cancelacfd.sat.gob.mx','Folios');
        $folios = $cancelacion->appendChild($folios);

		// $UUID="B0386F3E-BE99-4B13-8450-957CE5BA27B2";
        // $uuid = $objCancelacion->createElementNS('http://cancelacfd.sat.gob.mx','UUID', $UUID);
        $uuid = $objCancelacion->createElementNS('http://cancelacfd.sat.gob.mx','UUID', $UUID);
        $uuid = $folios->appendChild($uuid);

        $objDsig = new XMLSecurityDSig();
        $objDsig->setCanonicalMethod(XMLSecurityDSig::C14N);

        $objDsig->addReference($objCancelacion,XMLSecurityDSig::SHA1,array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'),array('force_uri' => TRUE));

        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1,array('type' => 'private'));
        
        // $obj =new myCFDX("CFDI",$version);
        // $file_key_path=realpath(__DIR__."/roca740806tn01212211545s.key");
        // $file_key_password="disa2400";
        // $pkeyid= $obj->getPrivateKey($file_key_path,$file_key_password,$to_string=true);
  
        $objKey->loadKey($key_content);

        $objDsig->sign($objKey);

        // $file_cer_path=realpath(__DIR__."/00001000000202686531.cer");
        // $pcerid=$obj->getCertificate($file_cer_path,$to_string=false);
		$objDsig->add509Cert($cer_content,false,false,array("issuerSerial"=>1));

        $objDsig->appendSignature($cancelacion);

        $objCancelacion->formatOutput = true;

        $xmlCancelacion = $objCancelacion->saveXML();

        // esto sirve si te piden los de ateb que les mandes que mandes el XML PARA cancelar
 		// $objCancelacion->save('mandoACancelar.xml');
 		
		// echo $xmlCancelacion;
        $objCancelacion = new DOMDocument();
        $objCancelacion->loadXML(file_get_contents($invoice_electronic_xml_file_path));
        return $xmlCancelacion;
    }*/

}

?>