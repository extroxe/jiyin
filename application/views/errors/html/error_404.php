
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title>404</title>
	<!-- bootstrap.css -->
	<link rel="stylesheet" href="/source/assets/bootstrap-2.3.2/css/bootstrap.css" media="screen">
	<link rel="stylesheet" href="/source/assets/bootstrap-2.3.2/css/bootstrap-responsive.min.css" media="screen">
	<link rel="stylesheet" href="/source/assets/font-awesome/css/font-awesome.css" media="screen">
	<link rel="stylesheet" href="/source/css/font-slider.css" />
	<link rel="stylesheet" href="/source/css/header.css" />
	<link rel="stylesheet" href="/source/css/sign_up.css" />
	<link rel="stylesheet" href="/source/css/404.css" />

	<!-- index.body.css -->
	<!--<link rel="stylesheet" type="text/css" href="css/index.css">-->
	<!-- footer.css -->
	<!--<link rel="stylesheet" type="text/css" href="css/footer.css">-->
	<!-- jquery.js -->
	<script src="/source/assets/jquery/jquery-3.1.1.js"></script>
	<!-- bootstrap.js -->
	<script src="/source/assets/bootstrap-2.3.2/js/bootstrap.js"></script>
	<!-- header.js -->
	<script src="/source/js/header.js"></script>
	<!-- index.js -->
	<!--<script type="text/javascript" src="js/index.js"></script>-->
	<!-- lazyload.js -->

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<![endif]-->
</head>
<body style="background-color: #f2f7ff">
<!-- Top -->
<nav id="top" style="background-color: #F6F6F6;padding-bottom: 0;">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12" style="padding-right: 50px">
				<ul class="top-link pull-right">
					<?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
						<li >
							<img class="img-circle top-avatar" id="top_avatar" src="<?php echo !empty($_SESSION['avatar_path']) ? $_SESSION['avatar_path'] : ''; ?>" alt="">
							<a href="<?php echo '/index/user_center'; ?>"><?php echo !empty($_SESSION['nickname']) ? $_SESSION['nickname'] : '';; ?></a>
						</li>
						<li ><a href="<?php echo '/index/sign_out'; ?>">登出</a></li>
					<?php else: ?>
						<li ><a href="<?php echo '/index/sign_in'; ?>">登录</a></li>
						<li ><a href="<?php echo '/index/sign_up'; ?>">注册</a></li>
					<?php endif; ?>
					<li ><a href="<?php echo '/order/order_list'; ?>">我的订单</a></li>
					<li >
						<a href="<?php echo '/index/service'; ?>">健康服务<span class="fa fa-angle-down" style="margin-left: 4px"></span></a>
						<div class="drop_down" style="display: none;">
							<ul >
								<li style="display: block;"><a href="">健康服务</a></li>
								<li style="display: block;"><a href="">健康服务</a></li>
								<li style="display: block;"><a href="">健康服务</a></li>
								<li style="display: block;"><a href="">健康服务</a></li>
							</ul>
						</div>
					</li>
					<li><a href="<?php echo $_SERVER['SERVER_NAME']; ?>">我的城</a></li>
				</ul>
			</div>
		</div>
	</div>
</nav>
<h1 id="keTitle" >~ ~ 访 问 的 页 面 不 存 在 ~ ~ ( ´･ω･` )</h1>
<div class="demo">
	<p align="center">
		<span>4</span>
		<span>0</span>
		<span>4</span>
	</p>
	<p align="center" style="font-family: '微软雅黑'">您还可以访问</p>
	<div id="turn_up" >
		<a class="btn btn-2 btn-link" href="/">商城首页</a>
		<a class="btn btn-2 btn-link" href="<?php echo '/shopping_cart'; ?>">购物车</a>
		<a class="btn btn-2 btn-link" href="javascript:void(0)">热门推荐</a>
	</div>
</div>

<!-- footer -->
<footer style="margin-top: 100px">
	<div class="container">
		<div class="row-fluid">
			<div class="span12">
				<ul class="foot_label">
					<li><a href="javascript:void(0)">关于我们</a></li>
					<li><a href="javascript:void(0)">联系我们</a></li>
					<li><a href="javascript:void(0)">手机商城</a></li>
					<li><a href="javascript:void(0)">我的贴吧</a></li>
					<li><a href="javascript:void(0)">我的城</a></li>
				</ul>
			</div>
		</div>
		<br>

<!--		<div class="copyright">-->
<!--			<div class="container">-->
<!--				<div class="row-fluid">-->
<!--					<div class="span12" style="color:#aaa; text-align: center">-->
<!--						Copyright &copy; 2016. All rights reserved.-->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->

	</div>
</footer>
<script>
	var _hmt = _hmt || [];
	(function() {
		var hm = document.createElement("script");
		hm.src = "https://hm.baidu.com/hm.js?c181a068a633bab12df08a455a79aeec";
		var s = document.getElementsByTagName("script")[0];
		s.parentNode.insertBefore(hm, s);
	})();
</script>
</body>
</html>