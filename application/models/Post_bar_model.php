<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Post_bar_model.php
 *
 *     Description: 贴吧模型
 *
 *         Created: 2017-02-08 14:28:00
 *
 *          Author: Tangyu
 *
 * =====================================================================================
 */
class Post_bar_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }

    /**
     * 获取推荐贴吧
     */
    public function get_recommend_post_bar($num = 0, $is_recommended = 1)
    {
        if (intval($num) < 1){
            return FALSE;
        }
        //获取结果条数
        $this->db->select('post_bar.name, post_bar.id');
        $this->db->where('post_bar.is_recommended', $is_recommended);
        $count = $this->db->count_all_results('post_bar');
        $rand = mt_rand(0, $count);

        $this->db->select('
                post_bar.name, 
                post_bar.id,
                post_bar.post_count,
                post_bar.focus_count,
                post_bar.description
                ');
        $this->db->where('post_bar.is_recommended', $is_recommended);
        //根据rand随机生成数，改变limit条件，实现随机获取
        if ($count - $rand >= $num){
            $this->db->limit($num, $rand);
        }else{
            $this->db->limit($num, $count - $rand);
        }
        $result = $this->db->get('post_bar');
        if ($result && $result->num_rows() > 0){
            return $result->result_array();
        }else{
            return FALSE;
        }
    }

    /**
     * 获取贴吧信息
     */
    public function get_post_bar_by_id($id = 0)
    {
        $data = ['success' => FALSE, 'msg' => '获取失败'];

        if (intval($id) < 1){
            $data['msg'] = '参数错误';
            return $data;
        }
        $this->db->select('post_bar.name,
                           post_bar.post_count,
                           post_bar.description,
                           post_bar.focus_count');
        $this->db->where('post_bar.id', $id);
        $result = $this->db->get('post_bar');

        if ($result && $result->num_rows() > 0){
            $data = ['success' => TRUE, 'msg' => '获取成功', 'data' => $result->row_array()];
        }

        return $data;
    }

    /**
     * 获取、查找贴吧
     */
    public function paginate($page = 1, $page_size = 10, $key_words = '')
    {
        $data = ['success' => FALSE, 'msg' => '获取失败', 'total_page' => 1, 'data' => array()];
        if (intval($page) < 1 || intval($page_size) < 1){
            $data['msg'] = '参数错误';
            return $data;
        }
        $this->db->select(' post_bar.id,
                            post_bar.name,
                            post_bar.description,
                            post_bar.post_count,
                            post_bar.is_recommended,
                            post_bar.create_time,
                            user.username');

        if (!empty($key_words)){
            $this->db->like('post_bar.name', $key_words);
        }
        $this->db->join('user', 'user.id = post_bar.create_by', 'left');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('post_bar');

        if ($result && $result->num_rows() > 0){
            $data['success'] = TRUE;
            $data['data'] = $result->result_array();

            //获取贴吧总分页数
            $this->db->select('post_bar.id');
            if (!empty($key_words)){
                $this->db->like('post_bar.name', $key_words);
            }
            $page_result = $this->db->get('post_bar');
            if ($page_result && $page_result->num_rows() > 0){
                $total = $page_result->num_rows();
                $data['total_page'] = ceil($total / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }else{
            $data['msg'] = '没有贴吧';
        }

        return $data;
    }

    /**
     * 分页获取关注的贴吧
     */
    public function get_focus_post_bar_paginate($page = 1, $page_size = 10, $user_id = 0)
    {
        $data = ['success' => FALSE, 'msg' => '获取失败', 'total_page' => 1, 'data' => array()];
        if (intval($page_size) < 1 || intval($page) < 1 || intval($user_id) < 1){
            $data['msg'] = '参数错误';
            return $data;
        }
        $this->db->select('focused_post_bar.post_bar_id,
                           post_bar.name,
                           post_bar.focus_count,
                           post_bar.post_count');
        $this->db->join('post_bar', 'focused_post_bar.post_bar_id = post_bar.id', 'left');
        $this->db->where('focused_post_bar.user_id', $user_id);
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('focused_post_bar');
        if ($result && $result->num_rows() > 0){
            $data['success'] = TRUE;
            $data['msg'] = '获取成功';
            $data['data'] = $result->result_array();

            $this->db->select('focused_post_bar.id');
            $this->db->where('focused_post_bar.user_id', $user_id);
            $page_result = $this->db->get('focused_post_bar');
            if ($page_result && $page_result->num_rows() > 0){
                $total = $page_result->num_rows();
                $data['total_page'] = ceil($total / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }else{
            $data['msg'] = '暂无更多关注贴吧';
        }

        return $data;
    }
    /**
     * 关注贴吧
     */
    public function focus_post_bar($insert = [])
    {
        if (empty($insert) || !is_array($insert)){
            return FALSE;
        }

        $this->db->trans_start();
        $result = $this->jys_db_helper->add('focused_post_bar', $insert);
        if ($result && $result['success']){
            $res = $this->jys_db_helper->set_update('post_bar', $insert['post_bar_id'], ['focus_count' => 'focus_count + 1'], FALSE);
            if ($res){
                $this->db->trans_complete();
                return TRUE;
            }
        }

        $this->db->trans_rollback();
        return FALSE;
    }

    /**
     * 取消关注
     */
    public function cancel_focus_post_bar($condition = array())
    {
        if (empty($condition) || !is_array($condition)){
            return FALSE;
        }

        $this->db->trans_start();
        if ($this->jys_db_helper->delete_by_condition('focused_post_bar', $condition)){
            if ($this->jys_db_helper->set_update('post_bar', $condition['post_bar_id'], ['focus_count' => 'focus_count - 1'], FALSE)){
                $this->db->trans_complete();
                return TRUE;
            }
        }

        $this->db->rollback();
        return FALSE;
    }

    /**
     * 获取user的发帖信息
     */
    public function get_user_post_info($page = 1, $page_size = 10, $user_id = 0)
    {
        $data = ['success' => FALSE, 'msg' => '获取发帖信息失败', 'data' => NULL, 'total_page' => 0, 'total_post' => 0];
        if (intval($user_id) < 1 || intval($page) < 1 || intval($page_size) < 1){
            return $data;
        }

        $this->db->select('
            post.id,
            post.post_bar_id,
            post.title,
            post.publish_time,
            post.is_stickied,
            post.page_view,
            post.comment_count,
            post_bar.name as bar_name_post
        ');
        $this->db->join('post_bar', 'post.post_bar_id = post_bar.id', 'left');
        $this->db->join('user', 'user.id = post.user_id', 'left');
        $this->db->where('post.user_id', $user_id);
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $this->db->order_by('post.publish_time', 'DESC');
        $result = $this->db->get('post');

        if ($result && $result->num_rows() > 0){
            $data = ['success' => TRUE, 'msg' => '获取发帖信息成功', 'data' => $result->result_array()];

            $this->db->select('post.id');
            $this->db->where('post.user_id', $user_id);
            $page_result = $this->db->get('post');
            if ($page_result && $page_result->num_rows()){
                $data['total_post'] = $page_result->num_rows();
                $data['total_page'] = ceil($data['total_post'] / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }else{
            $data['msg'] = '暂无该用户发帖信息';
        }

        return $data;
    }

    /**
     * 获取用户关注的用户
     */
    public function get_focus_user($page = 1, $page_size = 10, $user_id = 0)
    {
        $data = ['success' => FALSE, 'msg' => '获取关注用户失败', 'data' => NULL, 'total_page' => 0, 'total_focus' => 0];
        if (intval($user_id) < 1 || intval($page) < 1 || intval($page_size) < 1){
            return $data;
        }

        $this->db->select('
            focus_user.focus_id as focus_user_id,
            focus_user.user_id as fans_user_id,
            user.nickname,
            attachment.path as avatar_path
        ');
        $this->db->join('user', 'user.id = focus_user.focus_id', 'left');
        $this->db->join('attachment', 'attachment.id = user.avatar', 'left');
        $this->db->where('focus_user.user_id', $user_id);
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('focus_user');
        if ($result && $result->num_rows() > 0){
            $data = ['success' => TRUE, 'msg' => '获取关注信息成功', 'data' => $result->result_array()];

            $this->db->select('focus_user.id');
            $this->db->where('focus_user.user_id', $user_id);
            $page_result = $this->db->get('focus_user');
            if ($page_result && $page_result->num_rows()){
                $data['total_focus'] = $page_result->num_rows();
                $data['total_page'] = ceil($data['total_focus'] / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }else{
            $data['msg'] = '暂无关注信息';
        }

        return $data;
    }

    /**
     * 获取用户粉丝
     */
    public function get_fans($page = 1, $page_size = 10, $user_id = 0)
    {
        $data = ['success' => FALSE, 'msg' => '获取粉丝信息失败', 'data' => NULL, 'total_page' => 0];
        if (intval($user_id) < 1 || intval($page) < 1 || intval($page_size) < 1){
            return $data;
        }

        $this->db->select('
            focus_user.focus_id as focus_user_id,
            focus_user.user_id as fans_user_id,
            user.nickname,
            attachment.path as avatar_path
        ');
        $this->db->join('user', 'user.id = focus_user.user_id', 'left');
        $this->db->join('attachment', 'attachment.id = user.avatar', 'left');
        $this->db->where('focus_user.focus_id', $user_id);
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('focus_user');
        if ($result && $result->num_rows() > 0){
            $data = ['success' => TRUE, 'msg' => '获取粉丝信息成功', 'data' => $result->result_array()];

            $this->db->select('focus_user.id');
            $this->db->where('focus_user.focus_id', $user_id);
            $page_result = $this->db->get('focus_user');
            if ($page_result && $page_result->num_rows()){
                $data['fans'] = $page_result->num_rows();
                $data['total_page'] = ceil($data['fans'] / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }else{
            $data['msg'] = '暂无关注信息';
        }

        return $data;
    }
}