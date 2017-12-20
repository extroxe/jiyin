<!--<script src="--><?//=site_url('source/assets/jquery/jquery-3.1.1.min.js')?><!--"></script>-->
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script src="<?=site_url('source/assets/angular/angular.min.js')?>"></script>
<script src="<?=site_url('source/admin/vendor/angular/angular-file-upload/angular-file-upload.min.js')?>"></script>
<script src="<?=site_url('source/admin/vendor/jquery/md5/spark-md5.js')?>"></script>
<script src="<?=site_url('source/mobile/js/app.js')?>"></script>
<script src="<?=site_url('source/mobile/js/dropload.min.js')?>"></script>
<script src="<?=site_url('source/mobile/js/pdf/pdf.compatibility.js')?>"></script>
<script src="<?=site_url('source/mobile/js/pdf/pdf.js')?>"></script>
<script src="<?=site_url('source/mobile/js/pdf/pdf.worker.js')?>"></script>
<script src="<?=site_url('source/mobile/js/pdf.js')?>"></script>
<script src="<?=site_url('source/mobile/js/pdf.worker.js')?>"></script>
<script src="<?=site_url('source/mobile/js/jquery.media.js')?>"></script>
<script src="<?=site_url('source/mobile/js/pdf/jquery.touchSwipe.js')?>"></script>
<script src="<?=site_url('source/mobile/js/pdf/jquery.touchPDF.js')?>"></script>
<script src="<?=site_url('source/mobile/js/pdf/jquery.panzoom.js')?>"></script>
<script src="<?=site_url('source/mobile/js/pdf/jquery.mousewheel.js')?>"></script>
<script src="<?=site_url('source/assets/seedsui/scripts/lib/seedsui/seedsui.min.js')?>"></script>
<!--<script src="--><?//=site_url('source/mobile/js/zepto.min.js')?><!--"></script>-->
<?php if (isset($need_gaode_api) && $need_gaode_api):?>
    <script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=f751de256e5a028b11c8b2cc8b4d4ad4&plugin=AMap.DistrictSearch"></script>
<?php endif;?>
<? if(isset($js) && !empty($js)):?>
<?php foreach($js as $row): ?>
    <script type="text/javascript" src="<?=site_url();?>source/mobile/js/<?=$row?>.js"></script>
<?php endforeach; ?>
<? endif;?>

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