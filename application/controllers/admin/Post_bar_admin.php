<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Post_bar_admin.php
 *
 *   Description: 贴吧后台控制器
 *
 *       Created: 2017-02-08 11:13:46
 *
 *        Author: Tangyu
 *
 * =========================================================
 */
class Post_bar_admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'jys_db_helper']);
        $this->load->model(['Common_model', 'Post_bar_model']);
    }

    /**
     * 添加贴吧
     */
    public function add()
    {
        //验证表单信息
        $this->form_validation->set_rules('name', '贴吧名称', 'trim|required|min_length[3]|max_length[12]');
        $this->form_validation->set_rules('is_recommended', '是否推荐', 'trim|required|numeric');

        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']){
            $post['name']               = $this->input->post('name', TRUE);
            $post['is_recommended']     = $this->input->post('is_recommended', TRUE);
            $post['create_by']          = $_SESSION['user_id'];
            $post['create_time']        = date('Y-m-d H:i:s');
            $post['update_time']        = $post['create_time'];

            $data = $this->jys_db_helper->add('post_bar', $post);
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 修改贴吧
     */
    public function update()
    {
        $this->form_validation->set_rules('id', '贴吧id', 'trim|required|numeric');
        $this->form_validation->set_rules('name', '贴吧名称', 'trim|required|min_length[3]|max_length[12]');
        $this->form_validation->set_rules('is_recommended', '是否推荐', 'trim|required|numeric');

        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']){
            $id                         = intval($this->input->post('id', TRUE));
            $post['name']               = $this->input->post('name', TRUE);
            $post['is_recommended']     = $this->input->post('is_recommended', TRUE);
            $post['update_time']        = date('Y-m-d H:i:s');

            if ($this->jys_db_helper->update('post_bar', $id, $post)){
                $data['success'] = TRUE;
                $data['msg'] = '更新贴吧信息成功';
            }else{
                $data['success'] = FALSE;
                $data['msg'] = '更新贴吧信息失败';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '更新信息时输入错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除贴吧
     */
    public function delete()
    {
        $id = intval($this->input->post('id', TRUE));

        $bar_info = $this->jys_db_helper->get('post_bar', $id);
        if (!empty($bar_info) && !empty($bar_info['post_count'])){
            $data['success'] = FALSE;
            $data['msg'] = '该贴吧下帖子数量不为0，不能删除';
        }else{
            if ($this->jys_db_helper->delete('post_bar', $id)){
                $data['success'] = TRUE;
                $data['msg'] = '删除贴吧成功';
            }else{
                $data['success'] = FALSE;
                $data['msg'] = '删除贴吧失败';
            }
        }

        echo json_encode($data);
    }
    
    /**
     * 是否推荐开关
     */
    public function is_recommended_switch()
    {
        $id = intval($this->input->post('id', TRUE));
        $bar_info = $this->jys_db_helper->get('post_bar', $id);

        if (!empty($bar_info) && $bar_info['is_recommended'] == 0){
            $bar_info['is_recommended'] = 1;
        }else if (!empty($bar_info) && $bar_info['is_recommended'] == 1){
            $bar_info['is_recommended'] = 0;
        }
        if ($this->jys_db_helper->update('post_bar', $id, $bar_info)){
            $data['success'] = TRUE;
            $data['msg'] = '修改成功';
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '修改失败';
        }

        echo json_encode($data);
    }

    /**
     * 获取、查找贴吧
     */
    public function get_post_bar()
    {
        $page = $this->input->post('page') ? $this->input->post('page') : 1;
        $page_size = $this->input->post('page_size') ? $this->input->post('page_size') : 10;
        $key_words = $this->input->post('key_words') ? $this->input->post('key_words', TRUE) : '';
        $result = $this->Post_bar_model->paginate($page, $page_size, $key_words);

        if ($result['success']){
            $data = [
                'success' => TRUE,
                'total_page' => $result['total_page'],
                'data' => $result['data'],
                'msg' => '获取贴吧列表成功'
            ];
        }else{
            $data = [
                'success' => FALSE,
                'msg' => $result['msg']
            ];
        }

        echo json_encode($data);
    }
}