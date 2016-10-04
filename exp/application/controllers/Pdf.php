<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf extends CI_Controller {

public $id;
public $sys;
public $source_module;
public $options;
public $_CONFIG_MODULE;

	public function __construct(){
		parent::__construct();
		  
		$this->load->model('functions_model');
		$this->load->helper('pdf/pdf_helper');
		$this->http_params=array_merge($_GET,$_POST);
		$this->load->model("vars_system_model");
		$this->load->helper('xml/xml_helper');

		$this->sys=$this->vars_system_model->_vars_system();

		$this->http_params   =array(
		"id"=>(!empty($this->http_params["id"]) ? strip_tags( $this->security->xss_clean( decode_id($this->http_params["id"]) ) ) :""),
		"source_module"=>(!empty($this->http_params["source_module"]) ? strip_tags( $this->security->xss_clean( decode_id($this->http_params["source_module"]) ) ) :""),
		"template"=>(!empty($this->http_params["template"]) ? strip_tags( $this->security->xss_clean( $this->http_params["template"] ) ) :""),
		"options"=>(!empty($this->http_params["options"]) ? $this->http_params["options"] :array()),
		"action"=>(!empty($this->http_params["action"]) ? $this->http_params["action"] :array()),
		);
		extract($this->http_params);

		$this->id=$id;
		$this->source_module=$source_module;
		$this->action=$action;
		$this->_CONFIG_MODULE=config_module_pdf($source_module);


	}
	public function index() {
	$data=array(
		"id"=>$this->id,
		"module"=>$this->source_module, // se comporta como un "module" para evitar el permiso read pdf
		"source_module"=>$this->source_module."payment/",
		"_CONFIG_MODULE"=>$this->_CONFIG_MODULE,
		);

	$this->load->template('pdf/PdfPrint_view',$data);
		
	}

	public function printGet() {
		$response_pdf=printGet($this->source_module,$this->id,$this->http_params,$this->options);
		if(!$response_pdf["status"])
		return print_r(json_encode($response_pdf));	
		else
		$this->get_action($response_pdf);	
	}

	public function get_action($response_pdf){

		$filenames=array();
		$pdftk=new pdftk();

		foreach($response_pdf as &$v):	
		if(empty($v["pdf"]["filename"]))
		continue;	
			$filenames[]=$v["pdf"]["filename"];

				// $filename_tmp=tempnam("","PDF");
				// $handler=fopen($filename_tmp,"a");
				// fwrite($handler,$v["pdf"]["filecontent"]);
				// fclose($handler);

				// $v["filename"]=$filename_tmp;
				// $pdftk->setInputFile(array("filename"=>$v["filename"]));
			
				$filename_tmp=tempnam("","PDF");
				@$handler=fopen($filename_tmp.".pdf","a");
				if(@fwrite($handler,$v["pdf"]["filecontent"])===false)
				{ $file=null;  break; }

				// $v["filename"]=$filename_tmp.".pdf";
				// $pdftk->setInputFile(array("filename"=>$v["filename"]));

		endforeach;

			unset($v); // important, is reference

			// if(count($id)==1)
			$filename=pathinfo($filenames[0],PATHINFO_FILENAME).( count($filenames)==1 ? "" : "-multi" );
			// else
			// $filename="multi-doc-".uniqid();
		$file = $filename_tmp.".pdf";

			if($this->action=="display"):


		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="' . $filename . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file));
		header('Accept-Ranges: bytes');

		@readfile($file);
		exit();

			header('Content-type: application/pdf');
			// $pdftk->setOutputFile($save_file);
			// echo $pdftk->_renderPdf();

			elseif($this->action=="download"):

			header('Content-type: application/pdf');
			header('Content-Disposition: attachment; filename="'.$filename.'.pdf"');
		// header('Content-Disposition: inline; filename="' . $filename . '"');

			@readfile($file);

			//header("Content-Length: 0");
			// echo $pdftk->_renderPdf();
			// $pdftk->downloadOutput();

			endif;

			// delete temp files

			foreach($data as $v)
			@unlink($v["filename"]);

	}

}
?>