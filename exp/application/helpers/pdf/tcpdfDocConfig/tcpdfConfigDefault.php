<?php

return array(

	"encoding"=>"UTF-8", // in this case, i dont know why "mb_strtoupper()" dont use the "mbstring.internal_encoding" php.ini value
	"cell_border"=>0,
	"cell_fill"=>0,
	// "primary_color"=>hex2rgb($sys["config"]["general_document_primary_hex_color"]),
	"primary_color"=>"140596",
	"secondary_color"=>array(0,0,0),
	"font_color"=>array(0,0,0),
	"font"=>"" /* "arial" can be used too, but performance can vary on not local web servers */,
	"font_style"=>"",
	"font_size"=>8,
	"font_size_small"=>6,
	"font_size_xsmall"=>4,
	"font_size_big"=>12,
	/*"cell_height"=>7,*/

);

?>