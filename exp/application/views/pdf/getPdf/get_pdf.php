<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!empty($_CONFIG_MODULE))
    extract($_CONFIG_MODULE);

if(!empty($data_records))
    extract($data_records);

ini_set("memory_limit","30M");
// Titulo de factura
$doc["name"]=!empty($DOC_TYPE_NAME)?$DOC_TYPE_NAME:"";

$doc["yLimit"]=258.5;

// if($sys["config"]["show_discount_items"] and in_array("show_discounts",$DEFAULT_TEMPLATE_OPTIONS)):
// $doc["details_config"]=require(_UWBS_RRP."_resources/scripts/php/system/recycled/tcpdf-doc-details-config-estandar-dos.php");
// elseif($sys["config"]["show_discount_items"]):
// $doc["details_config"]=require(_UWBS_RRP."_resources/scripts/php/system/recycled/tcpdf-doc-details-config-estandar-dos-show-discount-items.php");
// else:
// $doc["details_config"]=require(_UWBS_RRP."_resources/scripts/php/system/recycled/tcpdf-doc-details-config-estandar-dos.php");
// endif;

$doc["details_config"]=require($DETAILS_CONFIG);

// doc config
$doc["config"]=require($DEFAULT_TCPDFCONFIG);
// ...

$pdf=new MYPDF("P","mm","Letter",true,$doc["config"]["encoding"],false); // to set a custom paper size " $pdf=new TCPDF("P","mm",array(216.5,215.5),true,$doc["config"]["encoding"],false); "
$pdf->setCustomVars(array("config"=>$doc["config"],"watermark"=>$DEFAULT_TEMPLATE_WATERMARK,"bottom_page_number"=>true,"bottom_page_text_top"=>"Este documento es una representaciòn impresa de u","bottom_page_promotional"=>true));
$pdf->setFontSubsetting(false);
$pdf->setFont($doc["config"]["font"],"",$doc["config"]["font_size"]);
$pdf->SetLineStyle(array("color"=>array(228,228,228))); /* RGB format color */
$pdf->SetMargins(0,0,0,true);

// patch, tcpdf constructor set encoding to 'ascii'

mb_internal_encoding("UTF-8");

// doc page add


$pdf->setTemplateImage($template["template_image_page_main"],$template["template_image_page_main_dpi"]);
$pdf->setWatermarkXY(73,5);
$pdf->AddPage();
$pdf->SetAutoPageBreak(false,0);

// doc logo
do {

	if(!$sys["enterprise_fiscal"]["logo"])
	 break;

	@$file=APPPATH.$sys["storage"]["enterprise_fiscal"]."enterprise_fiscal/".$sys["enterprise_fiscal"]["logo"];

	if(!$file)
	 break;

	$pdf->Image($file,5,15,30,15,$pdf->getImageFileType($sys["enterprise_fiscal"]["logo"]),'','T',true,72,'',false,false,false,false,false,false);

} while(0);

//-----------------------------------------------------------

$x = 40;
$y = 8;

// enterprise name
printMultiCellTitle($pdf,$doc,$x,$y,100,0,mb_strtoupper($sys["enterprise_fiscal"]["name"]),array("ln"=>1,"font_style"=>"B","font_size"=>10));

// doc name
//printCellTitle($pdf,$doc,74,18,"auto",0,$doc["name"],array("font_size"=>$doc["config"]["font_size_big"],"font_style"=>"B"));

$y = $pdf->GetY()+2;

//Direccion de la empresa
$dir = $sys["enterprise_fiscal"]["street"] . ' ' .
	$sys["enterprise_fiscal"]["outside_number"] . ' ' .
	$sys["enterprise_fiscal"]["colony"] ;

$dir_2 = (!empty($enterprise_country_text)?$enterprise_country_text:' ') . ', ' .
		$sys["enterprise_fiscal"]["city"] . ', ' .
		(!empty($enterprise_country_text)?$enterprise_country_text:' ') . ', C.P.' .
		$sys["enterprise_fiscal"]["zip_code"] ;

printCellValue($pdf, $doc, $x, $y+1, 5, 0, mb_strtoupper($dir), array("ln"=>1));
$y = $pdf->GetY();
printCellValue($pdf, $doc, $x, $y, 5, 0, mb_strtoupper($dir_2), array("ln"=>1));

$y = $pdf->GetY() + 4;
//RFC
printCellTitle($pdf, $doc, $x, $y, "auto", 0, "RFC", array("font_style"=>"B"));
printCellValue($pdf, $doc, 47, $y, "35", 0, mb_strtoupper($sys["enterprise_fiscal"]["rfc"]));

//Telefono
printCellTitle($pdf, $doc, "current", $y, "auto", 0, "TEL", array("font_style"=>"B"));
printCellValue($pdf, $doc, 88, $y, "auto", 0, ($sys["enterprise_fiscal"]["phone"]?mb_substr($sys["enterprise_fiscal"]["phone"],0,26):""), array("ln"=>1));

//Regimen
$y = $pdf->GetY()+3;
// "font_size" => 6
printCellTitle($pdf, $doc, $x, $y, "auto", 0, "Régimen Fiscal:", array("font_style"=>"B","font_size" => 6));
printCellValue($pdf, $doc, 58, $y, "auto", 0, mb_strtoupper($sys["enterprise_fiscal"]["tax_regime"]), array("ln"=>1,"font_size" => 6));

//Datos del Cliente
$x = 5;
$y = $pdf->GetY() + 8;

printCellTitle($pdf, $doc, $x, $y, "auto", 0, "DATOS DEL ".(!empty($client["id"])?"CLIENTE":"PROVEEDOR"), array("font_style"=>"B"));
printCellValue($pdf, $doc, "current", $y, "auto", 0,(!empty($client["id"])?$client["id"]:$provider["id"]) );

//RFC
printCellTitle($pdf, $doc, "current", $y, "60", 0, "RFC", array("font_style"=>"B", "align" => "R"));
printCellValue($pdf, $doc, "current", $y, "auto", 0, (!empty($client["rfc"])?$client["rfc"]:$provider["rfc"]), array("ln" => 1));

//Nombre del Cliente
$y = $pdf->GetY();
printCellValue($pdf, $doc, $x, $y, "auto", 0, mb_strtoupper((!empty($client["name"])?$client["name"]:$provider["name"]) ), array("ln" => 1));

//Calle y numero
$y = $pdf->GetY();
if(!empty($client["subsidiary"]['name']))
printCellValue($pdf, $doc, $x, $y, "auto", 0, ucwords($client["subsidiary"]['name'] .' '.$client["subsidiary"]['street']  . ' ' . $client["subsidiary"]['outside_number'].($client["subsidiary"]['inside_number'] ?" #int. ".$client["subsidiary"]['inside_number']:" ")), array("ln" => 1));

if(!empty($provider["subsidiary"]['name']))
printCellValue($pdf, $doc, $x, $y, "auto", 0, ucwords($provider["subsidiary"]['name'] .' '.$provider["subsidiary"]['street']  . ' ' . $provider["subsidiary"]['outside_number'].($provider["subsidiary"]['inside_number'] ?" #int. ".$provider["subsidiary"]['inside_number']:" ")), array("ln" => 1));

//Colonia 
$y = $pdf->GetY();
if(!empty($client["subsidiary"]['colony']))
printCellValue($pdf, $doc, $x, $y, "auto", 0, 'Col. ' . ucwords($client["subsidiary"]['colony']), array("ln" => 1));

if(!empty($provider["subsidiary"]['colony']))
printCellValue($pdf, $doc, $x, $y, "auto", 0, 'Col. ' . ucwords($provider["subsidiary"]['colony']), array("ln" => 1));

//Delegacion 
$x = $pdf->GetY();
if(!empty($client["subsidiary"]['delegation']))
printCellValue($pdf, $doc, $x, $y, "auto", 0, 'Delegación. ' . ucwords($client["subsidiary"]['delegation']), array("ln" => 1));

if(!empty($provider["subsidiary"]['delegation']))
printCellValue($pdf, $doc, $x, $y, "auto", 0, 'Delegación. ' . ucwords($provider["subsidiary"]['delegation']), array("ln" => 1));

//Ciudad
$y = $pdf->GetY();
$x = 5;

if(!empty($client["subsidiary"]['city']))
printCellValue($pdf, $doc, $x, $y, "auto", 0, ucwords($client["subsidiary"]['city'] . ' ' . $client["subsidiary"]['state_text']), array("ln" => 1));

if(!empty($provider["subsidiary"]['city']))
printCellValue($pdf, $doc, $x, $y, "auto", 0, ucwords($provider["subsidiary"]['city'] . ' ' . $provider["subsidiary"]['state_text']), array("ln" => 1));

//Codigo postal
$x = $pdf->GetY();
if(!empty($client["subsidiary"]['zip_code']))
printCellValue($pdf, $doc, $x, $y, "auto", 0, ucwords("C.P.".$client["subsidiary"]['zip_code']));

if(!empty($provider["subsidiary"]['zip_code']))
printCellValue($pdf, $doc, $x, $y, "auto", 0, ucwords("C.P.".$provider["subsidiary"]['zip_code']));

//Telefono
$y = $pdf->GetY();
$x = 5;

if(!empty($client["subsidiary"]['phone']))
printCellValue($pdf, $doc, $x, $y+3, "auto", 0, mb_strtoupper("TEL.".$client["subsidiary"]['phone']));

if(!empty($provider["subsidiary"]['phone']))
printCellValue($pdf, $doc, $x, $y+3, "auto", 0, mb_strtoupper("TEL.".$provider["subsidiary"]['phone']));

//...
// Vendedor
// if(in_array("include_seller",$DEFAULT_TEMPLATE_OPTIONS,true) and $seller["name"]){
// $y = $pdf->GetY()+5;
// printCellTitle($pdf, $doc, $x, $y, "auto", 0, "Vendedor:", array("font_style"=>"B","font_size" => 6));
// printCellValue($pdf, $doc, 17, $y, "auto", 0, ucwords($seller["name"]), array("ln"=>1,"font_size" => 6));
// }

// // Tipo de Cambio
// if(in_array("include_currency",$DEFAULT_TEMPLATE_OPTIONS,true) and $currency["text"]){
// printCellTitle($pdf, $doc, 80, $y, "auto", 0, "T.C:", array("font_style"=>"B","font_size" => 6));
// printCellValue($pdf, $doc, 85, $y, "auto", 0, $currency["text"]." ".$currency["wid"], array("ln"=>1,"font_size" => 6));
// }

//Factura Folio
$y = 7;
$x = 170;

printCellTitle($pdf, $doc, 170, $y, "15", 0, $doc["name"], array("font_style"=>"B"), array("ln"=>1, "align" => "L"));
printCellValue($pdf, $doc, $x, $y+4, "15", 0, $folio, array("ln"=>1, "align" => "C"));

//Folio Fiscal
$y = $pdf->GetY()+5;

printCellTitle($pdf, $doc, $x, $y, "15", 0,(!empty($uuid)?"Folio fiscal":""), array("font_style"=>"B"), array("ln"=>1, "align" => "C"));
printCellValue($pdf, $doc, $x, $y+5, "15", 0, (!empty($uuid)?$uuid:""), array("ln"=>1, "align" => "C"));

//Serie del Certificado
$y = $pdf->GetY()+5;

printCellTitle($pdf, $doc, $x-15, $y, "25", 0, (!empty($cert_number)?"No. de Serie del Certificado del CFDI":""), array("font_style"=>"B"), array("ln"=>1, "align" => "C"));
printCellValue($pdf, $doc, $x, $y+4, "15", 0, (!empty($cert_number)?$cert_number:""), array("ln"=>1, "align" => "C"));

//Lugar de Expedicion
$y = $pdf->GetY()+5;

printCellTitle($pdf, $doc, $x-7, $y, "25", 0, "Lugar de Expedición", array("font_style"=>"B"), array("ln"=>1, "align" => "C"));
printCellValue($pdf, $doc, $x, $y+4, "15", 0, ucwords($sys["enterprise_fiscal"]["city"]), array("align" => "C"));
printCellValue($pdf, $doc, $x, $y+8, "15", 0, ucwords((!empty($enterprise_state_text)? $enterprise_state_text: "")), array("ln"=>1, "align" => "C"));

//Fecha y Hora de expedicion
$y = $pdf->GetY()+5;

printCellTitle($pdf, $doc, $x-10, $y, "25", 0, "Fecha y Hora de Expedición", array("font_style"=>"B"), array("ln"=>1, "align" => "C"));
printCellValue($pdf, $doc, $x, $y+4, "15", 0, mb_substr($registred_on,0,10) .' '. mb_substr($registred_on,11,8), array("ln"=>1, "align" => "C"));


//---------------------------------------------------------------------------------
$x = 5;
$y = $pdf->GetY()+6;

//Condicion de pago
printCellTitle($pdf, $doc, $x-60, $y, "auto", 0, "Cond. de Pago", array("font_style"=>"B"));
printCellValue($pdf, $doc, "current", $y, "auto", 0,mb_strtoupper((!empty($payment_condition_text)?$payment_condition_text:"") ), array("ln"=>1));


//---------------------------------------------------------------------------------

// Datos de la Tabla

require($TCPDF_DETAILS_HEADER);

// ...multi page behaviour, this script modifies/makes variables (( $yTmp, $yLimitLimited, make$details_additional_pages, etc ))


$xTmp=3;
$yTmp=83;
$yLimitLimited=193.5;
$yLimitFull=$doc["yLimit"];
$details_tmp1 = $details_tmp;
if(!empty($details_tmp))
require($TCPDF_MULTI_PAGE);

if(!empty($details_tmp1))
$details_tmp= $details_tmp2[0]?:$details_tmp1;

//obtenemos el ancho del ultimo registro
if(!empty($details_tmp))
foreach ($details_tmp as $k => $v)
$heightTmp = $pdf->getStringHeight(105.5,$v[2]); //toma el ancho de el ultimo registro 	

//queremos saber si son mas de una pagina, esto es para saber cuanto va medir
// if($d==0):
// 	$yLimit=!$details_additional_pages ? $yLimitLimited : $yLimitFull ;
// else:
// 	$yLimit=$yLimitFull;
// endif;
if(!empty($heightTmp))
$yTmp=$pdf->GetY()+$heightTmp+5;
// if(in_array("include_promissory",$DEFAULT_TEMPLATE_OPTIONS))
// $yLimitLimited-=21;

// lo comente INCORPORANDO
// if(in_array("include_promissory",$DEFAULT_TEMPLATE_OPTIONS,true)):
// 	$yLimitLimited-=21;
// elseif(in_array("include_comment",$DEFAULT_TEMPLATE_OPTIONS,true)):
// 	$yLimitLimited-=15;
// else:
// 	$yLimitLimited-=5;
// endif;

if($yLimitLimited<=$yTmp):
	$yTmp = 17;
	myTCPDF_addPage($pdf,$template_image_page_generic,$template_image_page_generic_dpi,72,5.5);
	myTCPDF_printDocName($pdf,$doc,4,5.5,$doc["name"],"- continuación");
endif;

// if(in_array("include_promissory",$DEFAULT_TEMPLATE_OPTIONS,true)):
// 	$yTmp = 167;
// elseif(in_array("include_comment",$DEFAULT_TEMPLATE_OPTIONS,true)):
// 	$yTmp = 175;
// elseif(in_array("include_caption",$DEFAULT_TEMPLATE_OPTIONS,true)):
// 	// $yTmp = 230;
// else:
	$yTmp = 185;
// endif;

if(!empty($partiality_number)){

$tmp=array();
$partiality_number=$partiality_number?" ".$partiality_number." de ".$partiality_number:"";

$tmp[]=array(0=>"EFECTOS FISCALES AL PAGO",1=>array_search($method_of_payment,array_flip($sys["forms_fields"]["method_of_payment"])).$partiality_number);

$cells_config_tmp=array(

	array("x"=>"base","width"=>"auto","height"=>0,"font_color"=>$doc["config"]["secondary_color"],"ln"=>1,"text_uppercase"=>true),
	array("x"=>"base","width"=>"auto","height"=>0,"font_color"=>$doc["config"]["secondary_color"],"ln"=>1,"text_uppercase"=>true),

);

if($tmp)
myTCPDF_printDataByContinuousCell($pdf,$doc,3,$yTmp,$cells_config_tmp,$tmp,2);

}

// ...printing

// $yTmp=$pdf->GetY()+2;s
$yTmp=205;

$tmp=array();

$tmp[]=array(0=>"cantidad con letra",1=>$total_with_letter);

$cells_config_tmp=array(

	array("x"=>"base","width"=>158,"height"=>0,"font_color"=>$doc["config"]["primary_color"],"font_style"=>"B","ln"=>1,"text_uppercase"=>true),
	array("x"=>"base","width"=>158,"height"=>0,"font_color"=>$doc["config"]["secondary_color"],"ln"=>1,"multi_cell"=>true),

);

if($tmp)
myTCPDF_printDataByContinuousCell($pdf,$doc,3,$yTmp,$cells_config_tmp,$tmp,2);

$yTmp = $pdf->GetY()+2;
$tmp=array();

//Metodo de pago
if(!empty($sat_version) and $sat_version==="cfdi"):

	if(!empty($account_payment_text))
	$tmp[]=array(0=>"Método de Pago",1=>$payment_method_text);
	else
	$tmp[]=array(0=>"Método de Pago",1=>$payment_method_text." / # Cta. ".(!empty($account_payment_text)?$account_payment_text:"") );

else:

	$tmp[]=array(0=>"Método de Pago",1=>$payment_method_text);
	if(!empty($account_payment_text))
	$tmp[]=array(0=>"Num. cta. Pago",1=>(!empty($account_payment_text)?$account_payment_text:""));

endif;

$cells_config_tmp=array(
	array("x"=>"current","width"=>"auto","height"=>0,"font_color"=>$doc["config"]["primary_color"],"font_style"=>"B","ln"=>0),
	array("x"=>"current","width"=>"auto","height"=>0,"font_color"=>$doc["config"]["secondary_color"],"ln"=>0),
);

if($tmp)
	myTCPDF_printDataByContinuousCell($pdf,$doc,3,$yTmp,$cells_config_tmp,$tmp,2);

if(!empty($payment_method) and $payment_method!=1 ){
//Numero de cuenta
printCellTitle($pdf, $doc, "current", $yTmp, "auto", 0, "Número de Cuenta Terminación(es):", array("font_style"=>"B"));
// edite porque no le aparecia el numero de terminacion correcto 
// printCellValue($pdf, $doc, "current", $yTmp, "auto", 0, ($payment_method==20?$payment_method_text:$client['method_payment']['number']), array("ln"=>1));
printCellValue($pdf, $doc, "current", $yTmp, "auto", 0, ($payment_method==20?$payment_method_text:$payment_method_account), array("ln"=>1));
}

$yTmp = $pdf->GetY()+5;
$tmp=array();

if(in_array("include_comment",$DEFAULT_TEMPLATE_OPTIONS,true) and $comment)
$tmp[]=array(0=>"observaciones",1=>mb_substr($comment,0,60));

if(!empty($client_payment_methods_legend_title))
$tmp[]=array(0=>$client_payment_methods_legend_title,1=>$client_payment_methods_legend_value);

if(!empty($discount_reason))
$tmp[]=array(0=>"motivo descuento",1=>mb_substr($discount_reason,0,60));

if(!empty($customs_information_compact))
$tmp[]=array(0=>"información aduanera",1=>$customs_information_compact);


if(!empty($warranty) and !empty($_WARRANTY_ENABLED))
$tmp[]=array(0=>"garantía",1=>mb_substr($warranty,0,60));

$cells_config_tmp=array(

	array("x"=>"base","width"=>158,"height"=>0,"font_color"=>$doc["config"]["primary_color"],"font_style"=>"B","ln"=>1,"text_uppercase"=>true),
	array("x"=>"base","width"=>(in_array("include_promissory",$DEFAULT_TEMPLATE_OPTIONS,true)?100:158),"height"=>0,"font_color"=>$doc["config"]["secondary_color"],"ln"=>1,"multi_cell"=>true),

);

if($tmp)
myTCPDF_printDataByContinuousCell($pdf,$doc,3,$yTmp,$cells_config_tmp,$tmp,2);

$totals[0]["name"]="subtotal";
$totals[1]["name"]="iva";
$totals[2]["name"]="total";

if(!empty($xml_array)):
// totals
$totals[0]["value"]=$xml_array["@attributes"]["subTotal"];
$totals[1]["value"]=$xml_array["cfdi:Impuestos"]["@attributes"]["totalImpuestosTrasladados"];
$totals[2]["value"]=$xml_array["@attributes"]["total"];

else:
// totals
$totals[0]["value"]=$sub_total;
$totals[1]["value"]=$tax_iva;
$totals[2]["value"]=$import;

endif;

if($totals):

	$tmp=array();

	foreach($totals as $v)
	$tmp[]=array(0=>$v["name"]." ".(empty($v["percent"]) ? "" : $v["percent"]." %"),1=>( empty($v["percent"]) ? "" : "" ),2=>"\$".number_format($v["value"],2,".",","));

	$cells_config_tmp=array(

		array("x"=>"base","width"=>23,"height"=>0,"align"=>"R","font_color"=>$doc["config"]["primary_color"],"font_style"=>"B","text_uppercase"=>true),
		array("x"=>"current","width"=>2.5,"height"=>0,"align"=>"R","font_color"=>$doc["config"]["secondary_color"],"ln"=>0),
		array("x"=>"current","width"=>22.5,"height"=>0,"align"=>"R","font_color"=>$doc["config"]["secondary_color"],"ln"=>1),

	);
		// $yTmp=$pdf->GetY()-25;
		$yTmp=205;

	myTCPDF_printDataByContinuousCell($pdf,$doc,161,$yTmp,$cells_config_tmp,$tmp);
endif;

$yTmp=$pdf->GetY();	

if(!empty($date_expires) and $PROMISSORY_ENABLED){

		$tmp2[]=array(0=>"pagaré",1=>"por el presente pagaré reconozco deber y me obligo a pagar en esta ciudad o en cualquiera que se me requiera el pago a ".$sys["enterprise_fiscal"]["name"]." el ".( $date_expires==="0000-00-00" ? "dia de ____________ del ________" : mb_substr($date_expires,8,2)."/".mb_substr($date_expires,5,2)."/".mb_substr($date_expires,0,4) )." la cantidad descrita en los totales de este mismo documento, y de no pagar a tiempo este pagaré causará intereses moratorios al {$sys["config"]["invoice_promissory_note_month_interest_percent"]} % mensual. ".$client["name"]." FIRMA _______________________________________");

		$cells_config_tmp2=array(

		array("x"=>"base","width"=>100,"height"=>0,"font_color"=>$doc["config"]["primary_color"],"font_style"=>"B","ln"=>1,"text_uppercase"=>true),
		array("x"=>"base","width"=>100,"height"=>0,"font_color"=>$doc["config"]["secondary_color"],"ln"=>1,"multi_cell"=>true),

		);

		// if($tmp2 and in_array("include_promissory",$DEFAULT_TEMPLATE_OPTIONS,true))
		myTCPDF_printDataByContinuousCell($pdf,$doc,110,$yTmp+3,$cells_config_tmp2,$tmp2,2);

}

//--------
//datos que estan abajo del total

$x = 3;
$y = 219;

//Serie y certificado del sat
if(!empty($cert_number_sat)):
printCellTitle($pdf, $doc, $x, $y, "auto", 0, "No. de Serie del Certificado del SAT", array("font_style"=>"B"));
printCellValue($pdf, $doc, "current", $y, "80", 0, $cert_number_sat);
endif;

if(!empty($cert_date)):
printCellTitle($pdf, $doc, "current", $y, "auto", 0, "Fecha y Hora de Certificación", array("font_style"=>"B"));
printCellValue($pdf, $doc, "current", $y, "auto", 0, $cert_date, array("ln"=>1));
endif;


// posisiones de x y y y width para el sello de la factura
$x1 = 3;
$y1 = 230;
$_w = 170;

$tmp=array();

if(!empty($sat_version))
$tmp[]=array(0=>"sello digital",1=>(!empty($digital_seal)?$digital_seal:"") );

if(!empty($sat_version) and $sat_version==="CFDI")
$tmp[]=array(0=>"Sello sat",1=>(!empty($sat_seal)?$sat_seal:""));

$cells_config_tmp=array(

	array("x"=>"base","width"=>$_w,"height"=>0,"font_color"=>$doc["config"]["primary_color"],"font_style"=>"B","ln"=>1,"text_ucwords"=>true),
	array("x"=>"base","width"=>$_w,"height"=>0,"font_color"=>$doc["config"]["secondary_color"],"ln"=>1,"multi_cell"=>true),

);

if($tmp)
myTCPDF_printDataByContinuousCell($pdf,$doc,$x1,$y1,$cells_config_tmp,$tmp,0.5);


//Cadena original

// ... ...original string should have a limited y coord

$y1=$pdf->GetY();

$tmp=array();
$tmp[]=array(0=>(!empty($original_string_name)?$original_string_name:""),1=>(!empty($original_string)?$original_string:"") );

$cells_config_tmp=array(

	array("x"=>"base","width"=>$_w,"height"=>0,"font_color"=>$doc["config"]["primary_color"],"font_style"=>"B","ln"=>1,"text_ucwords"=>true),
	array("x"=>"base","width"=>$_w+40,"height"=>0,"font_color"=>$doc["config"]["secondary_color"],"ln"=>1,"multi_cell"=>true),

);

if($tmp)
myTCPDF_printDataByContinuousCell($pdf,$doc,$x1,$y1,$cells_config_tmp,$tmp,0.5);


// QR code

if(!empty($qrcode_cbb)):

	$style=array(
		'border'=>0,
		'padding'=>0,
		'hpadding'=>0,
		'hpadding'=>0,
		'fgcolor'=>array(0,0,0),
		'bgcolor'=>false, //array(255,255,255)
		'module_width'=>1, // width of a single module in points
		'module_height'=>1 // height of a single module in points
	);

	$pdf->write2DBarcode($qrcode_cbb,"QRCODE,H", 180, 225, 27.5,27.5,$style,"N"); // QRCODE,H : QR-CODE Best error correction

endif;


//---------------------------------------------------------------------------------

// series pages??

while(!empty($series_data)) {

	myTCPDF_addPage($pdf,$template_image_page_generic,$template_image_page_generic_dpi,72,5.5);
	myTCPDF_printDocName($pdf,$doc,4,5.5,$doc["name"],"- relación de series");
	$series_data=printGenericDataList($pdf,$doc,3,16,$doc["yLimit"],$series_data);

}

// ...
return array(

	"status"=>1,
	"msg"=>"success",
	"data"=>array(

		"folio"=>(!empty($folio)?$folio:""),
        // "attachements"=>$invoice_electronic_xml_attachement_file_name_and_content,
		// "xml"=>array("filename"=>mb_substr($invoice_electronic_xml_file_name,0,-4).".xml","filecontent"=>$invoice_electronic_xml_file_content),
		// "pdf"=>array("filename"=>$folio.".pdf","filecontent"=>$pdf->Output()),
		"pdf"=>array("filename"=>$folio.".pdf","filecontent"=>$pdf->Output("","S")),
		// "pdf"=>array("filename"=>str_replace(".xml", "", $invoice_electronic_xml_file_name).".pdf","filecontent"=>$pdf->Output("","I")),

	)

);

?>