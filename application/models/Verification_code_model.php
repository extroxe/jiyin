<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Verification_code_model.php
 *
 *     Description: 验证码模型
 *
 *         Created: 2016-12-7 11:38:54
 *
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
Class Verification_code_model extends CI_Model{
    private $_valid_time = 10;
    /**
     * User_model 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->_valid_time = intval($this->config->item('verification_code_valid_time')) > 0 ? intval($this->config->item('verification_code_valid_time')) : 10;
        $this->load->library(['encrypt', 'jys_message']);
    }

    /**
     * 获取验证码
     * @param $reciver 接受者number
     * @param $purpose 用途
     * @param $user_id 用户ID
     */
    public function get_verification_code($reciver, $purpose = Jys_system_code::VERIFICATION_CODE_PURPOSE_REGISTER, $user_id = NULL) {
        if (empty($reciver) || is_null($purpose)) {
            return FALSE;
        }
        $condition = array('reciver'=>$reciver, 'purpose_id'=>$purpose);

        if (intval($user_id) > 0) {
            $condition['user_id'] = $user_id;
        }
        $this->jys_db_helper->delete_by_condition('verification_code', $condition);

        $code = $this->_generate_code($purpose);
        switch ($purpose){
            case 1:
                $message = '【赛安基因城】您正在进行用户注册操作，验证码为'.$code.'验证码有效期为'.$this->_valid_time.'分钟';
                $res = $this->jys_message->send_message($reciver, $message);
                break;
            case 2:
                $message = '【赛安基因城】您正在进行手机绑定或手机修改密码操作，验证码为'.$code.',验证码有效期为'.$this->_valid_time.'分钟';
                $res = $this->jys_message->send_message($reciver, $message);
                break;
            case 3:
                $res['success'] = $this->_send_email();
                break;
            case 4:
                $message = '【赛安基因城】您正在进行查询报告操作，验证码为'.$code.',验证码有效期为'.$this->_valid_time.'分钟';
                $res = $this->jys_message->send_message($reciver, $message);
                break;
            case 5:
                $message = '【赛安基因城】您正在进行找回密码操作，验证码为'.$code.',验证码有效期为'.$this->_valid_time.'分钟';
                $res = $this->jys_message->send_message($reciver, $message);
                break;
            case 6:
                $message = '【赛安基因城】您正在进行用户登录操作，验证码为'.$code.',验证码有效期为'.$this->_valid_time.'分钟';
                $res = $this->jys_message->send_message($reciver, $message);
                break;
        }
        $insert = array('reciver'=>$reciver, 'user_id'=>$user_id, 'code'=>$code, 'purpose_id'=>$purpose, 'create_time'=>date('Y-m-d H:i:s'));
        if (intval($user_id) > 0) {
            $insert['user_id'] = $user_id;
        }
        $res = json_decode($res, TRUE);
        $result = $this->jys_db_helper->add('verification_code', $insert);
        if ($result['success'] && isset($res['code']) && $res['code']==0) {
            return $code;
        }else if (isset($res['error']) && !empty($res['error'])){
            //若发送短信失败，进行错误日志操作
            $data = date("Y-m-d H:i:s")."短信发送失败，失败错误信息：".$res['error']."\n\n";
            if (file_put_contents(APPPATH."logs\\logs_message_error_logs", $data, FILE_APPEND)){
                return FALSE;
            }
        }else {
            return FALSE;
        }
    }

    /**
     * 校验验证码
     * @param $phone 手机号
     * @param $code 验证码
     * @param $purpose 用途
     * @param null $user_id 用户ID
     */
    public function check_code($phone, $code, $purpose, $user_id = NULL) {
        if (empty($phone) || empty($code) || empty($purpose)) {
            return FALSE;
        }
        $condition = array('reciver'=>$phone, 'code'=>$code, 'purpose_id'=>$purpose);
        if (intval($user_id) > 0) {
            $condition['user_id'] = $user_id;
        }
        $flag = FALSE;
        // 开启事务
        $this->db->trans_start();

        $code = $this->jys_db_helper->get_where('verification_code', $condition);

        if (!empty($code) && is_array($code) && isset($code['create_time'])) {
            $create_time = strtotime($code['create_time']);
            $current_time = time();

            // 验证码有效时间
            if ($current_time - $create_time > (60 * $this->_valid_time)) {
                $flag = FALSE;
            }else {
                $flag = TRUE;
            }
            $this->jys_db_helper->delete_by_condition('verification_code', $condition);
        }

        // 结束事务
        $this->db->trans_complete();

        return $flag;
    }

    /**
     * 生成验证码
     */
    private function _generate_code($purpose = 1) {
        if ($purpose != Jys_system_code::VERIFICATION_CODE_PURPOSE_EMAIL){
            return rand(1000, 9999);
        }else if ($purpose == Jys_system_code::VERIFICATION_CODE_PURPOSE_EMAIL){
            return $this->encrypt->encode(time());
        }
    }

    /**
     * 发送邮箱
     *
     * @param string $email 邮箱账号
     * @param string $content 邮箱内容
     * @return bool
     */
    private function _send_email($email = '', $content = ''){
        return TRUE;
    }
}