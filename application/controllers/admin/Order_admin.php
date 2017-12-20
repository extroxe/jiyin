<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =========================================================
 *
 *      Filename: Order_admin.php
 *
 *   Description: 订单管理
 *
 *       Created: 2016-11-23 11:19:46
 *
 *        Author: sunzuosheng
 *
 * =========================================================
 */
class Order_admin extends CI_Controller
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_kdniao', 'Jys_weixin', 'Jys_excel']);
        $this->load->model(['Order_model', 'Express_model', 'Report_model']);
    }

    public function text(){
        // $db1 = $this->load->database('default',TRUE); 
        // $db2 = $this->load->database('additional', TRUE); //本地数据库

        /***************以下是从基因数据库同步过来的数据************************/
        //同步order
        // $page = 1;
        // $page_size = 100;
        // $db1->select('id');
        // $result = $db1->get('order');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // // aa($total_page);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $res = $db1->get('order', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     $response = $db2->insert_batch('order', $data);
        // }
        // aa($response);

        //同步order_commodity
        // $page = 1;
        // $page_size = 100;
        // $db1->select('id');
        // $result = $db1->get('order_commodity');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $res = $db1->get('order_commodity', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     // aa($data);
        //     $response = $db2->insert_batch('order_commodity', $data);
        // }
        // aa($response);

        //同步report 一共300多页数据
        // $page = 1;
        // $page_size = 1000;
        // $db1->select('id');
        // $result = $db1->get('report');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $res = $db1->get('report', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     foreach ($data as $key => $value) {
        //         unset($data[$key]['template_id']);
        //         unset($data[$key]['project_num']);
        //     }
        //     $response = $db2->insert_batch('report', $data);
        // }
        // aa($response);

        //同步user 7页
        // $page = 1;
        // $page_size = 1000;
        // $db1->select('id');
        // $result = $db1->get('user');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // // aa($total_page);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $res = $db1->get('user', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     $response = $db2->insert_batch('user', $data);
        // }
        // aa($response);

        //同步banner
        // $page = 1;
        // $page_size = 100;
        // $db1->select('id');
        // $result = $db1->get('banner');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $res = $db1->get('banner', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     $response = $db2->insert_batch('banner', $data);
        // }
        // aa($response);

        //同步user_agent 7页
        // $page = 1;
        // $page_size = 1000;
        // $db1->select('id');
        // $result = $db1->get('user_agent');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $res = $db1->get('user_agent', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     $response = $db2->insert_batch('user_agent', $data);
        // }
        // aa($response);

        //同步order_commodity_template  300多页
        // $page = 1;
        // $page_size = 1000;
        // $db1->select('id');
        // $result = $db1->get('report');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('order_commodity_id, template_id, project_num, create_time');
        //     $res = $db1->get('report', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     $response = $db2->insert_batch('order_commodity_template', $data);
        // }
        // aa($response);


        /***************以下是从120测试数据库同步过来的数据************************/
        //同步commodity
        // $page = 1;
        // $page_size = 30;
        // $db1->select('id');
        // $db1->where('id > 261');
        // $result = $db1->get('commodity');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $db1->where('id > 261');
        //     $res = $db1->get('commodity', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     $response = $db2->insert_batch('commodity', $data);
        // }
        // aa($response);

        //同步commodity_center
        // $page = 1;
        // $page_size = 100;
        // $db1->select('id');
        // $result = $db1->get('commodity_center');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $res = $db1->get('commodity_center', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     $response = $db2->insert_batch('commodity_center', $data);
        // }
        // aa($response);

        //同步commodity_specification
        // $page = 1;
        // $page_size = 100;
        // $db1->select('id');
        // $db1->where('status_id', 1);
        // $result = $db1->get('commodity_specification');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $db1->where('status_id', 1);
        //     $res = $db1->get('commodity_specification', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     $response = $db2->insert_batch('commodity_specification', $data);
        // }
        // aa($response);

        //同步commodity_specification_template  注意：template_id需要系统更新一下。原系统与本系统的template_id不一致
        // $page = 1;
        // $page_size = 100;
        // $db1->select('id');
        // $db1->where('commodity_id > 261');
        // $result = $db1->get('commodity_specification_template');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $db1->where('commodity_id > 261');
        //     $res = $db1->get('commodity_specification_template', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     // aa($data);
        //     $response = $db2->insert_batch('commodity_specification_template', $data);
        // }
        // aa($response);

        //同步agent_commodity
        // $page = 1;
        // $page_size = 100;
        // $db1->select('id');
        // $result = $db1->get('agent_commodity');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $res = $db1->get('agent_commodity', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     $response = $db2->insert_batch('agent_commodity', $data);
        // }
        // aa($response);

        //同步agent_index
        // $page = 1;
        // $page_size = 100;
        // $db1->select('id');
        // $result = $db1->get('agent_index');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $res = $db1->get('agent_index', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     $response = $db2->insert_batch('agent_index', $data);
        // }
        // aa($response);

        //同步agent_home
        // $page = 1;
        // $page_size = 100;
        // $db1->select('id');
        // $result = $db1->get('agent_home');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // //分页插入
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $res = $db1->get('agent_home', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     $response = $db2->insert_batch('agent_home', $data);
        // }
        // aa($response);
        
    }

    /**
     * 订单管理 查询、获取订单信息
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function paginate($page = 1, $page_size = 10)
    {
        $keyword = $this->input->post('keyword', TRUE);
        $start_create_time = $this->input->post('start_create_time', TRUE);
        $end_create_time = $this->input->post('end_create_time', TRUE);
        $off_line = $this->input->post('off_line', TRUE);
        $order_status = $this->input->post('order_status', TRUE);
        $is_agent = $this->input->post('is_agent', TRUE);
        $is_point = $this->input->post('is_point', TRUE) ? intval($this->input->post('is_point', TRUE)) : 0;
        $role_id = $_SESSION['role_id'];
        $condition = array();
        if (!empty($start_create_time)) {
            $condition['order.create_time >='] = $start_create_time;
        }
        if (!empty($end_create_time)) {
            $condition['order.create_time <='] = $end_create_time;
        }
        if (!empty($order_status)) {
            $condition['order.status_id'] = $order_status;
        }
        if(!empty($off_line)){
            //线下订单
            $condition['order.terminal_type'] = jys_system_code::TERMINAL_TYPE_LINE;
        } else {
            //线上订单
            $condition['order.terminal_type !='] = jys_system_code::TERMINAL_TYPE_LINE;
        }
        if ($_SESSION['role_id'] == jys_system_code::ROLE_AGENT) {
            //代理商用户下单
            $condition['user_agent.agent_id'] = $_SESSION['user_id'];
        }

        $data = $this->Order_model->paginate($page, $page_size, $is_point, $keyword, $condition, $is_agent, $role_id);
        if($data['success']){
            foreach ($data['data'] as $key => $value){
                if($off_line == 1){
                    $data['data'][$key]['sub_order'] = $this->get_off_line_sub_order($value['id']);
                } else {
                    $data['data'][$key]['sub_order'] = $this->get_sub_order($value['id']);
                }
            }
        }
        echo json_encode($data);
    }

    /**
     * 修改订单
     */
    public function update()
    {
        //验证表单信息
        $this->form_validation->set_rules('id', '订单ID', 'trim|required|numeric');
        $this->form_validation->set_rules('number', '订单编号', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('total_price', '订单总价', 'trim|required|numeric');
        $this->form_validation->set_rules('user_discount_coupon_id', '优惠券ID', 'trim|numeric');
        $this->form_validation->set_rules('payment_amount', '实际支付金额', 'trim|numeric');
        $this->form_validation->set_rules('payment_id', '支付方式', 'trim|numeric');
        $this->form_validation->set_rules('payment_order', '支付单号', 'trim|max_length[100]');
        $this->form_validation->set_rules('terminal_type', '终端类型', 'trim|required|numeric');
        $this->form_validation->set_rules('status_id', '订单状态', 'trim|required|numeric');
        $this->form_validation->set_rules('express_company_id', '快递公司ID', 'trim|numeric');
        $this->form_validation->set_rules('express_number', '快递单号', 'trim|numeric');
        $this->form_validation->set_rules('predict_complete_time', '预计完成时间', 'trim|required');
        $this->form_validation->set_rules('payment_time', '支付时间', 'trim');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //处理数据
            $id = intval($this->input->post('id', TRUE));
            $post['number'] = $this->input->post('number', TRUE);
            $post['total_price'] = floatval($this->input->post('total_price', TRUE));
            $user_discount_coupon_id = intval($this->input->post('user_discount_coupon_id', TRUE));
            $post['payment_id'] = intval($this->input->post('payment_id', TRUE));
            $post['payment_order'] = $this->input->post('payment_order', TRUE);
            $post['terminal_type'] = intval($this->input->post('terminal_type', TRUE));
            $post['status_id'] = intval($this->input->post('status_id', TRUE));
            $post['express_company_id'] = $this->input->post('express_company_id', TRUE) ? intval($this->input->post('express_company_id', TRUE)) : NULL;
            $post['express_number'] = $this->input->post('express_number', TRUE);
            $post['predict_complete_time'] = $this->input->post('predict_complete_time', TRUE);

            if ($user_discount_coupon_id > 0) {
                $post['user_discount_coupon_id'] = $user_discount_coupon_id;
            }

            if ($post['status_id'] == jys_system_code::ORDER_STATUS_FINISHED) {
                // 修改订单状态为已完成
                $data = $this->Order_model->finish_order($post['number']);
            } else if ($post['status_id'] == jys_system_code::ORDER_STATUS_REFUNDING) {
                // 修改订单状态为退款中
                $data['success'] = FALSE;
                $data['msg'] = '无法直接修改订单状态为退款中，请使用用户账户提交退款申请';
            } else if ($post['status_id'] == jys_system_code::ORDER_STATUS_REFUNDED) {
                // 修改订单状态为已退款
                $data['success'] = FALSE;
                $data['msg'] = '无法直接修改订单状态为已退款，请在退款管理中进行相关审核操作';
            } else if ($post['status_id'] == jys_system_code::ORDER_STATUS_UNREFUNDED) {
                // 修改订单状态为未退款
                $data['success'] = FALSE;
                $data['msg'] = '无法直接修改订单状态为未退款，请在退款管理中进行相关审核操作';
            } else {
                // 当订单状态为取消时,往erp回传
                // if ($post['status_id'] == jys_system_code::ORDER_STATUS_CANCELED) {
                //     $erp_result = $this->Order_model->cancel_order_to_erp($id);
                // }
                if ($post['status_id'] == jys_system_code::ORDER_STATUS_DELIVERED) {
                    // 修改订单状态为已发货时，需要检查是否填写了物流公司和物流单号
                    if (empty($post['express_company_id']) || empty($post['express_number'])) {
                        echo json_encode([
                            'success' => FALSE,
                            'msg' => '发货请填写物流公司及物流单号'
                        ]);
                        exit;
                    } else {
                        // 订阅物流信息
                        if (!$this->Order_model->subscribe_express_info($post['express_company_id'], $post['express_number'])) {
                            echo json_encode([
                                'success' => FALSE,
                                'msg' => '订阅快递信息失败'
                            ]);
                            exit;
                        }
                    }
                }

                // 除已完成之外的其他状态，都直接更新订单表即可
                if ($this->jys_db_helper->update('order', $id, $post)) {
                    $data['success'] = TRUE;
                    $data['msg'] = '更新成功';

                    $this->Order_model->notify_inform_order_info($id);
                }
            }
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        if ($data['success']) {
            $order = $this->jys_db_helper->get('order', $id);
            $express_company = $this->jys_db_helper->get('express_company', $order['express_company_id']);
            $user = $this->jys_db_helper->get('user', $order['user_id']);

            if ($order['status_id'] == Jys_system_code::ORDER_STATUS_DELIVERED) {
                $url = site_url('weixin/index/logistics_details/' . $order['id']);
                $tm = $this->config->item('wx_tm_order_delivered');
                $info = [
                    'first' => [
                        'value' => '亲，宝贝已经启程了，好想快点来到你身边',
                        'color' => '#000000'
                    ],
                    'keyword1' => [
                        'value' => $order['number'],
                        'color' => '#000000'
                    ],
                    'keyword2' => [
                        'value' => $express_company['name'],
                        'color' => '#000000'
                    ],
                    'keyword3' => [
                        'value' => $post['express_number'],
                        'color' => '#000000'
                    ],
                    'remark' => [
                        'value' => '点击查看完整的物流信息。如有疑问，可于工作日9:00-17:00拨打赛安基因客服热线：400-100-3908，我们将在第一时间为您服务！',
                        'color' => '#000000'
                    ]
                ];
            } else if ($order['status_id'] == Jys_system_code::ORDER_STATUS_PAID) {
                $sub_orders = $this->Order_model->show_sub_order($order['id'])['data'];
                $commodity_name_str = '';
                if (!empty($sub_orders)) {
                    foreach ($sub_orders as $sub_order) {
                        $commodity_name_str .= $sub_order['commodity_name'] . '*' . $sub_order['amount'] . ' ';
                    }
                }

                $url = site_url('weixin/index/order_detail/' . $order['id']);
                $tm = $this->config->item('wx_tm_order_payment_success');

                $info = [
                    'first' => [
                        'value' => '您的订单已支付成功',
                        'color' => '#000000'
                    ],
                    'keyword1' => [
                        'value' => $user['username'],
                        'color' => '#000000'
                    ],
                    'keyword2' => [
                        'value' => $order['number'],
                        'color' => '#000000'
                    ],
                    'keyword3' => [
                        'value' => '¥' . $order['payment_amount'],
                        'color' => '#000000'
                    ],
                    'keyword4' => [
                        'value' => $commodity_name_str,
                        'color' => '#000000'
                    ],
                    'remark' => [
                        'value' => '如有疑问，可于工作日9:00-17:00拨打赛安基因客服热线：400-100-3908，我们将在第一时间为您服务！',
                        'color' => '#000000'
                    ]
                ];
            } else if ($order['status_id'] == Jys_system_code::ORDER_STATUS_REFUNDING) {
                $url = site_url('weixin/index/order_detail/' . $order['id']);
                $tm = $this->config->item('wx_tm_order_refund_request');

                $info = [
                    'first' => [
                        'value' => '亲爱的' . $user['username'] . '，您有笔订单正在申请退款',
                        'color' => '#000000'
                    ],
                    'keyword1' => [
                        'value' => $order['number'],
                        'color' => '#000000'
                    ],
                    'keyword2' => [
                        'value' => '¥' . $order['payment_amount'],
                        'color' => '#000000'
                    ],
                    'keyword3' => [
                        'value' => '退款',
                        'color' => '#000000'
                    ],
                    'remark' => [
                        'value' => '如有疑问，可于工作日9:00-17:00拨打赛安基因客服热线：400-100-3908，我们将在第一时间为您服务！',
                        'color' => '#000000'
                    ]
                ];
            } else if ($order['status_id'] == Jys_system_code::ORDER_STATUS_UNREFUNDED) {
                $sub_orders = $this->Order_model->show_sub_order($order['id'])['data'];
                $commodity_name_str = '';
                if (!empty($sub_orders)) {
                    foreach ($sub_orders as $sub_order) {
                        $commodity_name_str .= $sub_order['commodity_name'] . ' ';
                    }
                }

                $url = site_url('weixin/index/order_detail/' . $order['id']);
                $tm = $this->config->item('wx_tm_order_refund_rejected');

                $info = [
                    'first' => [
                        'value' => '亲爱的' . $user['username'] . '，您的退款申请被商家驳回，可与商家协商沟通。',
                        'color' => '#000000'
                    ],
                    'keyword1' => [
                        'value' => '¥' . $order['payment_amount'],
                        'color' => '#000000'
                    ],
                    'keyword2' => [
                        'value' => $commodity_name_str,
                        'color' => '#000000'
                    ],
                    'keyword3' => [
                        'value' => $order['number'],
                        'color' => '#000000'
                    ],
                    'remark' => [
                        'value' => $this->config->item('wx_tm_remarks'),
                        'color' => '#000000'
                    ]
                ];
            } else if ($order['status_id'] == Jys_system_code::ORDER_STATUS_REFUNDED) {
                $url = site_url('weixin/index/order_detail/' . $order['id']);
                $tm = $this->config->item('wx_tm_order_refund_through');

                $info = [
                    'first' => [
                        'value' => '亲爱的' . $user['username'] . '，您的退款申请已审核通过，订单金额已退回到账户。',
                        'color' => '#000000'
                    ],
                    'keyword1' => [
                        'value' => $order['number'],
                        'color' => '#000000'
                    ],
                    'keyword2' => [
                        'value' => '¥' . $order['payment_amount'],
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
            } else if ($order['status_id'] == Jys_system_code::ORDER_STATUS_FINISHED) {
                $url = site_url('weixin/index/order_detail/' . $order['id']);
                $tm = $this->config->item('wx_tm_order_finished');

                $info = [
                    'first' => [
                        'value' => '亲爱的' . $user['username'] . '，您的订单已完成。',
                        'color' => '#000000'
                    ],
                    'keyword1' => [
                        'value' => $order['number'],
                        'color' => '#000000'
                    ],
                    'keyword2' => [
                        'value' => substr($order['finnished_time'], 0, 16),
                        'color' => '#000000'
                    ],
                    'remark' => [
                        'value' => $this->config->item('wx_tm_remarks'),
                        'color' => '#000000'
                    ]
                ];
            }
            if (!empty($user['openid']) && !empty($tm) && !empty($info) && !empty($url)) {
                $this->jys_weixin->send_template_message($user['openid'], $tm, $info, $url);
            }
        }

        echo json_encode($data);
    }

    /**
     * 订阅快递订单信息
     * @param $express_company_code 快递公司代码
     * @param $express_number 快递单号
     * @param string $order_number 订单号
     * @param string $callback 自定义回调信息
     * @return 订阅成功返回TRUE，订阅失败返回FALSE
     */
    private function _subscribe_express_info($express_company_code, $express_number, $order_number = "", $callback = "")
    {
        if (empty($express_company_code) || empty($express_number)) {
            return FALSE;
        }
        $dist_result = $this->jys_kdniao->dist($express_company_code, $express_number, $order_number, $callback);
        if (!empty($dist_result) && is_array($dist_result) && isset($dist_result['Success']) && $dist_result['Success']) {
            // 订阅快递信息成功
            return TRUE;
        } else {
            // 订阅快递信息失败
            return FALSE;
        }
    }

    /**
     * 修改订单状态
     */
    public function update_status()
    {
        $id = intval($this->input->post('id', TRUE));
        $status_id = intval($this->input->post('status_id', true));
        if ($this->jys_db_helper->update('order', $id, ['status_id' => $status_id])) {
            $data['success'] = TRUE;
            $data['msg'] = '更新成功';
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '更新失败';
        }

        echo json_encode($data);
    }

    /**
     * 修改订单金额
     */
    public function modify_order_amount()
    {
        $order_id = $this->input->post('order_id', TRUE);
        $amount = $this->input->post('modify_amount', TRUE);
        $reason = $this->input->post('modify_reason', TRUE);

        $order = $this->jys_db_helper->get('order', $order_id);
        if (!empty($order)) {
            $update['payment_amount'] = $amount;
            $update['modify_reason'] = $reason;

            if ($this->jys_db_helper->update('order', $order_id, $update)) {
                $result = ['success' => TRUE, 'msg' => '修改订单金额成功'];
            } else {
                $result = ['success' => FALSE, 'msg' => '修改订单金额失败'];
            }
        } else {
            $result = ['success' => FALSE, 'msg' => '修改订单金额失败，订单不存在'];
        }

        echo json_encode($result);
    }

    /**
     * 根据订单ID显示子订单
     */
    public function show_sub_order()
    {
        $order_id = $this->input->post('order_id', TRUE);

        $data = $this->Order_model->show_sub_order($order_id);

        echo json_encode($data);
    }

    /**
     * 根据订单ID查询子订单，拼入主订单中
     */
    public function get_sub_order($order_id)
    {
        $data = $this->Order_model->show_sub_order($order_id);

        return $data['data'];
    }

    /**
     * 根据订单ID查询子订单，拼入主订单中
     */
    public function get_off_line_sub_order($order_id)
    {
        $data = $this->Order_model->show_off_line_sub_order($order_id);

        return $data['data'];
    }

    /**
     * 更新子订单
     */
    public function update_sub_order()
    {
        //验证表单信息
        $this->form_validation->set_rules('id', '子订单ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('order_id', '订单ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('number', '订单编号', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('commodity_id', '商品ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('price', '商品单价', 'trim|required|numeric');
        $this->form_validation->set_rules('amount', '商品数量', 'trim|required|numeric');
        $this->form_validation->set_rules('total_price', '订单总价', 'trim|required|numeric');
        $this->form_validation->set_rules('points', '订单获取积分', 'trim|required|numeric');
        $this->form_validation->set_rules('express_company_id', '快递公司ID', 'trim|numeric');
        $this->form_validation->set_rules('express_number', '快递单号', 'trim|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //处理数据
            $id = intval($this->input->post('id', TRUE));
            $post['order_id'] = intval($this->input->post('order_id', TRUE));
            $post['number'] = $this->input->post('number', TRUE);
            $post['commodity_id'] = intval($this->input->post('commodity_id', TRUE));
            $post['price'] = floatval($this->input->post('price', TRUE));
            $post['amount'] = intval($this->input->post('amount', TRUE));
            $post['total_price'] = floatval($this->input->post('total_price', TRUE));
            $post['points'] = intval($this->input->post('points', TRUE));
            $post['express_company_id'] = $this->input->post('express_company_id', TRUE) ? intval($this->input->post('express_company_id', TRUE)) : NULL;
            $post['express_number'] = $this->input->post('express_number', TRUE);

            if ($this->jys_db_helper->update('order_commodity', $id, $post)) {
                $data['success'] = TRUE;
                $data['msg'] = '更新成功';
            }
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 根据订单ID查询快递信息
     */
    public function show_express_info_by_order_id($order_id)
    {
        $result = array('success' => FALSE, 'msg' => '此单无物流信息', 'data' => array());

        if (empty($order_id) || intval($order_id) < 1) {
            $result['success'] = FALSE;
            $result['msg'] = '订单ID不正确';
            echo json_encode($result);
            exit;
        }

        $result = $this->Express_model->show_express_info_by_order_id($order_id);

        echo json_encode($result);
    }

    /**
     * 根据子订单id获取快递信息
     */
    public function show_express_info_by_order_commodity_id($order_commodity_id)
    {
        $result = array('success' => FALSE, 'msg' => '此单无物流信息', 'data' => array());

        if (empty($order_commodity_id) || intval($order_commodity_id) < 1) {
            $result['success'] = FALSE;
            $result['msg'] = '订单ID不正确';
            echo json_encode($result);
            exit;
        }

        $result = $this->Express_model->show_express_info_by_order_id($order_commodity_id);

        echo json_encode($result);
    }

    /**
     * 查询所有已经付款单还未发货的订单信息
     */
    public function get_paid_order()
    {
        $result = $this->Order_model->get_paid_order();
        echo json_encode($result);
    }

    /**
     * 查询所有线下订单信息
     */
    public function get_off_line_order()
    {
        $result = $this->Order_model->get_off_line_order();
        echo json_encode($result);
    }

    /**
     * 导出订单
     */
    public function download_order()
    {

        $start_create_time = $this->input->get('start_create_time', TRUE);
        $end_create_time = $this->input->get('end_create_time', TRUE);
        $order_status = $this->input->get('order_status', TRUE);
        $keyword = $this->input->get('keyword', TRUE);
        $is_point = $this->input->get('is_point', TRUE);
        $is_agent = $this->input->get('is_agent', TRUE);
        $order_id = $this->input->get('order_id', TRUE);
        $is_online = $this->input->get('is_online', TRUE);
        $order_id_array = explode('_', $order_id);

        $result = $this->Order_model->get_report_info($start_create_time, $end_create_time, $order_status, $keyword, $is_point, $is_agent, $order_id_array, $is_online);
        $header = ['订单编号', '子订单编号', '商品名称', '购买数量', '订单状态', '收件人姓名', '收件人手机号', '收件人地址', '快递公司', '快递单号', '下单终端', '支付方式', '代理商下单', '下单时间'];
        $file_name = 'order_table_' . date('Y-m-d_His');
        if (!$result['success']) {
            $result['data'] = array();
        }

        $this->jys_excel->export_orders_info_list($header, $result['data'], $file_name);
    }

    /**
     * 获取所有的订单状态
     */
    public function get_all_order_status()
    {
        $result = ['success' => FALSE, 'msg' => '获取订单状态列表失败', 'data' => array()];
        $data = $this->jys_db_helper->get_where_multi('system_code', ['type' => 'order_status']);
        if (!empty($data)) {
            $result['success'] = TRUE;
            $result['msg'] = '获取订单状态列表成功';
            $result['data'] = $data;
        }

        echo json_encode($result);
    }

    /**
     * 根据线下订单ID显示子订单商品名称
     */
    public function show_sub_commodity_name()
    {
        $order_id = $this->input->post('order_id', TRUE);

        $data = $this->Order_model->show_sub_commodity_name($order_id);

        echo json_encode($data);
    }

    /**
     * 根据线上订单ID显示子订单商品名称
     */
    public function show_sub_order_commodity()
    {
        $order_id = $this->input->post('order_id', TRUE);

        $data = $this->Order_model->show_sub_order_commodity($order_id);

        echo json_encode($data);
    }

    /**
     * 后台管理员手动取消订单
     */
    public function cancel_order()
    {
        $reason = $this->input->post('reason', TRUE);
        $order_id = $this->input->post('order_id', TRUE);
        $user_id = $_SESSION['user_id'];

        if (empty($reason) || empty($order_id) || empty($user_id)) {
            echo json_encode(array('success' => FALSE, 'msg' => '请检查原因是否输入'));
            exit;
        }

        $this->db->trans_begin();
        $order = $this->jys_db_helper->get('order', $order_id);
        if (!empty($order) && $order['status_id'] == jys_system_code::ORDER_STATUS_NOT_PAID) {
            $add = array(
                'order_id' => $order_id,
                'reason' => $reason,
                'user_id' => $user_id,
                'create_time' => date('Y-m-d H:i:s')
            );
            $this->jys_db_helper->update('order', $order_id, array('status_id' => jys_system_code::ORDER_STATUS_CANCELED));
            if (!empty($order['user_discount_coupon_id'])) {
                //修改用户的优惠券信息
                $this->jys_db_helper->update_by_condition('user_discount_coupon', array('id' => $order['user_discount_coupon_id'], 'user_id' => $order['user_id']), array('status_id' => jys_system_code::USER_DISCOUNT_COUPON_STATUS_UNUSED));
            }
            $data = $this->jys_db_helper->add('cancel_order_record', $add);
        } else {
            $data = array('success' => FALSE, 'msg' => '取消订单失败，未获取到订单消息或该订单状态不允许取消操作');
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        echo json_encode($data);
    }

    /**
     * 后台一键发货
     */
    public function delivery_order()
    {
        $result = array('success' => FALSE, 'msg' => '发货失败');
        $this->form_validation->set_rules('tracking_company', '快递公司', 'trim|required|numeric');
        $this->form_validation->set_rules('tracking_number', '快递编号', 'trim|required');

        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']) {
            $order_id = $this->input->post('order_id', TRUE);
            $tracking_company_id = $this->input->post('tracking_company', TRUE);
            $tracking_number = $this->input->post('tracking_number', TRUE);

            $express_company = $this->jys_db_helper->get('express_company', $tracking_company_id);
            $order = $this->jys_db_helper->get('order', $order_id);
            if (empty($express_company)) {
                $result['msg'] = '快递公司错误';
            } elseif (empty($order)) {
                $result['msg'] = '订单错误';
            } elseif ($order['status_id'] != jys_system_code::ORDER_STATUS_PAID) {
                $result['msg'] = '当前订单状态无法进行发货操作';
            } else {
                $update = array(
                    'express_company_id' => $tracking_company_id,
                    'express_company_name' => $express_company['name'],
                    'express_number' => $tracking_number,
                    'status_id' => Jys_system_code::ORDER_STATUS_DELIVERED,
                    'delivered_time' => date('Y-m-d H:i:s'),
                    'update_time' => date('Y-m-d H:i:s')
                );

                if ($this->jys_db_helper->update('order', $order_id, $update)) {
                    $result['msg'] = '一键发货成功';
                    $result['success'] = TRUE;
                }
            }
        } else {
            $result['msg'] = '输入错误'.$res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 后台修改订单状态
     */
    public function modify_order()
    {
        $result = array('success' => FALSE);
        $order_id = $this->input->post('order_id', TRUE);

        $order = $this->jys_db_helper->get('order', $order_id);
        if (!empty($order)) {
            switch ($order['status_id']) {
                // 付款确认
                case jys_system_code::ORDER_STATUS_NOT_PAID:
                    $update = array('status_id' => jys_system_code::ORDER_STATUS_PAID);
                    $result['msg'] = '订单状态已修改为 确认付款';
                    break;
                // 发货确认
                case jys_system_code::ORDER_STATUS_PAID:
                    if (empty($order['express_company_id']) || empty($order['express_company_name']) || empty($order['express_number'])) {
                        $update = array();
                        $result['msg'] = '该订单尚未填写快递单号以及快递公司信息，暂时不能将订单置为已发货状态';
                    }
                    break;
                // 寄回确认
                case jys_system_code::ORDER_STATUS_DELIVERED:
                    $update = array('status_id' => jys_system_code::ORDER_STATUS_SENT_BACK);
                    $result['msg'] = '订单状态已修改为 已寄回';
                    break;
                // 正在检测确认
                case jys_system_code::ORDER_STATUS_SENT_BACK:
                    $update = array('status_id' => jys_system_code::ORDER_STATUS_ASSAYING);
                    $result['msg'] = '订单状态已修改为 正在检测';
                    break;
                // 订单完成
                case jys_system_code::ORDER_STATUS_ASSAYING:
                    $update = array('status_id' => jys_system_code::ORDER_STATUS_FINISHED);
                    $result['msg'] = '订单状态已修改为 已完成';
                    break;
            }

            if (!empty($update)) {
                $update['update_time'] = date('Y-m-d H:i:s');

                if ($this->jys_db_helper->update('order', $order_id, $update)) {
                    $result['success'] = TRUE;
                } else {
                    $result['msg'] = '订单状态修改失败';
                }
            }
        } else {
            $result['msg'] = '该订单不存在';
        }

        echo json_encode($result);
    }
}