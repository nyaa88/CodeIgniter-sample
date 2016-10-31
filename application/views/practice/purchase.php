

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

<div class="cart purchase_left">
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
			<td><?php echo $ses['cart'][$num]?></td>
			<td><?php
				$calc = $product[$num]['price'] * $ses['cart'][$num];
				echo $calc; ?>円</td>
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
<?php if(!empty($member)):?>
	<div class="purchase_right">
		<h4>送付先</h4>
		<dl>
			<dt>氏名</dt>
			<dd><?php echo $member['family_name']." ".$member['first_name']?></dd>
			<dt>住所</dt>
			<dd><?php echo $member['name']." ".$member['address']?></dd>
		</dl>
	</div>
<?php	endif; ?>
<div class="center">
	<a href="<?php echo site_url('practice/cart') ?>" class="back button">カートへ戻る</a>
	<a href="<?php echo site_url('practice/order') ?>" class="back button">注文を決定する</a>
</div>