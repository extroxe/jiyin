<?php
/*
 * =====================================================================================
 *
 *       Filename:  template_view.php
 *
 *    Description:  视图模板文件，自动加载必要视图以及参数视图
 *
 *        Created:  2015-03-17 11:47:48
 *
 *         Author:  wuhaohua
 *        Company:  uvge
 *
 * =====================================================================================
 */
?>
<?php
$this -> load -> view('mobile/includes/header_view');
$this -> load -> view('mobile/includes/css_view');
$this -> load -> view('mobile/'.$main_content);
$this -> load -> view('mobile/includes/footer_view');
$this -> load -> view('mobile/includes/js_view');
?>
