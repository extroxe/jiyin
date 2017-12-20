<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  index.php
 *
 *     Description:  网站前台默认控制器
 *
 *         Created:  2016-06-10 20:41:45
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
		$this->load->library(['form_validation', 'encryption', 'Jys_alipay']);
		$this->load->helper('cookie');
		$this->load->model(['User_model', 'Category_model', 'Banner_model', 'Commodity_model', 'Common_model', 'Express_model', 'Report_model', 'Verification_code_model', 'System_setting_model']);
    }

	/**
	 * 商城首页
	 */
    public function index($is_weixin = 0){
		$data['title'] = "赛安基因";
		$data['js'] = array('index');
		$data['css'] = array('index');
		$data['main_content'] = 'index';
		$data['collection'] = $this->Category_model->get_category();
		$data['member_category'] = $this->Category_model->get_member_category();
		$data['banner'] = $this->Banner_model->get_home_banner(5, jys_system_code::BANNER_POSITION_PC_HOME);
        if (!empty($_SESSION['user_id']) && !empty($_SESSION['role_id']) && intval($_SESSION['role_id']) == jys_system_code::ROLE_USER) {
            $user_info = $this->User_model->get_user_detail_by_condition(['user.id'=>$_SESSION['user_id']]);
        }else {
            $user_info = array();
        }
        //今日上新
        $data['new_recent'] = $this->Commodity_model->get_commodity_by_condition(['commodity.is_point' => 0, 'commodity_specification.status_id' => jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED], TRUE, TRUE, 4, $user_info)['data'];
        if (!empty($data['new_recent'])) {
            // 格式化商品名
            $data['new_recent'] = $this->Common_model->format_commodity_name($data['new_recent']);
        }
        //热卖
        $data['recommend_hot_sale'] = $this->Commodity_model->get_home_recommend(4, jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_SALE, $user_info);
        if (!empty($data['recommend_hot_sale'])) {
            // 格式化商品名
            $data['recommend_hot_sale'] = $this->Common_model->format_commodity_name($data['recommend_hot_sale']);
        }
        //热换
        $data['recommend_hot_exchange'] = $this->Commodity_model->get_home_recommend(4, jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_EXCHANGE);
        if (!empty($data['recommend_hot_exchange'])) {
            // 格式化商品名
            $data['recommend_hot_exchange'] = $this->Common_model->format_commodity_name($data['recommend_hot_exchange']);
        }
        //限时折扣
		$data['flash_sale'] = $this->Commodity_model->get_flash_sale(3)['data'];
        if (!empty($data['flash_sale'])) {
            // 格式化商品名
            $data['flash_sale'] = $this->Common_model->format_commodity_name($data['flash_sale']);
        }
		$data['recommend_hot_sale_cover'] = $this->System_setting_model->get_hot_sale_cover()['data'];
		$data['recommend_hot_exchange_cover'] = $this->System_setting_model->get_hot_exchange_cover()['data'];
		$data['isset_search'] = TRUE;
		$data['isset_nav'] = TRUE;
		$data['is_home'] = TRUE;
		$data['is_weixin'] = $is_weixin;
		$this->load->view('includes/template_view', $data);
    }

	/**
	 * 获取显示折扣
	 */
	public function get_flash_sale(){
		$data = $this->Commodity_model->get_flash_sale(3)['data'];
		
		echo json_encode($data);
	}

	/**
	 * 搜索页
	 */
	public function search(){
		$search = $this->input->get();
		if (empty($search)){
			show_404();
		}

		$search_str = $this->Commodity_model->search($search);

		$page_size = isset($search['page_size']) ? $search['page_size'] : 9;
		if (isset($search['page']) && intval($search['page']) > 0){
			$page = $search['page'];
		}else{
			$page = 1;
		}

		$data['title'] = "赛安生物-搜索";
		$data['js'] = array('search');
		$data['css'] = array('search');
		$data['main_content'] = 'search';
		$data['search'] = $search;
		$data['collection'] = $this->Category_model->get_category();
		$result = $this->Commodity_model->search_paginate($page, $page_size, $search_str);
        if ($result['success']) {
            $user_info = [];
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['role_id']) && intval($_SESSION['role_id']) == jys_system_code::ROLE_USER) {
                $user_info = $this->User_model->get_user_detail_by_condition(['user.id'=>$_SESSION['user_id']]);
                $commodity = $this->Commodity_model->calculate_discount_price($result['data'], $user_info['price_discount']);
            } else {
                $commodity = $result['data'];
            }

            // 格式化商品名字
            $commodity = $this->Common_model->format_commodity_name($commodity);

            $data['search_commodity'] = $commodity;
            $data['render'] = $this->Common_model->render($page, $result['total_page']);
            $data['category'] = $this->Common_model->unique_queue($data['search_commodity'], ['category_id', 'category_name']);
            $data['type'] = $this->Common_model->unique_queue($data['search_commodity'], ['type_id', 'type']);

            $all_point_flag = TRUE;
            if (!empty($data['search_commodity'])){
                foreach ($data['search_commodity'] as $row){
                    if (!$row['is_point']){
                        $all_point_flag = FALSE;
                    }
                }
            }

            //根据商品类型，获取相应热卖 $commodity_type == 1 或者热换商品 $commodity_type == 2
            $commodity_type = $all_point_flag == TRUE ? jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_EXCHANGE : jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_SALE;
            $data['recommend'] = $this->Commodity_model->get_home_recommend(4, $commodity_type, $user_info);
            if (!empty($data['recommend'])) {
                // 格式化商品名
                $data['recommend'] = $this->Common_model->format_commodity_name($data['recommend']);
            }
        } else {
        	$all_point_flag = '';
            $data['search_commodity'] = array();
            $data['recommend'] = array();
            $data['render'] = array();
            $data['category'] = array();
            $data['type'] = array();
        }

		$data['all_point_flag'] = $all_point_flag;
		$data['isset_search'] = TRUE;
		$data['isset_nav'] = TRUE;
		$this->load->view('includes/template_view', $data);
	}

	/**
	 * 注册页面
	 */
	public function sign_up(){
		$data['title'] = "赛安生物-注册";
		$data['js'] = array('sign_up', 'jquery.validate.min');
		$data['css'] = array('sign_up');
		$data['main_content'] = 'sign_up';
		$data['isset_search'] = FALSE;
		$data['isset_nav'] = FALSE;
		$data['sign_up_flag'] = TRUE;
		$data['simple_footer'] = TRUE;
		$this->load->view('includes/template_view', $data);
	}

	/**
	 * 登录页面
	 */
	public function sign_in(){
		if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])){
			redirect('user/user_center');
		}
		$data['title'] = "赛安生物-登录";
		$data['js'] = array('sign_in');
		$data['css'] = array('sign_in');
		$data['main_content'] = 'sign_in';
		$data['isset_search'] = FALSE;
		$data['isset_nav'] = FALSE;
		$data['sign_in_flag'] = TRUE;
		$data['simple_footer'] = TRUE;
		$this->load->view('includes/template_view', $data);
	}

	/**
	 * 找回密码
	 */
	public function find_password(){

		$data['title'] = "赛安生物-找回密码";
		$data['js'] = array('template','find_password');
		$data['css'] = array('find_password');
		$data['main_content'] = 'find_password';
		$data['isset_search'] = FALSE;
		$data['isset_nav'] = FALSE;
		$this->load->view('includes/template_view', $data);
	}


	/**
	 * 健康服务页面
	 */
	public function service(){
		$data['title'] = "赛安生物-健康服务";
		$data['js'] = array('template','service');
		$data['css'] = array('service');
		$data['main_content'] = 'service';
		$data['isset_search'] = FALSE;
		$data['isset_nav'] = FALSE;
		$this->load->view('includes/template_view', $data);
	}

	/**
	 * 查询报告
	 */
	public function search_report(){
		$data['title'] = "赛安生物-查询报告";
		$data['js'] = array('template', 'search_report');
		$data['css'] = array('search_report');
		$data['main_content'] = 'search_report';
		$data['isset_search'] = TRUE;
		$data['isset_nav'] = TRUE;
		$data['collection'] = $this->Category_model->get_category();
		$this->load->view('includes/template_view', $data);
	}

	/**
	 * 账号登录验证
	 */
	public function do_login(){
		//验证表单信息
		$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[6]|max_length[256]');
		$this->form_validation->set_rules('auto_login', '自动登录', 'trim|numeric');

		//表单验证是否通过, 若不通过 返回表单错误信息，停止执行
		$res = $this->Common_model->deal_validation_errors();

		if ($res['success']){
			//处理数据
			$username = $this->input->post('username', TRUE);
			$password = $this->input->post('password', TRUE);
			$auto_login = intval($this->input->post('auto_login', TRUE));

			if (!empty(get_cookie('username')) && !empty(get_cookie('password'))){
				if ($password == get_cookie('password')){
					$password = $this->encryption->decrypt(get_cookie('password'));
				}
			}

			$data = $this->User_model->check_user($username, $password);

			if ($auto_login === 1 && $data['success'] && empty(get_cookie('username')) && empty(get_cookie('password'))){
				set_cookie('username', $username, 3600*24*7);
				set_cookie('password', $this->encryption->encrypt($password), 3600*24*7);
			}
		}else{
			$data['success'] = FALSE;
			$data['msg'] = '输入有错误';
			$data['error'] = $res['msg'];
		}

		echo json_encode($data);
	}

	/**
	 * 手机号登录验证
	 */
	public function do_login_by_phone(){
		$data = array('success' => FALSE, 'msg' => '登录失败');

		//验证表单信息
        $this->form_validation->set_rules('phone', '手机号码', 'trim|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['regex_match'=>'请填写正确的手机号']);
        $this->form_validation->set_rules('code', '验证码', 'trim|required');

		//表单验证是否通过, 若不通过 返回表单错误信息，停止执行
		$res = $this->Common_model->deal_validation_errors();

		if ($res['success']){
			//处理数据
			$phone = $this->input->post('phone');
			$code = $this->input->post('code');

			//验证验证码是否正确
			if ($this->Verification_code_model->check_code($phone, $code, Jys_system_code::VERIFICATION_CODE_PURPOSE_LOGIN)){
				//验证手机号是否存在
                $data = $this->User_model->check_user_by_phone($phone);
            }else{
                $data['msg'] = '验证码有误';
            }

		}else{
			$data['msg'] = '输入有错误'.$res['msg'];
		}

		echo json_encode($data);
	}

	/**
	 * 微信扫描后进入手机号绑定验证
	 */
	public function bind_phone_for_login(){
		$data = array('success' => FALSE, 'msg' => '绑定失败', 'data' => '');

		//验证表单信息
        $this->form_validation->set_rules('phone', '手机号码', 'trim|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['regex_match'=>'请填写正确的手机号']);
        $this->form_validation->set_rules('code', '验证码', 'trim|required');

		//表单验证是否通过, 若不通过 返回表单错误信息，停止执行
		$res = $this->Common_model->deal_validation_errors();

		if ($res['success']){
			//处理数据
			$phone = $this->input->post('phone');
			$code = $this->input->post('code');

			//验证验证码是否正确
			if ($this->Verification_code_model->check_code($phone, $code, Jys_system_code::VERIFICATION_CODE_PURPOSE_PHONE)){
				//验证手机号是否存在
                $result = $this->User_model->check_user_by_phone($phone);
                if ($result['success'] == TRUE) {
                	if (!empty($result['data'])) {
                		$data['msg'] = '当前手机号已绑定了微信，请登录系统后解绑重新绑定';
                	}else{
                		//将微信号绑定到用户信息中
                		$update = ['id' => $_SESSION['user_id'], 'openid' => $_SESSION['openid']];
                		$update_result = $this->jys_db_helper->update_by_condition('user', ['id' => $update['id']], $update);
                		$data['success'] = TRUE;
                		$data['msg'] = '绑定成功';
                	}
                }else{
                	//当前手机号没有绑定任何账号，进入注册页面填写用户信息
                	$data['success'] = TRUE;
                	$data['msg'] = '绑定成功';
                	$data['data'] = $phone;
                }
            }else{
                $data['msg'] = '验证码有误';
            }

		}else{
			$data['msg'] = '输入有错误'.$res['msg'];
		}

		echo json_encode($data);
	}

	/**
	 * 登出
	 */
	public function sign_out(){
		session_unset();
		header('Location:'.site_url());
	}

	/**
	 * 根据商品ID获取商品
	 */
	public function get_commodity_by_id(){
		$specification_id = $this->input->post('specification_id', TRUE);
		if (!empty($_SESSION['agent_id']) && isset($_SESSION['agent_id'])) {
			$agent_id = $_SESSION['agent_id'];
		}else{
			$agent_id = NULL;
		}
        $commodity = $this->Commodity_model->get_commodity_list_by_condition(['commodity_specification.id' => $specification_id], FALSE, $agent_id);
        if (!empty($commodity)) {
            $commodity = $this->Common_model->format_commodity_name($commodity);
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['role_id']) && intval($_SESSION['role_id']) == jys_system_code::ROLE_USER) {
                $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $_SESSION['user_id']]);
                $commodity = $this->Commodity_model->calculate_discount_price($commodity, $user_info['price_discount']);
            }
            $result = array('success' => TRUE, 'msg' => '获取商品信息成功', 'data' => $commodity);
        } else {
            $result = array('success' => FALSE, 'msg' => '获取商品信息失败', 'data' => NULL);
        }

		echo json_encode($result);
	}

	/**
	 * 更新快递物流信息（异步回调接口）
	 */
	public function update_logistics_info(){
		$request = $this->input->post();
        file_put_contents(APPPATH.'/logs/kdniao_log_'.date('Ymd'), date('Y-m-d H:i:s')."\n".json_encode($request)."\n\n", FILE_APPEND);
		$request_data = json_decode($request['RequestData'], TRUE);
		$logistics_arrs = $request_data['Data'];

		$response = [
			'EBusinessID' => $request_data['EBusinessID'],
			'UpdateTime' => date('Y-m-d H:i:s'),
			'Success' => false,
			'Reason' => ''
		];

		$response['Success'] = $this->Express_model->update_logistics_info($logistics_arrs);

		echo json_encode($response);
	}

	/**
	 * 根据条件获取报告
	 */
	public function get_report_by_condition(){
		$identity_card = $this->input->post('identity_card');
		$phone = $this->input->post('phone');
		$verification_code = $this->input->post('verification_code');
		if ($this->Verification_code_model->check_code($phone, $verification_code, Jys_system_code::VERIFICATION_CODE_PURPOSE_SEARCH_REPORT)){
			$result = $this->Report_model->get_report_by_condition($identity_card, $phone);
		}else{
			$result['success'] = FALSE;
			$result['msg'] = '验证码错误';
		}

		echo json_encode($result);
	}

	/**
	 * 找回密码
	 */
	public function update_password_by_verified_code(){
		$this->form_validation->set_rules('phone', '手机号码', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['regex_match'=>'请填写正确的手机号']);
		$this->form_validation->set_rules('code', '验证码', 'trim|required');
		$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[6]|max_length[256]');

		$res = $this->Common_model->deal_validation_errors();

		if ($res['success']){
			//处理数据
			$phone      = $this->input->post('phone', TRUE);
			$code       = $this->input->post('code', TRUE);
			$password   = $this->input->post('password', TRUE);

			if ($this->Verification_code_model->check_code($phone, $code, Jys_system_code::VERIFICATION_CODE_PURPOSE_FIND_PASSWORD)){
				$data['success'] = $this->jys_db_helper->update_by_condition('user', ['phone'=>$phone], ['password'=>password_hash($password, PASSWORD_DEFAULT)]);
					$data['msg'] = '修改成功';
			}else{
				$data['success'] = FALSE;
				$data['msg'] = '修改失败';
			}
		}else{
			$data['success'] = FALSE;
			$data['msg'] = '输入有错误';
			$data['error'] = $res['msg'];
		}

		echo json_encode($data);
	}

	/**
	 * 填写报告页面
	 */
	public function add_report(){
		$data['title'] = "赛安生物-添加报告";
		$data['js'] = array('YMDClass.mini','template','add_report', 'jquery.validate.min');
		$data['css'] = array('personal_info', 'header');
		$data['main_content'] = 'add_report';
		$data['isset_search'] = FALSE;
		$data['isset_nav'] = FALSE;
        $data['need_gaode_api'] = TRUE;
		$this->load->view('includes/template_view', $data);
	}
}