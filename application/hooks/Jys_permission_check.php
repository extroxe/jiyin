<?php
/**
 * =====================================================================================
 *
 *         Filename:  Jys_permission_check.php
 *
 *      Description:  系统权限检查类
 *
 *          Created:  2016-12-15 10:51:17
 *
 *           Author:  wuhaohua
 *
 * =====================================================================================
 */
class Jys_permission_check {
    private $_CI;

    public function __construct() {
        $this -> _CI = & get_instance();
        $this->_CI->load->library('session');
        $this->_CI->load->helper('url');
    }

    public function index() {
        $this->_check();
    }

    /**
     * 检测系统访问权限
     */
    private function _check() {
        $uri_controller = $this->_CI->uri->segment(1);
        $uri_function = $this->_CI->uri->segment(2);

        if ($uri_function == 'test') {
            // 各个控制器用来测试的方法直接放过
        }else {
            if ($uri_controller == 'admin') {
                // 打开的是管理端
                $this->_check_admin();
            }else if ($uri_controller == 'weixin') {
                // 打开的是微信页面
                $this->_check_weixin();
            }else {
                // 打开的是用户端
                $this->_check_user();
            }
        }
    }

    /**
     * 检测管理员访问权限
     */
    private function _check_admin() {
        $uri_controller = $this -> _CI -> uri -> segment(2);
        $uri_function = $this -> _CI -> uri -> segment(3);

        if ($uri_controller == "admin" && ($uri_function == "" || $uri_function == "index" || $uri_function == "login")) {
            // 只有这个路径不检查，其他都需要检查
        }
        else if(($uri_controller == 'postage_admin' && ($uri_function == 'get_postage_by_order')) || ($uri_controller == 'erp_admin' && ($uri_function == 'upload_excel_attachment_report_info'))) {
            // 获取邮费，不需要检查
        }
        else {
            $id = $this->_CI->session->userdata('user_id');
            $role_id = $this -> _CI -> session -> userdata("role_id");

            if (!empty($id) && intval($id) > 0 
                && (intval($role_id) == intval(Jys_system_code::ROLE_ADMINISTRATOR)
                    OR intval($role_id) == intval(jys_system_code::ROLE_AGENT)))
            {
                // 通过检查，不做拦截
            }else {
                if ($this->_is_ajax_request()) {
                    echo json_encode(array('success'=>FALSE, 'timeout'=>TRUE, 'msg'=>'您的登陆信息已失效，请刷新页面，重新登陆！'));
                }else {
                    redirect('admin/admin');
                }
                exit;
            }
        }
    }

    /**
     * 检测微信用户
     */
    private function _check_weixin() {
        $uri_controller = $this -> _CI -> uri -> segment(2);
        $uri_function = $this -> _CI -> uri -> segment(3);

        $test_user_info = array(
            'user_id'=>459,
            'username'=>'whh306318848',
            'name'=>'吴昊骅',
            'nickname'=>'大大',
            'avatar_path'=>'source/uploads/ead29f110c1765688eeebd0ac93aca0c.jpg',
            'openid'=>'oVKdms_Cc6a3KCW5o7Xs-cs_fLxE',
            'role_id'=>jys_system_code::ROLE_USER,
            'agent_id'=>470
        );

        //$this -> _CI -> session -> set_userdata($test_user_info);

        $id = $this->_CI->session->userdata('user_id');
        $open_id = $this->_CI->session->userdata('openid');
        $role_id = $this -> _CI -> session -> userdata("role_id");

        // 忽略检查路径列表
        $uncheck_list = array(
            'weixin'=>array(),
            'index'=>array('', 'index', 'sign_up', 'sign_in', 'category', 'commodity_detail', 'search_result', 'search', 'get_recommend', 'show_404', 'show_500', 'show_nowifi', 'get_category', 'saian_search', 'aggrement'),
            'integral'=>array('', 'index', 'commodity_detail'),
            'user' => array('center', 'evaluation_list', 'post', 'view_post', 'visit', 'add_report', 'search_report', 'search_report_by_number'),
            'agent' => array('entrance', 'home', 'hinter', 'agent_search', 'weixin_entrance')
        );

        foreach ($uncheck_list as $controller => $function_list) {
            if (empty($function_list) || !is_array($function_list) || count($function_list) < 1) {
                // 整个控制器都不需要检查
                if ($uri_controller == $controller) {
                    return;
                }
            }else {
                // 控制器下的某个接口不需要检查
                foreach ($function_list as $function) {
                    if ($uri_controller == $controller && $uri_function == $function) {
                        return;
                    }
                }
            }
        }

        if (empty($open_id)) {
            // 未获取到用户的open_id
            $appid = $this -> _CI -> config -> item('wx_appid');
            $redirect_uri = urlencode(site_url('/weixin/weixin/get_userinfo'));
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            $location = "Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$url}#wechat_redirect";
            header($location);
        }else {


            if ($uri_controller == "") {
                // 访问的是index控制器的index方法，不做检查
                return;
            }

            if (!empty($id) && intval($id) > 0 && intval($role_id) == intval(jys_system_code::ROLE_USER)) {
                // 用户已经登录
                return;
            }else if (intval($role_id) == intval(jys_system_code::ROLE_AGENT_USER) && intval($id) > 0 && intval($this -> _CI -> session -> userdata("agent_id")) > 0 && !empty($this -> _CI -> session -> userdata("uid"))) {
                // 代理商用户已登录
                return;
            }else {
                // 用户未登录
                if ($this->_is_ajax_request()) {
                    echo json_encode(array('success'=>FALSE, 'timeout'=>TRUE, 'msg'=>'请先登录再进行该操作'));
                }else {
                    redirect('weixin/index/sign_in');
                }
                exit;
            }
        }
    }

    /**
     * 检测用户访问权限
     */
    private function _check_user() {
        $uri_controller = $this -> _CI -> uri -> segment(1);
        $uri_function = $this -> _CI -> uri -> segment(2);

        // 忽略检查路径列表
        $uncheck_list = array(
            'index'=>array(),
            'verification_code'=>array(),
            'commodity'=>array(),
            'category'=>array(),
            'article'=>array(),
            'order' => array('pdf_view'),
            'user' => array('register', 'check_username', 'check_phone', 'check_user_phone_valid', 'check_report', 'add_report_userInfo', 'login_from_weixin', 'fill_in_userinfo'),
            'post_bar'=>array('search_post_bar', 'get_post_bar_by_id', 'get_recommend_post_bar', 'get_fans', 'get_focus_user', 'get_user_post_info'),
            'post'=>array('paginate', 'paginate_comment', 'get_post_by_id'),
            'my_city'=>array('post_bar', 'post', 'view_post', 'visit', 'visit_lists', 'follow_lists', 'focus_lists', 'follow_lists', 'post_bar_search'),
            'alipay'=>array(),
            'auto_operation'=>array()
        );

        foreach ($uncheck_list as $controller => $function_list) {
            if (empty($function_list) || !is_array($function_list) || count($function_list) < 1) {
                // 整个控制器都不需要检查
                if ($uri_controller == $controller) {
                    return;
                }
            }else {
                // 控制器下的某个接口不需要检查
                foreach ($function_list as $function) {
                    if ($uri_controller == $controller && $uri_function == $function) {
                        return;
                    }
                }
            }
        }

        $id = $this->_CI->session->userdata('user_id');
        $role_id = $this -> _CI -> session -> userdata("role_id");

        if ($uri_controller == "attachment" && intval($id) > 0 && intval($role_id) > 0) {
            // 附件控制器全局使用，只要登录之后就可以使用
        }else if (intval($id) < 1 || ($role_id != jys_system_code::ROLE_USER && $role_id != jys_system_code::ROLE_AGENT_USER)) {
            if($this->_is_ajax_request()) {
                echo json_encode(array('success'=>FALSE, 'timeout'=>TRUE, 'msg'=>'请先登录再进行该操作'));
            }else {
                redirect('index');
            }
            exit;
        }
    }

    /**
     * 判断是否是ajax请求
     * @return bool 是ajax请求则返回true，不是则返回false
     */
    private function _is_ajax_request(){
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && ($_SERVER["HTTP_X_REQUESTED_WITH"] === "XMLHttpRequest")){
            return TRUE;
        }else{
            return FALSE;
        }
    }
}