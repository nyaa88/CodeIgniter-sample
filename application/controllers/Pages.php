<?php
class Pages extends CI_Controller{
	public function view($page = 'home'){
		if(! file_exists(APPPATH.'views/pages/'.$page.'.php')){
			// ページが見つかりません！
			show_404();
		}

		$data['title'] = ucfirst($page); //頭文字を大文字に

		$this->load->view('templates/header',$data);
		$this->load->view('pages/'.$page, $data);
		$this->load->view('templates/footer',$data);
	}
}