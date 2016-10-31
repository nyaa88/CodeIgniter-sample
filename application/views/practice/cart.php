
<?php
$ses = $this->session->all_userdata();
$sum = "";
// 	echo "<pre>";
// 	print_r ($this->session->all_userdata());
// 	var_dump($product);
// 	var_dump($ses);
// 	var_dump($olddata);
// 	echo "</pre>";

if(isset($error_message)){
	echo $error_message;
}
	?>

<div class="cart">
<?php if(!empty($product)):?>
<table>
	<tr>
		<th>商品名</th>
		<th>単価</th>
		<th>個数</th>
		<th>金額</th>
	</tr>
	<?php
	foreach ($ses['cart'] as $num => $value):
	$hidden['pro_id'] = $product[$num]['id'];
	echo form_open('practice/cart',NULL,$hidden);?>

	<tr>
		<td><?php echo $product[$num]['name']?> (id:<?php echo $product[$num]['id']?>)</td>
		<td><?php echo $product[$num]['price']?>円</td>
		<td><?php
				for ($i = 0; $i <= 20; $i++) {
					$options[$i] = $i;
				}
				echo form_dropdown('num',$options,$ses['cart'][$num]); //numがselected
				echo form_submit('change', '変更');?>
			<?php
				if(empty($olddata['cart'][$num])){//前のデータで項目がなかったら0にする
					$olddata['cart'][$num] = '0';
				}
				if(!($olddata['cart'][$num] == $ses['cart'][$num])){ //前のデータと比較して数の推移を表示
				echo "<p>".$olddata['cart'][$num]." → ".$ses['cart'][$num]."</p>";
				} ?>
		</td>
		<td><?php
			$calc = $product[$num]['price'] * $ses['cart'][$num];
			echo $calc;
		?>円</td>
	</tr>
	<?php
	echo form_close();
	$sum += $calc;
	endforeach;

	?>
	<tr id="sum">
		<td colspan="3">合計</td>
		<td><?php echo $sum;?>円</td>
	</tr>
</table>

<?php
else :
	echo "<h3>カートは空です。</h3>";
endif;?>




</div>
<div class="center">
	<a href="<?php echo $back ?>" class="back button"><?php echo $back_message?></a>
	<?php
	if(!empty($product)){
		echo '<a href="'.site_url('practice/purchase').'" class="back button">レジへ進む</a>';
	}else{
		echo '<a href="" class="back button" disabled>レジへ進む</a>';
	}
	?>
</div>