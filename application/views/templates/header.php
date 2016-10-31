<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>CodeIgniter Tutorial</title>
		<link type="text/css" rel="stylesheet" href="<?=base_url();?>css/practice.css" />

	</head>

<body>
<header>
	<h1><?php echo $title; ?></h1>
	<div class="menu">
		<a href="<?php echo base_url('practice/'); ?>">トップ</a>
		<a href="<?php echo base_url('practice/cart');?>">カート</a>
		<?php
			if($login[0] == 'logout'){
				echo "<a href=".base_url('practice/members').">購入履歴</a>";
			}
		?>
		<a href="<?php echo base_url('practice/') . $login[0] ?>"><?php echo $login[1] ;?></a>

	</div>

</header>
<div id="contents">
