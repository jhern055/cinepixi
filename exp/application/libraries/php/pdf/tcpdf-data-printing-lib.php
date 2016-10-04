<?php

function printCell_universal($pdfObj,$doc,$x,$y,$width=0,$height=0,$text="",$params_additional=null,$multi_cell=null,$font_color=null) {

	// NOTE :: $params_additional can overwrite almost any other param function

	if($params_additional and is_array($params_additional)) {

		if(isset($params_additional["pdfObj"])) unset($params_additional["pdfObj"]);
		if(isset($params_additional["doc"])) unset($params_additional["doc"]);
		if(isset($params_additional["x"])) unset($params_additional["x"]);
		if(isset($params_additional["y"])) unset($params_additional["y"]);

		extract($params_additional);

	}
	
	if($x=="current") $x=$pdfObj->GetX();
	if($y=="current") $y=$pdfObj->GetY();
	if(!isset($multi_cell) or $multi_cell===null) $multi_cell=false;
	if(!isset($font_style)) $font_style="";
	if(!isset($font_size)) $font_size=$doc["config"]["font_size"];
	if(!isset($ln)) $ln=0;
	if(!isset($align)) $align="L";
	if(!isset($stretch)) $stretch=0;
	$height_max=( (!empty($height_max) and $height_max>$height) ? $height_max : 0 );
	if(!isset($font_color) or $font_color===null) $font_color=array(0,0,0); // rgb format
	
	if(!empty($text_uppercase)) $text=mb_strtoupper($text,"UTF-8");
	if(!empty($text_lowercase)) $text=mb_strtolower($text,"UTF-8");
	if(!empty($text_ucfirst)) $text=ucfirst($text);
	if(!empty($text_ucwords)) $text=ucwords($text);

	$pdfObj->setXY($x,$y);
	$pdfObj->setFont($doc["config"]["font"],$font_style,$font_size);
	$pdfObj->SetTextColor($font_color[0],$font_color[1],$font_color[2]);

	if($width=="auto") $width=$pdfObj->GetStringWidth($text)+2; // "+2" is cuz it seems cells have an "internal" 2 padding left

	if(!$multi_cell)
	$pdfObj->Cell($width,$height,$text,$doc["config"]["cell_border"],$ln,$align,$doc["config"]["cell_fill"],"",$stretch);
	else
	$pdfObj->MultiCell($width,$height,$text,$doc["config"]["cell_border"],$align,$doc["config"]["cell_fill"],$ln,null,null,true,$stretch,false,false,$height_max);

	// restore defaults

	$pdfObj->SetTextColor($doc["config"]["secondary_color"][0],$doc["config"]["secondary_color"][1],$doc["config"]["secondary_color"][2]);
	$pdfObj->setFont($doc["config"]["font"],$font_style,$doc["config"]["font_size"]);

};

function printCellTitle($pdfObj,$doc,$x,$y,$width=0,$height=0,$text="",$params_additional=null) {

	printCell_universal($pdfObj,$doc,$x,$y,$width,$height,$text,$params_additional,false,$doc["config"]["primary_color"]);

};

function printCellTitle_dos($pdfObj,$doc,$x,$y,$width=0,$height=0,$text="",$params_additional=null) {

	printCell_universal($pdfObj,$doc,$x,$y,$width,$height,$text,$params_additional,false,$doc["config"]["tertiary_color"]);

};

function printMultiCellTitle($pdfObj,$doc,$x,$y,$width=0,$height=0,$text="",$params_additional=null) {

	printCell_universal($pdfObj,$doc,$x,$y,$width,$height,$text,$params_additional,true,$doc["config"]["primary_color"]);

};

function printCellValue($pdfObj,$doc,$x,$y,$width=0,$height=0,$text="",$params_additional=null) {

	printCell_universal($pdfObj,$doc,$x,$y,$width,$height,$text,$params_additional,false,$doc["config"]["secondary_color"]);

};

function printMultiCellValue($pdfObj,$doc,$x,$y,$width=0,$height=0,$text="",$params_additional=null) {

	printCell_universal($pdfObj,$doc,$x,$y,$width,$height,$text,$params_additional,true,$doc["config"]["secondary_color"]);

};

function printDataByRowsCols_header($pdfObj,$doc,$config,$x,$y) {

	for($c=0;$c<count($config["cells"]);$c++):

		$config_tmp=$config["cells"][$c];

		if($c==0)
		{ $xTmp=$x;  $yTmp=$y; }
		else
		{ $xTmp="current";  $yTmp=$y; }

		printCellTitle($pdfObj,$doc,$xTmp,$yTmp,$config_tmp["width"],0,$config_tmp["title"],$config_tmp["header_additional"]);

	endfor;

};

function printDataByRowsCols_header_dos($pdfObj,$doc,$config,$x,$y) {

	for($c=0;$c<count($config["cells"]);$c++):

		$config_tmp=$config["cells"][$c];

		if($c==0)
		{ $xTmp=$x;  $yTmp=$y; }
		else
		{ $xTmp="current";  $yTmp=$y; }

		printCellTitle_dos($pdfObj,$doc,$xTmp,$yTmp,$config_tmp["width"],0,$config_tmp["title"],$config_tmp["header_additional"]);

	endfor;

};

function printDataByRowsCols_item($pdfObj,$doc,$config,$item=array()) {

	for($c=0;$c<count($config["cells"]);$c++):

		$config_tmp=$config["cells"][$c];

		if($c==0)
		{ $xTmp="current";  $yTmp=$pdfObj->GetY(); }
		else
		{ $xTmp="current";  $yTmp="current"; }
		printCell_universal($pdfObj,$doc,$xTmp,$yTmp,$config_tmp["width"],0,$item[$c],$config_tmp["item_additional"]);
	endfor;

};

function printDataByRowsCols_list($pdfObj,$doc,$config,$x,$y,$yLimit,$data,$lineDivision=false) {

	$widht_tmp=0;
	$index_tmp=0;
	foreach($config["cells"] as $k=>$v):

	if($widht_tmp<$v["width"] and !empty($v["item_additional"]["multi_cell"]))
	$index_tmp=$k;

	endforeach;

	if(!$index_tmp):
	foreach($config["cells"] as $k=>$v):

	if($widht_tmp<$v["width"])
	$index_tmp=$k;

	endforeach;
	endif;

	// ...

	$data_residue=array();
	$c=0;
	foreach($data as $item) {

		$heightTmp=$pdfObj->getStringHeight($config["cells"][$index_tmp]["width"],$item[$index_tmp]);
		if($yLimit>0 and $pdfObj->GetY()+$heightTmp>=$yLimit)
			{ $data_residue[]=$item;  continue;}
		
		if($lineDivision and $c>0)
		$pdfObj->Line($x+1,$y-1,212,$y-1);

		$pdfObj->setXY($x,$y);

		$y+=$heightTmp;

		if($yLimit<=$y):
			$data_residue[]=$item;continue;
		endif;

		printDataByRowsCols_item($pdfObj,$doc,$config,$item);

		$c++;

	}

	return $data_residue;

};

function printGenericDataList($pdfObj,$doc,$x,$y,$yLimit,$data,$lineDivision=false) {

	$data_residue=array();
	$cellMaxWidth=210;
	$c=0;

	foreach($data as $item) {

		$tmp=array();
		$tmp[]=$item["title"];
		$tmp[]=$item["value"];
		$tmp=implode("\n",$tmp);

		$heightTmp=$pdfObj->getStringHeight($cellMaxWidth,$tmp);

		if($yLimit>0 and $pdfObj->GetY()+$heightTmp>=$yLimit)
		{ $data_residue[]=$item;  continue; }

		if($lineDivision and $c>0)
		$pdfObj->Line($x+1,$y-1,212,$y-1);

		if($item["title"])
		printMultiCellTitle($pdfObj,$doc,$x,$y,$cellMaxWidth,0,$item["title"],array("ln"=>1));

		if($item["value"])
		printMultiCellValue($pdfObj,$doc,$x,"current",$cellMaxWidth,0,$item["value"],array("ln"=>1));

		$y=$pdfObj->GetY()+2; // +2 is for additional spacing

		$c++;

	}

	return $data_residue;

};

function myTCPDF_addPage($pdfObj,$template_image,$template_image_dpi,$watermark_x,$watermark_y) {

	if($template_image)
	$pdfObj->setTemplateImage($template_image,$template_image_dpi);

	$pdfObj->setWatermarkXY($watermark_x,$watermark_y);

	$pdfObj->AddPage();
	$pdfObj->SetAutoPageBreak(false,0);

};

function myTCPDF_printDocName($pdfObj,$doc,$x,$y,$title,$value) {

	printCellTitle($pdfObj,$doc,$x,$y,"auto",0,$title,array("font_size"=>$doc["config"]["font_size_big"]));
	printCellValue($pdfObj,$doc,"current",$y,"auto",0,$value,array("font_size"=>$doc["config"]["font_size_big"]));

};

function myTCPDF_printDocName_dos($pdfObj,$doc,$x,$y,$title,$value) {

	printCellTitle($pdfObj,$doc,$x,$y,"auto",0,$title,array("font_size"=>$doc["config"]["font_size_big"],"font_color"=>$doc["config"]["tertiary_color"]));
	printCellValue($pdfObj,$doc,"current",$y,"auto",0,$value,array("font_size"=>$doc["config"]["font_size_big"],"font_color"=>$doc["config"]["tertiary_color"]));

};

function myTCPDF_printDetails($pdfObj,$doc,$x,$y,$yLimit,$yBreakSpeace,$rowsColsConfig,$data,$virtualMode=false) {

	// virtual mode allow do pdf object changes and revert them, but return modified $data.

	if($virtualMode)
	$pdfObj=clone $pdfObj;

	// ...

	$xTmp=$x;
	$yTmp=$y;

	// header

	printDataByRowsCols_header($pdfObj,$doc,$rowsColsConfig,$xTmp,$yTmp);

	// values

	$yTmp+=$yBreakSpeace;

	$data=printDataByRowsCols_list($pdfObj,$doc,$rowsColsConfig,$xTmp,$yTmp,$yLimit,$data);
	
	// ...

	return $data; // returns data not printed if $yLimit was reached

};

function myTCPDF_printDetails_dos($pdfObj,$doc,$x,$y,$yLimit,$yBreakSpeace,$rowsColsConfig,$data,$virtualMode=false) {

	// virtual mode allow do pdf object changes and revert them, but return modified $data.

	if($virtualMode)
	$pdfObj=clone $pdfObj;

	// ...

	$xTmp=$x;
	$yTmp=$y;

	// header

	printDataByRowsCols_header_dos($pdfObj,$doc,$rowsColsConfig,$xTmp,$yTmp);

	// values

	$yTmp+=$yBreakSpeace;

	$data=printDataByRowsCols_list($pdfObj,$doc,$rowsColsConfig,$xTmp,$yTmp,$yLimit,$data);

	// ...

	return $data; // returns data not printed if $yLimit was reached

};

function myTCPDF_printDataByContinuousCell($pdfObj,$doc,$x,$y,$cellsConfig,$data,$verticalSpacingByItem=0) {

/* NOTE :: la funcion printGenericDataList() puede ser reemplazada por esta funcion, si a esta funcion le añadimos $yLimit y la capacidad de retornar $data_residue */

$xTmp=$x;
$yTmp=$y;

$pdfObj->setXY($xTmp,$yTmp);

$c=0;

foreach($data as $item):  

	$c2=0;

	if($verticalSpacingByItem and $c>0)
	$pdfObj->SetY( $pdfObj->GetY()+$verticalSpacingByItem );

	foreach($cellsConfig as $cellConfig):

	if($cellConfig["x"]=="base")
	$xTmp=$x;
	else
	$xTmp="current";

	printCell_universal($pdfObj,$doc,$xTmp,"current",null,null,$item[$c2],$cellConfig);

	//$xTmp=$pdfObj->GetX();
	//$yTmp=$pdfObj->GetY();

	$c2++;

	endforeach;

	$c++;

endforeach;

};

function print_detailDataPreparing($detail,$c) {

	// session vars

	// $sys=$_SESSION["sys"];

	// ...

	// if(!is_array($options))
	// $options=array();

	if(!is_array($detail))
	$data=array();
	else {
		
		// // si tiene predeterminado que se vean los descuentos en pdf
	 // 	if($sys["config"]["show_discount_items"]){

		// 	if($detail["id_parent"]){
		// 	$data=array($c,number_format($detail["quantity"],$sys["config"]["article_quantity_decimals_number"],".",","),$detail["unit"],( $detail["article_name"].", ".$detail["description"].( (!in_array("series_relation_page",$options,true) and $detail["serial_codes"]) ? "\n".implode(", ",$detail["serial_codes"]) : "" ) ),"-","-");
		// 	}else{

		// 		// if($detail["discounts"]){
		// 		// $data=array($c,number_format($detail["quantity"],$sys["config"]["article_quantity_decimals_number"],".",","),$detail["unit"],( $detail["article_name"].", ".$detail["description"].( (!in_array("series_relation_page",$options,true) and $detail["serial_codes"]) ? "\n".implode(", ",$detail["serial_codes"]) : "" ) ),"\$".number_format($detail["price"],$sys["config"]["article_price_decimals_number"],".",","),$detail["discount"],"\$".($detail["discount"]?number_format($discount_div=( ($detail["discount"]/100)*$detail["price"] ),2,".",","):""),"\$".number_format($price_less_discount=$detail["price"]-$discount_div,2,".",","),"\$".number_format($price_less_discount*$detail["quantity"],2,".",","));
		// 		if($detail["invoice_folio"]){ // saldos iniciales
		// 		$data=array($c,$detail["invoice_folio"],$detail["invoice_date"],$detail["description"],"\$".number_format($detail["total"],$sys["config"]["article_price_decimals_number"],".",","));			
		// 		}elseif(in_array("show_discounts",$options)){ // saldos iniciales
		// 			$data=array($c,number_format($detail["quantity"],$sys["config"]["article_quantity_decimals_number"],".",","),$detail["unit"],( $detail["article_name"].", ".$detail["description"].( (!in_array("series_relation_page",$options,true) and $detail["serial_codes"]) ? "\n".implode(", ",$detail["serial_codes"]) : "" ) ),"\$".number_format($detail["price"],$sys["config"]["article_price_decimals_number"],".",","),"\$".number_format($detail["total_sub"],2,".",","));
		// 		}elseif(in_array("is_remission",$options)){ // remisiones
		// 			$data=array($c,number_format($detail["quantity"],$sys["config"]["article_quantity_decimals_number"],".",","),$detail["unit"],( $detail["article_name"].", ".$detail["description"].( (!in_array("series_relation_page",$options,true) and $detail["serial_codes"]) ? "\n".implode(", ",$detail["serial_codes"]) : "" ) ),"\$".number_format($detail["price"],$sys["config"]["article_price_decimals_number"],".",","),"\$".number_format($detail["total_sub"],2,".",","));
		// 		}else{  //facturas
		// 		$data=array($c,number_format($detail["quantity"],$sys["config"]["article_quantity_decimals_number"],".",","),$detail["unit"],( $detail["article_name"].", ".$detail["description"].( (!in_array("series_relation_page",$options,true) and $detail["serial_codes"]) ? "\n".implode(", ",$detail["serial_codes"]) : "" ) ),"\$".number_format($detail["price"],$sys["config"]["article_price_decimals_number"],".",","),$detail["discount"],"\$".($detail["discount"]?number_format($discount_div=( ($detail["discount"]/100)*$detail["price"] ),2,".",","):""),"\$".number_format($price_less_discount=$detail["price"]-$discount_div,2,".",","),"\$".number_format($price_less_discount*$detail["quantity"],2,".",","));


		// 			// $data=array($c,number_format($detail["quantity"],$sys["config"]["article_quantity_decimals_number"],".",","),$detail["unit"],( $detail["article_name"].", ".$detail["description"].( (!in_array("series_relation_page",$options,true) and $detail["serial_codes"]) ? "\n".implode(", ",$detail["serial_codes"]) : "" ) ),"\$".number_format($detail["price"],$sys["config"]["article_price_decimals_number"],".",","),"-","-","-","\$".number_format($detail["total_sub"],2,".",","));
		// 		}
				
		// 	}
		
		// } 	
		// else{

		// $data=array($c,number_format($detail["quantity"],2,".",","),(!empty($detail["unit"])?$detail["unit"]:""),( $detail["article_name"].", ".$detail["description"].( (!in_array("series_relation_page",$options,true) and $detail["serial_codes"]) ? "\n".implode(", ",$detail["serial_codes"]) : "" ) ),"\$".number_format($detail["price"],$sys["config"]["article_price_decimals_number"],".",","),"\$".number_format($detail["total_sub"],2,".",","));
		$data=array($c,number_format($detail["quantity"],2,".",","),(!empty($detail["unit"])?$detail["unit"]:""),( $detail["article_name"].", ".$detail["description"] ),"\$".number_format($detail["price"],2,".",","),"\$".number_format($detail["total_sub"],2,".",","));

		// }

	}

	return $data;

};
?>
