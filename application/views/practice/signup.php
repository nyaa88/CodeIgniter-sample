<div id="signup">
<?php
echo form_open('practice/signup'); //POST先のメソッド
echo validation_errors();
?>

<dl>
	<dt>メールアドレス</dt>
	<dd><?php echo form_input("email", $this->input->post("email"));	?></dd>
	<dt>パスワード</dt>
	<dd><?php echo form_password("password");	?></dd>
	<dt>パスワード確認</dt>
	<dd><?php echo form_password("cpassword");	?></dd>
	<dt>氏名</dt>
	<dd><?php
		echo form_input("family_name",$this->input->post('family_name'));
		echo form_input("first_name",$this->input->post('first_name'));	?></dd>
	<dt>性別</dt>
	<dd><?php
		echo "<label>". form_radio("gender","0",TRUE,$this->input->post('gender')) . "男性</label>";
		echo "<label>". form_radio("gender","1",FALSE,$this->input->post('gender')) . "女性</label>";
	?></dd>
	<dt>生年月日</dt>
	<dd><?php if($this->input->post('birthday')){
				$birth = $this->input->post('birthday');
			}else{
				$birth = "1980-01-01";
		}?><input type="date" name="birthday" value="<?php echo $birth; ?>"></dd>
	<dt>住所</dt>
	<dd>都道府県<?php
	foreach ($prefecture as $value){
		$pre_array[$value['id']] = $value['name'];
	}
	//var_dump($pre_array);
	echo form_dropdown('prefecture_id',$pre_array, $this->input->post('prefecture_id'));
	echo form_input("address", $this->input->post('address'));
	?></dd>
</dl>
<?php echo form_submit("signup_submit", "会員登録");?>

<?php echo form_close();?>

</div>