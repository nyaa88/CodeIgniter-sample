<?php
//	print_r ($this->session->all_userdata());


// echo '<pre>';
// var_dump($purchase);
// echo '</pre>';
	?>
<h3>購入履歴</h3>
<div class="cart purchase_history">
<?php
if(!empty($purchase)):
foreach($purchase as $id => $pur_arr): //purchase_idの数繰り返す
$calc = "";
?>
<h4><?php echo $pur_arr[0]['date']?></h4>
	<div class="purchase_left">

		<table>
			<tr>
				<th>商品名</th>
				<th>単価</th>
				<th>個数</th>
				<th>金額</th>
			</tr>
			<?php
			foreach ($pur_arr as $key =>$value): //同じpurchase_idの数繰り返す
			?>
				<tr>
					<td><?php echo $value['name']?></td>
					<td><?php echo $value['pro_price']?></td>
					<td><?php echo $value['num']?></td>
					<td><?php
					$calc[$key] = $value['pro_price'] * $value['num'];
					echo $calc[$key];
					//echo $pur_arr['pur_price'];
					?></td>
				</tr>
			<?php endforeach;?>
			<tr id="sum">
				<td colspan="3">合計</td>
				<td><?php
				$sum = "";
				foreach ($calc as $value){
					$sum = $sum + $value;
				}
				echo $sum;
				?>円</td>
			</tr>
		</table>
	</div>
	<div class="purchase_right">
		<h4>請求先</h4>
		<dl>
			<dt>氏名</dt>
			<dd><?php echo $member['family_name']." ".$member['first_name']?></dd>
			<dt>住所</dt>
			<dd><?php echo $member['name']." ".$member['address']?></dd>
		</dl>
	</div>
<?php
endforeach;
?><?php
else:?>
	<div class="purchase_left">
		<h4>購入履歴がありません</h4>
	</div>

<?php
endif;
?>

</div>
