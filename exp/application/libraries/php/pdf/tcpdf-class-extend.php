<?php

class MYPDF extends TCPDF {

	// ...

    public $customVars=array();

    public function setCustomVars($customVars) {

		$this->customVars=$customVars;

    }

	// set template design by image

    public function setWatermarkXY($x,$y) {

		$this->_watermarkX=$x;
		$this->_watermarkY=$y;

    }

	// set template design by image

    public function setTemplateImage($file,$dpi) {

		$this->_templateImageFile=$file;
		$this->_templateImageDpi=$dpi;

    }

	// page header

    public function Header() {

		// template by image??

		if($this->_templateImageFile):

		$this->SetAutoPageBreak(false,0); // without this the image will be rezised
		$this->Image($this->_templateImageFile,0,0,"","",'','','top-left',false,$this->_templateImageDpi,'',false,false,0,false,false,false);

		endif;

		// watermark??

		if($this->customVars["watermark"]):

		// ...

		$this->SetXY($this->_watermarkX,$this->_watermarkY);
		$this->setFont($this->customVars["config"]["font"],$this->customVars["config"]["font_style"],$this->customVars["config"]["font_size_big"]);
		$this->SetTextColor($this->customVars["config"]["primary_color"][0],$this->customVars["config"]["primary_color"][1],$this->customVars["config"]["primary_color"][2]);

		// ...

		//$this->SetAlpha(0.5);
		$this->Cell(74+64.5,0,$this->customVars["watermark"],$this->customVars["config"]["cell_border"],0,"R",false,'',0,false/*,"T","M"*/);
		//$this->SetAlpha(1);

		// restore defaults

		$this->SetTextColor($this->customVars["config"]["font_color"][0],$this->customVars["config"]["font_color"][1],$this->customVars["config"]["font_color"][2]);
		$this->setFont($this->customVars["config"]["font"],$this->customVars["config"]["font_style"],$this->customVars["config"]["font_size"]);

		endif;

	}

	// page footer

	public function Footer() {

		// bottom page text top??

		if($this->customVars["bottom_page_text_top"]):

			// ...

			$this->SetXY(3,-17.5);
			$this->setFont($this->customVars["config"]["font"],$this->customVars["config"]["font_style"],8);
			$this->SetTextColor(205,201,201);

			// ...

			$this->Cell(0,8,$this->customVars["bottom_page_text_top"],$this->customVars["config"]["cell_border"],0,'C',false,'',0,false/*,'T','C'*/);

			// restore defaults

			$this->SetTextColor($this->customVars["config"]["font_color"][0],$this->customVars["config"]["font_color"][1],$this->customVars["config"]["font_color"][2]);
			$this->setFont($this->customVars["config"]["font"],$this->customVars["config"]["font_style"],$this->customVars["config"]["font_size"]);

		endif;

		// bottom page promotional??

		if($this->customVars["bottom_page_promotional"]):

			// if "bottom_page_text" is set, is recommended not use this feature cuz content will overflow

			// ...

			$this->SetXY(21,-12.5);
			$this->setFont($this->customVars["config"]["font"],$this->customVars["config"]["font_style"],8);
			$this->SetTextColor($this->customVars["config"]["secondary_color"][0],$this->customVars["config"]["secondary_color"][1],$this->customVars["config"]["secondary_color"][2]);

			// ...

			/* NOTE i was unable to put this link using writeHTML() and writeHTMLCell() methods, cuz displayed link is a little ugly */

			// $text=$_SESSION["sys"]["config"]["pdf_template_promo_text"];
			// $text=explode("\n",$text);

			$xTmp=$this->GetX();

			// foreach($text as $v) {

			// 	$this->Write(8,$v,$_SESSION["sys"]["config"]["pdf_template_promo_url"],false,'L',false,0,false,true);  
			// 	$this->SetXY($xTmp,$this->GetY()+3);

			// }

			// restore defaults

			$this->SetTextColor($this->customVars["config"]["font_color"][0],$this->customVars["config"]["font_color"][1],$this->customVars["config"]["font_color"][2]);
			$this->setFont($this->customVars["config"]["font"],$this->customVars["config"]["font_style"],$this->customVars["config"]["font_size"]);

		endif;

		// bottom page text?

		if(!empty($this->customVars["bottom_page_text"])):

			// ...

			$this->SetXY(2,-11);
			$this->setFont($this->customVars["config"]["font"],$this->customVars["config"]["font_style"],$this->customVars["config"]["font_size_big"]);
			$this->SetTextColor($this->customVars["config"]["primary_color"][0],$this->customVars["config"]["primary_color"][1],$this->customVars["config"]["primary_color"][2]);

			// ...

			$this->Cell(0,8,$this->customVars["bottom_page_text"],$this->customVars["config"]["cell_border"],0,'C',false,'',0,false/*,'T','C'*/);

			// restore defaults

			$this->SetTextColor($this->customVars["config"]["font_color"][0],$this->customVars["config"]["font_color"][1],$this->customVars["config"]["font_color"][2]);
			$this->setFont($this->customVars["config"]["font"],$this->customVars["config"]["font_style"],$this->customVars["config"]["font_size"]);

		endif;

		// bottom page number??

		if($this->customVars["bottom_page_number"]):

			// ...

			$this->SetXY(2,-11);
			$this->setFont($this->customVars["config"]["font"],$this->customVars["config"]["font_style"],$this->customVars["config"]["font_size_big"]);
			$this->SetTextColor($this->customVars["config"]["primary_color"][0],$this->customVars["config"]["primary_color"][1],$this->customVars["config"]["primary_color"][2]);

			// ...

			$text=$this->getAliasNumPage().'/'.$this->getAliasNbPages();
			$this->Cell(0,8,$text,$this->customVars["config"]["cell_border"],0,'R',false,'',0,false/*,'T','C'*/);

			// restore defaults

			$this->SetTextColor($this->customVars["config"]["font_color"][0],$this->customVars["config"]["font_color"][1],$this->customVars["config"]["font_color"][2]);
			$this->setFont($this->customVars["config"]["font"],$this->customVars["config"]["font_style"],$this->customVars["config"]["font_size"]);

		endif;

	}

}

?>