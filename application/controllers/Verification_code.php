<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Verification_code.php
 *
 *     Description: 验证码控制器
 *
 *         Created: 2016-12-7 11:31:09
 *
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
class Verification_code extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('verification_code_model');
    }

    /**
     * 注册时获取验证码
     */
    public function get_register_code() {
        $result = array('success'=>FALSE, 'msg'=>'获取验证码失败');
        $this->form_validation->set_rules('phone', '手机号码', 'trim|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['regex_match'=>'请填写正确的手机号']);

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            $phone = $this->input->post('phone');
            $code = $this->verification_code_model->get_verification_code($phone, Jys_system_code::VERIFICATION_CODE_PURPOSE_REGISTER);
            if (!empty($code)) {
                $result['success'] = TRUE;
                $result['msg'] = '获取验证码成功';
            }
        }else {
            $result['success'] = FALSE;
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 手机验证获取验证码
     */
    public function get_verified_phone_code(){
        $result = array('success'=>FALSE, 'msg'=>'获取验证码失败');
        $this->form_validation->set_rules('phone', '手机号码', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['regex_match'=>'请填写正确的手机号']);
        $this->form_validation->set_rules('purpose_id', '验证码目的ID', 'trim');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            $user_id = (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : NULL;
            $phone = $this->input->post('phone');
            $purpose_id = $this->input->post('purpose_id', TRUE) ? $this->input->post('purpose_id', TRUE) : Jys_system_code::VERIFICATION_CODE_PURPOSE_PHONE;
            $code = $this->verification_code_model->get_verification_code($phone, $purpose_id, $user_id);
            if (!empty($code)) {
                $result['success'] = TRUE;
                $result['msg'] = '获取验证码成功';
            }
        }else {
            $result['success'] = FALSE;
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 确认手机绑定验证码
     */
    public function check_verified_phone_code(){
        $this->form_validation->set_rules('phone', '手机号码', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['regex_match'=>'请填写正确的手机号']);
        $this->form_validation->set_rules('code', '验证码', 'trim|required');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //验证是否登录
            if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])){
                echo json_encode([
                    'success' => FALSE,
                    '请先登录'
                ]);
                exit;
            }

            $phone = $this->input->post('phone', TRUE);
            $code = $this->input->post('code', TRUE);


            if ($this->verification_code_model->check_code($phone, $code, jys_system_code::VERIFICATION_CODE_PURPOSE_PHONE, $_SESSION['user_id'])){
                $this->jys_db_helper->update('user', $_SESSION['user_id'], ['phone' => $phone, 'update_time' => date('Y-m-d H:i:s')]);
                $result['success'] = TRUE;
                $result['msg'] = '验证成功';
            }else{
                $result['success'] = FALSE;
                $result['msg'] = '验证失败';
            }
        }else {
            $result['success'] = FALSE;
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 发送验证邮箱
     */
    public function send_verified_email(){
        $this->form_validation->set_rules('email', '邮件', 'trim|regex_match[/^\w+@\w+\..+$/]',['regex_match'=>'邮件格式不符合规则']);

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //验证是否登录
            if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])){
                echo json_encode([
                    'success' => FALSE,
                    '请先登录'
                ]);
                exit;
            }

            $email = $this->input->post('email', TRUE);

            if ($this->verification_code_model->get_verification_code($email, jys_system_code::VERIFICATION_CODE_PURPOSE_EMAIL, $_SESSION['user_id'])){
                $result['success'] = TRUE;
                $result['msg'] = '发送成功';
            }else{
                $result['success'] = FALSE;
                $result['msg'] = '发送失败';
            }
        }else {
            $result['success'] = FALSE;
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     *发送短信接口
     */
    public function send_message()
    {
        $phone = $this->input->POST('phone', TRUE);
        $message = $this->input->POST('message', TRUE);

        $result = $this->verification_code_model->send_message($phone, $message);

        if ($result['success'] && empty($result['FailPhone'])){
            $data = ['success' => TRUE, 'msg' => '发送短信成功'];
        }else{
            $data = ['success' => FALSE, 'msg' => $result['error']];
        }

        echo json_encode($data);
    }

    /**
     * 注册时获取验证码
     */
    public function get_register_code_for_login() {
        $result = array('success'=>FALSE, 'msg'=>'获取验证码失败');
        $this->form_validation->set_rules('phone', '手机号码', 'trim|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['regex_match'=>'请填写正确的手机号']);

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            $phone = $this->input->post('phone');
            $code = $this->verification_code_model->get_verification_code($phone, Jys_system_code::VERIFICATION_CODE_PURPOSE_LOGIN);
            if (!empty($code)) {
                $result['success'] = TRUE;
                $result['msg'] = '获取验证码成功';
            }
        }else {
            $result['success'] = FALSE;
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }
}