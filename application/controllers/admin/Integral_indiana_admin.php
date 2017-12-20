<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Integral_indiana_admin.php
 *     Description: 积分夺宝后台管理
 *         Created: 2017-03-03 09:30:47
 *          Author: TangYu
 *
 * =====================================================================================
 */
class Integral_indiana_admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['form_validation', 'Jys_db_helper']);
        $this->load->model(['Common_model','Integral_indiana_model']);
    }

    /**
     * 分页获取所有夺宝活动信息
     * @param int $page
     * @param int $page_size
     * @author TangYu
     */
    public function get_all_indiana_info($page = 1, $page_size = 10)
    {
        $result = $this->Integral_indiana_model->get_all_indiana_info($page, $page_size);
        //计算剩余柱数
        if ($result['success']) {
            for ($i = 0; $i < count($result['data']); $i++) {
                $result['data'][$i]['remain_bet'] = ceil($result['data'][$i]['total_points'] / $result['data'][$i]['amount_bet']) - $result['data'][$i]['current_bet'];
            }
        }
        echo json_encode($result);
    }
    /**
     * 添加积分夺宝活动
     * @author TangYu
     */
    public function add()
    {
        $this->form_validation->set_rules('commodity_id', '商品ID', 'trim|required|integer');
        $this->form_validation->set_rules('commodity_specification_id', '商品规格ID', 'trim|required|integer');
        $this->form_validation->set_rules('total_points', '所需总积分', 'trim|required|integer');
        $this->form_validation->set_rules('amount_bet', '每柱积分', 'trim|required|integer');
        $this->form_validation->set_rules('user_total_point', '中奖规则：用户总积分大于多少', 'trim|integer');
        $this->form_validation->set_rules('user_expenditure', '中奖规则：用户消费额大于多少', 'trim|numeric');
        $this->form_validation->set_rules('register_start_time', '中奖规则：注册时间段起点', 'trim');
        $this->form_validation->set_rules('register_end_time', '中奖规则：注册时间段终点', 'trim');

        $result = $this->Common_model->deal_validation_errors();
        if ($result['success']){
            $date = date('Y-m-d H:i:s');

            $insert = [
                'commodity_id' => $this->input->post('commodity_id', TRUE),
                'commodity_specification_id' => $this->input->post('commodity_id', TRUE),
                'total_points' => $this->input->post('total_points', TRUE),
                'amount_bet' => $this->input->post('amount_bet', TRUE),
                'user_total_point' => $this->input->post('user_total_point') ? $this->input->post('user_total_point', TRUE) : NULL,
                'user_expenditure' => $this->input->post('user_expenditure') ? $this->input->post('user_expenditure', TRUE) : NULL,
                'register_start_time' => $this->input->post('register_start_time') ? $this->input->post('register_start_time', TRUE) : NULL,
                'register_end_time' => $this->input->post('register_end_time') ? $this->input->post('register_end_time', TRUE) : NULL,
                'create_time' => $date,
                'update_time' => $date
            ];
            $res = $this->jys_db_helper->add('integral_indiana', $insert);

            if ($res['success']){
                $data = ['success' => TRUE, 'msg' => '添加夺宝活动成功'];
            }else{
                $data = ['success' => FALSE, 'msg' => '添加夺宝活动失败'];
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '添加夺宝活动失败 '.$result['msg']];
        }

        echo json_encode($data);
    }
    
    /**
     * 修改积分夺宝
     * @author TangYu
     */
    public function update()
    {
        $this->form_validation->set_rules('id', '活动ID', 'trim|required|integer');
        $this->form_validation->set_rules('total_points', '所需总积分', 'trim|required|integer');
        $this->form_validation->set_rules('user_total_point', '中奖规则：用户总积分大于多少', 'trim|integer');
        $this->form_validation->set_rules('user_expenditure', '中奖规则：用户消费额大于多少', 'trim|numeric');
        $this->form_validation->set_rules('register_start_time', '中奖规则：注册时间段起点', 'trim');
        $this->form_validation->set_rules('register_end_time', '中奖规则：注册时间段终点', 'trim');

        $result = $this->Common_model->deal_validation_errors();
        if ($result['success']){
            $date = date('Y-m-d H:i:s');

            $indiana_id = $this->input->post('id', TRUE);
            $update = [
                'total_points' => $this->input->post('total_points', TRUE),
                'user_total_point' => $this->input->post('user_total_point') ? $this->input->post('user_total_point', TRUE) : 0,
                'user_expenditure' => $this->input->post('user_expenditure') ? $this->input->post('user_expenditure', TRUE) : 0,
                'register_start_time' => $this->input->post('register_start_time') ? $this->input->post('register_start_time', TRUE) : NULL,
                'register_end_time' => $this->input->post('register_end_time') ? $this->input->post('register_end_time', TRUE) : NULL,
                'update_time' => $date
            ];
            $res = $this->jys_db_helper->update('integral_indiana', $indiana_id, $update);

            if ($res){
                $data = ['success' => TRUE, 'msg' => '修改夺宝活动成功'];
            }else{
                $data = ['success' => FALSE, 'msg' => '修改夺宝活动失败'];
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '修改夺宝活动失败', 'error' => $result['msg']];
        }

        echo json_encode($data);
    }
    
    /**
     * 删除积分夺宝
     * @author TangYu
     */
    public function delete()
    {
        $indiana_id = $this->input->post('id', TRUE);

        if (intval($indiana_id) < 1){
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            echo json_encode($data);
            exit;
        }

        //判断活动是否有人参与 或者 活动处于已结束状态 可删除
        $integral_indiana_bet = $this->jys_db_helper->get_where('integral_indiana_bet', ['integral_indiana_id' => $indiana_id]);
        $integral_indiana = $this->jys_db_helper->get('integral_indiana', $indiana_id);
        if (!$integral_indiana_bet || $integral_indiana['status'] == Jys_system_code::INTEGRAL_INDIANA_STATUS_DONE){
            $res = $this->jys_db_helper->soft_delete('integral_indiana', ['id' => $indiana_id], ['status' => Jys_system_code::INTEGRAL_INDIANA_STATUS_DELETED]);
            if ($res['success']){
                $data = ['success' => TRUE, 'msg' => '删除夺宝活动成功'];
            }else{
                $data = ['success' => FALSE, 'msg' => '修改夺宝活动失败'];
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '该夺宝活动已有用户参与，无法删除'];
        }

        echo json_encode($data);
    }

    /**
     * 获取当前夺宝活动，夺宝者下注、中奖信息
     * @param int $indiana_id
     * @param int $page
     * @param int $page_size
     * @author TangYu
     */
    public function indiana_bet_info($indiana_id = 0, $page = 1, $page_size = 10)
    {
        if (intval($indiana_id) < 1 || intval($page) < 1 || intval($page_size) < 1){
            $data = ['success' => FALSE, 'msg' => '参数错误', 'data' => NULL];
            echo json_encode($data);
            exit;
        }
        $data = $this->Integral_indiana_model->indiana_bet_info($indiana_id, $page, $page_size);
        $indiana_result = $this->jys_db_helper->get_where('integral_indiana_result', ['integral_indiana_id' => $indiana_id]);

        //处理下注信息、中奖信息
        if ($data['success'] && $indiana_result){
            for ($i = 0; $i < count($data['data']); $i++){
                $data['data'][$i]['bet_point'] = $data['data'][$i]['count_bet'] * $data['data'][$i]['point'];
                if ($data['data'][$i]['bet_id'] == $indiana_result['integral_indiana_bet_id']){
                    $data['data'][$i]['is_win'] = TRUE;
                }else{
                    $data['data'][$i]['is_win'] = FALSE;
                }
            }
        }
        echo json_encode($data);
    }

    /**
     * 修改夺宝中奖者
     * @author TangYu
     */
    public function modify_winner()
    {
        $indiana_id = $this->input->post('indiana_id', TRUE);
        $integral_indiana_bet_id = $this->input->post('integral_indiana_bet_id', TRUE);
        $user_id = $this->input->post('user_id', TRUE);

        $update_info = array();
        if (!empty($indiana_id) && !empty($integral_indiana_bet_id) && !empty($user_id)){
            $update_info = [
                'integral_indiana_id' => intval($indiana_id),
                'integral_indiana_bet_id' => intval($integral_indiana_bet_id),
                'user_id' => intval($user_id),
                'status' => 0,
                'create_time' => date('Y-m-d H:i:s')
            ];
        }
        $result = $this->Integral_indiana_model->modify_winner($indiana_id, $update_info);

        echo json_encode($result);
    }

    /**
     * 后台审核夺宝结果
     * @param int $indiana_id
     * @author TangYu
     */
    public function operate_result($indiana_result_id = 0)
    {
        if (empty($indiana_result_id) || intval($indiana_result_id) < 1){
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            echo json_encode($data);
            exit;
        }

        $result = $this->jys_db_helper->update('integral_indiana_result', $indiana_result_id, ['status' => Jys_system_code::INTEGRAL_INDIANA_RESULT_STATUS_PASS]);
        if ($result){
            $data = ['success' => TRUE, 'msg' => '审核成功'];
        }else{
            $data = ['success' => FALSE, 'msg' => '审核失败'];
        }

        echo json_encode($data);
    }
}