<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Post.php
 *
 *   Description: 帖子前台控制器
 *
 *       Created: 2017-2-10 15:58:15
 *
 *        Author: sunzusoheng
 *
 * =========================================================
 */
class Post extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Post_model']);
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
        $this->form_validation->set_rules('status_id', '状态', 'trim|required|integer');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $post_bar_id = $this->input->post('post_bar_id', TRUE);
            $title = $this->input->post('title', TRUE);
            $content = $this->input->post('content', FALSE);
            $status_id = $this->input->post('status_id', TRUE);
            $user_id = $_SESSION['user_id'];

            $result = $this->Post_model->add($post_bar_id, $user_id, $title , $content , 0 , $status_id);
        }else {
            $result['msg'] = '参数错误';
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 按条件分页获取帖子
     * @param int $page 页数
     * @param int $page_size 页面大小
     */
    public function paginate($page = 1, $page_size = 10) {
        $result = array('success'=>FALSE, 'msg'=>'查询失败', 'data'=>array(), 'total_page'=>0);
        if (intval($page_size) < 1 || intval($page) < 1) {
            $result['msg'] = '参数错误';
            echo json_encode($result);
            exit;
        }

        $post_bar_id = $this->input->post('post_bar_id', TRUE);

        $result = $this->Post_model->paginate($page, $page_size, $post_bar_id);

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

        $post_id = intval($this->input->post('post_id', TRUE));

        $result = $this->Post_model->paginate_comment($page, $page_size, $post_id);

        echo json_encode($result);
    }

    /**
     * 根据帖子id获取帖子内容
     *
     * @param $id
     * @return array
     */
    public function get_post_by_id($id){
        $user_id = 0;
        if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) > 0) {
            $user_id = intval($_SESSION['user_id']);
        }
        $result = $this->Post_model->get_by_id($id, $user_id);

        echo json_encode($result);
    }

    /**
     * 收藏帖子接口
     */
    public function collect_post() {
        $result = array('success'=>FALSE, 'msg'=>'收藏帖子失败');
        $post_id = $this->input->post('post_id', TRUE);

        if (intval($post_id) < 1) {
            $result['msg'] = '请选择要收藏的帖子';
            echo json_encode($result);
            exit;
        }

        $result = $this->Post_model->collect_post($_SESSION['user_id'], $post_id);
        echo json_encode($result);
    }

    /**
     * 取消收藏帖子接口
     */
    public function cancel_collect_post() {
        $result = array('success'=>FALSE, 'msg'=>'取消收藏帖子失败');
        $post_id = $this->input->post('post_id', TRUE);

        if (intval($post_id) < 1) {
            $result['msg'] = '请选择要收藏的帖子';
            echo json_encode($result);
            exit;
        }

        if ($this->jys_db_helper->delete_by_condition('collect_post', ['user_id'=>$_SESSION['user_id'], 'post_id'=>$post_id])) {
            $result['success'] = TRUE;
            $result['msg'] = '取消收藏成功';
        }

        echo json_encode($result);
    }

    /**
     * 分页获取当前用户收藏的帖子
     *
     * @param int $page
     * @param int $page_size
     */
    public function paginate_collect_post($page = 1, $page_size = 10) {
        if ($this->input->post('user_id')){
            $user_id = $this  ->input->post('user_id', TRUE);
        }else if (!$this->input->post('user_id') && $_SESSION['user_id']){
            $user_id = $_SESSION['user_id'];
        }else{
            $user_id = 0;
        }
        $result = $this->Post_model->paginate_collect_post($page, $page_size, $user_id);

        echo json_encode($result);
    }

    /**
     * 浏览帖子，浏览量自增1
     */
    public function view_increment(){
        $id = $this->input->post('id', TRUE);
        $result = $this->Post_model->view_increment($id);

        echo json_encode($result);
    }
}