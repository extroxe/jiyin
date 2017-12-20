<script src="<?=site_url('source/assets/jquery/jquery-3.1.1.js')?>"></script>
<script src="<?=site_url('source/js/jquery-1.10.2.min.js')?>"></script>
<script src="<?=site_url('source/js/jquery.cookie.js')?>"></script>
<script src="<?=site_url('source/assets/laydate/laydate.js')?>"></script>
<script src="<?=site_url('source/assets/sweetalert/sweetalert.min.js');?>"></script>
<script src="<?=site_url('source/assets/bootstrap-2.3.2/js/bootstrap.js')?>"></script>
<script src="<?=site_url('source/js/header.js')?>"></script>
<script src="<?=site_url('source/js/pagination.js')?>"></script>
<script src="<?=site_url('source/js/jquery.lazyload.min.js')?>"></script>
<script src="<?=site_url('source/js/dropload.min.js')?>"></script>
<script src="<?=site_url('source/js/spark-md5.js')?>"></script>
<script src="<?=site_url('source/js/sweetalert.js')?>"></script>
<script src="<?=site_url('source/kindeditor/kindeditor-all.js')?>"></script>

<!--编辑器-->
<!--<script src="--><?//=site_url('source/js/utf8-php/ueditor.config.js')?><!--"></script>-->
<!--<script src="--><?//=site_url('source/js/utf8-php/ueditor.all.min.js')?><!--"></script>-->
<!--<script src="--><?//=site_url('source/js/utf8-php/lang/zh-cn/zh-cn.js')?><!--"></script>-->

<?php if (isset($need_gaode_api) && $need_gaode_api):?>
    <script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=f751de256e5a028b11c8b2cc8b4d4ad4&plugin=AMap.DistrictSearch"></script>
<?php endif;?>
<?php if(isset($js) && !empty($js)):?>
<?php foreach($js as $row): ?>
    <script type="text/javascript" src="<?=site_url();?>source/js/<?=$row?>.js"></script>
<?php endforeach; ?>
<?php endif;?>
<script>
    $(document).ready(function () {
        $("img").lazyload({effect: "fadeIn"});
    })
</script>

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