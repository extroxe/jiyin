	<link rel="stylesheet" href="<?=site_url('source/assets/bootstrap-2.3.2/css/bootstrap.css')?>" media="screen">
	<link rel="stylesheet" href="<?=site_url('source/assets/bootstrap-2.3.2/css/bootstrap-responsive.min.css')?>" media="screen">
	<link rel="stylesheet" href="<?=site_url('source/assets/font-awesome/css/font-awesome.css')?>" media="screen">
	<link rel="stylesheet" href="<?=site_url('source/css/font-slider.css')?>" />
	<link rel="stylesheet" href="<?=site_url('source/css/header.css')?>" />
	<link rel="stylesheet" href="<?=site_url('source/css/pagination.css')?>" />
	<link rel="stylesheet" href="<?=site_url('source/css/dropload.css')?>" />
	<link rel="stylesheet" href="<?=site_url('source/css/sweetalert.css')?>" />
	<link rel="stylesheet" href="<?=site_url('source/kindeditor/themes/default/default.css')?>" />

	<?php if ((isset($simple_footer) && !$simple_footer) || !isset($simple_footer)) : ?>
	<link rel="stylesheet" href="<?=site_url('source/css/footer.css')?>" />
	<?php endif; ?>
	<link rel="stylesheet" href="<?=site_url('source/assets/sweetalert/sweetalert.css')?>" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>/source/css/common.css" />
	<script>
		var SITE_URL = "<?php echo site_url();?>";
	</script>

	<?php if(isset($css) && !empty($css)):?>
		<?php foreach($css as $row):?>
			<link rel="stylesheet" href="<?php echo base_url(); ?>/source/css/<?php echo $row; ?>.css"/>
		<?php endforeach; ?>
	<?php endif; ?>
</head>
<body>
<?php if (isset($sign_in_flag) && $sign_in_flag): ?>
<header style="padding: 10px 0; margin-bottom: 0px; box-shadow: 0px 0px 20px rgba(190, 190, 190, .5)">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span6" style="text-align: center">
				<a href="<?php echo site_url(); ?>"><img style="width: 251px" src="<?=site_url('source/img/u2.png'); ?>"></a>
				<span style="font-size: 20px; border-left: 1px solid #999; padding-left: 15px; margin-left: 10px">欢 迎 登 陆</span>
			</div>

		</div>
	</div>
</header>
<?php elseif (isset($sign_up_flag) && $sign_up_flag): ?>
<header>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span6" style="text-align: center">
				<a href="<?php echo site_url(); ?>"><img style="width: 251px" src="<?=site_url('source/img/u2.png'); ?>"></a>
				<span class="signup">欢 迎 注 册</span>
			</div>
			<div class="span6" style="text-align: center;height: 80px">
				<div style="margin-top: 40px; height: 30px">
					<span style="color: #888">已有账号 ? </span>
					<a href="<?php echo site_url('index/sign_in'); ?>" style="color: #117d94">请登录</a>
				</div>
			</div>
		</div>
	</div>
</header>
<?php else:?>
<!-- Top -->
<nav id="top" style="background-color: #f9f9f9;padding-bottom: 0;">
	<div class="container-fluid" style="    width: 1140px;background: #f9f9f9;margin: 0 auto;">
		<?php if (isset($post_bar_flag) && $post_bar_flag): ?>
		<div style="width: 1140px; margin: 0 auto">
			<div style="width: 165px; margin-left: 0; float: left;">
				<a class="back_index" href="<?php echo site_url('my_city/post_bar'); ?>" style="text-decoration: none">
					<span class="fa fa-home home-color"></span>
					<span style="font-size: 14px;">返回贴吧首页</span>
				</a>
			</div>
			<div style="padding-right: 50px; width: 925px; float: left">
				<ul class="top-link pull-right">
					<?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $_SESSION['role_id'] == Jys_system_code::ROLE_USER): ?>
						<li >
							<img style="top: -1px;" class="img-circle top-avatar" id="top_avatar" src="<?php echo site_url($_SESSION['avatar_path'] ? $_SESSION['avatar_path'] : 'source/img/default-user.png'); ?>" alt="">
							<a href="<?php echo site_url('user/user_center'); ?>" target="_blank"><?php echo $_SESSION['username'] ? $_SESSION['username'] : $_SESSION['username']; ?></a>
						</li>
						<li ><a href="<?php echo site_url('index/sign_out'); ?>">退出</a></li>
						<li ><a href="<?php echo site_url('my_city/home'); ?>">我的主页</a></li>
						<li ><a href="<?php echo site_url('my_city/home/my_message'); ?>">消息</a></li>
					<?php else: ?>
						<li ><a href="<?php echo site_url('index/sign_in'); ?>">登录</a></li>
						<li ><a href="<?php echo site_url('index/sign_up'); ?>">注册</a></li>
						<li ><a href="<?php echo site_url('index/sign_in'); ?>">我的主页</a></li>
						<li ><a href="<?php echo site_url('index/sign_in'); ?>">消息</a></li>
					<?php endif; ?>
					<li><a href="<?php echo site_url(); ?>">基因商城</a></li>
				</ul>
			</div>
		</div>
		<?php else: ?>
		<div style="width: 1140px; margin: 0 auto">
			<?php if (!isset($is_home) || !$is_home): ?>
			<div style="width: 165px; margin-left: 0; float: left;">
				<a class="back_index" href="<?php echo site_url(); ?>" style="text-decoration: none">
					<span class="fa fa-home home-color"></span>
					<span style="font-size: 14px;">返回商城首页</span>
				</a>
			</div>
			<?php endif; ?>
			<div style="padding-right: 50px; width: 925px; float: left; <?php echo (isset($is_home) && $is_home) ? 'margin-left:165px;' : ''; ?>">
				<ul class="top-link pull-right">
					<?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $_SESSION['role_id'] == Jys_system_code::ROLE_USER): ?>
					<li >
						<img style="top: -1px;" class="img-circle top-avatar" id="top_avatar" src="<?php echo site_url($_SESSION['avatar_path'] ? $_SESSION['avatar_path'] : 'source/img/default-user.png'); ?>" alt="">
						<a href="<?php echo site_url('user/user_center'); ?>" target="_blank"><?php echo $_SESSION['nickname'] ? $_SESSION['nickname'] : $_SESSION['username']; ?></a>
					</li>
					<li ><a href="<?php echo site_url('index/sign_out'); ?>">退出</a></li>
					<?php else: ?>
					<li ><a href="<?php echo site_url('index/sign_in'); ?>">登录</a></li>
					<li ><a href="<?php echo site_url('index/sign_up'); ?>">注册</a></li>
					<?php endif; ?>
					<?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $_SESSION['role_id'] == Jys_system_code::ROLE_USER): ?>
					<li ><a href="<?php echo site_url('order/order_list'); ?>">我的订单</a></li>
					<?php else: ?>
					<li ><a href="<?php echo site_url('index/sign_in'); ?>">我的订单</a></li>
					<?php endif; ?>
					<li >
						<a href="<?php echo site_url('index/service'); ?>">健康服务</a>
					</li>
					<li >
						<a href="<?php echo site_url('index/add_report'); ?>">录入检测人信息</a>
					</li>
					<li><a href="<?php echo site_url('my_city/post_bar'); ?>">我的城</a></li>
				</ul>
			</div>
		</div>
		<?php endif; ?>
	</div>
</nav>
<?php endif; ?>
<?php if(isset($isset_search) && $isset_search): ?>
<!-- Header -->
<header class="container">
	<div class="row-fluid">
		<div style="height: 100px; float: left;">
			<div id="logo">
				<h2><a href="<?php echo site_url('index'); ?>" style="text-decoration: none;">
						<img <?php if (isset($is_cart) && $is_cart){ echo 'style="width: 200px;"'; }else{ echo 'style="width: 260px;"'; } ?> src="<?=site_url('source/img/u2.png'); ?>">
						<?php if (isset($is_cart) && $is_cart): ?>
						<span style="font-size: 28px;font-weight: bold;color: #117d94;vertical-align: text-top;">购物车</span>
						<?php endif; ?>
					</a>
				</h2>
			</div>
		</div>
		<div class="input_search" <?php if (isset($is_cart) && $is_cart){ echo 'style="float: right;margin-top: 12px;"'; } ?>>
			<?php if ((isset($is_home) && $is_home)) : ?>
			<form class="form-search">
				<input type="text" class="input-medium search-query" placeholder="请输入关键字搜索" value="<?php if (isset($search) && !empty($search['key_words'])) { echo $search['key_words']; } ?>">
				<a class="btn btn-link btn-3" style="border-radius: 0;margin-left: -5px;" id="search-btn" target="_blank"><i class="fa fa-search"></i></a>
			</form>
			<?php else : ?>
			<div class="form-search">
				<input type="text" class="input-medium search-query" placeholder="请输入关键字搜索" value="<?php if (isset($search) && !empty($search['key_words'])) { echo $search['key_words']; } ?>">
				<a class="btn btn-link btn-3" style="border-radius: 0;margin-left: -5px;" id="search-btn" target="_blank"><i class="fa fa-search"></i></a>
			</div>
			<?php endif; ?>
		</div>
		<?php if (!(isset($is_cart) && $is_cart)): ?>
		<div class="shopping_cart_btn">
			<div id="cart" style="position: relative;z-index: 100" >
				<a class="btn btn-link btn-3" href="<?=site_url('shopping_cart') ?>"><i class="fa fa-shopping-cart" style="margin-right: 10px"></i>我的购物车<span class="fa fa-angle-down" style="margin-left: 4px"></span></a>
				<div id="icon_cart">
                    <span id="icon_cart_num"></span>
                </div>

				<div class="cart_box">
					<div style="margin: 6px 0 10px 10px">
						<strong style="font-family: '黑体'; font-size: 17px; ">最近加入</strong>
					</div>
					<div id="items_list">
						<ul id="shopping-cart-content">
						</ul>
					</div>
					<div class="cart_bottom" style="margin-top: 15px">
						<span style="font-family: '微软雅黑'; font-size: 15px">共<span id="items_num">5</span>件商品</span>
						<span style="font-family: '微软雅黑'; font-size: 15px; float: right">总计：¥<span id="items_price">00.00</span></span>
					</div>
					<div style="width: 100%">
						<a href="<?=site_url('shopping_cart') ?>" id="orderpay" class="btn btn-link btn-2">去购物车结算</a>
					</div>
				</div>
				<div id="empty_cart">
                    <span class="fa fa-shopping-cart" style="font-size: 30px; margin-top:55px;"></span>
                    <span>你的购物车暂时没有物品</span>
                </div>
			</div>
		</div>
		<?php endif; ?>
	</div>
</header>
<?php endif; ?>
<?php if($isset_nav): ?>
<!-- Navigation -->
<nav id="menu" class="navbar">
	<div class="navbar" style="background-color: #222">
<!--		<div class="navbar-inn" style="    background-color: #222;text-align: center;">-->
			<div class="container" style="background-color: #222;text-align: center;width: <?php echo isset($is_home) && $is_home ? '1140' : '1280'; ?>px;">
					<ul class="nav">
						<?php if (!(isset($is_home) && $is_home)) : ?>
						<li id="all-catagray" class="<?php echo $main_content != 'search_report' ? 'active' : ''; ?>">
							<a>所有商品分类</a>
							<div id="nav-list">
								<div class="all-sort-list " id="nav-sort-list" style="margin: 0 auto">
									<?php if (!empty($collection)): ?>
									<?php foreach($collection as $row) : ?>
										<div class="item">
											<div class="list_nav" style="">
												<h5 style=" font-family: '微软雅黑'"><?php echo $row['type_name']; ?><span> > </span></h5>
												<ul>
													<?php foreach($row['category'] as $key => $category) : ?>
														<?php if ($key <= 3): ?>
															<li class="category" data-id="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></li>
														<?php endif; ?>
													<?php endforeach; ?>
												</ul>
											</div>
											<!--                        <h3><a href="#" class="category" data-id="--><?php //echo $row['id']; ?><!--">--><?php //echo $row['name']; ?><!--</a></h3>-->
											<div class="item-list clearfix" style="border: 1px solid #117d94">
												<div class="subitem">
													<?php foreach ($row['category'] as $category): ?>
														<dl class="fore">
															<dt><a href="<?php echo site_url('index/search?category='.$category['id'].'&result='.$category['name']); ?>"><?php echo $category['name']; ?></a></dt>
															<dd>
																<?php foreach($category['children_category'] as $child_category) : ?>
																	<em><a href="#" class="category" data-id="<?php echo $child_category['id']; ?>"><?php echo $child_category['name']; ?></a></em>
																<?php endforeach; ?>
															</dd>
														</dl>
													<?php endforeach; ?>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
									<?php endif; ?>
								</div>
							</div>
						</li>
						<?php endif; ?>
						<li  class="<?php echo $main_content == 'index' ? 'active' : ''; ?>"><a href="<?php echo site_url(); ?>">首页</a></li>
						<li><a href="#">健康体检</a></li>
						<li><a href="#">个体膳食</a></li>
						<li><a href="#">代谢免疫</a></li>
						<li><a href="#">女性美容</a></li>
						<li><a href="#">儿童天赋</a></li>
						<li class="<?php echo $main_content == 'search_report' ? 'active' : ''; ?>"><a href="<?php echo site_url('index/search_report'); ?>">报告查询</a></li>
					</ul>
<!--				</div>-->


<!--			</div>-->
		</div>
	</div>
</nav>
<?php endif; ?>
