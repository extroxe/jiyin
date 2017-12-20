<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Post_model.php
 *
 *     Description: 帖子模型
 *
 *         Created: 2017-2-8 14:42:44
 *
 *          Author: wuhaohua
 *
 * =====================================================================================
 */

class Post_model extends CI_Model{
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->library(['Jys_mongo']);
    }

    /**
     * 分页获取帖子
     * @param int $page 页数
     * @param int $page_size 页面大小
     * @param int $post_bar_id 贴吧ID
     * @param string $keyword 关键字
     */
    public function paginate($page = 1, $page_size = 10, $post_bar_id = 0, $condition = NULL,  $keyword = "") {
        $result = array('success'=>FALSE, 'msg'=>'查询失败', 'data'=>array(), 'total_page'=>0);
        if (intval($page_size) < 1 || intval($page) < 1) {
            $result['msg'] = '参数错误';
            return $result;
        }

        $this->db->select('
            post.*,
            post_bar.name as post_bar_name,
            user.username,
            user.nickname,
            user.role_id,
            role.name as role_name,
            status.name as status_name
        ');
        if (intval($post_bar_id) > 0) {
            $this->db->where('post.post_bar_id', intval($post_bar_id));
        }

        if (!empty($condition)){
            $this->db->where($condition);
        }

        if ($keyword != "") {
            $this->db->group_start();
            $this->db->like('post.title', $keyword);
            $this->db->or_like('post_bar.name', $keyword);
            $this->db->or_like('user.username', $keyword);
            $this->db->or_like('user.nickname', $keyword);
            $this->db->group_end();
        }
        $this->db->join('post_bar', 'post_bar.id = post.post_bar_id', 'left');
        $this->db->join('user', 'user.id = post.user_id', 'left');
        $this->db->join('system_code as status', "status.value = post.status_id AND status.type = '".jys_system_code::POST_STATUS."'", 'left');
        $this->db->join('system_code as role', "role.value = user.role_id AND role.type = '".jys_system_code::ROLE."'", 'left');
        $this->db->where('post.status_id', Jys_system_code::POST_STATUS_PUBLISHED);
        $this->db->order_by(' post.is_stickied DESC, post.create_time DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $post_list = $this->db->get('post');

        if ($post_list && $post_list->num_rows() > 0) {
            $result['success'] = TRUE;
            $result['msg'] = '获取帖子列表成功';
            $result['data'] = $post_list->result_array();

            foreach($result['data'] as $key => $row){
                $result['data'][$key]['content'] = htmlspecialchars_decode($row['content']);
            }

            $this->db->select('post.id');
            if (intval($post_bar_id) > 0) {
                $this->db->where('post.post_bar_id', intval($post_bar_id));
            }

            if (!empty($condition)){
                $this->db->where($condition);
            }

            if ($keyword != "") {
                $this->db->group_start();
                $this->db->like('post.title', $keyword);
                $this->db->like('post.content', $keyword);
                $this->db->or_like('post_bar.name', $keyword);
                $this->db->or_like('user.username', $keyword);
                $this->db->or_like('user.nickname', $keyword);
                $this->db->group_end();
            }
            $this->db->join('post_bar', 'post_bar.id = post.post_bar_id', 'left');
            $this->db->join('user', 'user.id = post.user_id', 'left');
            $this->db->where('post.status_id', Jys_system_code::POST_STATUS_PUBLISHED);
            $total = $this->db->get('post');
            if ($total && $total->num_rows() > 0) {
                $total_count = $total->num_rows();
                $result['total_page'] = ceil($total_count / $page_size * 1.0);
            }else {
                $result['total_page'] = 1;
            }
        }else {
            $result['msg'] = '没有符合要求的帖子';
        }

        return $result;
    }

    /**
     * 新增帖子
     * @param int $post_bar_id 贴吧ID
     * @param int $user_id 用户ID
     * @param string $title
     * @param string $content
     * @param int $is_stickied
     * @param int $status_id
     */
    public function add($post_bar_id = 0, $user_id = 0, $title = "", $content = "", $is_stickied = 0, $status_id = 1) {
        $result = array('success'=>FALSE, 'msg'=>'添加失败');
        if (intval($post_bar_id) < 1 || intval($user_id) < 1 || empty($title) || empty($content) || intval($status_id) < 1) {
            $result['msg'] = '帖子参数错误';
            return $result;
        }

        $data['post_bar_id'] = intval($post_bar_id);
        $data['user_id'] = intval($user_id);
        $data['title'] = $title;
        $data['content'] = htmlspecialchars($content);
        $data['is_stickied'] = intval($is_stickied);
        $data['status_id'] = intval($status_id);
        $data['create_time'] = date('Y-m-d H:i:s');
        if ($data['status_id'] == Jys_system_code::POST_STATUS_PUBLISHED) {
            $data['publish_time'] = $data['create_time'];
        }
        $this->db->trans_start();
        $result = $this->jys_db_helper->add('post', $data);
        if ($data['status_id'] == Jys_system_code::POST_STATUS_PUBLISHED) {
            // 如果状态为发表，则在贴吧帖子数中加1
            $this->jys_db_helper->set_update('post_bar', $data['post_bar_id'], ['post_count'=> 'post_count + 1'], FALSE);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $result['success'] = FALSE;
            $result['msg'] = '数据库事务执行失败';
        }

        return $result;
    }

    /**
     * 更新帖子
     * @param $id 帖子ID
     * @param $data 更新数据
     */
    public function update($id = 0, $data = array()) {
        $result = array('success'=>FALSE, 'msg'=>'更新失败');
        if (intval($id) < 0 || !is_array($data) || empty($data)) {
            $result['msg'] = '更新参数错误';
            return $result;
        }

        $this->db->trans_start();
        if ($this->jys_db_helper->update('post', $id, $data)) {
            $result['success'] = TRUE;
            $result['msg'] = '更新成功';
        }
        if (isset($data['post_bar_id']) && intval($data['post_bar_id']) > 0) {
            $published_count = $this->jys_db_helper->get_total_num('post', ['post_bar_id' => $data['post_bar_id'], 'status_id'=>Jys_system_code::POST_STATUS_PUBLISHED]);
            $this->jys_db_helper->update('post_bar', $data['post_bar_id'], ['post_count'=>$published_count]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $result['success'] = FALSE;
            $result['msg'] = '数据库事务执行失败';
        }

        return $result;
    }

    /**
     * 删除帖子
     * @param int $id 帖子ID
     */
    public function delete($id = 0) {
        $result = array('success'=>FALSE, 'msg'=>'删除失败');
        if (intval($id) < 1) {
            $result['msg'] = '请选择要删除的帖子';
        }

        $this->db->trans_start();
        if ($this->jys_db_helper->update('post', $id, ['status_id'=>jys_system_code::POST_STATUS_DELETED])) {
            $result['success'] = TRUE;
            $result['msg'] = '删除成功';
        }

        // 在贴吧帖子数中减1
        $post = $this->jys_db_helper->get('post', $id);
        if ($post && isset($post['post_bar_id']) && intval($post['post_bar_id']) > 0) {
            $this->jys_db_helper->set_update('post_bar', $post['post_bar_id'], ['post_count'=> 'post_count - 1'], FALSE);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $result['success'] = FALSE;
            $result['msg'] = '数据库事务执行失败';
        }

        return $result;
    }

    /**
     * 添加评论
     *
     * @param int $post_id 帖子ID
     * @param int $publisher_id 发表人
     * @param int $comment_id 评论ID
     * @param int $to_user_id 评论回复人ID
     * @param null $content 内容
     * @param int $status_id 评论状态
     * @return array
     */
    public function add_comment($post_id = 0, $publisher_id = 0, $root_comment_id = NULL, $comment_id = 0, $to_user_id = 0, $content = NULL, $status_id = Jys_system_code::COMMENT_STATUS_PUBLISHED){
        $result = array('success'=>FALSE, 'msg'=>'发表失败');
        if (intval($post_id) < 1 || intval($publisher_id) < 1 || empty($content) || intval($status_id) < 1){
            $result['msg'] = '评论参数错误';
            return $result;
        }

        $data['post_id']        = intval($post_id);
        $data['publisher_id']   = intval($publisher_id);
        $data['content']        = htmlspecialchars($content);
        $data['status_id']      = intval($status_id);
        $data['create_time']    = date('Y-m-d H:i:s');

        if (!empty($root_comment_id)) {
            $data['root_comment_id'] = intval($root_comment_id);
        }

        if (intval($comment_id) >= 1 && intval($to_user_id) >= 1){
            $data['comment_id'] = $comment_id;
            $data['to_user_id'] = $to_user_id;
        }

        $this->db->trans_start();
        $result = $this->jys_db_helper->add('comment', $data);

        if ($result['success']){
            $this->jys_db_helper->set_update('post', $data['post_id'], ['comment_count'=>'comment_count + 1'], FALSE);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['success'] = FALSE;
            $result['msg'] = '数据库事务执行失败';
        }

        return $result;
    }

    /**
     * 更新评论
     *
     * @param int $id 评论ID
     * @param int $status_id 评论状态
     * @return array
     */
    public function update_comment($id = 0, $status_id = 0){
        $result = array('success'=>FALSE, 'msg'=>'更新失败');
        if (intval($id) < 1 || intval($status_id) < 1){
            $result['msg'] = '评论参数错误';
            return $result;
        }

        $this->db->trans_start();
        
        $comment = $this->jys_db_helper->get('comment', $id);
        
        if ($this->jys_db_helper->update('comment', $id, ['status_id'=>intval($status_id)])) {
            $result['success'] = TRUE;
            $result['msg'] = '更新成功';
        }

        if ($comment['status_id'] != $status_id){
            if ($status_id == Jys_system_code::COMMENT_STATUS_PUBLISHED){
                $this->jys_db_helper->set_update('post', $comment['post_id'], ['comment_count'=>'comment_count + 1'], FALSE);
            }else if ($comment['status_id'] == Jys_system_code::COMMENT_STATUS_PUBLISHED){
                $this->jys_db_helper->set_update('post', $comment['post_id'], ['comment_count'=>'comment_count - 1'], FALSE);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['success'] = FALSE;
            $result['msg'] = '数据库事务执行失败';
        }

        return $result;
    }

    /**
     * 删除评论
     *
     * @param int $id 评论ID
     * @param int $user_id 用户ID
     * @return array
     */
    public function delete_comment($id = 0, $user_id = 0, $role_id = 0){
        $result = array('success'=>FALSE, 'msg'=>'删除失败');
        if (intval($id) < 1) {
            $result['msg'] = '请选择要删除的评论';
            return $result;
        }else if (intval($user_id) < 1){
            $result['msg'] = '请先登录';
            return $result;
        }
        $comment = $this->jys_db_helper->get('comment', $id);

        $this->db->trans_start();
        $delete_condition = [];
        if ($user_id == $comment['publisher_id']){
            $delete_condition['status_id'] = Jys_system_code::COMMENT_STATUS_OWNER_DELETED;
        }else{
            $post = $this->jys_db_helper->get('post', $comment['post_id']);
            if ($user_id == $post['user_id']){
                $delete_condition['status_id'] = Jys_system_code::COMMENT_STATUS_LANDLORD_DELETED;
            }else{
                if (intval($role_id) > 0 && $role_id == Jys_system_code::ROLE_ADMINISTRATOR){
                    $delete_condition['status_id'] = Jys_system_code::COMMENT_STATUS_MANAGER_DELETED;
                }
            }
        }

        if (!empty($delete_condition)){
            if ($this->jys_db_helper->update('comment', $id, $delete_condition)) {
                $result['success'] = TRUE;
                $result['msg'] = '删除成功';
            }

            // 帖子评论数减1
            if ($comment && isset($comment['post_id']) && intval($comment['post_id']) > 0) {
                $this->jys_db_helper->set_update('post', $comment['post_id'], ['comment_count'=>'comment_count - 1'], FALSE);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['success'] = FALSE;
            $result['msg'] = '数据库事务执行失败';
        }

        return $result;
    }

    /**
     * 分页获取帖子评论
     * @param int $page 页数
     * @param int $page_size 页面大小
     * @param int $post_id 贴子ID
     * @param string $keyword 关键字
     */
    public function paginate_comment($page = 1, $page_size = 10, $post_id = 0, $condition = NULL, $keyword = "") {
        $result = array('success'=>FALSE, 'msg'=>'查询失败', 'data'=>array(), 'total_page'=>0);
        if (intval($page_size) < 1 || intval($page) < 1) {
            $result['msg'] = '参数错误';
            return $result;
        }

        $this->db->select('
            comment.*,
            post.title as post_title,
            publisher.username,
            publisher.nickname,
            publisher.role_id,
            attachment.path as avatar_path,
            role.name as role_name,
            status.name as status_name,
            reply_to.content as reply_to_content,
            reply_to.create_time as reply_to_create_time,
            reply_to_status.name as reply_to_status_name,
            to_user.username as to_user_username,
            to_user.nickname as to_user_nickname,
            to_user.role_id as to_user_role_id,
            to_user_role.name as to_user_role_name
        ');
        if (intval($post_id) > 0) {
            $this->db->where('comment.post_id', intval($post_id));
        }

        if (!empty($condition)){
            $this->db->where($condition);
        }

        $this->db->where('comment.comment_id', NULL);
        $this->db->where('comment.to_user_id', NULL);
        $this->db->where('comment.status_id', Jys_system_code::COMMENT_STATUS_PUBLISHED);

        if ($keyword != "") {
            $this->db->group_start();
            $this->db->like('comment.content', $keyword);
            $this->db->or_like('reply_to.content', $keyword);
            $this->db->or_like('publisher.username', $keyword);
            $this->db->or_like('publisher.nickname', $keyword);
            $this->db->group_end();
        }

        $this->db->join('post', 'post.id = comment.post_id', 'left');
        $this->db->join('user as publisher', 'publisher.id = comment.publisher_id', 'left');
        $this->db->join('attachment', 'attachment.id = publisher.avatar', 'left');
        $this->db->join('comment as reply_to', 'reply_to.id = comment.comment_id', 'left');
        $this->db->join('user as to_user', 'to_user.id = comment.to_user_id', 'left');
        $this->db->join('system_code as status', "status.value = comment.status_id AND status.type = '".Jys_system_code::COMMENT_STATUS."'", 'left');
        $this->db->join('system_code as reply_to_status', "reply_to_status.value = reply_to.status_id AND reply_to_status.type = '".Jys_system_code::COMMENT_STATUS."'", 'left');
        $this->db->join('system_code as role', "role.value = publisher.role_id AND role.type = '".Jys_system_code::ROLE."'", 'left');
        $this->db->join('system_code as to_user_role', "to_user_role.value = to_user.role_id AND to_user_role.type = '".Jys_system_code::ROLE."'", 'left');
        $this->db->order_by('comment.create_time', 'ASC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $comment_list = $this->db->get('comment');

        if ($comment_list && $comment_list->num_rows() > 0) {
            $result['success'] = TRUE;
            $result['msg'] = '获取评论列表成功';
            $result['data'] = $this->get_reply($comment_list->result_array(), true);
            $this->db->select('comment.id');
            if (intval($post_id) > 0) {
                $this->db->where('comment.post_id', intval($post_id));
            }

            $this->db->where('comment.comment_id', NULL);
            $this->db->where('comment.to_user_id', NULL);

            if ($keyword != "") {
                $this->db->group_start();
                $this->db->like('comment.content', $keyword);
                $this->db->or_like('reply_to.content', $keyword);
                $this->db->or_like('publisher.username', $keyword);
                $this->db->or_like('publisher.nickname', $keyword);
                $this->db->group_end();
            }

            $this->db->join('comment as reply_to', 'reply_to.id = comment.comment_id', 'left');
            $this->db->join('user as publisher', 'publisher.id = comment.publisher_id', 'left');
            $total = $this->db->get('comment');
            if ($total && $total->num_rows() > 0) {
                $total_count = $total->num_rows();
                $result['total_page'] = ceil($total_count / $page_size * 1.0);
            }else {
                $result['total_page'] = 1;
            }
        }else {
            $result['msg'] = '没有符合要求的评论';
        }

        return $result;
    }

    /**
     * 根据帖子ID获取帖子内容
     *
     * @param $id
     * @return array
     */
    public function get_by_id($id, $user_id = 0){
        $result = array('success'=>FALSE, 'msg'=>'查询失败', 'data'=>array());
        if (empty($id) || intval($id) < 1){
            return $result;
        }

        $this->db->select('post.*,
                           user.nickname,
                           attachment.path as avatar_path');
        $this->db->join('user', 'user.id = post.user_id', 'left');
        $this->db->join('attachment', 'attachment.id = user.avatar', 'left');

        $this->db->where('post.id', $id);

        $res = $this->db->get('post');
        
        if ($res && $res->num_rows() > 0){
            $result['success'] = TRUE;
            $post = $res->row_array();
            if (intval($user_id) > 0) {
                $collect_post = $this->jys_db_helper->get_where('collect_post', ['user_id'=>$user_id, 'post_id'=>$id]);
                if ($collect_post && isset($collect_post['id']) && intval($collect_post['id']) > 0) {
                    $post['collect_post_id'] = $collect_post['id'];
                    $post['is_collected'] = TRUE;
                }else {
                    $post['collect_post_id'] = 0;
                    $post['is_collected'] = FALSE;
                }
            }else {
                $post['collect_post_id'] = 0;
                $post['is_collected'] = FALSE;
            }

            $post['content'] = htmlspecialchars_decode($post['content']);
            $result['data'] = $post;
            $result['msg'] = '查询成功';
        }

        return $result;
    }

    /**
     * 收藏某一篇帖子
     *
     * @param int $user_id 当前用户ID
     * @param int $post_id 要收藏的帖子ID
     */
    public function collect_post($user_id = 0, $post_id = 0) {
        $result = array('success'=>FALSE, 'msg'=>'收藏帖子失败');

        if (intval($user_id) < 1 || intval($post_id) < 1) {
            $result['msg'] = '参数错误';
            return $result;
        }

        $this->db->trans_start();
        $this->jys_db_helper->delete_by_condition('collect_post', ['user_id'=>$user_id, 'post_id'=>$post_id]);
        $add_result = $this->jys_db_helper->add('collect_post', ['user_id'=>$user_id, 'post_id'=>$post_id, 'create_time'=>date('Y-m-d H:i:s')]);
        if ($add_result['success']) {
            $result['success'] = TRUE;
            $result['msg'] = '收藏成功';
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $result['success'] = FALSE;
            $result['msg'] = '收藏失败，事务提交失败';
        }

        return $result;
    }

    /**
     * 分页获取用户收藏的帖子
     *
     * @param int $page
     * @param int $page_size
     * @param int $user_id
     */
    public function paginate_collect_post($page = 1, $page_size = 10, $user_id = 0) {
        $result = array('success'=>FALSE, 'msg'=>'获取收藏列表失败', 'data'=>array(), 'total_page'=>0);
        if (intval($page) < 1 || intval($page_size) < 1 || intval($user_id) < 1) {
            $result['msg'] = '参数错误';
            return $result;
        }

        $this->db->select('
            collect_post.*,
            post.post_bar_id,
            post.title,
            post.is_stickied,
            post.comment_count,
            post.page_view,
            post.publish_time,
            post_bar.name as post_bar_name
        ');
        $this->db->join('post', 'post.id = collect_post.post_id', 'left');
        $this->db->join('post_bar', 'post_bar.id = post.post_bar_id', 'left');
        $this->db->where('collect_post.user_id', $user_id);
        $this->db->where('post.status_id', Jys_system_code::POST_STATUS_PUBLISHED);
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $this->db->order_by('post.is_stickied', 'DESC');
        $this->db->order_by('collect_post.create_time', 'DESC');
        $collect_post = $this->db->get('collect_post');
        if ($collect_post && $collect_post->num_rows() > 0) {
            $result['success'] = TRUE;
            $result['msg'] = '获取收藏列表成功';
            $result['data'] = $collect_post->result_array();

            $this->db->select('collect_post.id');
            $this->db->join('post', 'post.id = collect_post.post_id', 'left');
            $this->db->join('post_bar', 'post_bar.id = post.post_bar_id', 'left');
            $this->db->where('post.status_id', jys_system_code::POST_STATUS_PUBLISHED);
            $this->db->where('collect_post.user_id', $user_id);
            $collect_post_total = $this->db->get('collect_post');
            if ($collect_post_total && $collect_post_total->num_rows() > 0) {
                $total = $collect_post_total->num_rows();
                $result['total_page'] = ceil($total / $page_size * 1.0);
            }else {
                $result['total_page'] = 0;
            }
        }else {
            $result['msg'] = '您未收藏任何帖子';
        }
        return $result;
    }

    /**
     * 根据评论获取相应的回复
     *
     * @param array $comments
     * @return array
     */
    public function get_reply($comments = [], $show_all = false) {
        if (empty($comments) || !is_array($comments)){
            return [];
        }

        $root_comment_ids = [];
        foreach ($comments as $comment) {
            array_push($root_comment_ids, $comment['id']);
        }

        $this->db->select('
            comment.*,
            post.title as post_title,
            publisher.username,
            publisher.nickname,
            publisher.role_id,
            attachment.path as avatar_path,
            role.name as role_name,
            status.name as status_name,
            reply_to.content as reply_to_content,
            reply_to.create_time as reply_to_create_time,
            reply_to_status.name as reply_to_status_name,
            to_user.username as to_user_username,
            to_user.nickname as to_user_nickname,
            to_user.role_id as to_user_role_id,
            to_user_role.name as to_user_role_name
        ');

        $this->db->where('comment.comment_id !=', NULL);
        $this->db->where('comment.to_user_id !=', NULL);
        if (!$show_all){
            $this->db->where('comment.status_id', Jys_system_code::COMMENT_STATUS_PUBLISHED);
        }

        $this->db->where_in('comment.root_comment_id', $root_comment_ids);

        $this->db->join('post', 'post.id = comment.post_id', 'left');
        $this->db->join('user as publisher', 'publisher.id = comment.publisher_id', 'left');
        $this->db->join('attachment', 'attachment.id = publisher.avatar', 'left');
        $this->db->join('comment as reply_to', 'reply_to.id = comment.comment_id', 'left');
        $this->db->join('user as to_user', 'to_user.id = comment.to_user_id', 'left');
        $this->db->join('system_code as status', "status.value = comment.status_id AND status.type = '".Jys_system_code::COMMENT_STATUS."'", 'left');
        $this->db->join('system_code as reply_to_status', "reply_to_status.value = reply_to.status_id AND reply_to_status.type = '".Jys_system_code::COMMENT_STATUS."'", 'left');
        $this->db->join('system_code as role', "role.value = publisher.role_id AND role.type = '".Jys_system_code::ROLE."'", 'left');
        $this->db->join('system_code as to_user_role', "to_user_role.value = to_user.role_id AND to_user_role.type = '".Jys_system_code::ROLE."'", 'left');
        $this->db->order_by('comment.create_time', 'ASC');
        $result = $this->db->get('comment');

        if ($result && $result->num_rows() > 0) {
            $replies = $result->result_array();

            foreach ($root_comment_ids as $root_comment_id) {
                $reply_arr = [];
                foreach ($replies as $reply){
                    if ($reply['root_comment_id'] == $root_comment_id) {
                        array_push($reply_arr, $reply);
                    }
                }
                foreach ($comments as $key => $comment) {
                    if ($comment['id'] == $root_comment_id) {
                        $comments[$key]['replies'] = $reply_arr;
                    }
                }
            }

        }

        return $comments;
    }

    /**
     * 浏览帖子自增1
     */
    public function view_increment($post_id = 0){
        $data['success'] = FALSE;
        $data['msg'] = '操作失败';
        if (intval($post_id) < 1){
            return $data;
        }

        $post_view = $this->jys_mongo->find_one('post_view', ['id'=>$post_id], 'sw-shines-shop');

        if (!empty($post_view)){
            $this->jys_mongo->increment('post_view', ['id'=>$post_id], ['num'=>1], 'sw-shines-shop');
            if ($post_view['num'] % 10 == 0){
                $this->jys_db_helper->update('post', $post_id, ['page_view'=>$post_view['num']]);
            }
            $data['success'] = TRUE;
            $data['msg'] = '操作成功';
        }else{
            $this->jys_mongo->insert('post_view', ['id'=>$post_id, 'num'=>1], 'sw-shines-shop');
        }

        return $data;
    }
}