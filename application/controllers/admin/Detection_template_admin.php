<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Detection_template_admin.php
 *
 *   Description: 检测项模板管理
 *
 *       Created: 2017-5-19 11:19:46
 *
 *        Author: liwen
 *
 * =========================================================
 */

class Detection_template_admin extends CI_Controller
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Detection_template_model', 'Common_model']);
    }

    /**
     * 分页获取模板
     */
    public function get_template_by_page() {

        $keyword    = $this->input->post('keyword', TRUE);
        $page       = intval($this->input->post('page', TRUE));
        $page_size  = $this->input->post('page_size', TRUE) ? intval($this->input->post('page_size', TRUE)) : 10;

        $data = $this->Detection_template_model->get_template_by_page($page, $page_size, $keyword);
        if ($data['success']) {
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key]['name'] = $data['data'][$key]['name'].'(最多'.$data['data'][$key]['project_count'].'项)';
            }
        }

        echo json_encode($data);
    }

    /**
     * 获取所有的检测模板
     */
    public function get_detection_template() {
        $result = $this->Detection_template_model->get_detection_template();

        echo json_encode($result);
    }

    /**
     * 添加模板
     */
    public function add_template()
    {

        //验证表单信息
        $this->form_validation->set_rules('name', '模板名称', 'trim|required|min_length[1]|max_length[200]');
        $this->form_validation->set_rules('description', '模板描述', 'trim');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //处理数据
            $post['name']        = $this->input->post('name', TRUE);
            $post['description'] = $this->input->post('description', TRUE);
            $post['hy_template_id'] = $this->input->post('hy_template_id', TRUE);

            $data = $this->Detection_template_model->add_template($post);
        }else{
            $data['success'] = FALSE;
            $data['msg']     = '输入有错误';
            $data['error']   = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 修改模板
     */
    public function update_template() {
        //验证表单信息
        $this->form_validation->set_rules('id', '模板ID', 'trim|required|numeric');
        $this->form_validation->set_rules('name', '模板名称', 'trim|required|min_length[1]|max_length[200]');
        $this->form_validation->set_rules('description', '模板描述', 'trim');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //处理数据
            $id          = intval($this->input->post('id', TRUE));
            $name        = $this->input->post('name', TRUE);
            $description = $this->input->post('description', TRUE);
            $hy_template_id = $this->input->post('hy_template_id', TRUE);

            $data = $this->Detection_template_model->update_template($id, $name, $description, $hy_template_id);
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
    public function delete_template()
    {
        $id = intval($this->input->post('id', true));

        $data = $this->Detection_template_model->delete_template($id);

        echo json_encode($data);
    }
}