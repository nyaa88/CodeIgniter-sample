<?php
class Practice_model extends CI_Model{
	public function __construct(){
		$this->load->database();
	}

	public function get_login(){ //ヘッダーでログイン状況に応じた表示
		if($this->session->userdata("is_logged_in")){
			return array('logout','ログアウト');//ログインしてたらログアウトへのリンク
		}else{
			return array('login','ログイン');
		}
	}


	public function get_product($id = FALSE){ //商品
		if($id === FALSE){ //$idがない = 全商品
			$query = $this->db->get('product'); //productテーブル全部
			return $query->result_array();
		}

		$this->db->select('product.*,maker.name makername'); // nameがproductとmakerでかぶるのでメーカーの方は別名をつける[makername]で表示
		$this->db->from('product');
		$cond = 'product.maker_id = maker.id';
		$this->db->join('maker', $cond);
		$this->db->where_in('product.id', $id);

		$query = $this->db->get();

		return $query->row_array();
	}

	public function get_maker(){ //indexで表示するメーカー一覧
		$query = $this->db->get('maker');
		return $query->result_array();
	}

	public function get_maker2($id = FALSE){ //「practice/maker/$id」で表示するメーカー別の商品一覧
		$this->db->select('maker.id mak_id,maker.name mak_name,product.id,product.name,product.price,product.internal_capacity');
		$this->db->from('maker');
		$cond = 'maker.id = product.maker_id';
		$this->db->join('product',$cond);
		$this->db->where_in('maker.id',$id);

		$query = $this->db->get();
		$product = $query->result_array();
		return $product;
	}

	public function can_log_in(){	//ログインフォーム入力後の処理
		$this->db->where("mail_address", $this->input->post("email"));	//POSTされたemailデータとDB情報を照合する
		$this->db->where("password", md5($this->input->post("password")));	//POSTされたパスワードデータとDB情報を照合する
		$query = $this->db->get("member");

		if($query->num_rows() == 1){	//ユーザーが存在した場合の処理
			return true;
		}else{					//ユーザーが存在しなかった場合の処理
			return false;
		}
	}

	public function cart_product($id_arr){ //DBからカートの商品情報を取得
		$this->db->select('id,name,price');
		$this->db->from('product');
			$id = "";
			foreach ($id_arr as $key => $value){ // [id]=個数の配列から、whereで使えるように[0]=>id [1]=>idの形にする
				$id[] .= $key;
			}
		$this->db->where_in('id',($id));
		$query = $this->db->get();
		$product =  $query->result_array(); //[0]=>array{[id]=>"" [name]=>"" [price]=>""}[1]=>{～}の形

		foreach ($product as $value){ //取得したデータを使いやすいように加工→controllersで$data['cart'][id][データ]
			$id = $value['id'];
			$product[$id] = $value;
		}

		if(isset($product[0])){ //加工しても[0]が残ってしまうので削除
			unset($product[0]);
		}
		return $product;
	}

	public function get_cart($post_data){ //カートに商品を追加変更等の処理
	 	$olddata = $this->session->all_userdata(); //セッション読み込み

		if (!isset($olddata['cart'])){ //カートが空なら
			$olddata['cart'][] = '';
		}

		if (isset($post_data['cart_submit'])){ //商品ページからの追加
			if(isset($olddata['cart'][$post_data['pro_id']])){ // 追加する商品がすでにカートにあれば数を足す
				$new_num = $olddata['cart'][$post_data['pro_id']] + $post_data['num'];
			}else{ // 初めて追加する商品なら
				$new_num = $post_data['num'];
			}

			$newdata = array(
					'cart' => array(
							$post_data['pro_id'] => $new_num)); //[商品id] => '個数'
			$re_data['cart'] = array_replace_recursive($olddata['cart'], $newdata['cart']); //セッションの前のデータに新しいデータを上書き

		}else{ //商品追加以外でのカートの表示
			if (!isset($post_data['pro_id'])){ //変更ボタンが押されてなければ
				$re_data['cart'] = $olddata['cart']; // セッションはそのまま
			}else{ //数の変更なら
				// id でセッション検索、削除
				$re_data['cart'] = $olddata['cart'];
				if ($post_data['num'] == 0){ // 個数が0なら項目を削除
					if(isset($re_data['cart'][$post_data['pro_id']])){ //既に削除済みじゃなければ
						unset($re_data['cart'][$post_data['pro_id']]);
					}
				}else{ //個数が1以上なら上書き
					$re_data['cart'][$post_data['pro_id']] = $post_data['num'];
				}
			}
		}

		if(isset($re_data['cart'][0])){
			unset($re_data['cart'][0]);
		}

 			return  $re_data;
	}

	public function get_address($email){ //ログインしている会員の住所を取得
		$this->db->select('id,address,prefecture_id,family_name,first_name');
		$this->db->where('mail_address', $email);	//メールアドレスとDB情報を照合する
		$query = $this->db->get("member");

		if($query->num_rows() == 1){	//ユーザーが存在した場合の処理
			//var_dump($query->result_array());
			$address = $query->row_array();

			$this->db->select('name');
			$this->db->where('id', $address['prefecture_id']);	//都道府県を取得
			$query = $this->db->get("prefecture");

			$address =array_merge($address,$query->row_array());

			return $address;
		}else{					//ユーザーが存在しなかった場合の処理
			return false;
		}
	}
// 	public function get_member($email){ //ログインしている会員の名前とid
// 		$this->db->select('id,family_name,first_name');
// 		$this->db->where('mail_address', $email);	//メールアドレスとDB情報を照合する
// 		$query = $this->db->get("member");
// 		if($query->num_rows() == 1){
// 			return $query->row_array();
// 		}
// 	}

	public function get_purchase($member){
		//var_dump($member);

		$this->db->select('purchase.price pur_price,date,purchase_id,purchase.num,product.name,product.price pro_price');
		$this->db->from('purchase');

		$cond = 'purchase.product_id = product.id';
		$this->db->join('product',$cond);
		$this->db->where('member_id', $member['id']);	//メールアドレスとDB情報を照合する
		$this->db->order_by('date', 'DESC');

		$query = $this->db->get();

		if($query->result_array()){ //purchase_idで配列を作り直す

			$purchase = $query->result_array();
			foreach ($purchase as $key => $value){
				$pur_re_arr[$value['purchase_id']][] = $value;
			}
			return $pur_re_arr;
		}else{
			return "0";////////////////////////////
		}
	}
	public function get_prefecture(){
		$this->db->from('prefecture');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function signup(){

	}
	public function add_temp_users($key){

		//add_temp_usersのモデルの実行時に、以下のデータを取得して、$dataと紐づける
		$data=array(
				"mail_address"=>$this->input->post("email"),
				"password"=>md5($this->input->post("password")),
				'family_name'=>$this->input->post('family_name'),
				'first_name'=>$this->input->post('first_name'),
				'gender'=>$this->input->post('gender'),
				'birthday'=>$this->input->post('birthday'),
				'prefecture_id'=>$this->input->post('prefecture_id'),
				'address'=>$this->input->post('address'),
				"key"=>$key
		);

		//$dataをDB内のtemp_memberに挿入したあとに、$queryと紐づける
		$query=$this->db->insert('temp_member', $data);

		if($query){		//データ取得が成功したらTrue、失敗したらFalseを返す
			return true;
		}else{
			return false;
		}
	}
	public function is_valid_key($key){
		$this->db->where("key", $key);	// $keyと等しいレコードを指定
		$query = $this->db->get('temp_member');		//temp_memberテーブルから情報を取得

		if($query->num_rows() == 1){
			return true;
		}else return false;
	}

	public function add_users($key){

		//keyのテーブルを選択
		$this->db->where("key", $key);

		//memberテーブルからすべての値を取得
		$temp_user = $this->db->get('temp_member');

		if($temp_user){
			$row = $temp_user->row();
			//$rowでは、temp_usersの行を返します。
			//しかし、このままでは1行目のみが返されるので、さらに以下を追記する。

			$data = array(	//$rowで取得した値のうち、必要な情報のみを取得する
					"mail_address"=>$row->mail_address,
					"password"=>$row->password,
					'family_name'=>$row->family_name,
					'first_name'=>$row->first_name,
					'gender'=>$row->gender,
					'birthday'=>$row->birthday,
					'prefecture_id'=>$row->prefecture_id,
					'address'=>$row->address
			);

			$did_add_user = $this->db->insert("member", $data);

			if($did_add_user){		//did_add_userが成功したら以下を実行
				$this->db->where("key", $key);
				$this->db->delete("temp_member");
				//return true;
				return $data["mail_address"];
			}return false;
		}
	}
	public function add_order($sesdata){
		//member_idを取得
		$this->db->select('id');
		$this->db->from('member');
		$this->db->where("mail_address", $sesdata['email']);
		$query = $this->db->get();
		$id = $query->row_array();
		//var_dump($id); //array{[id]=>"6"
		//echo "<br>".$id['id']."<br>";

		//priceを取得するためのproduct_id
		$product_id = "";
		foreach ($sesdata['cart'] as $key => $value){
			$product_id[] .= $key;
		}
// 		echo "product_id<br>";
// 		var_dump($product_id);

		$this->db->select('id,price');
		$this->db->from('product');
		$this->db->where_in('id',$product_id);
		$query = $this->db->get();

		$price = $query->result_array();

		foreach ($price as $value){ //id => price に配列作り直し
			$price_arr[$value['id']] = $value['price'];
		}
// 		echo "price_arr<br>";
// 		var_dump($price_arr);

		//SELECT `purchase_id` FROM `purchase`	order by `purchase_id` desc limit 1
		$this->db->select('purchase_id');
		$this->db->order_by('purchase_id', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get('purchase');
		$last_pur_id = $query->row_array(); //["purchase_id"]=>string(2) "60"
		$new_pur_id = $last_pur_id['purchase_id'] + 1 ;
		//var_dump($last_pur_id);

		foreach ($sesdata['cart'] as $pro_id => $num){
			$order = array(
				'member_id' =>$id['id'],
				'product_id' => $pro_id,
				'price' => $price_arr[$pro_id],
				//'date' => '',
				'purchase_id' => $new_pur_id,
				'num' => $num
			);

			//var_dump($order);
			$sql = $this->db->insert('purchase', $order);
			if($sql){		//did_add_userが成功したら以下を実行
				//return true;
			}else{
				return false;
			}
		}

		return true;
	}

	public function add_product($post_data){

			$data = array(
					'maker_id'=>$post_data['maker'],
					'name'=>$post_data['name'],
					'type_name'=>$post_data['type_name'],
					'internal_capacity'=>$post_data['internal_capacity'],
					'preservation_method'=>$post_data['preservation_method'],
					'calorie'=>$post_data['calorie'],
					'price'=>$post_data['price']
			);

			$did_add_product = $this->db->insert('product', $data);

			if ($did_add_product){
				return true;
			}else{
				return false;
			}

	}

	public function add_maker($post_data){

		$data = array(
				'name'=>$post_data['name'],
				'prefecture_id'=>$post_data['prefecture']

		);

		$did_add_maker = $this->db->insert('maker', $data);

		if ($did_add_maker){
			return true;
		}else{
			return false;
		}

	}


}