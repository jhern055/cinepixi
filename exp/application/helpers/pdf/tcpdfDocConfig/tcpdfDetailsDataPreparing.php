<?php

$tmp=array();

if(!empty($details))
if($details):

	$c=1;
	foreach($details as $v):

		$tmp[]=print_detailDataPreparing($v,$c);
	$c++;
	endforeach;

endif;

$details_tmp=$tmp;
unset($tmp);

?>