<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Express_model.php
 *
 *     Description: 快递模型
 *
 *         Created: 2016-12-27 15:50:49
 *
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
class Express_model extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->library(['Jys_kdniao', 'Jys_mongo']);
    }

    /**
     * 根据订单ID查询订单发货物流信息
     * @param $order_id 订单ID
     * @return array
     */
    public function show_express_info_by_order_id($order_id, $user_id = 0) {
        $result = array('success'=>FALSE, 'msg'=>'此单无物流信息', 'data'=>array());
        if (intval($order_id) < 1) {
            $result['msg'] = '订单ID或用户信息不正确';
            return $result;
        }

        $this->db->select('order.id,
                       order.number,
                       order.express_number,
                       order.express_company_id,
                       order.delivered_time,
                       order.status_id,
                       order_status.name as status,
                       order.create_time,
                       express_company.name as express_company_name,
                       express_company.code as express_company_code,
                       express_company.trajectory_query');
        $this->db->join('system_code as order_status', 'order_status.value = order.status_id', 'left');
        $this->db->join('express_company', 'express_company.id = order.express_company_id');
        $this->db->where('order.id', $order_id);
        if (intval($user_id) >= 1){
            $this->db->where('order.user_id', $user_id);
        }
        $this->db->where('order_status.type', jys_system_code::ORDER_STATUS);
        $order = $this->db->get('order');

        if ($order && $order->num_rows() > 0) {
            $order = $order->row_array();
            $traces = [];
            $collection = substr($order['create_time'], 0, 4).substr($order['create_time'], 5, 2);
            $logistics_info = $this->jys_mongo->find_one($collection, ['order_number'=>$order['number']], 'sw-shines-shop');
            if (!empty($logistics_info)){
                foreach (json_decode($logistics_info['content'], TRUE) as $key => $row){
                    $traces[$key]['AcceptTime'] = $row['AcceptTime'];
                    $traces[$key]['AcceptStation'] = $row['AcceptStation'];
                }
            }

            if (!empty($traces) && is_array($traces)){
                $result['success'] = TRUE;
                $result['msg'] = '查询成功';
                $express_info['express_company_name'] = $order['express_company_name'];
                $express_info['express_number'] = $order['express_number'];
                $express_info['Traces'] = $traces;
                $result['data'] = $express_info;
            }
        }else {
            $result['msg'] = '未查询到订单信息';
        }

        return $result;
    }

    /**
     * 根据子订单ID查询订单发货物流信息
     * @param $order_id 订单ID
     * @return array
     */
    public function show_express_info_by_order_commodity_id($order_commodity_id) {
        $result = array('success'=>FALSE, 'msg'=>'此单无物流信息', 'data'=>array());
        if (intval($order_commodity_id) < 1) {
            $result['msg'] = '订单ID或用户信息不正确';
            return $result;
        }

        $this->db->select('order_commodity.id,
                       order_commodity.number,
                       order_commodity.express_number,
                       order_commodity.express_company_id,
                       order_commodity.delivered_time,
                       order_commodity.create_time,
                       express_company.name as express_company_name,
                       express_company.code as express_company_code,
                       express_company.trajectory_query');
        $this->db->join('express_company', 'express_company.id = order_commodity.express_company_id');
        $this->db->where('order_commodity.id', $order_commodity_id);
        $order = $this->db->get('order_commodity');

        if ($order && $order->num_rows() > 0) {
            $order = $order->row_array();
            $traces = [];
            $collection = substr($order['create_time'], 0, 4).substr($order['create_time'], 5, 2);
            $logistics_info = $this->jys_mongo->find_one($collection, ['order_number'=>$order['number']], 'sw-shines-shop');
            if (!empty($logistics_info)){
                foreach (json_decode($logistics_info['content'], TRUE) as $key => $row){
                    $traces[$key]['AcceptTime'] = $row['AcceptTime'];
                    $traces[$key]['AcceptStation'] = $row['AcceptStation'];
                }
            }

            if (!empty($traces) && is_array($traces)){
                $result['success'] = TRUE;
                $result['msg'] = '查询成功';
                $express_info['express_company_name'] = $order['express_company_name'];
                $express_info['Traces'] = $traces;
                $result['data'] = $express_info;
            }
        }else {
            $result['msg'] = '未查询到订单信息';
        }

        return $result;
    }

    /**
     * 更新物流信息
     *
     * @param array $logistics
     * @return bool
     */
    public function update_logistics_info($logistics_arrs = []){
        if (empty($logistics_arrs)){
            return FALSE;
        }

        foreach ($logistics_arrs as $logistics){
            $order = $this->jys_db_helper->get_where('order', ['express_number'=>$logistics['LogisticCode']]);

            if (!$order){
                $order = $this->jys_db_helper->get_where('order_commodity', ['express_number'=>$logistics['LogisticCode']]);
            }

            if ($order && is_array($logistics['Traces']) && !empty($logistics['Traces'])) {
                $collection = substr($order['create_time'], 0, 4).substr($order['create_time'], 5, 2);
                $traces = [];
                foreach ($logistics['Traces'] as $key => $trace){
                    $traces[$key]['AcceptStation']   = $trace['AcceptStation'];
                    $traces[$key]['AcceptTime']      = $trace['AcceptTime'];
                }

                $data = [
                    'order_number' => $order['number'],
                    'content' => json_encode($traces),
                    'update_time' => time()
                ];

                if (isset($order['order_id']) && !empty($order['order_id'])){
                    $data['sub_order'] = TRUE;
                }else{
                    $data['sub_order'] = FALSE;
                }

                if (!$this->jys_mongo->upsert($collection, ['order_number'=>$order['number']], $data, 'sw-shines-shop')) {
                    return FALSE;
                }
            }else {
                return FALSE;
            }
        }

        return TRUE;
    }
}