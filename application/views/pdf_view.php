<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <title>PDF浏览视图</title>
    <style>
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div id="pdf" style="height: 100%;"></div>
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="<?=site_url('source/assets/pdf/pdf_media.js')?>"></script>
    <script type="text/javascript">
        $(function() {
            $('#pdf').media({
                width: window.screen.width,
                height: window.screen.height,
                autoplay: true,
                src : '<?php echo site_url($pdf_url); ?>'
            });
        });
    </script>
</body>
</html>