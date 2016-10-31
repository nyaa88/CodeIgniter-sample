<?php
class Practice extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('practice_model');
		$this->load->helper('url_helper');
	}

	public function index(){
		$data['maker'] = $this->practice_model->get_maker();
		$data['login'] = $this->practice_model->get_login();
		$data['title'] = 'index';

		$this->load->view('templates/header',$data);
		$this->load->view('practice/index', $data);
		$this->load->view('templates/footer',$data);

	}

	public function product(){
		$data['product'] = $this->practice_model->get_product();
		$data['title'] = '商品一覧';
		$data['login'] = $this->practice_model->get_login();

		$this->load->library('form_validation');

		$this->load->view('templates/header',$data);
		$this->load->view('practice/product', $data);
		$this->load->view('templates/footer',$data);
	}

	public function view($id = NULL){ //商品詳細
		$data['product_item'] = $this->practice_model->get_product($id);
		$data['login'] = $this->practice_model->get_login();

		if (empty($data['product_item'])){

			show_404();
		}

		$this->load->library('form_validation');

		$data['title'] = $data['product_item']['name'];

		$this->load->view('templates/header', $data);
		$this->load->view('practice/view',$data);
		$this->load->view('templates/footer');
	}

	public function maker($id = NULL){ //メーカー検索
		$data['product'] = $this->practice_model->get_maker2($id);
		$data['login'] = $this->practice_model->get_login();

		if (empty($data['product'])){
			show_404();
		}

		$this->load->library('form_validation');

		$data['title'] = $data['product'][0]['mak_name'];

		$this->load->view('templates/header', $data);
		$this->load->view('practice/product',$data);
		$this->load->view('templates/footer');
	}

	/* ----------------------ログイン----------------------- */

	public function login(){ //ログインフォーム
		$this->load->library('form_validation'); //form validationライブラリをロード
		$this->load->helper('security'); //これも読み込まないとxss_cleanが使えないっぽい？

		$this->form_validation->set_rules("email", "メール", "required|trim|xss_clean|callback_validate_credentials"); // validate_credentialsというメソッドを呼び出します。
		$this->form_validation->set_rules("password", "パスワード", "required|md5|trim");//set_rules(入力フィールドの名前,エラーメッセージに使用される名前、実際のルール(required = 必須))

 		if ($this->form_validation->run()){ //バリデーションエラーの有無
 			$data = array(
 					"email" => $this->input->post("email"),
 					"is_logged_in" => 1
 			);
 			$this->session->set_userdata($data); //セッションにデータを保存

 			$back = $this->session->all_userdata();

 			redirect($back['back_url']);

 		}else{ //最初に開いたとき、エラー有り
 			$data['title'] = 'ログイン';
 			$data['login'] = $this->practice_model->get_login();
 			if(empty($_SERVER['HTTP_REFERER'])){//直接アクセス
 				$back['back_url'] =  base_url('practice/');
 			}else{
 				$url = $this->session->all_userdata();
 				if(empty($url['back_url'])){ //ログアウト直後の場合
 					$url['back_url'] = $_SERVER['HTTP_REFERER'];
 				}
 				if($url['back_url'] ==  'practice/purchase'){
 					$back['back_url'] = $url['back_url'];
 				}else{
 					$back['back_url'] =  $_SERVER['HTTP_REFERER']; //ログインする前にいたページ
 				}
 			}

 			$back['back_url'] = str_replace(base_url(),'',$back['back_url']);

 			$this->session->set_userdata($back);//セッションに保存

 			$this->load->view('templates/header',$data);
 			$this->load->view('practice/login',$data);
 			$this->load->view('templates/footer');
 		}
 		//テスト用
 		//tamaki.nara97@gmail.com
 		//pass
	}
	public function validate_credentials(){ //Email情報がPOSTされたときに呼び出されるコールバック機能
		$this->load->model("practice_model");

		if($this->practice_model->can_log_in()){	//ユーザーがログインできたあとに実行する処理
			return true;
		}else{					//ユーザーがログインできなかったときに実行する処理
			$this->form_validation->set_message("validate_credentials", "ユーザー名かパスワードが異なります。");
			return false;
		}
	}
	public function members(){
		if($this->session->userdata("is_logged_in")){	//ログインしている場合の処理
			$data['login'] = $this->practice_model->get_login();

			$ses_data = $this->session->all_userdata();
			$data['member'] = $this->practice_model->get_address($ses_data['email']); //get_member→addressに。請求先をpurchaseテーブルに含むときはメンバーのidと名前だけでいいのでget_memberにする？
			$data['purchase'] = $this->practice_model->get_purchase($data['member']);

			$data['title'] = $data['member']['family_name'].$data['member']['first_name'].'さんのメンバーページ';

			$this->load->view('templates/header',$data);
			$this->load->view('practice/members',$data);
			$this->load->view('templates/footer');
		}else{									//ログインしていない場合の処理
			redirect ('practice/restricted');
		}

	}

	public function restricted(){
		$data['title'] = 'ログインしてください';
		$data['login'] = $this->practice_model->get_login();

		$this->load->view('templates/header',$data);
		$this->load->view('practice/restricted');
		$this->load->view('templates/footer');
	}

	public function logout(){
		$this->session->sess_destroy();		//セッションデータの削除

		redirect ('practice/login');		//ログインページにリダイレクトする
	}

	/* ----------------------ログインここまで----------------------- */

	public function cart(){
		$this->load->library('form_validation'); //form validationライブラリをロード
		$this->load->helper('security'); //これも読み込まないとxss_cleanが使えないっぽい？

		$this->form_validation->set_rules("num", "個数", "required|trim|xss_clean|numeric"); // validate_credentialsというメソッドを呼び出します。

		$olddata = $this->session->all_userdata();
		$data['olddata'] = $olddata; //前のデータ比較用

		if(empty($_SERVER['HTTP_REFERER'])){//直接カートへアクセス
			$data['back'] =  base_url('practice/');
			$data['back_message'] = "トップページ";
		}else{//どこかのページからのリンクでカートへアクセス
			if($_SERVER['HTTP_REFERER'] !== base_url('practice/cart')){ //カートをリロード・数の変更の場合以外は上書き
				$data['back'] =  $_SERVER['HTTP_REFERER'];
				$olddata['back_url'] = $data['back'];
			}else{ //カートの再表示
				//echo $olddata['back_url'];
				$data['back'] = $olddata['back_url'];
			}
			$data['back_message'] = "元のページへ戻る";
		}

		// cartの追加変更
	 		$post_data = $this->input->post();
			$re_data = $this->practice_model->get_cart($post_data);

		// cartに入ってる商品の詳細をDBから取得
			$data['product'] = $this->practice_model->cart_product($re_data['cart']);

		unset($olddata['cart']); //前のカートデータを削除(0になった商品があると消してからじゃないとだめ)
		$ses_data = array_replace_recursive($olddata,$re_data); //ログインしたメンバー情報を含めたセッションデータと新しいカートのデータの結合

		if (!$this->form_validation->run()){ //バリデーションエラーの有無
			//echo "エラーーーーーーーー";
			$this->form_validation->set_message('num', '%s は半角数字で入力してください。'); //????????????????表示されない
			$data['error_message'] = validation_errors();
			$this->session->set_userdata($olddata);
		}else{
		//上書きしたデータをセッションに保存
 			$this->session->set_userdata($ses_data);
		}

		$data['title'] = 'カート';
		$data['login'] = $this->practice_model->get_login();

		$this->load->view('templates/header',$data);
		$this->load->view('practice/cart',$data);
		$this->load->view('templates/footer');

// 		if(!isset($re_data['cart'][0])){
// 		echo "<br>".'$data[product]';
// 		var_dump($data['product']);
// 		}
// 		echo "<br>"."re_data";
// 		var_dump($re_data);
// 		echo "<br>"."new ses_data";
// 		var_dump($ses_data);
	}

	public function purchase(){ //購入確認ページ
		$this->load->library('form_validation'); //form validationライブラリをロード

		$back['back_url'] =  'practice/purchase';
		$this->session->set_userdata($back);

		$ses_data = $this->session->all_userdata();
		if(empty($ses_data['is_logged_in'])){
			redirect ('practice/login');
		}

		if(!empty($ses_data['cart'])){
		$data['product'] = $this->practice_model->cart_product($ses_data['cart']);
		$data['member'] = $this->practice_model->get_address($ses_data['email']);
		}

		$data['title'] = '購入';
		$data['login'] = $this->practice_model->get_login();


		//var_dump($this->session->all_userdata());



		$this->load->view('templates/header',$data);
		$this->load->view('practice/purchase',$data);
		$this->load->view('templates/footer');
	}

	public function order(){ //購入処理
		$sesdata = $this->session->all_userdata();
		if(empty($sesdata['is_logged_in'])){
			redirect ('practice/login');
		}

		//Emailライブラリを読み込む。メールタイプをHTMLに設定（デフォルトはテキストです）
		$this->load->library("email", array("mailtype"=>"html"));

		//送信元の情報
		$this->email->from("tamaki.nara97@gmail.com", "送信元");

		//送信先の設定
		$this->email->to($sesdata['email']);

		//タイトルの設定
		$this->email->subject("ご注文を受け付けました");

		//メッセージの本文
		$message = "ご注文ありがとうございます。";

		$message .= "";

		$this->email->message($message);
		if($this->practice_model->add_order($sesdata)){
			if($this->email->send()){
				$this->session->unset_userdata('cart');
				$data['title'] = 'ご注文ありがとうございます。メールを送信しました。';
				//echo "メールを送信しました。";//Message has been sent.
			}else{
				$data['title'] = 'メールを送信できませんでした。';
				//echo "メールを送信できませんでした。"; //Coulsn't send the message.
			}
		}else{
			$data['title'] = 'データベースに追加できませんでした。';
		}


		$data['login'] = $this->practice_model->get_login();

		$this->load->view('templates/header',$data);
		$this->load->view('practice/order',$data);
		$this->load->view('templates/footer');
	}




	public function signup(){ //会員登録
		$this->load->library('form_validation'); //form validationライブラリをロード


		$this->form_validation->set_rules('email','メールアドレス','required|trim|valid_email|is_unique[member.mail_address]');
		$this->form_validation->set_rules('password','パスワード','required|trim');
		$this->form_validation->set_rules('cpassword','パスワードの確認','required|trim|matches[password]');

		$this->form_validation->set_error_delimiters('<div class="err">', '</div>');
		$err_msg = array(
				'required' => '%sを入力してください。',
				'valid_email' => '正しい%sを入力してください',
				'is_unique' => '入力した%sはすでに登録されています。',
				'matches' => 'パスワードが不一致です。'
		);


		$this->form_validation->set_message($err_msg);

		if($this->form_validation->run()){
			//ランダムキーを生成する
			$key=md5(uniqid());

			//Emailライブラリを読み込む。メールタイプをHTMLに設定（デフォルトはテキストです）
			$this->load->library("email", array("mailtype"=>"html"));

			//送信元の情報
			$this->email->from("tamaki.nara97@gmail.com", "送信元");

			//送信先の設定
			$this->email->to($this->input->post("email"));

			//タイトルの設定
			$this->email->subject("仮登録が完了しました。");

			//メッセージの本文
			$message = "会員登録ありがとうございます。";

			// 各ユーザーにランダムキーをパーマリンクに含むURLを送信する
			$message .= "\n( http://nya88.sub.jp/sample/practice/resister_user/".$key." )\n"; //捨てアドじゃないアドレスで確認する
			$message .= "こちらをクリックして、会員登録を完了してください。";

			$this->email->message($message);

			//ユーザーに確認メールを送信できた場合、ユーザーを temp_member DBに追加する
			if($this->practice_model->add_temp_users($key)){
				if($this->email->send()){
					$data['title'] = 'メールを送信しました。';
					//echo "メールを送信しました。";//Message has been sent.
				}else{
					$data['title'] = 'メールを送信できませんでした。';
					//echo "メールを送信できませんでした。"; //Coulsn't send the message.
				}
			}else{
				$data['title'] = 'データベースに追加できませんでした。';

				//echo "データベースに追加できませんでした。"; //problem adding to database
			}
			$data['login'] = $this->practice_model->get_login();
			$this->load->view('templates/header',$data);
			$this->load->view('templates/footer');

		}else{
			$data['prefecture'] = $this->practice_model->get_prefecture();

			$data['title'] = '会員登録';
			$data['login'] = $this->practice_model->get_login();

			$this->load->view('templates/header',$data);
			$this->load->view('practice/signup',$data);
			$this->load->view('templates/footer');
		}
	}
	public function resister_user($key){
		//echo $key;
		if($this->practice_model->is_valid_key($key)){	//キーが正しい場合は、以下を実行
			//echo "valid key";

			if($newemail = $this->practice_model->add_users($key)){	//add_usersがTrueを返したら以下を実行
				$data = array(
					"email" => $newemail,
					"is_logged_in" => 1
				);

			$this->session->set_userdata($data);
			redirect ('practice/members');

			}else{
				$data['title'] = 'ユーザーを追加することができません。再度お試しください。';
				$data['login'] = $this->practice_model->get_login();
				$this->load->view('templates/header',$data);
				$this->load->view('templates/footer');
				//echo "ユーザーを追加することができません。再度お試しください。"; //fail to add user. please try again
			}

		}else{
			$data['title'] = 'キーが無効です。';
			$data['login'] = $this->practice_model->get_login();
			$this->load->view('templates/header',$data);
			$this->load->view('templates/footer');
			//echo "キーが無効です。"; //invalid key
		}
	}
	public function add_product(){ //商品追加
		$this->load->library('form_validation'); //form validationライブラリをロード

		$data['maker'] = $this->practice_model->get_maker();

		$this->form_validation->set_rules('name','商品名','required|trim|is_unique[product.name]');
		$this->form_validation->set_rules('type_name','種類別名称','required|trim');
		$this->form_validation->set_rules('internal_capacity','内容量','required|trim');
		$this->form_validation->set_rules('price','価格','required|trim|is_natural');

		$this->form_validation->set_error_delimiters('<div class="err">', '</div>');
		$err_msg = array(
				'required' => '%sを入力してください。',
				'is_unique' => '入力した%sはすでに登録されています。',
				'integer' => '数字を入力してください'
		);

		$this->form_validation->set_message($err_msg);

		if($this->form_validation->run()){ //エラーがなければDBに登録
			$post_data = $this->input->post();
			if($this->practice_model->add_product($post_data)){ //DBに登録できたら結果ページへ
				redirect ('practice/add_product_result');
			}
		}else{
			$data['title'] = '商品追加';
			$data['login'] = $this->practice_model->get_login();
			$this->load->view('templates/header',$data);
			$this->load->view('practice/add_product',$data);
			$this->load->view('templates/footer');
		}
	}
	public function add_product_result(){
		$data['title'] = '商品追加結果';
		$data['login'] = $this->practice_model->get_login();
		$this->load->view('templates/header',$data);
		$this->load->view('practice/add_product_result',$data);
		$this->load->view('templates/footer');
	}

	public function add_maker(){
		$this->load->library('form_validation'); //form validationライブラリをロード

		$data['prefecture'] = $this->practice_model->get_prefecture();

		$this->form_validation->set_rules('name','メーカー名','required|trim|is_unique[maker.name]');
		$this->form_validation->set_rules('prefecture','都道府県','required|trim|integer');

		$err_msg = array(
				'required' => '%sを入力してください。',
				'is_unique' => '入力した%sはすでに登録されています。',
		);

		if($this->form_validation->run()){ //エラーがなければDBに登録
			$post_data = $this->input->post();
			if($this->practice_model->add_maker($post_data)){ //DBに登録できたら結果ページへ
				$data['result'] =  "メーカーを登録しました。";
			}
		}
		$data['title'] = 'メーカー追加';
		$data['login'] = $this->practice_model->get_login();
		$this->load->view('templates/header',$data);
		$this->load->view('practice/add_maker',$data);
		$this->load->view('templates/footer');

	}

}
