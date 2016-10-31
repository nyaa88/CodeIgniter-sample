<?php
	echo validation_errors();
?>
<div  id="view">
<table>
	<tr>
		<td>商品ID</td>
		<td><?php echo $product_item['id']?></td>
	</tr>
	<tr>
		<td>メーカー</td>
		<td><?php echo $product_item['makername']?></td>
	</tr>
	<tr>
		<td>種類別名称</td>
		<td><?php echo $product_item['type_name']?></td>
	</tr>
	<tr>
		<td>原材料</td>
		<td><?php echo $product_item['ingredients']?></td>
	</tr>
	<tr>
		<td>内容量</td>
		<td><?php echo $product_item['internal_capacity']?></td>
	</tr>
	<tr>
		<td>保存方法</td>
		<td><?php echo $product_item['preservation_method']?></td>
	</tr>
	<tr>
		<td>カロリー</td>
		<td><?php
		if($product_item['calorie'] == 0){
			echo "--";
		}else{
			echo $product_item['calorie'];
		}
		?>kcal</td>
	</tr>
	<tr>
		<td>値段</td>
		<td>\<?php echo $product_item['price']?></td>
	</tr>
</table>

	<div class="product_item">
		<div>
		<p>\<?php echo $product_item['price']?></p>
			<?php
			$hidden['pro_id'] = $product_item['id'];
			echo form_open('practice/cart',NULL,$hidden); //POST先のメソッド

			for ($i = 0; $i <= 20; $i++) {
				$options[$i] = $i;
			}
			echo form_dropdown('num',$options,1);
			//echo form_input("num", '1');	// value='1'
			echo form_submit("cart_submit", 'カートに追加');
			echo form_close();
			?>
		</div>
	</div>
	<a href="<?php echo site_url('practice/product'); ?>" class="button">商品一覧</a>
</div>