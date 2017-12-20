<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  User.php
 *
 *     Description:  微信用户控制器
 *
 *         Created:  2016-12-29 17:20:12
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */

Class User extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->library(['form_validation', 'Jys_weixin_jssdk']);
        $this->load->model(['Post_model', 'User_model']);
    }

    /**
     * 积分详情
     */
    public function integral_detail(){
        $data['title'] = "积分详情";
        $data['js'] = array('integral_detail');
        $data['css'] = array('integral_detail');
        $data['main_content'] = 'integral_detail';
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 个人中心
     */
    public function center(){
        $data['title'] = "个人中心";
        $data['js'] = array('user_center');
        $data['css'] = array('user_center');
        $data['main_content'] = 'user_center';
        $data['tab_nav'] = TRUE;
        $data['active_flag'] = 4;
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 个人信息
     */
    public function user_info(){
        $data['title'] = "个人信息";
        $data['js'] = array('user_info');
        $data['css'] = array('user_info');
        $data['main_content'] = 'user_info';
        $this->load->view('mobile/includes/template_view', $data);
    }
    
    /**
     * 订单详情
     */
    public function order_detail(){
        $data['title'] = "订单详情";
        $data['js'] = array('order_detail');
        $data['css'] = array('order_detail');
        $data['main_content'] = 'order_detail';
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 查看订单列表
     */
    public function order_list($status_id = 0){
        $data['title'] = "订单列表";
        $data['js'] = array('order_list');
        $data['css'] = array('order_list');
        $data['main_content'] = 'order_list';
        $data['status_id'] = $status_id;
        if (isset($_SESSION['agent_id']) && intval($_SESSION['agent_id']) > 0) {
            // 获取代理商主题色
            $agent_info = $this->jys_db_helper->get('user', intval($_SESSION['agent_id']));
            if ($agent_info && !empty($agent_info['color'])) {
                $data['color'] = $agent_info['color'];
            }
        }
        $this->load->view('mobile/includes/template_view', $data);
    }


    /**
     * 收货地址
     */
    public function receipt_address(){
        $data['title'] = "收货地址";
        $data['js'] = array('receipt_address');
        $data['css'] = array('receipt_address');
        $data['main_content'] = 'receipt_address';
        $data['need_gaode_api'] = TRUE;
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 修改密码
     */
    public function change_password(){
        $data['title'] = "修改密码";
        $data['js'] = array('change_password');
        $data['css'] = array('change_password');
        $data['main_content'] = 'change_password';
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 评价晒图
     */
    public function evaluation($order_id, $status_id = 0){
        $data['title'] = "评价晒图";
        $data['js'] = array('evaluation');
        $data['css'] = array('evaluation');
        $data['order_id'] = $order_id;
        $data['status_id'] = $status_id;
        $data['main_content'] = 'evaluation';
        $this->load->view('mobile/includes/template_view', $data);
    }
    /**
     * 评价列表
     */
    public function evaluation_list($commodity_id){
        $data['title'] = "累计评价";
        $data['js'] = array('evaluation_list');
        $data['css'] = array('evaluation_list');
        $data['commodity_id'] = $commodity_id;
        $data['main_content'] = 'evaluation_list';
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 我的优惠券
     */
    public function discount_coupon(){
        $data['title'] = "我的优惠券";
        $data['js'] = array('discount_coupon');
        $data['css'] = array('discount_coupon');
        $data['main_content'] = 'discount_coupon';
        $this->load->view('mobile/includes/template_view', $data);
    }
    /**
     * 会员中心
     */
    public function member_center() {
        $data['title'] = "会员中心";
        $data['js'] = array('member_center');
        $data['css'] = array('user_center', 'member_center');
        $data['main_content'] = 'member_center';
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 我的报告
     */
    public function my_report() {
        $data['title'] = "我的报告";
        $data['js'] = array('my_report');
        $data['css'] = array('my_report');
        $data['main_content'] = 'my_report';
        $data['need_gaode_api'] = TRUE;
        $data['jssdk_config'] = $this->jys_weixin_jssdk->getSignPackage();
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 添加报告信息
     */
    public function add_report($is_agent = 0) {
        $data['title'] = "检测信息录入";
        $data['js'] = array('add_report');
        $data['css'] = array('add_report');
        $data['main_content'] = 'add_report';
        $data['is_agent'] = $is_agent;
        $data['need_gaode_api'] = TRUE;
        $data['jssdk_config'] = $this->jys_weixin_jssdk->getSignPackage();
        $this->load->view('mobile/includes/template_view', $data);
    }
    /**
 * 查询报告
 */
    public function search_report($is_agent = 0) {
        $data['title'] = "报告查询";
        $data['js'] = array('search_report');
        $data['css'] = array('search_report');
        $data['main_content'] = 'search_report';
        $data['is_agent'] = $is_agent;
        $data['need_gaode_api'] = TRUE;
        $this->load->view('mobile/includes/template_view', $data);
    }
    /**
     * 根据报告编号查询报告
     */
    public function search_report_by_number($is_agent = 0) {
        $data['title'] = "报告查询";
        $data['js'] = array('search_number_report');
        $data['css'] = array('search_report');
        $data['main_content'] = 'search_number_report';
        $data['is_agent'] = $is_agent;
        $data['need_gaode_api'] = TRUE;
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 我的城
     */
    public function my_city($block = 'bar') {
        $data['title'] = "我的城";
        $data['js'] = array('my_city_'.$block);
        $data['css'] = array('my_city');
        $data['main_content'] = 'my_city';
        $data['block'] = $block;

        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 发送信息
     */
    public function send_message($user_id = 0){
        if (intval($user_id) < 1){
            $this->load->view('errors/html/mobile/error_404');
        }else{
            $data['title'] = "发信息";
            $data['js'] = array('send_message');
            $data['css'] = array('send_message');
            $data['main_content'] = 'send_message';
            $data['user_id'] = $user_id;

            $this->load->view('mobile/includes/template_view', $data);
        }
    }

    /**
     * 查看信息
     */
    public function read_message($id = 0){
        if (intval($id) < 1){
            $this->load->view('errors/html/mobile/error_404');
        }else{
            $data['title'] = "信息";
            $data['js'] = array('read_message');
            $data['css'] = array('read_message');
            $data['main_content'] = 'read_message';
            $data['message_id'] = $id;

            $this->load->view('mobile/includes/template_view', $data);
        }
    }

    /**
     * 我的收藏
     */
    public function collection_list() {
        $data['title'] = "我的收藏";
        $data['js'] = array('collection_list');
        $data['css'] = array('collection_list');
        $data['main_content'] = 'collection_list';
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 我的家族
     */
    public function my_family() {
        $data['title'] = "我的家族";
        $data['js'] = array('my_family');
        $data['css'] = array('my_family');
        $data['main_content'] = 'my_family';
        $this->load->view('mobile/includes/template_view', $data);
    }
    /**
     * 我的家族个人信息
     */
    public function family_info() {
        $data['title'] = "个人信息";
        $data['js'] = array('family_info');
        $data['css'] = array('family_info');
        $data['main_content'] = 'family_info';
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 检测报告
     */
    public function examining_report() {
        $data['title'] = "检测报告";
        $data['js'] = array('examining_report');
        $data['css'] = array('examining_report');
        $data['main_content'] = 'examining_report';
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 查看贴吧帖子
     */
    public function post($post_id = 0) {
        if (empty($post_id) || intval($post_id) < 1){
            redirect('weixin/index/show_404');
            exit;
        }
        $data['title'] = "贴吧";
        $data['js'] = array('post');
        $data['css'] = array('post');
        $data['main_content'] = 'post';
        $this->load->view('mobile/includes/template_view', $data);
    }
    /**
     * 查看帖子详情
     */

    public function view_post($post_bar_id = 0, $post_id = 0) {
        if(empty($post_bar_id) || intval($post_bar_id) < 1 || empty($post_id) || intval($post_id) < 1){
            show_404();
        };
        $data['title'] = "贴吧详情";
        $data['js'] = array('view_post');
        $data['css'] = array('view_post');
        $data['post_bar_id']=$post_bar_id;
        $data['post_id']=$post_id;
        $data['main_content'] = 'view_post';
        $this->Post_model->view_increment($post_id);
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 发表帖子评论
     */
    public function publish_evaluation() {
        $data['title'] = "发表回复";
        $data['js'] = array('publish_evaluation');
        $data['css'] = array('publish_evaluation');
        $data['main_content'] = 'publish_evaluation';
        $this->load->view('mobile/includes/template_view', $data);
    }
    
    /**
     * 发帖
     */
    public function publish_post($post_id = 0) {
        if (empty($post_id) || intval($post_id) < 1){
            redirect('weixin/index/show_404');
            exit;
        }
        $data['title'] = "发表帖子";
        $data['js'] = array('publish_post');
        $data['css'] = array('publish_post');
        $data['main_content'] = 'publish_post';
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 查看他人主页
     */
    public function visit($user_id = 0) {
        if (empty($user_id) || intval($user_id) < 1){
            redirect('weixin/index/show_404');
            exit;
        }

        if (isset($_SESSION['user_id']) && $user_id == $_SESSION['user_id']){
            $this->my_city('user');
        }else{
            if (isset($_SESSION['user_id'])){
                if($this->jys_db_helper->get_where('focus_user', ['user_id' => $_SESSION['user_id'], 'focus_id' => $user_id])){
                    $data['is_focused'] = TRUE;
                }else{
                    $data['is_focused'] = FALSE;
                }
            }

            $data['title'] = "好友主页";
            $data['js'] = array('visit_others_home');
            $data['css'] = array('visit_others_home');
            $data['user_id'] = $user_id;
            $data['user_info'] = $this->User_model->get_user_detail_by_condition(['user.id' => $user_id]);
            $data['main_content'] = 'visit_others_home';

            $this->load->view('mobile/includes/template_view', $data);
        }
    }

}