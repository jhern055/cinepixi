<?php

return array (

	'version' => '2.2',
	'serie' => '',
	'folio' => '',
	'fecha' => '',
	'noAprobacion' => '',
	'anoAprobacion' => '',
	'formaDePago' => '',
	'subTotal' => 0,
	'descuento' => 0,
	'total' => 0,
	'metodoDePago' => '',
	'LugarExpedicion' => '',
	'tipoDeComprobante' => '',  
	'Emisor' => array (
	'rfc' => '',
	'nombre' => '',
	'DomicilioFiscal' => array (
		'calle' => '',
		'noExterior' => '',
		'noInterior' => '',
		'colonia' => '',
		'localidad' => '',
		'referencia' => '',
		'municipio' => '',
		'estado' => '',
		'pais' => '',
		'codigoPostal' => '',
	),
	'ExpedidoEn' => array (
		'calle' => '',
		'noExterior' => '',
		'noInterior' => '',
		'colonia' => '',
		'localidad' => '',
		'referencia' => '',
		'municipio' => '',
		'estado' => '',
		'pais' => '',
		'codigoPostal' => '',
	),
	'RegimenFiscal' => array (
		array ('Regimen' => '',),
	),
	),
	'Receptor' => array (
		'rfc' => 'XAXX010101000',
		'nombre' => 'público en general',
		'Domicilio' => array (
			'calle' => '',
			'noExterior' => '',
			'noInterior' => '',
			'colonia' => '',
			'localidad' => '',
			'referencia' => '',
			'municipio' => '',
			'estado' => '',
			'pais' => 'México',
			'codigoPostal' => '',
		),
	),
	'Conceptos' => array (
		'Concepto' => array (

			array (
				'cantidad' => 0,
				'unidad' => '',
				'descripcion' => '',
				'valorUnitario' => 0,
				'importe' => 0,
			),

		),
	),
	'Impuestos' => array (
/*
		'Retenciones' => array (
			'Retencion' => array (
				array (
					'impuesto' => 'IVA',
					'importe' => 0,
				),
			),
		),
*/
		'Traslados' => array (
			'Traslado' => array (
				array (
					'impuesto' => 'IVA',
					'tasa' => '16',
					'importe' => 0,
				),
			),
		),
	),

);

?>