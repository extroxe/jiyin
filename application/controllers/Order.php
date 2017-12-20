<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =====================================================================================
 *
 *        Filename: Order.php
 *
 *     Description: 用户订单中心
 *
 *         Created: 2016-11-24 19:19:48
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
class Order extends CI_Controller
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_qrcode', 'Jys_alipay']);
        $this->load->model(['Order_model', 'User_model', 'Commodity_model', 'Discount_coupon_model', 'Express_model', 'Category_model', 'Shopping_cart_model', 'Postage_model']);
    }

    /**
     * 订单结算页面
     */
    public function settlement($ids = NULL, $is_point = 0)
    {
        if (empty($ids)) {
            return FALSE;
        }

        $shopping_cart_ids = explode('-', $ids);
        $user_id = $_SESSION['user_id'];

        $data['title'] = "赛安生物-订单结算";
        $data['js'] = array('template', 'order_settlement');
        $data['need_gaode_api'] = TRUE;
        $data['css'] = array('order_settlement');
        $data['ids'] = $ids;
        $data['main_content'] = 'order_settlement';
        if ($is_point == 1) {
            $data['settlement'] = $this->Commodity_model->get_commodity_by_condition(['commodity.id' => $shopping_cart_ids[0]], TRUE, TRUE)['data'];
        } else {
            $data['settlement'] = $this->Order_model->get_order_settlement($shopping_cart_ids, $user_id);
            $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $_SESSION['user_id']]);
            $data['settlement'] = $this->Commodity_model->calculate_discount_price($data['settlement'], $user_info['price_discount']);
        }
        if (empty($data['settlement'])) {
            show_404();
        }

        $data['settlement'] = $this->Common_model->format_commodity_name($data['settlement']);
        $total_price = 0;
        foreach ($data['settlement'] as $value) {
            if (!empty($value['flash_sale_price'])) {
                $total_price = $total_price + $value['flash_sale_price'] * $value['amount'];
            } else {
                $total_price = $total_price + $value['price'] * $value['amount'];
            }
        }

        $data['discount'] = $this->Discount_coupon_model->get_user_discount_coupon_list_by_user_id($_SESSION['user_id'], jys_system_code::USER_DISCOUNT_COUPON_STATUS_UNUSED, $total_price)['data'];
        $data['payments'] = $this->jys_db_helper->get_where_multi('system_code', ['type' => jys_system_code::PAYMENT]);
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 订单状态页面
     */
    public function status($is_point_flag = 0)
    {
        $data['title'] = "赛安生物-订单状态";
        $data['js'] = array('order_status');
        $data['css'] = array('order_status', 'sign_up');
        $data['main_content'] = 'order_status';
        $data['isset_nav'] = FALSE;
        $data['simple_footer'] = TRUE;
        $data['is_point_flag'] = $is_point_flag;
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 订单列表
     */
    public function order_list()
    {
        $data['title'] = "赛安生物-订单列表";
        $data['js'] = array('template', 'order_list');
        $data['css'] = array('order_list');
        $data['main_content'] = 'order_list';
        $data['isset_search'] = TRUE;
        $data['isset_nav'] = TRUE;
        $data['collection'] = $this->Category_model->get_category();
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 订单详情
     */
    public function detail($order_id = 0)
    {
        if (empty($order_id) || intval($order_id) < 1) {
            show_404();
            return;
        }

        $data['title'] = "赛安生物-订单详情";
        $data['js'] = array('order_detail');
        $data['css'] = array('order_detail');
        $data['main_content'] = 'order_detail';
        $data['isset_nav'] = TRUE;
        $data['collection'] = $this->Category_model->get_category();
        $data['order'] = $this->Order_model->get_order_by_condition(array('order.id' => $order_id, 'order.user_id' => $_SESSION['user_id']))['data'];

        if (empty($data['order'])) {
            show_404();
            return;
        }

        $data['sent_back_flag'] = TRUE;
        $sub_orders = $data['order']['sub_orders'];
        foreach ($sub_orders as $sub_order) {
            if (empty($sub_order['express_company_id']) || empty($sub_order['express_number'])) {
                $data['sent_back_flag'] = FALSE;
            }
        }
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 用户手动取消订单
     */
    public function cancel_order()
    {
        $order_id = $this->input->post('order_id', TRUE);

        $order = $this->jys_db_helper->get('order', $order_id);
        if (empty($order) || $order['status_id'] != jys_system_code::ORDER_STATUS_NOT_PAID) {
            $data = array('success' => FALSE, 'msg' => '该订单状态无法执行取消操作');
        } else {
            $this->db->trans_start();
            $this->jys_db_helper->update('user_discount_coupon', $order['user_discount_coupon_id'], array('status_id' => jys_system_code::USER_DISCOUNT_COUPON_STATUS_UNUSED));
            $this->jys_db_helper->update('order', $order_id, array('status_id' => jys_system_code::ORDER_STATUS_CANCELED));
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $data = array('success' => FALSE, 'msg' => '执行事务失败');
            } else {
                $data = array('success' => TRUE, 'msg' => '取消订单成功');
            }
        }

        echo json_encode($data);
    }

    /**
     * 用户发表评价
     */
    public function evaluation()
    {
        $data['title'] = "赛安生物-用户评价";
        $data['js'] = array('template', 'evaluation');
        $data['css'] = array('evaluation');
        $data['main_content'] = 'evaluation';
        $data['isset_search'] = TRUE;
        $data['isset_nav'] = FALSE;
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 浏览PDF视图
     */
    public function pdf_view($attachment_id = NULL)
    {
        if (empty($attachment_id) || intval($attachment_id) < 1) {
            return FALSE;
        }

        $data['pdf_url'] = $this->Order_model->get_pdf_url($attachment_id);
        $this->load->view('pdf_view', $data);
    }

    /**
     * 分页获取用户订单信息
     */
    public function get_order_by_page($order_status = NULL)
    {
        $user_id['user_id'] = $this->session->user_id;
        $page = intval($this->input->post('page', TRUE));
        $page_size = $this->input->post('page_size', TRUE) ? intval($this->input->post('page_size', TRUE)) : 10;
        if (!empty($order_status)) {
            $order_status = explode("-", $order_status);
        }

        $data = $this->Order_model->paginate_for_user($page, $page_size, $user_id['user_id'], $order_status);
        if ($data['success']) {
            for ($i = 0; $i < count($data['data']); $i++) {
                // 格式化子订单商品名字
                $data['data'][$i]['sub_orders'] = $this->Common_model->format_commodity_name($data['data'][$i]['sub_orders']);
            }
        }

        echo json_encode($data);
    }

    /**
     * 根据订单ID显示子订单
     */
    public function show_sub_order_for_user()
    {
        $order_id = $this->input->post('order_id', TRUE);

        $data = $this->Order_model->show_sub_order($order_id);

        echo json_encode($data);
    }

    public function get_sub_order_by_id()
    {
        $order_commodity_id = $this->input->post('order_commodity_id');

        $result = $this->Order_model->get_sub_order_by_id($order_commodity_id);

        echo json_encode($result);
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

        $result = $this->Express_model->show_express_info_by_order_id($order_id, $_SESSION['user_id']);

        echo json_encode($result);
    }

    /**
     * 用户修改订单完成状态
     */
    public function update_status()
    {
        //验证表单信息
        $this->form_validation->set_rules('status_id', '订单状态', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //处理数据
            $id = intval($this->input->post('id', TRUE));
            $post['status_id'] = intval($this->input->post('status_id', TRUE));

            if ($this->jys_db_helper->update('order', $id, $post)) {
                $data['success'] = TRUE;
                $data['msg'] = '更新订单成功';
            }
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 添加订单
     */
    public function add()
    {
        //验证表单信息
        $this->form_validation->set_rules('address_id', '地址ID', 'trim|required|numeric');
        $this->form_validation->set_rules('user_discount_coupon_id', '用户优惠券ID', 'trim');
        $this->form_validation->set_rules('message', '买家留言', 'trim|max_length[100]');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //处理数据
            $shopping_cart_ids = explode('-', $this->input->post('ids', TRUE));
            $is_point_flag = intval($this->input->post('is_point_flag', TRUE));
            $user_id = $_SESSION['user_id'];
            $user_discount_coupon_id = $this->input->post('user_discount_coupon_id', TRUE) ? intval($this->input->post('user_discount_coupon_id', TRUE)) : NULL;
            $address_id = $this->input->post('address_id', TRUE);
            $message = $this->input->post('message', TRUE);
            $terminal_type = $this->input->post('terminal_type', TRUE) ? $this->input->post('terminal_type', TRUE) : jys_system_code::TERMINAL_TYPE_PC;
            $payment_id = $is_point_flag ? jys_system_code::PAYMENT_POINTPAY : $this->input->post('payment_id', TRUE);

            // 判断是否有代理商ID，依此作为是否为代理商下单的依据，以及获取订单信息时的按代理商订单处理
            $is_agent_user = !empty($_SESSION['agent_id']) ? TRUE : FALSE;
            $user_info = $this->User_model->get_user_detail_by_condition(array('user.id' => $user_id), FALSE, $is_agent_user);

            if (intval($user_discount_coupon_id) > 0) {
                // 使用了优惠券
                $user_discount_coupon = $this->Discount_coupon_model->get_user_discount_coupon_by_id($user_discount_coupon_id);
                if (isset($user_discount_coupon['success']) && $user_discount_coupon['success']) {
                    $user_discount_coupon = $user_discount_coupon['data'][0];
                } else {
                    $user_discount_coupon = NULL;
                }
            } else {
                $user_discount_coupon = NULL;
            }

            //获取邮费
            $res = $this->_get_postage($address_id, $this->input->post('ids', TRUE), $terminal_type);
            if ($res['success']) {
                $freight = $res['data'];
            } else {
                echo json_encode(array('success' => FALSE, 'msg' => '获取邮费失败'));
                exit;
            }

            $data = $this->Order_model->add($user_id, $shopping_cart_ids, $is_point_flag, $address_id, $payment_id, $terminal_type, $user_discount_coupon, $message, $freight, $user_info);
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 获取订单列表nav
     */
    public function get_order_list_nav()
    {
        $user_id = $_SESSION['user_id'];

        $orders = $this->jys_db_helper->get_where_multi('order', ['user_id' => $user_id]);
        $data = $this->Order_model->get_order_list_nav($orders, $_SESSION['user_id']);

        echo json_encode($data);
    }

    /**
     * 更新子订单快递信息
     */
    public function update_sub_order()
    {
        //验证表单信息
        $this->form_validation->set_rules('id', '子订单ID', 'trim|required|numeric');
        $this->form_validation->set_rules('express_company_id', '快递公司ID', 'trim|required|numeric');
        $this->form_validation->set_rules('express_number', '快递单号', 'trim|required|max_length[50]');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            //处理数据
            $id = intval($this->input->post('id', TRUE));
            $post['express_company_id'] = intval($this->input->post('express_company_id', TRUE));
            $post['express_number'] = $this->input->post('express_number', TRUE);


            // 订阅物流信息
            if (!$this->Order_model->subscribe_express_info($post['express_company_id'], $post['express_number'])) {
                echo json_encode([
                    'success' => FALSE,
                    'msg' => '订阅快递信息失败'
                ]);
                exit;
            }


            if ($this->jys_db_helper->update('order_commodity', $id, $post)) {
                $data['success'] = TRUE;
                $data['msg'] = '修改成功';
            } else {
                $data['success'] = FALSE;
                $data['msg'] = '修改失败';
            }
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 微信支付页面控制器
     */
    public function wechat_pay($order_id)
    {
        if (intval($order_id) < 1) {
            show_404();
        }
        $order = $this->Order_model->get_order_by_condition(array('order.id' => $order_id));
        if (empty($order) || $order['success'] == false || empty($order['data'])) {
            show_404();
        }
        $url = site_url() . 'order/detail/' . $order_id;
        if ($order['data']['status_id'] != 10) {
            redirect($url, 'location', 303);
        } else {
            $wx_code_url = $this->Order_model->wechat_pay_unified_order($order_id);
            if (!$wx_code_url['success']) {
                redirect($url, 'location', 303);
            } else {
                $order['data']['wx_code_url'] = $wx_code_url['code_url'];
                $order['data']['wx_code_url_time'] = $wx_code_url['wx_code_url_time'];
            }
        }

        $data['title'] = "赛安生物-微信支付";
        $data['js'] = array('wechat_pay');
        $data['css'] = array('wechat_pay', 'sign_up');
        $data['main_content'] = 'wechat_pay';
        $data['isset_nav'] = FALSE;
        $data['simple_footer'] = TRUE;
        $data['order'] = $order['data'];
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 展示微信支付二维码
     * @param $order_id 订单ID
     */
    public function show_wechat_pay_qrcode($order_id)
    {
        $result = $this->Order_model->wechat_pay_unified_order($order_id);
        if ($result['success']) {
            $this->jys_qrcode->create_qrcode($result['code_url']);
        } else {
            show_error("错误", "请检查参数是否错误");
        }
    }

    /**
     * 获取微信支付（公众号）JSAPI页面调用的支付参数
     */
    public function get_wechat_pay_js_api_parameters()
    {
        $order_id = $this->input->post('order_id');
        $result = array('success' => FALSE, 'msg' => '');
        if (intval($order_id) < 1) {
            $result['msg'] = '订单信息不正确';
            echo json_encode($result);
            exit;
        }

        $openid = $_SESSION['openid'];
        $result = $this->Order_model->wechat_pay_unified_order($order_id, "JSAPI", $openid);

        echo json_encode($result);
    }

    /**
     *  微信支付测试函数
     */
//    public function test()
//    {
//        $this->load->library('Jys_kdniao');
//        $result = $this->jys_kdniao->ebusiness_order_handle('HHTT', '589707398027');
//        $result = $this->jys_kdniao->ebusiness_order_handle('STO', '3320792054688');
//        $result = $this->Express_model->show_express_info_by_order_id(37);
//
//        echo json_encode($result);
//        $this->Order_model->auto_cancel_not_paid_order();

//        $this->Order_model->notify_inform_order_info(0, '7548021491444393');
//        $this->db->select('order.id');
//        $this->db->where('order_commodity.id', NULL);
//        $this->db->join('order_commodity', 'order_commodity.order_id = order.id');
//        $result = $this->db->get('order1');
//        if ($result) {
//            echo json_encode($result->result_array());
//        }else {
//            echo "dadasda";
//        }

//        echo phpinfo();
//        $this->load->library('Jys_message');
//        $result = $this->jys_message->send_message('13088088576,18228592394', " 赛安基因温馨提示：您的检测信息已录入。录入姓名：张三，样本编号：1232313。请妥善保存此短信，以便日后查询报告。因检测项目不同，赛安基因将在收样后8-20个工作日出具电子检测报告。快速查询链接：http://suo.im/2QNrW4");
//        dd($result);

//        $db1 = $this->load->database('default', TRUE);
//        $db2 = $this->load->database('additional', TRUE); //本地数据库

        /***************以下是从基因数据库同步过来的数据************************/
        //同步order
        // $page = 1;
        // $page_size = 100;
        // $db1->select('id');
        // $result = $db1->get('order');
        // $total_page = ceil($result->num_rows() / $page_size * 1.0);
        // // aa($total_page);
        // //分页插入
        // $response = 0;
        // for ($page; $page <= $total_page ; $page++) {
        //     $db1->select('*');
        //     $res = $db1->get('order', $page_size, ($page - 1) * $page_size);
        //     $data = $res->result_array();
        //     $response += $db2->insert_batch('order', $data);
        // }
        // aa($response);

        //同步order_commodity
//         $page = 1;
//         $page_size = 100;
//         $db1->select('id');
//         $result = $db1->get('order_commodity');
//         $total_page = ceil($result->num_rows() / $page_size * 1.0);
//         //分页插入
//        $response = 0;
//         for ($page; $page <= $total_page ; $page++) {
//             $db1->select('*');
//             $res = $db1->get('order_commodity', $page_size, ($page - 1) * $page_size);
//             $data = $res->result_array();
//             // aa($data);
//             $response += $db2->insert_batch('order_commodity', $data);
//         }
//         aa($response);

        //同步report 一共300多页数据
//        $page = 1;
//        $page_size = 1000;
//        $db1->select('id');
//        $db1->where('id > 259237');
//        $result = $db1->get('report');
//        $total_page = ceil($result->num_rows() / $page_size * 1.0);
//        //分页插入
//        $response = 0;
//        for ($page; $page <= $total_page; $page++) {
//            $db1->select('*');
//            $db1->where('id > 259237');
//            $res = $db1->get('report', $page_size, ($page - 1) * $page_size);
//            $data = $res->result_array();
//            foreach ($data as $key => $value) {
//                unset($data[$key]['template_id']);
//                unset($data[$key]['project_num']);
//            }
//            $response += $db2->insert_batch('report', $data);
//        }
//        aa($response);

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

//        $this->load->model('Report_model');
//        $number_list = array('170935901', '170935909', '170923750', '170928969', '170928967');
//        $this->Report_model->send_report_status_information_to_user($number_list);
//    }

    /**
     *物流详情页根据订单ID获取订单详情
     */
    public function get_order_by_id()
    {
        $data = array(
            'success' => FALSE,
            'msg' => '获取订单详情失败',
            'data' => NULL
        );
        $id = $this->input->post('id', TRUE);
        if (empty($id) || intval($id) < 1) {
            $data['msg'] = '订单ID不正确';
            echo json_encode($data);
            exit;
        }
        $order_info = $this->Order_model->get_order_by_condition(array('order.id' => $id, 'order.user_id' => $_SESSION['user_id']));

        if ($order_info['success']) {
            // 格式化子订单商品名字
            $order_info['data']['sub_orders'] = $this->Common_model->format_commodity_name($order_info['data']['sub_orders']);
            $data = array(
                'success' => TRUE,
                'msg' => '获取订单详情成功',
                'data' => $order_info['data']
            );
            if (isset($_SESSION['agent_id']) && intval($_SESSION['agent_id']) > 0) {
                $data['agent_id'] = intval($_SESSION['agent_id']);
            }
        }

        echo json_encode($data);
    }


    /**
     * 分页获取待评价列表
     */
    public function get_can_evaluate_order_by_page()
    {
        $page = $this->input->post('page', TRUE);
        $page_size = $this->input->post('page_size', TRUE);

        $result = array('success' => FALSE, 'msg' => '获取可评价列表失败', 'data' => array(), 'total_page' => 0);
        if (intval($page) < 1 || intval($page_size) < 1) {
            $result['msg'] = '参数错误';
            echo json_encode($result);
            exit;
        }

        $result = $this->Order_model->get_can_evaluate_order($page, $page_size, $_SESSION['user_id']);

        echo json_encode($result);
    }

    /**
     * 根据订单ID获取订单评价信息
     */
    function get_evaluation_by_order_id()
    {
        $id = $this->input->post('id', TRUE);
        $data = [
            'success' => FALSE,
            'msg' => '获取订单详情失败',
            'data' => NULL
        ];
        if (empty($id) || intval($id) < 1) {
            $data['msg'] = '订单ID不正确';
            echo json_encode($data);
            exit;
        }
        $evaluation = $this->Order_model->get_evaluation_by_order_id($id);

        echo json_encode($evaluation);
    }

    /**
     * 用户发表评价
     */
    public function evaluate_order()
    {
        //验证表单信息
        $this->form_validation->set_rules('commodity_id', '商品ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('order_id', '订单ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('order_commodity_id', '子订单ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('score', '分数', 'trim|required|less_than[6]|greater_than[0]');
        $this->form_validation->set_rules('content', '评价内容', 'trim|required');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        $result = array('success' => FALSE, 'msg' => '发表评价失败');
        if ($res['success']) {
            //处理数据
            $insert['commodity_id'] = $this->input->post('commodity_id', TRUE);
            $insert['order_id'] = $this->input->post('order_id', TRUE);
            $insert['order_commodity_id'] = $this->input->post('order_commodity_id', TRUE);
            $insert['score'] = $this->input->post('score', TRUE);
            $insert['content'] = $this->input->post('content', FALSE);

            $attachment_ids = $this->input->post('attachment_ids', TRUE);
            if (!empty($attachment_ids)) {
                $attachment_ids = explode('-', $attachment_ids);
            } else {
                $attachment_ids = array();
            }

            $result = $this->Order_model->evaluate_order($insert, $_SESSION['user_id'], $attachment_ids);
        } else {
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 添加奖品订单
     */
    public function add_prize_order()
    {
        $user_id = $_SESSION['user_id'];
        $commodity_id = $this->input->post('commodity_id', TRUE);
        $is_indiana = $this->input->post('is_indiana', TRUE);
        $address_id = $this->input->post('address_id', TRUE);

        if ($is_indiana == 1) {
            $integral_indiana_id = $this->input->post('id', TRUE);
            $result = $this->Order_model->add_prize_order($user_id, $commodity_id, $address_id, Jys_system_code::TERMINAL_TYPE_PC, TRUE, $integral_indiana_id);
        } else {
            $sweepstakes_commodity_id = $this->input->post('id', TRUE);
            $result = $this->Order_model->add_prize_order($user_id, $commodity_id, $address_id, Jys_system_code::TERMINAL_TYPE_PC, FALSE, $sweepstakes_commodity_id);
        }


        echo json_encode($result);
    }

    /**
     * 去领奖
     */
    public function receive_prize($id = 0, $insert_id = 0, $is_indiana = 1)
    {
        if (empty($id) || intval($id) < 0 || empty($insert_id) || intval($insert_id) < 0 || !$this->Order_model->exist_prize_places($_SESSION['user_id'], $id, $is_indiana)) {
            show_404();
        }

        $data['title'] = "赛安生物-订单结算";
        $data['js'] = array('template', 'receive_prize');
        $data['need_gaode_api'] = TRUE;
        $data['css'] = array('receive_prize');
        $data['main_content'] = 'receive_prize';
        if ($is_indiana == 1) {
            $integral_indiana = $this->jys_db_helper->get('integral_indiana', $id);
            $data['commodity'] = $this->Commodity_model->get_commodity_list_by_condition(['commodity.id' => $integral_indiana['commodity_id']], FALSE);
            $data['id'] = $insert_id;
            $data['is_indiana'] = TRUE;
        } else {
            $sweepstakes_commodity = $this->jys_db_helper->get('sweepstakes_commodity', $id);
            $data['commodity'] = $this->Commodity_model->get_commodity_list_by_condition(['commodity.id' => $sweepstakes_commodity['commodity_id']], FALSE);
            $data['id'] = $insert_id;
            $data['is_indiana'] = FALSE;
        }

        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 根据子订单ID获取物流公司信息
     */
    public function get_sent_back_info()
    {
        $sub_order_id = $this->input->post('id', TRUE);

        $sub_order = $this->jys_db_helper->get('order_commodity', $sub_order_id);

        if (!empty($sub_order)) {
            $result['success'] = TRUE;
            $result['msg'] = '获取成功';
            $result['data']['express_company_id'] = $sub_order['express_company_id'];
            $result['data']['express_number'] = $sub_order['express_number'];
        } else {
            $result['success'] = FALSE;
            $result['msg'] = '获取失败';
        }

        echo json_encode($result);
    }

    /**
     * 根据去编号（district_code）获取对应的freight记录
     */
    public function get_freight_by_district_code()
    {
        $district_code = $this->input->post('code', TRUE);
        $data = $this->jys_db_helper->get_where('freight', ['district_code' => $district_code]);
        echo json_encode($data);
    }

    /**
     * 根据订单内的商品信息及下单用户信息，返回该订单的邮费
     */
    public function get_postage_by_order()
    {
        $result = array('success' => FALSE, 'msg' => '获取订单运费失败', 'data' => 0);

        $address_id = $this->input->post('address_id', TRUE);
        $shopping_cart_ids = $this->input->post('shopping_cart_ids', TRUE);
        $terminal_type = $this->input->post('terminal_type', TRUE);

        if (intval($address_id) < 1) {
            $result['msg'] = '请选择收货地址';
            echo json_encode($result);
            exit;
        }
        if (empty($shopping_cart_ids)) {
            $result['msg'] = '请选择要结算的商品';
            echo json_encode($result);
            exit;
        }
        if (intval($terminal_type) < 1) {
            $result['msg'] = '请输入正确的终端类型';
            echo json_encode($result);
            exit;
        }
        $result = $this->_get_postage($address_id, $shopping_cart_ids, $terminal_type);

        echo json_encode($result);
    }

    /**
     * 计算邮费规则
     * @param int $address_id 地址ID
     * @param string $shopping_cart_ids 购物车ids
     * @param int $terminal_type 下单终端类型
     * @return array
     */
    private function _get_postage($address_id = 0, $shopping_cart_ids = '', $terminal_type = jys_system_code::TERMINAL_TYPE_PC)
    {
        if (empty($address_id) || empty($shopping_cart_ids)) {
            return array('success' => FALSE, 'msg' => '参数错误', 'data' => NULL);
        }
        $address_info = $this->jys_db_helper->get('address', intval($address_id));
        if (empty($address_info)) {
            $result['msg'] = '您选择的收货地址不正确';
            echo json_encode($result);
            exit;
        }

        $shopping_cart_id_list = explode('-', $shopping_cart_ids);
        $commodity_list = $this->Shopping_cart_model->get_commodity_specification_by_shopping_cart_id_list($shopping_cart_id_list);
        if (empty($commodity_list)) {
            $result['msg'] = '未找到符合要求的商品规格';
            echo json_encode($result);
            exit;
        }

        if (isset($_SESSION['agent_id']) && intval($_SESSION['agent_id'])) {
            // 代理商的客户
            $user_info = $this->User_model->get_user_detail_by_condition(array('user.id' => $_SESSION['user_id']), FALSE, TRUE);
        } else {
            // 普通客户
            $user_info = $this->User_model->get_user_detail_by_condition(array('user.id' => $_SESSION['user_id']));
        }

        $commodity_list = $this->Commodity_model->calculate_discount_price($commodity_list, $user_info['price_discount']);

        $result = $this->Postage_model->get_postage_by_order($address_info, $commodity_list, $user_info, $terminal_type);

        return $result;
    }
}