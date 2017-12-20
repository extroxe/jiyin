<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =====================================================================================
 *
 *        Filename:  Alipay.php
 *
 *     Description:  支付宝控制器
 *
 *         Created:  2017-11-6 09:34:37
 *
 *          Author:  wuhaohua
 *
 * =====================================================================================
 */
class Alipay extends CI_Controller
{
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->library(['Jys_alipay']);
    }

    /**
     * 申请退款
     */
    public function alipay_refund()
    {
        $result = array('success' => FALSE, 'msg' => '申请退款失败');
        $order_id = $this->input->post('order_id', TRUE);
        $amount = $this->input->post('amount', TRUE);
        $reason = $this->input->post('reason', TRUE);
        $continue_refund = $this->input->post('continue_refund', TRUE);

        $order = $this->jys_db_helper->get('order', $order_id);
        if (empty($order)) {
            $result['msg'] = '订单不存在';
            echo json_encode($result);
            exit;
        }

        if (!empty($continue_refund) && $continue_refund == 1) {
            $out_request_no = $order['out_request_no'];
        } else {
            $out_request_no = '';
        }

        if (empty($amount)) {
            $amount = $order['payment_amount'];
        }

        $refund_res = $this->jys_alipay->alipay_refund($order['number'], $order['payment_order'], $amount, $reason, $out_request_no);
        if (!empty($refund_res) && $refund_res->code == 10000) {
            $result['success'] = TRUE;
            if ($refund_res->fund_change == 'N') {
                $result['msg'] = '申请退款成功，本次退款未发生资金变化';
            } elseif ($refund_res->fund_change == 'Y') {
                $result['msg'] = '申请退款成功，请注意核实账单';
            }
        } else {
            $result['msg'] = $result['msg']."，{$refund_res->sub_msg}";
            file_put_contents(APPPATH . "/logs/alipay_refund", "\n订单编号为{$order['number']}的订单申请退款失败，原因：{$refund_res->msg}\n\n", FILE_APPEND);
        }

        echo json_encode($result);
    }
    /**
     * 支付宝支付后接收支付宝异步通知回调接口
     */
    public function pay_notify()
    {
        $pay_info = $_POST;
        // 订单号
        $out_trade_no = $pay_info['out_trade_no'];
        // 支付号
        $trade_no = $pay_info['trade_no'];
        // 支付状态
        $trade_status = $pay_info['trade_status'];
        $order = $this->jys_db_helper->get_where('order', array('number' => $out_trade_no));
        if (!empty($order)) {
            if ($this->jys_alipay->alipay_notify($pay_info)) {
                if ($trade_status == 'TRADE_SUCCESS') {
                    // 交易成功
                    if ($order['payment_amount'] == $pay_info['total_amount'] && $order['status_id'] == jys_system_code::ORDER_STATUS_NOT_PAID) {
                        $update = array(
                            'status_id' => jys_system_code::ORDER_STATUS_PAID,
                            'payment_order' => $trade_no,
                            'payment_time' => date('Y-m-d H:i:s'),
                            'update_time' => date('Y-m-d H:i:s')
                        );
                        $this->jys_db_helper->update('order', $order['id'], $update);
                    } else {
                        $logs_data = date('Y-m-d H:i:s') . "\n订单编号为：{$out_trade_no}的订单支付成功，但支付金额与订单金额不一致或订单状态不正确，无法修改订单状态为已完成\n\n";
                    }
                } elseif ($trade_status == 'TRADE_FINISHED') {
                    $logs_data = date('Y-m-d H:i:s') . "\n订单编号为：{$out_trade_no}的订单处于已完成状态，无法支付\n\n";
                } elseif ($trade_status == 'TRADE_CLOSED') {
                    $logs_data = date('Y-m-d H:i:s') . "\n订单编号为：{$out_trade_no}的订单处于已关闭状态，无法支付\n\n";
                }

                file_put_contents(APPPATH . "/logs/alipay_" . date('Ymd'), $logs_data, FILE_APPEND);
                echo 'success';
            } else {
                file_put_contents(APPPATH . "/logs/alipay_" . date('Ymd'), date('Y-m-d H:i:s') . "\n订单编号为：{$out_trade_no}的订单支付验签失败\n\n", FILE_APPEND);
                echo 'false';
            }
        } else {
            file_put_contents(APPPATH . "/logs/alipay_" . date('Ymd'), date('Y-m-d H:i:s') . "\n订单编号为：{$out_trade_no}的订单不存在\n\n", FILE_APPEND);
            echo 'false';
        }
    }
}