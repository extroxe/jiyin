<link rel="stylesheet" href="<?=site_url('source/assets/seedsui/scripts/lib/seedsui/seedsui.min.css')?>" media="screen">
<link rel="stylesheet" href="<?=site_url('source/mobile/css/dropload.css')?>" media="screen">
<link rel="stylesheet" href="<?=site_url('source/mobile/css/common.css')?>" media="screen">
<link rel="stylesheet" href="<?=site_url('source/mobile/js/pdf/jquery.touchPDF.css')?>" media="screen">
<link rel="stylesheet" href="<?php echo base_url(); ?>/source/mobile/css/common.css" />
<style>
body {
	background-color: #F9F9F9;
}

[ng-cloak], .ng-cloak {
	display: none;
}
</style>
<?php if(isset($css) && !empty($css)):?>
	<?php foreach($css as $row):?>
		<link rel="stylesheet" href="<?php echo base_url(); ?>/source/mobile/css/<?php echo $row; ?>.css"/>
	<?php endforeach; ?>
<?php endif; ?>

<script>
	var SITE_URL = "<?php echo site_url();?>";
</script>
</head>
<body ng-app="app" ng-cloak class="ng-cloak">
<?php if (isset($is_search) && $is_search): ?>
<!--    搜索框-->
<header>
	<div class="titlebar search">
		<!--<i class="icon icon-menudot" style="color: #FFFFFF"></i>-->
		<form action="<?php echo site_url('weixin/index/search_result'); ?>" method="GET" class="inputbox lrmargin8 radius40 bordered" data-input="clear">
			<img style="width: 24px;margin-left: 5px;" src="<?php echo site_url('source/mobile/img/icon/search.png'); ?>">
			<input type="search" id="search_box" placeholder="请输入搜索内容" ng-model="content" class="search input-text" name="key_words"/>
			<i class="icon icon-clear-fill color-placeholder" id="empty_content" ></i>
		</form>
	</div>
</header>
<?php endif; ?>

