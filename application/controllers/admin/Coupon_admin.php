<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Coupon_admin.php
 *
 *   Description: 优惠券管理
 *
 *       Created: 2016-11-22 16:12:05
 *
 *        Author: sunzuosheng
 *
 * =========================================================
 */

class Coupon_admin extends CI_Controller{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_db_helper']);
        $this->load->model(['Common_model', 'Discount_coupon_model']);
    }

    /**
     * 分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function paginate($page = 1, $page_size = 10){
        $result = $this->Discount_coupon_model->paginate($page, $page_size);

        echo json_encode($result);
    }

    /**
     * 添加优惠券
     */
    public function add()
    {
        //验证表单信息
        $this->form_validation->set_rules('name', '名称', 'trim');
        $this->form_validation->set_rules('condition', '减免条件', 'trim|required');
        $this->form_validation->set_rules('privilege', '减免金额', 'trim|required|numeric');
        $this->form_validation->set_rules('start_time', '生效起始时间', 'trim|required');
        $this->form_validation->set_rules('end_time', '生效结束时间', 'trim|required');
        $this->form_validation->set_rules('useful_life', '有效期', 'trim|required');
        $this->form_validation->set_rules('status_id', '状态ID', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $post['name']                   = $this->input->post('name', TRUE);
            $post['condition']              = floatval($this->input->post('condition', TRUE));
            $post['privilege']              = floatval($this->input->post('privilege', TRUE));
            $post['start_time']             = $this->input->post('start_time', TRUE);
            $post['end_time']               = $this->input->post('end_time', TRUE);
            $post['useful_life']            = $this->input->post('useful_life', TRUE);
            $post['status_id']              = intval($this->input->post('status_id', TRUE));
            $post['create_time']            = date('Y-m-d H:i:s');

            if (empty($post['name'])){
                $post['name'] = '满'.$post['condition'].'减'.$post['privilege'];
            }

            $data = $this->jys_db_helper->add('discount_coupon', $post);
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 修改优惠券
     */
    public function update()
    {
        //验证表单信息
        $this->form_validation->set_rules('name', '名称', 'trim');
        $this->form_validation->set_rules('id', '优惠券ID', 'trim|required|numeric');
        $this->form_validation->set_rules('condition', '减免条件', 'trim|required');
        $this->form_validation->set_rules('privilege', '减免金额', 'trim|required|numeric');
        $this->form_validation->set_rules('start_time', '生效起始时间', 'trim|required');
        $this->form_validation->set_rules('end_time', '生效结束时间', 'trim|required');
        $this->form_validation->set_rules('useful_life', '有效期', 'trim|required');
        $this->form_validation->set_rules('status_id', '状态ID', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $id                             = intval($this->input->post('id', TRUE));
            $post['name']                   = $this->input->post('name', TRUE);
            $post['condition']              = floatval($this->input->post('condition', TRUE));
            $post['privilege']              = floatval($this->input->post('privilege', TRUE));
            $post['start_time']             = $this->input->post('start_time', TRUE);
            $post['end_time']               = $this->input->post('end_time', TRUE);
            $post['useful_life']            = $this->input->post('useful_life', TRUE);
            $post['status_id']              = intval($this->input->post('status_id', TRUE));
            $post['create_time']            = date('Y-m-d H:i:s');

            if ($this->jys_db_helper->update('discount_coupon', $id, $post)){
                $data['success'] = TRUE;
                $data['msg'] = '更新成功';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除优惠券
     */
    public function delete()
    {
        $id = intval($this->input->post('id', true));
        $res = $this->jys_db_helper->update('discount_coupon', $id, ['status_id'=>0]);

        if ($res){
            $data['success'] = TRUE;
            $data['msg'] = '删除成功';
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '删除失败';
        }
        echo json_encode($data);
    }

    /**
     * 根据优惠券ID获取优惠券发放情况
     */
    public function get_distribution_by_coupon_id() {
        $coupon_id = $this->input->post('id');
        $result = array('success'=>FALSE, 'msg'=>'获取优惠券发放情况失败', 'data'=>array());
        if (intval($coupon_id) < 1) {
            $result['msg'] = '优惠券ID不正确';
            echo json_encode($result);
            exit;
        }

        $result = $this->Discount_coupon_model->get_distribution_by_coupon_id($coupon_id);

        echo json_encode($result);
    }
}