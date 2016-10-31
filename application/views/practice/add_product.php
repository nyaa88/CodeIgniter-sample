<?php
	echo validation_errors();

	echo "<pre>";
	//var_dump($maker);
	echo "</pre>";
?>
<?php
echo form_open('practice/add_product');
?>
<dl>
	<dt>メーカー</dt>
		<dd>
		<?php
		foreach ($maker as $value){
			$options[$value['id']] = $value['name'];
		}
		//var_dump($options);
		echo form_dropdown('maker',$options,$this->input->post('maker'));

		?>
		</dd>
	<dt>商品名<span class="required"><必須></span></dt>
		<dd><?php echo form_input('name',$this->input->post('name'));?></dd>
	<dt>種類別名称<span class="required"><必須></span></dt>
		<dd><?php echo form_input('type_name',$this->input->post('type_name'));?></dd>
	<dt>原材料名</dt>
		<dd><?php echo form_input('ingredients',$this->input->post('ingredients'));?></dd>
	<dt>内容量<span class="required"><必須></span></dt>
		<dd><?php echo form_input('internal_capacity',$this->input->post('internal_capacity'));?></dd>
	<dt>保存方法</dt>
		<dd><?php echo form_input('preservation_method',$this->input->post('preservation_method'));?></dd>
	<dt>カロリー</dt>
		<dd><?php echo form_input('calorie',$this->input->post('calorie'));?></dd>
	<dt>単価<span class="required"><必須></span></dt>
		<dd><?php echo form_input('price',$this->input->post('price'));?></dd>
</dl>

<?php echo form_submit("add_product_submit", '商品を追加');?>

<?php echo form_close();?>