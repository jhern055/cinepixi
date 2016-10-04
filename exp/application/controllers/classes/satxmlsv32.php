<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."libraries/php/myCFDX/MyCFDX.php");
class Satxml {

// <getXmlArray>
public function getSaleXmlArray($data,$sys){

$version="3.2";

// <Generacion> del XML
$partiality_number=(!empty($data["bill_data"]["partiality_number"])?" ".$data["bill_data"]["partiality_number"]." de ".$data["bill_data"]["partiality_number"]:"");
if(empty($data["bill_data"]["method_of_payment"]))
$data["bill_data"]["method_of_payment"]=6;

$sale_specific=array(

"version"           =>$version,
"serie"             =>$data["bill_data"]["folio_serie"],
"folio"             =>$data["bill_data"]["folio_number"],
"fecha"             =>str_replace(" ","T",$data["bill_data"]["registred_on"]), // ISO 8601 aaaa-mm-ddThh:mm:ss
/*"sello"           =>"",*/ // set dinamically

"formaDePago"       =>array_search($data["bill_data"]["method_of_payment"],array_flip($sys["forms_fields"]["method_of_payment"]) ).$partiality_number, 
// "formaDePago"    =>"Pago en una sola exhibicion", // Pago en una sola exibición | Parcialidad 1 de X.

/*"noCertificado"   =>"",*/ // set dinamically
/*"certificado"     =>"",*/ // set dinamically
"condicionesDePago" =>array_search($data["bill_data"]["payment_condition"],array_flip($sys["forms_fields"]["payment_condition"])),
"subTotal"          =>$data["bill_data"]["sub_total"],
"descuento"         =>(!empty($data["bill_data"]["discount"])?$data["bill_data"]["discount"]:""),
"motivoDescuento"   =>(!empty($data["bill_data"]["discount_reason"])?$data["bill_data"]["discount_reason"]:""),
"total"             =>$data["bill_data"]["import"],
"metodoDePago"      =>"No Identificado",
// "metodoDePago"      =>array_search($data["bill_data"]["payment_condition"],array_flip($sys["forms_fields"]["payment_condition"]) ),
"LugarExpedicion"   =>$data["subsidiary"]["state_text"],
"NumCtaPago"        =>(!empty($data["bill_data"]["payment_method_account"])?mb_substr($data["bill_data"]["payment_method_account"],-4):""),
"tipoDeComprobante" =>"ingreso", // ingreso | egreso | traslado,
"Moneda"            =>$data["bill_data"]["type_of_currency_info"]["currency"], 
"TipoCambio"        =>$data["bill_data"]["exchange_rate"],

);

// ...

$sale_specific["cfdi:Emisor"]=array(

"rfc"=>"AAD990814BP7",
// "rfc"=>strtoupper(str_replace(array(" ","-"),"",trim($sys["enterprise_fiscal"]["rfc"]))),
"nombre"=>$sys["enterprise_fiscal"]["name"],
"cfdi:ExpedidoEn"=>array(

"calle"        =>$data["subsidiary"]["street"],
"noExterior"   =>$data["subsidiary"]["outside_number"],
"noInterior"   =>$data["subsidiary"]["inside_number"],
"colonia"      =>$data["subsidiary"]["colony"],
"localidad"    =>$data["subsidiary"]["location"],
"referencia"   =>$data["subsidiary"]["reference"],
"municipio"    =>$data["subsidiary"]["city"],
"estado"       =>$data["subsidiary"]["state_text"],
"codigoPostal" =>$data["subsidiary"]["zip_code"],
"pais"         =>$data["subsidiary"]["country"],

),

);

// ...

$sale_specific["cfdi:Emisor"]["cfdi:RegimenFiscal"]=array();

$regimen_tmp=!$sys["enterprise_fiscal"]["tax_regime"] ? array() : explode(",",$sys["enterprise_fiscal"]["tax_regime"]) ;

foreach($regimen_tmp as $v)
 $sale_specific["cfdi:Emisor"]["cfdi:RegimenFiscal"][]=array("Regimen"=>$v);

// ...

$sale_specific["cfdi:Receptor"]=array(

"rfc"=>strtoupper(str_replace(array(" ","-"),"",( ($sys["enterprise_fiscal"]["country"]==$data["client"]["subsidiary"]["country"]) ? trim($data["client"]["rfc"]) : $sys["config"]["invoice_electronic_foreign_client_rfc_generic"] ))), // "XAXX010101000" público en general, "XEXX010101000" clientes extranjeros
"nombre"=>$data["client"]["name"],
"cfdi:Domicilio"=>array(

"calle"        =>$data["client"]["subsidiary"]["street"],
"noExterior"   =>$data["client"]["subsidiary"]["outside_number"],
"noInterior"   =>$data["client"]["subsidiary"]["inside_number"],
"colonia"      =>$data["client"]["subsidiary"]["colony"],
"localidad"    =>$data["client"]["subsidiary"]["location"],
"referencia"   =>$data["client"]["subsidiary"]["reference"],
"municipio"    =>$data["client"]["subsidiary"]["city"],
"estado"       =>$data["client"]["subsidiary"]["state_text"],
"pais"         =>$data["client"]["subsidiary"]["country_text"],
"codigoPostal" =>$data["client"]["subsidiary"]["zip_code"],

),

);

// ...

			$sale_specific["cfdi:Conceptos"]=array(
			"cfdi:Concepto"=>array()
			);

foreach($data["bill_data"]["details"] as $k=>$v) {

	// $v["description_tmp"]=array();
	if($v["article"] or $v["article_name"]) 
	$v["description_tmp"][]=( $v["article_name"] ? $v["article_name"] : "articulo id {$v["article"]}" );

	if($v["description"]) $v["description_tmp"][]=$v["description"];
	$v["description_tmp"]=implode(", ",$v["description_tmp"]);

	$tmp=array(

		"noIdentificacion" =>$v["article_name"],
		"cantidad"         =>$v["quantity"],
		// "unidad"           =>$v["unit"],
		"unidad"           =>"PIEZA",
		"descripcion"      =>$v["description_tmp"],
		"valorUnitario"    =>$v["price"],
		"importe"          =>$v["total_sub"],

	);

	if(!empty($v["customs_information"])) {

	$tmp["cfdi:InformacionAduanera"]=array();

		foreach($v["customs_information"] as $v2):

		$tmp["cfdi:InformacionAduanera"][]=array(

		"numero" =>$v2["number"],
		"fecha"  =>$v2["date"],
		"aduana" =>$v2["name"],

		);

		endforeach;

	}

	// ...

	if( !empty( $v["serial_codes"]) ) {

		$tmp["cfdi:Parte"]=array();

		foreach($v["serial_codes"] as $v2):

			 $tmp2=array(

				"cantidad"         =>1,
				"descripcion"      =>"info adicional",
				"noIdentificacion" =>$v2,

			 );

			$tmp["cfdi:Parte"][]=$tmp2;

		endforeach;

	}

	$sale_specific["cfdi:Conceptos"]["cfdi:Concepto"][]=$tmp;

}

// ...

$sale_specific["cfdi:Impuestos"]=array();

$tax_retenciones=array();
$tax_traslados=array();

foreach($data["bill_data"]["details"] as $k=>$v) {

if(!empty($v["tax_iva_retained"]))
$tax_retenciones[]=array("impuesto"=>"IVA","importe"=>$v["tax_iva_retained_calculated"]);

if(!empty($v["tax_isr"]))
$tax_retenciones[]=array("impuesto"=>"ISR","importe"=>$v["tax_isr_calculated"]);

if(!empty($v["tax_iva"]))
$tax_traslados[]=array("impuesto"=>"IVA","tasa"=>$v["tax_iva"],"importe"=>$v["tax_iva_calculated"]);

if(!empty($v["tax_ieps"]))
$tax_traslados[]=array("impuesto"=>"IEPS","tasa"=>$v["tax_ieps"],"importe"=>$v["tax_ieps_calculated"]);

}

$sale_specific["cfdi:Impuestos"]["totalImpuestosRetenidos"]=0;

if($tax_retenciones):

foreach($tax_retenciones as $v)
$sale_specific["cfdi:Impuestos"]["totalImpuestosRetenidos"]+=(float) number_format($v["importe"],6,".","");

		$sale_specific["cfdi:Impuestos"]["cfdi:Retenciones"]=array();
		$sale_specific["cfdi:Impuestos"]["cfdi:Retenciones"]["cfdi:Retencion"]=$tax_retenciones;

endif;

$sale_specific["cfdi:Impuestos"]["totalImpuestosTrasladados"]=0;

	if($tax_traslados):

		foreach($tax_traslados as $v)
		$sale_specific["cfdi:Impuestos"]["totalImpuestosTrasladados"]+=(float) number_format($v["importe"],6,".","");

			$sale_specific["cfdi:Impuestos"]["cfdi:Traslados"]=array();
			$sale_specific["cfdi:Impuestos"]["cfdi:Traslados"]["cfdi:Traslado"]=$tax_traslados;

	endif;

 return array("status"=>1,"msg"=>"Se creo con exito el xml tmp","data"=>$sale_specific);
	 

	}

// </getXmlArray>
}


?>