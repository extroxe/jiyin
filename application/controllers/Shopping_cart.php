<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Shopping_cart.php
 *
 *     Description:  购物车控制器
 *
 *         Created:  2016-11-24 16:10:56
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */

Class Shopping_cart extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->library(['form_validation', 'Jys_db_helper']);
        $this->load->model(['Common_model', 'Shopping_cart_model', 'Commodity_model', 'User_model']);
    }

    /**
     * 购物车页面
     */
    public function index(){
        $data['title'] = "赛安生物-购物车";
        $data['js'] = array('template', 'cart');
        $data['css'] = array('cart');
        $data['main_content'] = 'cart';
        $data['isset_search'] = TRUE;
        $data['isset_nav'] = FALSE;
        $data['is_cart'] = TRUE;
        $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $_SESSION['user_id']]);
        $shopping_carts = $this->Shopping_cart_model->all($_SESSION['user_id'])['data'];
        if (!empty($shopping_carts)) {
            $shopping_carts = $this->Common_model->format_commodity_name($shopping_carts);
        }

        $data['shopping_carts'] = $this->Commodity_model->calculate_discount_price($shopping_carts, $user_info['price_discount']);

        $data['commodity_type_id'] = $this->Commodity_model->get_commodity_list_by_condition(['commodity.id'=>$data['shopping_carts'][0]['commodity_id']], FALSE)['type_id'];
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 立即购买购物车页面
     */
    public function buy_now($shopping_cart_id = 0){
        if (empty($shopping_cart_id)){
            return FALSE;
        }

        $data['title'] = "赛安生物-购物车";
        $data['js'] = array('cart');
        $data['css'] = array('cart');
        $data['main_content'] = 'cart';
        $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $_SESSION['user_id']]);
        $shopping_carts = $this->Shopping_cart_model->all($_SESSION['user_id'], ['shopping_cart.id' => $shopping_cart_id])['data'];
        $data['shopping_carts'] = $this->Commodity_model->calculate_discount_price($shopping_carts, $user_info['price_discount']);
        if (empty($data['shopping_carts']) || $data['shopping_carts'][0]['is_point']){
            show_404();
        }

        $data['commodity_type_id'] = $this->Commodity_model->get_commodity_list_by_condition(['commodity.id'=>$data['shopping_carts'][0]['commodity_id']], FALSE)['type_id'];

        $data['isset_search'] = TRUE;
        $data['isset_nav'] = FALSE;
        $data['is_cart'] = TRUE;
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 获取该用户的全部购物车信息
     */
    public function all(){
        if (isset($_SESSION['agent_id']) && intval($_SESSION['agent_id']) > 0) {
            $data['agent_id'] = intval($_SESSION['agent_id']);
            $agent_id = intval($_SESSION['agent_id']);
        }else{
            $agent_id = NULL;
        }

        $data = $this->Shopping_cart_model->all($_SESSION['user_id'], ['commodity.type_id !=' => jys_system_code::COMMODITY_TYPE_MEMBER], $agent_id);
        if ($data['success']) {
            $data['data'] = $this->Common_model->format_commodity_name($data['data']);
        }

        if (!empty($_SESSION['user_id']) && !empty($_SESSION['role_id']) && (intval($_SESSION['role_id']) == jys_system_code::ROLE_USER || intval($_SESSION['role_id']) == jys_system_code::ROLE_AGENT_USER)) {
            $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $_SESSION['user_id']]);
            $data['data'] = $this->Commodity_model->calculate_discount_price($data['data'], $user_info['price_discount'], $agent_id);
        }

        echo json_encode($data);
    }

    /**
     * 获取购物车商品数量
     */
    public function amount(){
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];   
        }else{
            $user_id = NULL;
        }
        $amount = $this->Shopping_cart_model->amount($user_id);

        echo json_encode(intval($amount));
    }

    /**
     * 添加购物车
     */
    public function add(){
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])){
            echo json_encode(array(
                'success' => FALSE,
                'msg' => '请先登录'
            ));
            exit;
        }

        //验证表单信息
        $this->form_validation->set_rules('commodity_id', '商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('specification_id', '规格ID', 'trim|required|numeric');
        $this->form_validation->set_rules('amount', '商品数量', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $post['user_id']      = $_SESSION['user_id'];
            $post['commodity_id'] = intval($this->input->post('commodity_id', TRUE));
            $post['specification_id'] = intval($this->input->post('specification_id', TRUE));
            $post['amount']       = intval($this->input->post('amount', TRUE));
            // $is_buy_now为0时表示添加购物车，$is_buy_now为1时表示立即购买
            $is_buy_now           = intval($this->input->post('is_buy_now', TRUE));
            $post['create_time']  = date('Y-m-d H:i:s');

            if ($post['amount'] > 10) {
                $post['amount'] = 10;
            }
            $shopping_cart_id = $this->jys_db_helper->is_exist('shopping_cart', ['user_id' => $post['user_id'], 'specification_id' => $post['specification_id']]);
            $commodity = $this->Commodity_model->get_commodity_list_by_condition(['commodity.id' => $post['commodity_id']], FALSE);
            if ($commodity['is_point']){
                echo json_encode([
                    'success' => FALSE,
                    'msg' => '积分商品不能添加到购物车'
                ]);
                exit;
            }

            if ($shopping_cart_id){
                if ($commodity['type_id'] == jys_system_code::COMMODITY_TYPE_MEMBER){
                    // 若产品为会员产品就只能购买一件
                    if ($this->jys_db_helper->set_update('shopping_cart', $shopping_cart_id, ['amount'=>1], FALSE)){
                        $data['success'] = TRUE;
                        $data['msg'] = '添加成功，在购物车等亲~';
                        $data['insert_id'] = $shopping_cart_id;
                    }else{
                        $data['success'] = FALSE;
                        $data['msg'] = '';
                    }
                }else{
                    if ($is_buy_now) {
                        // 立即购买
                        if ($this->jys_db_helper->set_update('shopping_cart', $shopping_cart_id, ['amount' => 1], FALSE)){
                            $data['success'] = TRUE;
                            $data['msg'] = '添加成功，在购物车等亲~';
                            $data['insert_id'] = $shopping_cart_id;
                        }else{
                            $data['success'] = FALSE;
                            $data['msg'] = '';
                        }
                    } else {
                        // 加入购物车
                        $condition = array(
                            'user_id' => $post['user_id'],
                            'commodity_id'=> $post['commodity_id'],
                            'specification_id' => $post['specification_id']
                        );
                        $shopping_cart_data = $this->jys_db_helper->get_where('shopping_cart', $condition);
                        if (!empty($shopping_cart_data) && is_array($shopping_cart_data)) {
                            $amount = $post['amount'] + intval($shopping_cart_data['amount']);
                            if ($amount > 10) {
                                $amount = 10;
                            }
                            if ($this->jys_db_helper->set_update('shopping_cart', $shopping_cart_id, [' amount' => $amount], FALSE)){
                                $data['success'] = TRUE;
                                $data['msg'] = '添加成功，在购物车等亲~';
                                $data['insert_id'] = $shopping_cart_id;
                            }else{
                                $data['success'] = FALSE;
                                $data['msg'] = '';
                            }
                        } else {
                            $data = $this->jys_db_helper->add('shopping_cart', $post, TRUE);
                            if ($data['success']) {
                                $data['msg'] = '添加成功，在购物车等亲~';
                            }
                        }
                    }
                }
            } else {
                $data = $this->jys_db_helper->add('shopping_cart', $post, TRUE);
                if ($data['success']) {
                    $data['msg'] = '添加成功，在购物车等亲~';
                }
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 再次购买
     */
    public function add_again()
    {
        $input = $this->input->post('data', TRUE);

        $input = json_decode($input, TRUE);
        if (json_last_error() != JSON_ERROR_NONE || empty($input)) {
            echo json_encode(array('success' => FALSE, 'msg' => '传入参数不正确'));
            exit;
        }

        for ($i = 0; $i < count($input); $i++) {
            if (!empty($input[$i]['amount']) && !empty($input[$i]['commodity_id']) && !empty($input[$i]['specification_id'])) {
                $input[$i]['user_id'] = $_SESSION['user_id'];
            } else {
                echo json_encode(array('success' => FALSE, 'msg' => '数据不正确，加入购物车失败'));
                exit;
            }
        }

        $result = $this->jys_db_helper->add_batch('shopping_cart', $input);

        echo json_encode($result);
    }

    /**
     * 修改购物车
     */
    public function update(){
        //验证表单信息
        $this->form_validation->set_rules('id', '购物车ID', 'trim|required|numeric');
        $this->form_validation->set_rules('amount', '商品数量', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $id                   = intval($this->input->post('id', TRUE));
            $post['user_id']      = $_SESSION['user_id'];
            $post['amount']       = intval($this->input->post('amount', TRUE));

            $data = $this->jys_db_helper->update('shopping_cart', $id, $post);
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除购物车
     */
    public function delete(){
        $id = explode(',', $this->input->post('id', TRUE));

        $data = $this->jys_db_helper->delete('shopping_cart',$id);

        echo json_encode($data);
    }

    /**
     * 购物车数量增量1
     */
    public function increment_num(){
        //验证表单信息
        $this->form_validation->set_rules('id', '购物车ID', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $id = intval($this->input->post('id', TRUE));

            $data = $this->jys_db_helper->set_update('shopping_cart', $id, ['amount' => 'amount+1'], FALSE);
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 购物车数量减量1
     */
    public function decrement_num(){
        //验证表单信息
        $this->form_validation->set_rules('id', '购物车ID', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $id = intval($this->input->post('id', TRUE));

            if ($this->jys_db_helper->set_update('shopping_cart', $id, ['amount' => 'amount-1'], FALSE)) {
                $data['success'] = TRUE;
                $data['msg'] = '减少购物车商品成功';
            }else {
                $data['success'] = FALSE;
                $data['msg'] = '减少购物车商品失败';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }
}