<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  index.php
 *
 *     Description:  微信默认控制器
 *
 *         Created:  2016-12-19 14:22:26
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */

Class Index extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
		$this->load->library(['form_validation', 'Jys_weixin_jssdk']);
		$this->load->model(['Banner_model', 'Commodity_model', 'Common_model', 'Order_model', 'Category_model', 'Article_model', 'User_model']);
    }

	/**
	 * 微信首页
	 */
	public function index(){
		$data['title'] = "首页";
		$data['js'] = array('index');
		$data['css'] = array('index');
		$data['main_content'] = 'index';
		$data['is_search'] = TRUE;
		$data['tab_nav'] = TRUE;
		$data['active_flag'] = 1;
		$data['banner'] = $this->Common_model->deal_banner_url($this->Banner_model->get_home_banner(5, jys_system_code::BANNER_POSITION_WEIXIN_HOME));
		$this->load->view('mobile/includes/template_view', $data);
	}

	public function saian_search()
	{
		$data['current_timestamp'] = time();
		$this->load->view('mobile/saian_search', $data);
	}

	/**
	 * 注册
	 */
	public function sign_up(){
		$data['title'] = "注册";
		$data['js'] = array('sign_up');
		$data['css'] = array('sign_up');
		$data['main_content'] = 'sign_up';
		$this->load->view('mobile/includes/template_view', $data);
	}

	/**
	 * 登录
	 */
	public function sign_in(){
		$data['title'] = "登录";
		$data['js'] = array('sign_in');
		$data['css'] = array('sign_in');
		$data['main_content'] = 'sign_in';
		$this->load->view('mobile/includes/template_view', $data);
	}

	/**
	 * 微信登出
	 */
	public function weixin_sign_out(){
		$openid = $_SESSION['openid'];
		session_unset();
		$_SESSION['openid'] = $openid;
		header('Location:'.site_url() . 'weixin/user/center');
	}

	/**
	 * 商品分类
	 */
	public function category($parent_id = 0){
		$data['title'] = "分类";
		$data['js'] = array('category');
		$data['css'] = array('category');
		$data['main_content'] = 'category';
		$data['is_search'] = TRUE;
		$data['tab_nav'] = TRUE;
		$data['active_flag'] = 2;
        $data['parent_id'] = $parent_id;
		$data['collection'] = $this->Category_model->get_category();
		$this->load->view('mobile/includes/template_view', $data);
	}

	/**
	 * 获取所有分类
	 */
	public function get_category() {
		$result = array('success'=>FALSE, 'msg'=>'获取分类信息失败', 'data'=>array());

		$collection = $this->Category_model->get_category();
		if (!empty($collection)) {
			$result['success'] = TRUE;
			$result['msg'] = '查询成功';
			$result['data'] = $collection;
		}

		echo json_encode($result);
	}

	/**
	 * 购物车
	 */
	public function shopping_cart(){
		$data['title'] = "购物车";
		$data['js'] = array('shopping_cart');
		$data['css'] = array('shopping_cart');
		$data['main_content'] = 'shopping_cart';
		$this->load->view('mobile/includes/template_view', $data);
	}

	/**
	 * 个人信息
	 */
	public function personal_info(){
		$data['title'] = "个人中心";
		$data['js'] = array('personal_info');
		$data['css'] = array('personal_info');
		$data['main_content'] = 'personal_info';
		$this->load->view('mobile/includes/template_view', $data);
	}

	/**
	 * 支付成功
	 */
	public function pay_status($order_id = 0){
        if (empty($order_id) || intval($order_id) < 1){
            show_404();
            exit;
        }
		$data['title'] = "订单状态";
		$data['js'] = array('pay_status');
		$data['css'] = array('pay_status');
		$data['main_content'] = 'pay_status';
        $data['order_id'] = $order_id;
		$this->load->view('mobile/includes/template_view', $data);
	}

	/**
	 * 物流详情
	 */
	public function logistics_details($order_id = 0){
        if (empty($order_id) || intval($order_id) < 1){
            show_404();
            exit;
        }
		$data['title'] = "查看进度";
		$data['js'] = array('logistics_details');
		$data['css'] = array('logistics_details');
		$data['main_content'] = 'logistics_details';
        $data['order_id'] = $order_id;
		$this->load->view('mobile/includes/template_view', $data);
	}

    /**
     * 商品详情页面
     * @param int $commodity_id
     * @param int $commodity_specification_id
     * @param int $status_id
     */
	public function commodity_detail($commodity_id = 0, $commodity_specification_id = 0, $status_id = 0){
		if (empty($commodity_id) || intval($commodity_id) < 1){
            redirect('weixin/index/show_404');
			exit;
		}

		$user_id = (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : NULL;
		
		$data['title'] = "商品详情";
		$data['js'] = array('commodity_detail');
		$data['css'] = array('commodity_detail');
		$data['main_content'] = 'commodity_detail';
		$data['thumbnails'] = $this->Commodity_model->get_pic_by_commodity_id($commodity_specification_id, TRUE)['data'];
		$data['commodity_id'] = $commodity_id;
		$data['commodity_specification_id'] = $commodity_specification_id;
		$data['status_id'] = $status_id;
		$data['point_enough_flag'] = $this->Commodity_model->check_point_enough($user_id, $commodity_id, 1);
		$this->load->view('mobile/includes/template_view', $data);
	}
	
	/**
	 * 订单详情
	 */
	public function order_detail($order_id = 0, $status_id = 0){
	    if (intval($order_id) < 1) {
            redirect('weixin/index/show_404');
            exit;
        }

		$user_id = 0;
		if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])){
			$user_id = $_SESSION['user_id'];
		}else{
			redirect('/weixin');
		}

		$data['title'] = "订单详情";
		$data['js'] = array('order_detail');
		$data['css'] = array('order_detail');
		$data['main_content'] = 'order_detail';
        $data['order_id'] = $order_id;
        $data['status_id'] = $status_id;
        $data['order'] = $this->Order_model->get_order_by_condition(array('order.id'=>$order_id, 'order.user_id'=>$user_id))['data'];
        if (empty($data['order'])) {
            redirect('weixin/index/show_404');
            exit;
        }

		$this->load->view('mobile/includes/template_view', $data);
	}

	/**
	 * 确认订单
	 */
	public function confirm_order($ids = NULL, $is_point_flag = 0){
		if (empty($ids)){
            redirect('weixin/index/show_404');
            exit;
		}

		$data['title'] = "确认订单";
		$data['js'] = array('confirm_order');
		$data['css'] = array('confirm_order');
		$data['main_content'] = 'confirm_order';
		$data['ids'] = $ids;
		$data['need_gaode_api'] = TRUE;
        if (isset($_SESSION['agent_id']) && intval($_SESSION['agent_id']) > 0) {
            $data['show_discount_coupon'] = FALSE;
        }else {
            $data['show_discount_coupon'] = TRUE;
        }
		if ($is_point_flag){
			$data['is_point'] = 1;
		}else{
			$data['is_point'] = 0;
		}
		$this->load->view('mobile/includes/template_view', $data);
	}

	/**
	 * 搜索结果
	 */
	public function search_result(){
		$data['title'] = "搜索结果";
		$data['js'] = array('search_result');
		$data['css'] = array('search_result');
		$data['main_content'] = 'search_result';
        $key_words = $this->input->get('key_words', TRUE);
        $category = $this->input->get('category', TRUE);
        if (!empty($key_words) && is_string($key_words)){
            $data['key_words'] = $key_words;
        }
        if (!empty($category) && is_numeric($category) && intval($category) > 0){
            $data['category'] = $category;
        }

        $this->load->view('mobile/includes/template_view', $data);
	}

	/**
	 * 获取搜索数据
	 */
	public function search()
    {
        $search = $this->input->post();

        if (empty($search)) {
            $this->show_404();
            return;
        }

        // 处理搜索条件
        $search_str = $this->Commodity_model->search($search);
        $page = !empty($search['page']) ? $search['page'] : 1;
        $page_size = !empty($search['page_size']) ? $search['page_size'] : 5;

        $data = $this->Commodity_model->search_paginate($page, $page_size, $search_str);
        if ($data['success']) {
            $data['data'] = $this->Commodity_model->get_commodity_evaluation_info($data['data']);
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['role_id']) && intval($_SESSION['role_id']) == jys_system_code::ROLE_USER) {
                $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $_SESSION['user_id']]);
                $data['data'] = $this->Commodity_model->calculate_discount_price($data['data'], $user_info['price_discount']);
            }

            // 根据是否有commodity_center.name组装商品的commodity_name字段
            for ($i = 0; $i < count($data['data']); $i++) {
                if (!empty($data['data'][$i]['commodity_center_name'])) {
                    $data['data'][$i]['commodity_name'] = $data['data'][$i]['commodity_name'].' '.$data['data'][$i]['commodity_center_name'].' '.$data['data'][$i]['package_type_name'];
                } elseif (!empty($data['data'][$i]['commodity_specification_name'])) {
                    $data['data'][$i]['commodity_name'] = $data['data'][$i]['commodity_name'].' '.$data['data'][$i]['commodity_specification_name'].' '.$data['data'][$i]['package_type_name'];
                } else {
                    $data['data'][$i]['commodity_name'] = $data['data'][$i]['commodity_name'].' '.$data['data'][$i]['package_type_name'];
                }
            }
        }

        if (empty($param)) {
            echo json_encode($data);
        } else {
            return $data['data'];
        }
    }

    /**
	 * 获取确认订单页面数据
	 */
	public function get_order_settlement(){
		$user_id = $_SESSION['user_id'];
		if (isset($_SESSION['agent_id']) && intval($_SESSION['agent_id']) > 0) {
            $agent_id = intval($_SESSION['agent_id']);
        }else{
            $agent_id = NULL;
        }
		$shopping_cart_ids = explode('-', $this->input->post('ids', TRUE));
		$result = $this->Order_model->get_order_settlement($shopping_cart_ids, $user_id, $agent_id);
		if (empty($result)){
			$data['success'] = FALSE;
			$data['msg'] = '获取数据失败';
			$data['data'] = NULL;
		}else{
            $result = $this->Common_model->format_commodity_name($result);
            $user_info = $this->User_model->get_user_detail_by_condition(['user.id'=>$_SESSION['user_id']]);
            $result = $this->Commodity_model->calculate_discount_price($result, $user_info['price_discount'], $agent_id);
		    if (isset($_SESSION['agent_id']) && intval($_SESSION['agent_id']) > 1) {
		        $data['agent_id'] = $_SESSION['agent_id'];
		        // 当前用户是代理商用户
                if (!empty($result) || is_array($result)) {
                    // 三件包邮产品的商品ID数组
                    $free_postage_commodity_id = $this->config->item('free_postage_commodity_ids');
                    // 包邮产品出现的次数
                    $free_postage_commodity_time = 0;
                    // 系统配置的邮费
                    $data['postage'] = 0;
                    foreach ($result as $item) {
                        if (in_array($item['commodity_id'], $free_postage_commodity_id)) {
                            $free_postage_commodity_time += intval($item['amount']);
                            if ($free_postage_commodity_time > 2) {
                                $data['postage'] = 0;
                                break;
                            }else {
                                $data['postage'] = $this->config->item('postage_price');
                            }
                        }
                    }
                }
            }


			$data['success'] = TRUE;
			$data['msg'] = '';
			$data['data'] = $result;
		}

		echo json_encode($data);
	}

	/**
	 * 获取积分商品信息
	 */
	public function get_point_commodity(){
		$id = $this->input->post('ids', TRUE);
		$data = $this->Commodity_model->get_commodity_by_condition(['commodity.id'=>$id], TRUE, TRUE);

		echo json_encode($data);
	}

	/**
	 * 手机端404页面
	 */
	public function show_404(){
		$this->load->view('errors/html/mobile/error_404');
	}

	/**
	 * 手机端404页面
	 */
	public function show_500(){
		$this->load->view('errors/html/mobile/error_500');
	}

	/**
	 * 手机端404页面
	 */
	public function show_nowifi(){
		$this->load->view('errors/html/mobile/error_nowifi');
	}

	/**
	 * 健康论坛
	 */

	public function forum_health(){
		$data['title'] = "健康论坛";
		$data['js'] = array('forum_health');
		$data['css'] = array('forum_health');
		$data['main_content'] = 'forum_health';
		$this->load->view('mobile/includes/template_view', $data);
	}
	/**
	 * 健康论坛文章详情
	 */

	public function health_article($id){
		if (intval($id) < 1){
			$this->show_404();
		}

		$data['title'] = "文章详情";
		$data['js'] = array('health_article');
		$data['css'] = array('health_article');
		$data['main_content'] = 'health_article';
		$data['article'] = $this->Article_model->get_by_condition(['article.id'=>$id, 'article.status_id'=>Jys_system_code::ARTICLE_STATUS_PUBLISHED]);
		$this->load->view('mobile/includes/template_view', $data);
	}

    /**
     * 获取jssdk配置参数
     */
	public function get_jssdk_config() {
	    $result = array('success'=>FALSE, 'msg'=>'获取jssdk配置参数失败', 'data'=>array());
	    $js_config = $this->jys_weixin_jssdk->getSignPackage();
        if (is_array($js_config) && !empty($js_config)) {
            $result['success'] = TRUE;
            $result['msg'] = '获取jssdk配置参数成功';
            $result['data'] = $js_config;
        }

        echo json_encode($result);
    }


	public function aggrement($is_agent = 0){
		$data['title'] = "知情同意书";
		$data['js'] = array('aggrement');
		$data['css'] = array('aggrement');
		$data['is_agent'] = $is_agent;
		$data['main_content'] = 'aggrement';
		$this->load->view('mobile/includes/template_view', $data);
	}

}