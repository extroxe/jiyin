<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Admin_model.php
 *     Description: 管理员模型
 *         Created: 2016-11-12 21:21:16
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
Class Admin_model extends CI_Model{
    public function __construct(){
        parent::__construct();
    }

    /**
     * 根据用户名验证用户(管理员)
     *
     * @param  array   $userinfo 用户填写的账号密码
     * @return array   $data     成功失败信息
     */
    public function varify_users($userinfo){
        $confirm_userinfo = $this->get_userinfo_by_username($userinfo['username']);
        if (empty($confirm_userinfo)){
            $data['msg'] = '用户不存在';
            $data['success'] = FALSE;
        }else{
            if ($userinfo['username'] == $confirm_userinfo['username'] 
                && password_verify($userinfo['password'], $confirm_userinfo['password']) 
                && ($confirm_userinfo['role_id'] == Jys_system_code::ROLE_ADMINISTRATOR
                    OR $confirm_userinfo['role_id'] == jys_system_code::ROLE_AGENT))
            {
                $_SESSION['username'] = $confirm_userinfo['username'];
                $_SESSION['user_id'] = $confirm_userinfo['id'];
                $_SESSION['role_id'] = $confirm_userinfo['role_id'];

                $data['msg'] = '登陆成功!';
                $data['success'] = TRUE;
                $data['user']['username'] = $confirm_userinfo['username'];
            }else{
                $data['msg'] = '用户或密码错误或非管理员登录';
                $data['success'] = FALSE;
            }
        }

        return $data;
    }

    /**
     * 根据用户名获取哟用户信息(管理员)
     * @param $username
     * @return mixed
     */
    public function get_userinfo_by_username($username){
        if (empty($username)) {
            return FALSE;
        }
        $this->db->select('user.*, attachment.path as avatar_path');
        $this->db->join('attachment', 'attachment.id = user.avatar', 'left');
        $this->db->where('user.username', $username);
        $result = $this->db->get('user');
        if ($result && $result->num_rows() > 0) {
            $userinfo = $result->row_array();
            if (isset($userinfo['avatar_path']) && !empty($userinfo['avatar_path'])) {
                $userinfo['avatar_path'] = base_url()."/".$userinfo['avatar_path'];
            }
            return $userinfo;
        }else {
            return FALSE;
        }
    }

    /**
     * 修改密码（管理员）
     * @param $old_password 原密码
     * @param $new_password 新密码
     */
    public function change_password($old_password, $new_password) {
        $result = array('success'=>TRUE, 'msg'=>'密码修改失败');
        if (empty($old_password) || empty($new_password)) {
            $result['msg'] = "新密码及旧密码不能为空";
            return $result;
        }

        if (!is_string($new_password) || strlen($new_password) < 6) {
            $result['msg'] = "新密码不得少于六位";
            return $result;
        }

        $userinfo = $this->get_userinfo_by_username($_SESSION['username']);

        if (empty($userinfo) || !is_array($userinfo) || count($userinfo) < 0) {
            $result['msg'] = "未找到符合要求的用户";
            return $result;
        }

        if ($userinfo['username'] == $_SESSION['username'] && password_verify($old_password, $userinfo['password'])) {
            if ($this->jys_db_helper->update('user', $_SESSION['user_id'], array('password'=>password_hash($new_password, PASSWORD_DEFAULT)))) {
                $result['success'] = TRUE;
                $result['msg'] = "密码修改成功";
            }
        }else {
            $result['msg'] = "原密码不正确";
        }
        return $result;
    }
    
}