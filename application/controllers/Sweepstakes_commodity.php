<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Sweepstakes_commodity.php
 *     Description: 积分抽奖商品控制器
 *         Created: 2017-03-01 15:01:47
 *          Author: TangYu
 *
 * =====================================================================================
 */
class Sweepstakes_commodity extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['Jys_db_helper']);
        $this->load->model(['Sweepstakes_commodity_model', 'Order_model', 'User_model']);
    }

    /**
     * 获取当前活动抽奖奖品(最多8个)
     * @author TangYu
     */
    public function get_sweepstakes_commodity()
    {
        $data = $this->Sweepstakes_commodity_model->get_sweepstakes_commodity();
        if (!empty($data['data'])) {
            for ($i = 0; $i < count($data['data']); $i++) {
                $data['data'][$i] = $this->Common_model->format_commodity_name($data['data'][$i]);
            }
        }
        echo json_encode($data);
    }

    /**
     * 抽奖，计算消费总额，获取用户信息，找出用户符合规则的奖品
     * @param int $sweepstakes_id
     * @author TangYu
     */
    public function find_one($sweepstakes_id = 0)
    {
        if (empty($sweepstakes_id) || intval($sweepstakes_id) < 1){
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            echo json_encode($data);
            exit;
        }

        $sweepstakes_info = $this->jys_db_helper->get('sweepstakes', $sweepstakes_id);
        $user_info = $this->jys_db_helper->get('user', $_SESSION['user_id']);
        //获取已完成订单情况，计算消费总额
        $user_expenditure = 0;
        $user_order_info = $this->Order_model->get_user_expenditure($_SESSION['user_id']);
        if ($user_order_info['success']){
            for ($i = 0; $i < count($user_order_info['data']); $i++){
                $user_expenditure += $user_order_info['data'][$i]['total_price'];
            }
        }
        $condition = [
            'current_point' => $user_info['current_point'],
            'user_expenditure' => $user_expenditure,
            'register_time' => $user_info['create_time'],
            'user_id' => $_SESSION['user_id'],
            'consume_points' => $sweepstakes_info['consume_points']
        ];

        $data = $this->Sweepstakes_commodity_model->find_one($condition);

        echo json_encode($data);
    }

    /**
     * 领取奖品，即把result表中status字段改为1
     * @param int $result_id
     * @author TangYu
     */
    public function receive($result_id = 0)
    {
        if (empty($result_id) || intval($result_id) < 1){
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            echo json_encode($data);
            exit;
        }

        $result = $this->Sweepstakes_commodity_model->receive($result_id, $_SESSION['user_id']);

        echo json_encode($result);
    }

    /**
     * 分页获取我的奖品
     * @param int $page
     * @param int $page_size
     * @author TangYu
     */
    public function get_my_prize($page = 1, $page_size = 10)
    {
        if (empty($page_size) || empty($page) || intval($page) < 1 || intval($page_size) < 1){
            $data = ['success' => FALSE, 'msg' => '参数错误', 'data' => NULL, 'total_page' => 0];
            echo json_encode($data);
            exit;
        }
        $result = $this->Sweepstakes_commodity_model->get_my_prize($_SESSION['user_id'], $page, $page_size);

        if (count($result['data']) >= 1){
            for ($i = 0; $i < count($result['data']); $i++){
                if ($result['data'][$i]['commodity_id'] == NULL){
                    $result['data'][$i]['prize_name'] = "积分".$result['data'][$i]['point'];
                }else if ($result['data'][$i]['point'] == NULL){
                    $result['data'][$i]['prize_name'] = $result['data'][$i]['commodity_name'];
                }
            }
        }

        echo json_encode($result);
    }

    /**
     * 分页获取滚动奖品
     * @param int $sweepstakes_id
     * @param int $page
     * @param int $page_size
     * @author TangYu
     */
    public function get_scroll_prize($sweepstakes_id = 0, $page = 1, $page_size = 10)
    {
        if (intval($sweepstakes_id) < 1 || intval($page) < 1 || intval($page_size) < 1){
            $data = ['success' => FALSE, 'msg' => '参数错误', 'data' => NULL, 'total_page' => 0];
            echo json_encode($data);
            exit;
        }
        $result = $this->Sweepstakes_commodity_model->get_scroll_prize($sweepstakes_id, $page, $page_size);

        //处理电话号码 和奖品名称
        if (count($result['data']) >= 1){
            for ($i = 0; $i < count($result['data']); $i++){
                $result['data'][$i]['phone'] = $this->User_model->filter_phone($result['data'][$i]['phone']);
                if ($result['data'][$i]['commodity_id'] == NULL){
                    $result['data'][$i]['prize_name'] = "积分".$result['data'][$i]['point'];
                }else if ($result['data'][$i]['point'] == NULL){
                    $result['data'][$i]['prize_name'] = $result['data'][$i]['commodity_name'];
                }
            }
        }

        echo json_encode($result);
    }

    /**
     * 积分抽奖
     */
    public function integral_draw(){
        $data['title'] = "积分抽奖";
        $data['js'] = array('template','awardRotate','integral_draw');
        $data['css'] = array('integral_draw');
        $data['main_content'] = 'integral_draw';
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $this->load->view('includes/template_view', $data);
    }
    /**
     * 中奖记录
     */
    public function my_prizes(){
        $data['title'] = "中奖记录";
        $data['js'] = array('template','awardRotate','my_prizes_lists');
        $data['css'] = array('my_prizes_lists');
        $data['main_content'] = 'my_prizes_lists';
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 去领奖
     */
    public function receive_prizes(){
        $data['title'] = "中奖记录";
        $data['js'] = array('template','awardRotate','my_prizes_lists');
        $data['css'] = array('my_prizes_lists');
        $data['main_content'] = 'my_prizes_lists';
        $data['isset_search'] = FALSE;
        $data['isset_nav'] = FALSE;
        $this->load->view('includes/template_view', $data);
    }
}