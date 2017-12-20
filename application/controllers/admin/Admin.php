<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Admin.php
 *     Description: 管理端控制器
 *         Created: 2016-11-12 20:20:47
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
Class Admin extends CI_Controller{
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation']);
        $this->load->library('Jys_db_helper');
        $this->load->model(['Common_model', 'Admin_model']);
    }

    /**
     * 系统入口
     */
    public function index(){
        if (isset($_SESSION['username']) && !is_null($_SESSION['username']) 
            && isset($_SESSION['role_id']) 
            && ($_SESSION['role_id'] == jys_system_code::ROLE_ADMINISTRATOR 
                 OR $_SESSION['role_id'] == jys_system_code::ROLE_AGENT))
        {
            $data['user_id'] = $_SESSION['user_id'];
            $data['role_id'] = $_SESSION['role_id'];
            $data['username'] = $_SESSION['username'];
            $this->load->view('admin/index', $data);
        } else {
            $this->load->view('admin/login');
        }
    }

    /**
     * 登录验证
     */
    public function login(){

        //验证用户
        $this->form_validation->set_rules('username', '用户名', 'trim|required|max_length[16]',['required'=>'用户名为必填',
                                            'max_length'=>'用户名最大为16']);
        $this->form_validation->set_rules('password', '密码', 'trim|required|min_length[6]|max_length[30]',['required'=>'密码为必填',
            'min_length'=>'密码最小为6位','max_length'=>'密码最大为16位']);

        //表单验证是否通过
        $result = $this->Common_model->deal_validation_errors();

        if ($result['success']) {
            //处理数据
            $userinfo = $this->input->post();

            $data = $this->Admin_model->varify_users($userinfo);
            $data['user']['id'] = $this->session->user_id;
            $data['user']['role_id'] = $this->session->role_id;
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $result['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 登出
     */
    public function logout(){
        session_unset();
        header("Location:".site_url('/admin/admin'));
    }

    /**
     * 获取当前管理员的个人信息
     */
    public function get_userinfo() {
        $result = array('success'=>FALSE, 'msg'=>'获取管理员信息失败', 'data'=>array());

        if (isset($_SESSION['user_id']) && isset($_SESSION['username']) 
            && isset($_SESSION['role_id']) && intval($_SESSION['user_id']) > 0 
            && ($_SESSION['role_id'] == Jys_system_code::ROLE_ADMINISTRATOR
                OR $_SESSION['role_id'] == Jys_system_code::ROLE_AGENT)) 
        {
            $userinfo = $this->Admin_model->get_userinfo_by_username($_SESSION['username']);
            if (!empty($userinfo) && is_array($userinfo) && count($userinfo) > 0) {
                unset($userinfo['password']);
                $result['success'] = TRUE;
                $result['msg'] = "获取用户信息成功";
                $result['data'] = $userinfo;
            }else {
                $result['msg'] = '当前用户不是管理员，请使用管理员账户登录后操作';
            }
        }
        echo json_encode($result);
    }

    /**
     * 修改密码接口
     */
    public function change_password() {
        // 设置验证规则
        $this->form_validation->set_rules('old_password', '新密码', 'trim|required|min_length[6]|max_length[30]',['required'=>'密码为必填',
            'min_length'=>'密码最小为6位','max_length'=>'密码最大为30位']);
        $this->form_validation->set_rules('new_password', '旧密码', 'trim|required|min_length[6]|max_length[30]',['required'=>'密码为必填',
            'min_length'=>'密码最小为6位','max_length'=>'密码最大为30位']);
        $this->form_validation->set_rules('confirm_password', '确认密码', 'trim|required|matches[new_password]',['required'=>'密码为必填',
            'matches'=>'确认密码与新密码不一致']);

        //表单验证是否通过
        $result = $this->Common_model->deal_validation_errors();

        if ($result['success']) {
            //处理数据
            $old_password = $this->input->post('old_password');
            $new_password = $this->input->post('new_password');
            $confirm_password = $this->input->post('confirm_password');

            $data = $this->Admin_model->change_password($old_password, $new_password);
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $result['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 更新管理员信息接口
     */
    public function update() {
        // 设置验证规则
        $this->form_validation->set_rules('name', '姓名', 'trim|required');
        $this->form_validation->set_rules('gender', '性别', 'trim|required|in_list[0,1]');
        $this->form_validation->set_rules('phone', '手机号码', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['regex_match'=>'请输入正确的手机号码']);
        $this->form_validation->set_rules('email', '电子邮箱', 'trim|regex_match[/^\w+@\w+\..+$/]',['regex_match'=>'请输入正确的电子邮箱地址']);
        $this->form_validation->set_rules('birthday', '出生日期', 'trim|regex_match[/^[1|2]\d{3}-[0|1]\d-[0-3][0-9]$/]',['regex_match'=>'请输入正确的出生日期']);

        //表单验证是否通过
        $result = $this->Common_model->deal_validation_errors();

        if ($result['success']) {
            // 处理数据
            $update['name'] = $this->input->post('name', TRUE);
            $update['gender'] = $this->input->post('gender', TRUE);
            $update['phone'] = $this->input->post('phone', TRUE);
            $email = $this->input->post('email', TRUE);
            $birthday = $this->input->post('birthday', TRUE);

            if (!empty($email)) {
                $update['email'] = $email;
            }
            if (!empty($birthday)) {
                $update['birthday'] = $birthday;
            }

            if ($this->jys_db_helper->update('user', $_SESSION['user_id'], $update)) {
                $data['success'] = TRUE;
                $data['msg'] = '更新管理员信息成功';
            }else {
                $data['success'] = TRUE;
                $data['msg'] = '更新管理员信息失败';
            }
        }else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $result['msg'];
        }

        echo json_encode($data);
    }
}