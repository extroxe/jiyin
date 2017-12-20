<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Detection_project_admin.php
 *
 *   Description: 检测项目管理
 *
 *       Created: 2017-5-19 11:19:46
 *
 *        Author: liwen
 *
 * =========================================================
 */

class Detection_project_admin extends CI_Controller
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Detection_project_model']);
    }

    /*
     * 分页获取检测项目
     */
    public function get_project_by_page() {

        $keyword     = $this->input->post('keyword', TRUE);
        $template_id = intval($this->input->post('template_id', TRUE));
        $page        = intval($this->input->post('page', TRUE)) ? intval($this->input->post('page', TRUE)) : 1;
        $page_size   = $this->input->post('page_size', TRUE) ? intval($this->input->post('page_size', TRUE)) : 10;

        $data = $this->Detection_project_model->get_project_by_page($page, $page_size, $keyword, $template_id);

        echo json_encode($data);
    }

    /*
     * 增加检测项目
     */
    public function add_project()
    {
        //验证表单信息
        $this->form_validation->set_rules('template_id', '模板ID', 'trim|required|numeric');
        $this->form_validation->set_rules('name', '检测项目名称', 'trim|required|min_length[1]|max_length[200]');
        $this->form_validation->set_rules('description', '检测项目描述', 'trim');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //处理数据
            $post['template_id'] = $this->input->post('template_id', TRUE);
            $post['name']        = $this->input->post('name', TRUE);
            $post['description'] = $this->input->post('description', TRUE);
            $post['hy_project_id'] = $this->input->post('hy_project_id', TRUE);

            $data = $this->Detection_project_model->add_project($post);
        }else{
            $data['success'] = FALSE;
            $data['msg']     = '输入有错误';
            $data['error']   = $res['msg'];
        }

        echo json_encode($data);
    }

    /*
     * 修改检测项目
     */
    public function update_project() {
        //验证表单信息
        $this->form_validation->set_rules('id', '检测项目ID', 'trim|required|numeric');
        $this->form_validation->set_rules('template_id', '模板ID', 'trim|required|numeric');
        $this->form_validation->set_rules('name', '检测项目名称', 'trim|required|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('description', '检测项目描述', 'trim');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //处理数据
            $id          = intval($this->input->post('id', TRUE));
            $template_id = intval($this->input->post('template_id', TRUE));
            $name        = $this->input->post('name', TRUE);
            $description = $this->input->post('description', TRUE);
            $hy_project_id = $this->input->post('hy_project_id', TRUE);

            $data = $this->Detection_project_model->update_project($id, $template_id, $name, $description, $hy_project_id);
        }else {
            $data['success'] = FALSE;
            $data['msg']     = '输入有错误';
            $data['error']   = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除模板
     */
    public function delete_project()
    {
        $id = intval($this->input->post('id', true));

        $data = $this->Detection_project_model->delete_project($id);

        echo json_encode($data);
    }
}