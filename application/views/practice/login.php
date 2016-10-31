
<?php
echo form_open('practice/login'); //POST先のメソッド
echo validation_errors();
?>

<dl>
	<dt>メールアドレス</dt>
	<dd><?php echo form_input("email", $this->input->post("email"));	?></dd>
	<dt>パスワード</dt>
	<dd><?php echo form_password("password");	?></dd>
</dl>
<?php echo form_submit("login_submit", "Login");?>

<?php echo form_close();?>

<div class="center">
	<a href="<?php echo site_url('practice/') ?>" class="back button">トップへ戻る</a>
	<a href="<?php echo base_url() . 'practice/signup' ?>" class="back button">会員登録する</a>
</div>