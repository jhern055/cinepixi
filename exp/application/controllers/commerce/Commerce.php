<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commerce extends CI_Controller {

	public function index() {
		$this->load->template_commerce('index');
	}

	public function category() {

		$this->load->model("admin/stock/catalog/category/category_model");
		$this->load->model("admin/stock/catalog/article/article_model");

		$name_category=(!empty($this->uri->segment(3)) ? strip_tags( $this->security->xss_clean( $this->uri->segment(3) ) ) :"");

		$id_category=$this->category_model->get_id_category_link($name_category);
		$data["articles"]=$this->article_model->get_articles_by_category($id_category);

		$this->load->template_commerce('category/index',$data);
	}	
}