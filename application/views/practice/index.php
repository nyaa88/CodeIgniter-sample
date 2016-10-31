

<h3>商品一覧</h3>
<a href="<?php echo site_url('practice/product'); ?>">商品一覧</a>


<h3>メーカー検索</h3>
<ul>
<?php foreach ($maker as $maker_item):?>
<li>
<a href="<?php echo site_url('practice/maker/'.$maker_item['id']); ?>"><?php echo $maker_item['name'];?></a>
</li>
<?php endforeach;?>
</ul>

<?php if($login[0] == 'login'): //ログインしていなければ?>
	<h3>ログイン</h3>
	<a href="<?php echo site_url('practice/login'); ?>">ログイン画面へ</a>
<?php else: //ログイン中なら?>
	<h3>メンバーページ</h3>
	<a href="<?php echo site_url('practice/members'); ?>">メンバーページへ</a>
<?php endif;?>

<h3>商品追加</h3>
<a href="<?php echo site_url('practice/add_product'); ?>">追加画面へ</a>


<h3>メーカー追加</h3>
<a href="<?php echo site_url('practice/add_maker'); ?>">追加画面へ</a>
