<?php
class News_model extends CI_Model{
	public function __construct(){
		$this->load->database();
	}


	public function get_news($slug = FALSE){
		if($slug === FALSE){ //$slugがない = 全記事
			$query = $this->db->get('news');
			return $query->result_array();
		}
		$slug = urldecode($slug);
		$query = $this->db->get_where('news',array('slug' => $slug));
		return $query->row_array();
	}


	public function set_news(){
		$this->load->helper('url');

		$slug = url_title($this->input->post('title'), 'dash', TRUE); //url_titleはURLヘルパーの関数。文字列を解析してスペースをハイフンに置換、全文字を小文字にする。

		$data = array(
				'title' => $this->input->post('title'), //post()はinputライブラリ(デフォルトで読み込まれる)のメソッド。サニタイズ。
				'slug' => $slug,
				'text' => $this->input->post('text')
		);

		return $this->db->insert('news', $data);
	}
}