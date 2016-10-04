<?php

$details_additional_pages=(bool) myTCPDF_printDetails($pdf,$doc,$xTmp,$yTmp,$yLimitLimited,7,$doc["details_config"],$details_tmp,true);
$c=0;

// ...process

while($details_tmp) {

	// add page??
	$d=$c; //esto lo ago para tomar el valor de $c
	if($c>0):

		myTCPDF_addPage($pdf,$template_image_page_details,$template_image_page_details_dpi,72,5.5);
		myTCPDF_printDocName($pdf,$doc,4,5.5,$doc["name"],"- continuación");
		if($status == 3 || $status == 18 || ($status == 12 and  !$credit_balance_destiny))
		$pdf->Image(FCPATH."_resources/images/interface/cancel.png", 5, 110, 200, 100, '', '', '', true, 150);

	endif;

	// coords

	if($c==0):

		if($status == 3 || $status == 18 || ($status == 12 and  !$credit_balance_destiny))
		$pdf->Image(FCPATH."css/_resources/images/interface/cancel.png", 5, 110, 200, 100, '', '', '', true, 150);
		$yLimit=!$details_additional_pages ? $yLimitLimited : $yLimitFull ;

	else:

		$xTmp=3;
		$yTmp=17;
		$yLimit=$yLimitFull;

	endif;

	// printing

	$details_tmp=myTCPDF_printDetails($pdf,$doc,$xTmp,$yTmp,$yLimit,7,$doc["details_config"],$details_tmp);
	// ...

	$details_tmp2[] = $details_tmp;

	$c++;	

}

?>