<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Article_admin.php
 *
 *   Description: 文章管理
 *
 *       Created: 2016-11-22 17:20:17
 *
 *        Author: sunzuosheng
 *
 * =========================================================
 */

class Article_admin extends CI_Controller{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_db_helper']);
        $this->load->model(['Article_model', 'Common_model']);
    }

    /**
     * 分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function paginate($page = 1, $page_size = 10){
        $data = $this->Article_model->paginate($page, $page_size);

        echo json_encode($data);
    }

    /**
     * 添加文章
     */
    public function add()
    {
        //验证表单信息
        $this->form_validation->set_rules('title', '文章标题', 'trim|required|min_length[3]|max_length[250]');
        $this->form_validation->set_rules('abstract', '文章摘要', 'trim|required|min_length[10]|max_length[200]');
        $this->form_validation->set_rules('content', '文章内容', 'trim|required|max_length[65535]');
        $this->form_validation->set_rules('thumbnail_id', '缩略图ID', 'trim|numeric');
        $this->form_validation->set_rules('status_id', '状态', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $post['title']                  = $this->input->post('title', TRUE);
            $post['abstract']               = $this->input->post('abstract', TRUE);
            $post['content']                = htmlspecialchars($this->input->post('content', FALSE));
            $post['thumbnail_id']           = intval($this->input->post('thumbnail_id', TRUE));
            $post['status_id']              = intval($this->input->post('status_id', TRUE));
            $post['create_time']            = date('Y-m-d H:i:s');
            $post['update_time']            = $post['create_time'];

            $data = $this->jys_db_helper->add('article', $post);
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 更新文章
     */
    public function update()
    {
        //验证表单信息
        $this->form_validation->set_rules('id', '文章ID', 'trim|required|numeric');
        $this->form_validation->set_rules('title', '文章标题', 'trim|required|min_length[3]|max_length[250]');
        $this->form_validation->set_rules('abstract', '文章摘要', 'trim|required|min_length[10]|max_length[200]');
        $this->form_validation->set_rules('content', '文章内容', 'trim|required|max_length[65535]');
        $this->form_validation->set_rules('thumbnail_id', '缩略图ID', 'trim|numeric');
        $this->form_validation->set_rules('status_id', '状态', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $id                             = intval($this->input->post('id', TRUE));
            $post['title']                  = $this->input->post('title', TRUE);
            $post['abstract']               = $this->input->post('abstract', TRUE);
            $post['content']                = htmlspecialchars($this->input->post('content', FALSE));
            $post['thumbnail_id']           = intval($this->input->post('thumbnail_id', TRUE));
            $post['status_id']              = intval($this->input->post('status_id', TRUE));
            $post['update_time']            = date('Y-m-d H:i:s');

            if ($this->jys_db_helper->update('article', $id, $post)){
                $data['success'] = TRUE;
                $data['msg'] = '修改成功';
            }else{
                $data['success'] = FALSE;
                $data['msg'] = '修改失败';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除文章
     */
    public function delete()
    {
        $id = intval($this->input->post('id', true));
        $res = $this->jys_db_helper->delete('article', $id);
        if ($res){
            $data['success'] = TRUE;
            $data['msg'] = '删除成功';
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '修改失败';
        }
        echo json_encode($data);
    }

    /**
     * 分页获取日志
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function paginate_log(){
        $page = $this->input->post('page');
        $page_size = $this->input->post('page_size');
        $keyword = $this->input->post('keyword');
        $interface_name = $this->input->post('interface_name');
        $start_create_time = $this->input->post('start_create_time');
        $end_create_time = $this->input->post('end_create_time');

        $result = $this->Article_model->paginate_log($page, $page_size, $keyword, $interface_name, $start_create_time, $end_create_time);

        echo json_encode($result);
    }

}