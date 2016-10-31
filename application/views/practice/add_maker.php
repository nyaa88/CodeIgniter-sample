<?php
	echo validation_errors();

	echo "<pre>";
	//var_dump($prefecture);
	echo "</pre>";
?>
<?php
if(!empty($result)){
echo "<h3>$result</h3>";
}
echo form_open('practice/add_maker');
?>
<dl>
	<dt>メーカー名<span class="required"><必須></span></dt>
		<dd><?php echo form_input('name',$this->input->post('name'));?></dd>
	<dt>都道府県</dt>
		<dd><?php
		foreach ($prefecture as $value){
			$options[$value['id']] = $value['name'];
		}
		//var_dump($options);
		echo form_dropdown('prefecture',$options,$this->input->post('prefecture'));
		//echo form_input('prefecture_id',$this->input->post('prefecture_id'));

		?></dd>
</dl>

<?php echo form_submit("add_maker_submit", 'メーカーを追加');?>

<?php echo form_close();?>