<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Banner_admin.php
 *     Description: 广告管理控制器
 *         Created: 2016-11-18 15:32:01
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
Class Banner_admin extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation']);
        $this->load->model(['Common_model', 'banner_model']);
    }

    /**
     * 分页获取广告列表
     */
    public function get_page_info() {
        $this->form_validation->set_rules('page', '页数', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('page_size', '页内数据个数', 'trim|required|greater_than[0]');

        $this->Common_model->deal_validation_errors();

        $page = $this->input->post('page');
        $page_size = $this->input->post('page_size');
        $keyword = $this->input->post('keyword');

        $result = $this->banner_model->get_page_info($page, $page_size, $keyword);
        echo json_encode($result);
    }

    /**
     * 添加广告接口
     */
    public function add() {
        $this->form_validation->set_rules('position_id', '位置', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('attachment_id', '图片', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('url', '链接', 'trim');

        $this->Common_model->deal_validation_errors();

        $position_id = $this->input->post('position_id');
        $attachment_id = $this->input->post('attachment_id');
        $agent_id = $this->input->post('agent_id');
        $url = $this->input->post('url');

        $result = $this->banner_model->add($position_id, $attachment_id, $url, $agent_id);
        echo json_encode($result);
    }

    /**
     * 更新广告接口
     */
    public function update() {
        $this->form_validation->set_rules('id', 'ID', 'trim|required|greater_than[0]');

        $this->Common_model->deal_validation_errors();

        $id = $this->input->post('id');
        $position_id = $this->input->post('position_id');
        $attachment_id = $this->input->post('attachment_id');
        $url = $this->input->post('url');

        $result = $this->banner_model->update($id, $position_id, $attachment_id, $url);
        echo json_encode($result);
    }

    /**
     * 删除广告接口
     */
    public function delete() {
        $this->form_validation->set_rules('id', 'ID', 'trim|required|greater_than[0]');

        $this->Common_model->deal_validation_errors();

        $id = $this->input->post('id');

        $result = $this->banner_model->delete($id);
        echo json_encode($result);
    }

}