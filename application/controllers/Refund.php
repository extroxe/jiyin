<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Refund.php
 *
 *     Description: 用户申请退款
 *
 *         Created: 2017-1-3 16:17:15
 *
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
class Refund extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->library(['Jys_weixin']);
        $this->load->model(['Order_model']);
    }

    /**
     * 获取子订单相关退款数据
     */
    public function get_suborder_refund_info()
    {
        $order_commodity_id = $this->input->post('order_commodity_id', TRUE);

        $result = $this->Order_model->get_suborder_refund_info($order_commodity_id);

        echo json_encode($result);
    }

    /**
     * 用户申请退款接口
     */
    public function application_for_refund() {
        $order_id = $this->input->post('order_id', TRUE);
        $order_commodity_id = $this->input->post('order_commodity_id', TRUE);
        $amount = $this->input->post('amount', TRUE);
        $reason = $this->input->post('reason', TRUE);

        $result = $this->Order_model->application_for_refund($order_commodity_id, $amount, $reason);
        if ($result['success']){
            $user_id = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
            $order = $this->jys_db_helper->get('order', $order_id);
            $user = $this->jys_db_helper->get('user', $user_id);
            $url = site_url('weixin/index/order_detail/'.$order['id']);
            $tm = $this->config->item('wx_tm_order_refund_request');

            $info = [
                'first' => [
                    'value' => '亲爱的'.$user['username'].'，您有笔订单正在申请退款',
                    'color' => '#000000'
                ],
                'keyword1' => [
                    'value' => $order['number'],
                    'color' => '#000000'
                ],
                'keyword2' => [
                    'value' => '¥'.$order['payment_amount'],
                    'color' => '#000000'
                ],
                'keyword3' => [
                    'value' => '退款',
                    'color' => '#000000'
                ],
                'remark' => [
                    'value' => $this->config->item('wx_tm_remarks'),
                    'color' => '#000000'
                ]
            ];
            $this->jys_weixin->send_template_message($user['openid'], $tm, $info, $url);
        }

        echo json_encode($result);
    }
}