<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Refund_admin.php
 *
 *   Description: 退款管理
 *
 *       Created: 2017-1-3 16:18:29
 *
 *        Author: wuhaohua
 *
 * =========================================================
 */
class Refund_admin extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation']);
        $this->load->model(['Order_model']);
    }

    /**
     * 分页获取退款申请以及查询
     */
    public function paginate() {
        $payment_id = $this->input->post('payment_id', TRUE);
        $page = $this->input->post('page', TRUE);
        $page_size = $this->input->post('page_size', TRUE);
        $keywords = $this->input->post('keywords', TRUE);

        $result = $this->Order_model->paginate_for_refund($page, $page_size, $payment_id, $keywords);

        echo json_encode($result);
    }

    /**
     * 审核退款接口
     */
    public function audit_refund() {
        //验证表单信息
        $this->form_validation->set_rules('id', '退款ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('audit_result', '审核结果', 'trim|required|in_list[true,false]');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //处理数据
            $id = $this->input->post('id', TRUE);
            $audit_result = $this->input->post('audit_result', TRUE);

            $data = $this->Order_model->audit_refund($id, $audit_result);
        }else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }
}