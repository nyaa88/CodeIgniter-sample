<?php
	echo validation_errors();
?>


<?php foreach ($product as $product_item): //ひとつの商品ごとにループ?>
	<div class="product_item">
		<h3>
		<?php
			if(mb_strlen($product_item['name']) < 14){
				echo $product_item['name'];
			}else{
				$keyword = mb_convert_kana($product_item['name'], 's'); //全角スペースを半角スペースに
				$ary_keyword = preg_split('/[\s]+/', $keyword, -1, PREG_SPLIT_NO_EMPTY);
				$valstr = 0;
				foreach( $ary_keyword as $val ){
					$valstr += mb_strlen($val);
					if($valstr < 14){
						echo $val." ";
					}else{
						echo $val."<br>";
						$valstr = 0;
					}
				}
			}
		?>
		</h3>
		<?php
			$hidden['pro_id'] = $product_item['id'];
			echo form_open('practice/cart',NULL,$hidden); //POST先のメソッド
		?>
		<p><?php echo $product_item['internal_capacity']?></p>
		<p><?php echo $product_item['price']?>円</p>

		<?php
			for ($i = 0; $i <= 20; $i++) {
				$options[$i] = $i;
			}
			echo form_dropdown('num',$options,1); //selectedが1
			//echo form_input("num", '1');	// value='1'
			echo form_submit("cart_submit", 'カートに追加');
			echo form_close();
		?>

		<a href="<?php echo site_url('practice/product/'.$product_item['id']); ?>" class="button">商品詳細へ</a>
	</div>

<?php endforeach;?>

