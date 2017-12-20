<?php if (isset($tab_nav) && $tab_nav && isset($active_flag)): ?>
<ul class="tabbar tabbar-footer">
    <li class="tab <?php echo $active_flag == 1 ? 'active' : '' ?>">
        <a href="{{ SITE_URL + 'weixin' }}">
            <?php if ($active_flag == 1) : ?>
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/home.png' }}" alt="">
            <?php else : ?>
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/home-o.png' }}" alt="">
            <?php endif; ?>
        </a>
        <label class="tab-label">首页</label>
    </li>
    <?php if (isset($integral) && $integral) : ?>
    <li class="tab <?php echo $active_flag == 2 ? 'active' : '' ?>">
        <a href="{{ SITE_URL + 'weixin/integral/exchange' }}">
            <?php if ($active_flag == 2) : ?>
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/change.png' }}" alt="">
            <?php else : ?>
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/change-o.png' }}" alt="">
            <?php endif; ?>
        </a>
        <label class="tab-label">兑换</label>
    </li>
    <?php else : ?>
    <li class="tab <?php echo $active_flag == 2 ? 'active' : '' ?>">
        <a href="{{ SITE_URL + 'weixin/index/category' }}">
            <?php if ($active_flag == 2) : ?>
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/category.png' }}" alt="">
            <?php else : ?>
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/category-o.png' }}" alt="">
            <?php endif; ?>
        </a>
        <label class="tab-label">分类</label>
    </li>
    <?php endif; ?>
    <li class="tab <?php echo $active_flag == 3 ? 'active' : '' ?>">
        <a href="{{ SITE_URL + 'weixin/index/shopping_cart' }}">
            <?php if ($active_flag == 3) : ?>
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/shopping-cart.png' }}" alt="">
            <?php else : ?>
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/shopping-cart-o.png' }}" alt="">
            <?php endif; ?>
        </a>
        <label class="tab-label">购物车</label>
    </li>
    <li class="tab <?php echo $active_flag == 4 ? 'active' : '' ?>">
        <a href="{{ SITE_URL + 'weixin/user/center' }}">
            <?php if ($active_flag == 4) : ?>
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/user-center.png' }}" alt="">
            <?php else : ?>
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/user-center-o.png' }}" alt="">
            <?php endif; ?>
        </a>
        <label class="tab-label">个人中心</label>
    </li>
</ul>
<?php endif; ?>