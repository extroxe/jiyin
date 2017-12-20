<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Post_bar_admin.php
 *
 *   Description: 贴吧前台控制器
 *
 *       Created: 2017-02-09 11:50:46
 *
 *        Author: Tangyu
 *
 * =========================================================
 */
class Post_bar extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['Jys_db_helper']);
        $this->load->model(['Post_bar_model', 'User_model']);
    }

    /**
     * 搜索贴吧
     */
    public function search_post_bar()
    {
        $key_words = $this->input->get('key_words', TRUE);
        if (empty($key_words)){
            $data = [
                'success' => FALSE,
                'msg' => '搜索条件不存在'
            ];
        }else{
            $result = $this->Post_bar_model->paginate(1, 10, $key_words);
            if ($result && $result['success']){
                $data = [
                    'success' => TRUE,
                    'total_page' => $result['total_page'],
                    'data' => $result['data'],
                    'msg' => '搜索贴吧列表成功'
                ];
            }else{
                $data = [
                    'success' => FALSE,
                    'msg' => $result['msg']
                ];
            }
        }

        echo json_encode($data);
    }

    /**
     * 根据贴吧id获取贴吧信息
     */
    public function get_post_bar_by_id($id = 0)
    {
        $result = ['success' => FALSE, 'msg' => '获取失败', 'data' => NULL];
        $id = $this->input->post('id') ? $this->input->post('id', TRUE) : $id;
        if (intval($id) < 1){
            $result['msg'] = '参数错误';
            echo json_encode($result);
            exit();
        }

        $result = $this->Post_bar_model->get_post_bar_by_id($id);
        if ($result){
            if (isset($_SESSION['user_id'])){
                $result['data']['is_focused'] = $this->is_focused($_SESSION['user_id'], $id);
            }else{
                $result['data']['is_focused'] = FALSE;
            }
        }

        echo json_encode($result);
    }

    /**
     * 获取推荐贴吧
     */
    public function get_recommend_post_bar()
    {
        $data = ['success' => FALSE, 'msg' => '获取失败', 'data' => NULL];
        $num = $this->input->post('num') ? $this->input->post('num', TRUE) : 3;
        if (intval($num) < 1){
            $data['msg'] = '参数错误';
            echo json_encode($data);
            exit();
        }

        $result = $this->Post_bar_model->get_recommend_post_bar($num);
        if ($result){
            $data = ['success' => TRUE, 'msg' => '获取成功', 'data' => $result];
            for ($i = 0; $i < count($data['data']); $i++){
                //判断当前用户是否已关注该推荐贴吧
                if (isset($_SESSION['user_id'])){
                    if ($this->is_focused($_SESSION['user_id'], $data['data'][$i]['id'])){
                        $data['data'][$i]['is_focused'] = TRUE;
                    }else{
                        $data['data'][$i]['is_focused'] = FALSE;
                    }
                }
            }
        }

        echo json_encode($data);
    }

    /**
     * 获取关注贴吧
     */
    public function get_focus_post_bar($page = 1, $page_size = 10)
    {
        $user_id = $this->input->post('user_id') ? $this->input->post('user_id', TRUE) : $_SESSION['user_id'];
        $data = ['success' => FALSE, 'msg' => '获取失败', 'total_page' => 0, 'data' => array()];
        if (intval($page_size) < 1 || intval($page) < 1){
            $data['msg'] = '参数错误';
            echo json_encode($data);
            exit();
        }

        $result = $this->Post_bar_model->get_focus_post_bar_paginate($page, $page_size, $user_id);

        echo json_encode($result);
    }

    /**
     * 关注贴吧
     */
    public function focus_post_bar()
    {
        $data = ['success' => FALSE, 'msg' => '关注失败'];
        $post_bar_id = $this->input->post('post_bar_id', TRUE);
        if (intval($post_bar_id) < 1){
            $data['msg'] = '参数错误';
            echo json_encode($data);
            exit;
        }

        $insert['user_id'] = $_SESSION['user_id'];
        $insert['post_bar_id'] = $post_bar_id;
        $insert['create_time'] = date('Y-m-d H:i:s');
        if ($this->Post_bar_model->focus_post_bar($insert)){
            $data = ['success' => TRUE, 'msg' => '关注成功'];
        }

        echo json_encode($data);
    }

    /**
     * 取消关注
     */
    public function cancel_focus_post_bar()
    {
        $data = ['success' => FALSE, 'msg' => '取消关注失败'];
        $post_bar_id = $this->input->post('post_bar_id', TRUE);
        if (intval($post_bar_id) < 1){
            $data['msg'] = '参数错误';
            echo json_encode($data);
            exit;
        }

        $condition = ['user_id' => $_SESSION['user_id'], 'post_bar_id' => $post_bar_id];
        if ($this->Post_bar_model->cancel_focus_post_bar($condition)){
            $data = ['success' => TRUE, 'msg' => '取消关注成功'];
        }

        echo json_encode($data);
    }
    
    /**
     * 判断是否已关注该贴吧
     */
    public function is_focused($user_id = 0, $post_bar_id = 0)
    {
        if (intval($user_id) < 1 || intval($post_bar_id) < 1){
            return FALSE;
        }

        $result = $this->jys_db_helper->get_where('focused_post_bar', array('user_id' => $user_id, 'post_bar_id' => $post_bar_id));
        if ($result){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    /**
     * 根据user_id 获取user的发帖信息
     */
    public function get_user_post_info($page = 1, $page_size = 10)
    {
        if ($this->input->post('user_id')){
            $user_id = $this->input->post('user_id', TRUE);
        }else{
            $user_id = 0;
        }
        $data = ['success' => FALSE, 'msg' => '获取该用户贴吧信息失败', 'data' => NULL];
        if (intval($user_id) < 1 || intval($page) < 1 || intval($page_size) < 1){
            $data['msg'] = '参数错误';
            echo json_encode($data);
            exit;
        }

        $data = $this->Post_bar_model->get_user_post_info($page, $page_size, $user_id);
        echo json_encode($data);
    }

    /**
     * 获取user关注的用户
     */
    public function get_focus_user($page = 1, $page_size = 10, $flag = FALSE)
    {
        $user_id = $this->input->post('user_id') ? $this->input->post('user_id', TRUE) : 0;
        $flag = $this->input->post('flag') ? $this->input->post('flag') : $flag;

        $data = ['success' => FALSE, 'msg' => '获取该用户关注失败', 'data' => NULL];
        if (intval($user_id) < 1 || intval($page) < 1 || intval($page_size) < 1){
            $data['msg'] = '参数错误';
            echo json_encode($data);
            exit;
        }
        
        $data = $this->Post_bar_model->get_focus_user($page, $page_size, $user_id);
        if ($flag){
            //获取关注列表下每一个用户的粉丝数和关注数
            for ($i = 0; $i < count($data['data']); $i++){
                $data['data'][$i]['focus_lists'] = $this->User_model->get_focus_num($data['data'][$i]['focus_user_id']);
                $data['data'][$i]['fans_lists'] = $this->User_model->get_fans_num($data['data'][$i]['focus_user_id']);
            }
            //当前用户已登录时，判断关注列表中的用户是否为当前用户的关注
            if (isset($_SESSION['user_id'])){
                for($i = 0; $i < count($data['data']); $i++){
                    //关注的user_id等于当前用户id时
                    if ($data['data'][$i]['focus_user_id'] == $_SESSION['user_id']){
                        $data['data'][$i]['is_me'] = TRUE;
                    }else{
                        $data['data'][$i]['is_me'] = FALSE;
                        if ($this->jys_db_helper->get_where('focus_user', ['user_id' => $_SESSION['user_id'], 'focus_id' => $data['data'][$i]['focus_user_id']])){
                            $data['data'][$i]['is_focused'] = TRUE;
                        }else{
                            $data['data'][$i]['is_focused'] = FALSE;
                        }
                    }
                }
            }
        }

        echo json_encode($data);
    }

    /**
     * 获取关注user的用户，即粉丝
     */
    public function get_fans($page = 1, $page_size = 10, $flag = FALSE)
    {
        $user_id = $this->input->post('user_id') ? $user_id = $this->input->post('user_id', TRUE) : 0;
        $flag = $this->input->post('flag') ? $this->input->post('flag') : $flag;

        $data = ['success' => FALSE, 'msg' => '获取粉丝信息失败', 'data' => NULL];
        if (intval($user_id) < 1 || intval($page) < 1 || intval($page_size) < 1){
            $data['msg'] = '参数错误';
            echo json_encode($data);
            exit;
        }

        $data = $this->Post_bar_model->get_fans($page, $page_size, $user_id);
        //获取该用户所有的粉丝和关注
        if ($flag){
            for ($i = 0; $i < count($data['data']); $i++){
                $data['data'][$i]['focus_lists'] = $this->User_model->get_focus_num($data['data'][$i]['fans_user_id']);
                $data['data'][$i]['fans_lists'] = $this->User_model->get_fans_num($data['data'][$i]['fans_user_id']);
            }
            //当前用户已登录时，判断粉丝列表中的用户是否为当前用户的关注
            if (isset($_SESSION['user_id'])){
                for($i = 0; $i < count($data['data']); $i++){
                    //关注的user_id等于当前用户id时
                    if ($data['data'][$i]['fans_user_id'] == $_SESSION['user_id']){
                        $data['data'][$i]['is_me'] = TRUE;
                    }else{
                        $data['data'][$i]['is_me'] = FALSE;
                        if ($this->jys_db_helper->get_where('focus_user', ['user_id' => $_SESSION['user_id'], 'focus_id' => $data['data'][$i]['fans_user_id']])){
                            $data['data'][$i]['is_focused'] = TRUE;
                        }else{
                            $data['data'][$i]['is_focused'] = FALSE;
                        }
                    }
                }
            }
        }
        echo json_encode($data);
    }
}