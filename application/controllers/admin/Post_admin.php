<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Post_admin.php
 *
 *   Description: 帖子管理
 *
 *       Created: 2017-2-8 14:38:59
 *
 *        Author: wuhaohua
 *
 * =========================================================
 */

class Post_admin extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_db_helper', 'Jys_attachment']);
        $this->load->model(['Post_model', 'Common_model']);
    }

    /**
     * 按条件分页获取帖子
     * @param int $page 页数
     * @param int $page_size 页面大小
     */
    public function paginate($page = 1, $page_size = 10) {
        $result = array('success'=>FALSE, 'msg'=>'查询失败', 'data'=>array(), 'total_page'=>0);
        $post_bar_id = $this->input->post('post_bar_id', TRUE);
        $keyword = $this->input->post('keyword', TRUE);
        if (intval($page_size) < 1 || intval($page) < 1) {
            $result['msg'] = '参数错误';
            echo json_encode($result);
            exit;
        }


        $result = $this->Post_model->paginate($page, $page_size, $post_bar_id, [], $keyword);

        echo json_encode($result);
    }

    /**
     * 新增帖子接口
     */
    public function add() {
        $result = array('success'=>FALSE, 'msg'=>'添加失败');
        //验证表单信息
        $this->form_validation->set_rules('post_bar_id', '贴吧ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('title', '标题', 'trim|required');
        $this->form_validation->set_rules('content', '内容', 'trim|required');
        $this->form_validation->set_rules('is_stickied', '置顶', 'trim|required|in_list[0,1]');
        $this->form_validation->set_rules('status_id', '状态', 'trim|required|integer');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $post_bar_id = $this->input->post('post_bar_id', TRUE);
            $title = $this->input->post('title', TRUE);
            $content = $this->input->post('content', FALSE);
            $is_stickied = $this->input->post('is_stickied', TRUE);
            $status_id = $this->input->post('status_id', TRUE);
            $user_id = $_SESSION['user_id'];

            $result = $this->Post_model->add($post_bar_id, $user_id, $title , $content , $is_stickied , $status_id);
        }else {
            $result['msg'] = '参数错误';
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 更新帖子接口
     */
    public function update() {
        $result = array('success'=>FALSE, 'msg'=>'更新失败');
        //验证表单信息
        $this->form_validation->set_rules('id', '帖子ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('post_bar_id', '贴吧ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('title', '标题', 'trim|required');
        $this->form_validation->set_rules('content', '内容', 'trim|required');
        $this->form_validation->set_rules('is_stickied', '置顶', 'trim|required|in_list[0,1]');
        $this->form_validation->set_rules('status_id', '状态', 'trim|required|integer');


        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $id = $this->input->post('id', TRUE);
            $data['post_bar_id'] = $this->input->post('post_bar_id', TRUE);
            $data['title'] = $this->input->post('title', TRUE);
            $data['content'] = $this->input->post('content', FALSE);
            $data['is_stickied'] = $this->input->post('is_stickied', TRUE);
            $data['status_id'] = $this->input->post('status_id', TRUE);
            if (intval($data['status_id']) == Jys_system_code::POST_STATUS_PUBLISHED) {
                $data['publish_time'] = date('Y-m-d H:i:s');
            }
            $result = $this->Post_model->update($id, $data);
        }else {
            $result['msg'] = '参数错误';
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 置顶帖子
     */
    public function stickied() {
        $result = array('success'=>FALSE, 'msg'=>'更新失败');
        //验证表单信息
        $this->form_validation->set_rules('id', '帖子ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('bar_id', '帖子ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('is_stickied', '置顶', 'trim|required|in_list[0,1]');


        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $id = $this->input->post('id', TRUE);
            $bar_id = $this->input->post('bar_id', TRUE);
            $data['is_stickied'] = $this->input->post('is_stickied', TRUE);
            $this->jys_db_helper->update_by_condition('post', ['post_bar_id'=>$bar_id], ['is_stickied'=>0]);
            $result['success'] = $this->jys_db_helper->update('post', $id, $data);
            $result['msg'] = '置顶成功';
        }else {
            $result['msg'] = '参数错误';
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 删除帖子接口
     */
    public function delete() {
        $result = array('success'=>FALSE, 'msg'=>'删除失败');
        //验证表单信息
        $this->form_validation->set_rules('id', '帖子ID', 'trim|required|is_natural_no_zero');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']) {
            $id = $this->input->post('id', TRUE);

            $result = $this->Post_model->delete($id);
        }else {
            $result['msg'] = '参数错误';
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 添加评论
     */
    public function add_comment(){
        $result = array('success'=>FALSE, 'msg'=>'添加失败');
        //验证表单信息
        $this->form_validation->set_rules('post_id', '贴子ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('content', '内容', 'trim|required');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $post_id = $this->input->post('post_id', TRUE);
            $publisher_id = $_SESSION['user_id'];
            $content = $this->input->post('content', FALSE);
            $root_comment_id = $this->input->post('root_comment_id', TRUE);
            $comment_id = $this->input->post('comment_id', TRUE);
            $to_user_id = $this->input->post('to_user_id', TRUE);

            $result = $this->Post_model->add_comment($post_id, $publisher_id, $root_comment_id, $comment_id, $to_user_id, $content);
        }else {
            $result['msg'] = '参数错误';
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 更新评论
     */
    public function update_comment(){
        $result = array('success'=>FALSE, 'msg'=>'更新失败');
        //验证表单信息
        $this->form_validation->set_rules('id', '评论ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('status_id', '评论状态', 'trim|required');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $id = $this->input->post('id', TRUE);
            $status_id = $this->input->post('status_id', TRUE);

            $result = $this->Post_model->update_comment($id, $status_id);
        }else {
            $result['msg'] = '参数错误';
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 删除评论
     */
    public function delete_comment(){
        $result = array('success'=>FALSE, 'msg'=>'删除失败');
        //验证表单信息
        $this->form_validation->set_rules('id', '评论ID', 'trim|required|is_natural_no_zero');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $id = $this->input->post('id', TRUE);
            $user_id = $_SESSION['user_id'];
            $role_id = $_SESSION['role_id'];

            $result = $this->Post_model->delete_comment($id, $user_id, $role_id);
        }else {
            $result['msg'] = '参数错误';
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 按条件分页获取帖子评论
     * @param int $page 页数
     * @param int $page_size 页面大小
     */
    public function paginate_comment($page = 1, $page_size = 10) {
        $result = array('success'=>FALSE, 'msg'=>'查询失败', 'data'=>array(), 'total_page'=>0);
        if (intval($page_size) < 1 || intval($page) < 1) {
            $result['msg'] = '参数错误';
            echo json_encode($result);
            exit;
        }

        $post_id = intval($this->input->post('post_bar_id', TRUE));
        $keyword = $this->input->post('keyword', TRUE);

        $result = $this->Post_model->paginate_comment($page, $page_size, $post_id, [], $keyword);

        echo json_encode($result);
    }
}