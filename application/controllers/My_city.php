<?php
if (!defined('BASEPATH'))
 exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: My_city.php
 *
 *   Description: 查询报告管理
 *
 *       Created: 2016-11-24 21:14:23
 *
 *        Author: zourui
 *
 * =========================================================
 */

class My_city extends CI_Controller {
 /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->library(['form_validation', 'Jys_db_helper']);
        $this->load->model(['Report_model', 'Common_model', 'Post_model', 'User_model']);
    }

    /**
     * 贴吧
     */
    public function post_bar(){
        $data['title'] = "赛安生物-我的城";
        $data['js'] = array('template','post_bar');
        $data['css'] = array('post_bar');
        $data['main_content'] = 'post_bar';
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $data['post_bar_flag'] = TRUE;
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 搜索结果页
     */
    public function post_bar_search(){
        $data['key_words'] = $this->input->get('key_words');
        /*if (empty($data['key_words'])){
            show_404();
        }*/

        $data['title'] = "赛安生物-搜索结果";
        $data['js'] = array('template','post_bar_search');
        $data['css'] = array('post_bar_search');
        $data['main_content'] = 'post_bar_search';
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $data['post_bar_flag'] = TRUE;
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 帖子
     */
    public function post($id = 0){
        if(empty($id) || intval($id) < 1){
            show_404();
        };

        $data['title'] = "赛安生物-帖子";
        $data['js'] = array('template','post');
        $data['css'] = array('post');
        $data['main_content'] = 'post';
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $data['post_bar_flag'] = TRUE;
        $this->load->view('includes/template_view', $data);
    }
    /**
     * 查看帖子评论及回复
     */
    public function view_post($post_bar_id=0, $post_id = 0){
        if(empty($post_bar_id) || intval($post_bar_id) < 1 || empty($post_id) || intval($post_id) < 1){
            show_404();
        }

        $data['title'] = "赛安生物-查看帖子";
        $data['js'] = array('template','view_post');
        $data['css'] = array('view_post');
        $data['main_content'] = 'view_post';
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $data['post_bar_flag'] = TRUE;
        $this->Post_model->view_increment($post_id);
        $this->load->view('includes/template_view', $data);
    }
    /**
     * 发帖
     */
    public function publish_post($post_bar_id = 0){
        if(empty($post_bar_id) || intval($post_bar_id) < 1){
            show_404();
        };
        $data['title'] = "赛安生物-发帖";
        $data['js'] = array('quill','template','publish_post');
        $data['css'] = array('quill.snow','publish_post');
        $data['main_content'] = 'publish_post';
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $data['post_bar_flag'] = TRUE;
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 我的主页
     */
    public function home($plate = NULL){
        $data['title'] = "赛安生物-我的主页";
        $data['js'] = array('template','home');
        $data['css'] = array('home');
        $data['main_content'] = 'home';
        $data['right_side'] = $plate;
        $data['user'] = $this->User_model->get_user_detail_by_condition(['user.id'=>$_SESSION['user_id']]);
        $data['focus_num'] = intval($this->User_model->get_focus_num($_SESSION['user_id']));
        $data['fans_num'] = intval($this->User_model->get_fans_num($_SESSION['user_id']));
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $data['post_bar_flag'] = TRUE;

        if (!empty($plate)){
            array_push($data['js'], $plate);
            array_push($data['css'], $plate);
        }else{
            array_push($data['js'], 'my_post');
            array_push($data['css'], 'my_post');
            $data['right_side'] = 'my_post';
        }

        $this->load->view('includes/template_view', $data);
    }

    /**
     * 吧友主页
     */
    public function visit($user_id = 0){
        if(empty($user_id) || intval($user_id) < 1){
            show_404();
        };

        if (isset($_SESSION['user_id']) && $user_id == $_SESSION['user_id']){
            $this->home();
        }else{
            $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $user_id]);
            $data['title'] = "赛安生物-".$user_info['nickname']."的贴吧";
            $data['user_info'] = $user_info;
            if (isset($_SESSION['user_id'])){
                if($this->jys_db_helper->get_where('focus_user', ['user_id' => $_SESSION['user_id'], 'focus_id' => $user_id])){
                    $data['is_focused'] = TRUE;
                }else{
                    $data['is_focused'] = FALSE;
                }
            }
            $data['js'] = array('template','visit_post_bar');
            $data['css'] = array('visit_post_bar');
            $data['main_content'] = 'visit_post_bar';
            $data['isset_search'] = FALSE;
            $data['isset_nav'] = FALSE;
            $data['post_bar_flag'] = TRUE;
            $this->load->view('includes/template_view', $data);
        }
    }

    /**
     * 吧友关注的人的列表
     */
    public function focus_lists($user_id = 0){
        if(empty($user_id) || intval($user_id) < 1){
            show_404();
        };
        $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $user_id]);
        $data['title'] = "赛安生物-".$user_info['nickname']."的关注";
        $data['user_info'] = $user_info;
        $data['js'] = array('template','visit_focus_list');
        $data['css'] = array('visit_focus_list');
        $data['main_content'] = 'visit_focus_list';
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $data['post_bar_flag'] = TRUE;
        $this->load->view('includes/template_view', $data);
    }
    /**
     * 吧有粉丝列表
     */
    public function follow_lists($user_id = 0){
        if(empty($user_id) || intval($user_id) < 1){
            show_404();
        };
        $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $user_id]);
        $data['title'] = "赛安生物-".$user_info['nickname']."的粉丝";
        $data['user_info'] = $user_info;
        $data['js'] = array('template','visit_follows_list');
        $data['css'] = array('visit_follows_list');
        $data['main_content'] = 'visit_follows_list';
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $data['post_bar_flag'] = TRUE;
        $this->load->view('includes/template_view', $data);
    }


    /**
     * 查询报告接口
     */
    public function get_report() {
        $this->form_validation->set_rules('phone', '手机号', 'regex_match[/^1(3|4|5|7|8)\d{9}$/]');
        $this->form_validation->set_rules('identity_card', '身份证号', 'regex_match[/^\d{5}[0-9xX]$/]');

        $res = $this->Common_model->deal_validation_errors();
        
        if ($res['success']) {
            $identity_card = strtoupper($this->input->post('identity_card'));
            $phone = $this->input->post('phone');
            $data = $this->Report_model->get_report_by_phone($identity_card, $phone);
            if (empty($data)) {
                $res['success'] = FALSE;
                $res['msg'] = '未查询到相关用户的报告';
            }else {
                $res['success'] = TRUE;
                $res['msg'] = '查询成功！';
                $this->session->phone = $phone;
                $this->session->identity_card = $identity_card;

            }
        }
        echo json_encode($res);
    }
    
    /**
     * 若提交的数据正确，则直接执行查询并输出
     */
    public function get_report_again() {
        $identity_card = $this->session->identity_card;
        $phone = $this->session->phone;
        $data['result'] = $this->Report_model->get_report_by_phone($identity_card, $phone);
        if (empty($data)) {
            $res['success'] = FALSE;
            $res['msg'] = '未查询到相关用户的报告';
        	echo json_encode($res);
        }else {
            $res['success'] = TRUE;
            $res['msg'] = '查询成功！';
            echo json_encode($res);
        }
    }

    /**
     * 获取用户报告
     */
    public function get_report_by_user_id()
    {
        $data = ['success' => FALSE, 'msg' => '获取检测报告失败', 'data' => NULL];
        $user_id = $_SESSION['user_id'];

        $result = $this->Report_model->get_report_by_user_id($user_id);
        if (!empty($result)){
            $data = ['success' => TRUE, 'msg' => '获取检测报告成功', 'data' => $result];
        }

        echo json_encode($data);
    }

    /**
     * 分页获取报告
     */
    public function get_report_by_page() {
        $user_id = $this->session->user_id;
        $page       = intval($this->input->post('page', TRUE)) ? intval($this->input->post('page', TRUE)) : 1;
        $page_size  = $this->input->post('page_size', TRUE) ? intval($this->input->post('page_size', TRUE)) : 10;

        $data = $this->Report_model->paginate_for_report($page, $page_size, $user_id);

        echo json_encode($data);
    }

    /**
     * 获取我的帖子列表
     *
     * @param int $page
     * @param int $page_size
     */
    public function get_my_post($page = 1, $page_size = 10){
        $result = array('success'=>FALSE, 'msg'=>'查询失败', 'data'=>array(), 'total_page'=>0);
        if (intval($page_size) < 1 || intval($page) < 1) {
            $result['msg'] = '参数错误';
            echo json_encode($result);
            exit;
        }

        $user_id = $_SESSION['user_id'] ? $_SESSION['user_id'] : -1;

        $result = $this->Post_model->paginate($page, $page_size, 0, ['user.id'=>$user_id]);

        echo json_encode($result);
    }
}