<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Sweepstakes_commodity_admin.php
 *     Description: 积分抽奖商品后台管理端控制器
 *         Created: 2017-03-01 12:52:47
 *          Author: TangYu
 *
 * =====================================================================================
 */
class Sweepstakes_commodity_admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['form_validation']);
        $this->load->library('Jys_db_helper');
        $this->load->model(['Common_model', 'Sweepstakes_commodity_model']);
    }

    /**
     * 添加积分抽奖奖品
     * @author TangYu
     */
    public function add()
    {
        $this->form_validation->set_rules('sweepstakes_id', '积分抽奖活动ID', 'trim|required|numeric');
        $this->form_validation->set_rules('commodity_id', '奖品商品ID', 'trim|numeric');
        $this->form_validation->set_rules('commodity_specification_id', '奖品商品规格ID', 'trim|numeric');
        $this->form_validation->set_rules('point', '奖品积分', 'trim|numeric');
        $this->form_validation->set_rules('total_number', '奖品总数', 'trim|required|numeric');
        $this->form_validation->set_rules('user_total_point', '用户总积分积分中奖规则', 'trim|numeric');
        $this->form_validation->set_rules('user_expenditure', '用户消费总额中奖规则', 'trim|numeric');
        $this->form_validation->set_rules('register_start_time', '注册时间段起点规则', 'trim');
        $this->form_validation->set_rules('register_end_time', '注册时间段终点规则', 'trim');

        $result = $this->Common_model->deal_validation_errors();
        if ($result['success']){
            $date = date('Y-m-d H:i:s');
            $commodity_id = $this->input->post('commodity_id') ? $this->input->post('commodity_id', TRUE) : 0;
            $commodity_specification_id = $this->input->post('commodity_specification_id') ? $this->input->post('commodity_specification_id', TRUE) : 0;
            $point = $this->input->post('point') ? $this->input->post('point', TRUE) : 0;

            $insert = [
                'sweepstakes_id' => $this->input->post('sweepstakes_id', TRUE),
                'total_number' => $this->input->post('total_number', TRUE),
                'current_number' => $this->input->post('total_number', TRUE),
                'user_total_point' => $this->input->post('user_total_point', TRUE),
                'user_expenditure' => $this->input->post('user_expenditure', TRUE),
                'register_start_time' => $this->input->post('register_start_time', TRUE),
                'register_end_time' => $this->input->post('register_end_time', TRUE),
                'create_time' => $date,
                'update_time' => $date
            ];
            if (!empty($commodity_id) && !empty($commodity_specification_id)){
                $insert['commodity_id'] = $commodity_id;
                $insert['commodity_specification_id'] = $commodity_specification_id;
            } else if (!empty($point)){
                $insert['point'] = $point;
            }

            $data = $this->Sweepstakes_commodity_model->add_sweepstakes_commodity('sweepstakes_commodity', $insert);
        }else{
            $data = ['success' => FALSE, 'msg' => '填入信息错误 '.$result['msg']];
        }

        echo json_encode($data);
    }

    /**
     * 更新积分抽奖奖品
     * @author TangYu
     */
    public function update()
    {
        $this->form_validation->set_rules('id', '奖品ID', 'trim|numeric');
        $this->form_validation->set_rules('point', '奖品积分', 'trim|numeric');
        $this->form_validation->set_rules('total_number', '奖品总数', 'trim|required|numeric');
        $this->form_validation->set_rules('current_number', '奖品剩余总数', 'trim|required|numeric');
        $this->form_validation->set_rules('user_total_point', '用户总积分积分中奖规则', 'trim|numeric');
        $this->form_validation->set_rules('user_expenditure', '用户消费总额中奖规则', 'trim|numeric');
        $this->form_validation->set_rules('register_start_time', '注册时间段起点规则', 'trim|required');
        $this->form_validation->set_rules('register_end_time', '注册时间段终点规则', 'trim|required');

        $result = $this->Common_model->deal_validation_errors();
        if ($result['success']){
            $sweepstakes_commodity_id = $this->input->post('id', TRUE);
            $point = $this->input->post('point') ? $this->input->post('point', TRUE) : 0;

            $update = [
                'total_number' => $this->input->post('total_number', TRUE),
                'current_number' => $this->input->post('current_number', TRUE),
                'user_total_point' => $this->input->post('user_total_point', TRUE),
                'user_expenditure' => $this->input->post('user_expenditure', TRUE),
                'register_start_time' => $this->input->post('register_start_time', TRUE),
                'register_end_time' => $this->input->post('register_end_time', TRUE),
                'update_time' => date('Y-m-d H:i:s')
            ];
            if (!empty($point)){
                $update['point'] = $point;
            }
            $update['update_time'] = date('Y-m-d H:i:s');

            $res = $this->jys_db_helper->update('sweepstakes_commodity', $sweepstakes_commodity_id, $update);
            if ($res['success']){
                $data = ['success' => TRUE, 'msg' => '更新活动奖品成功'];
            }else{
                $data = ['success' => FALSE, 'msg' => '更新活动奖品失败'];
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '填入信息错误', 'error' => $result['msg']];
        }

        echo json_encode($data);
    }

    /**
     * 删除抽奖奖品
     * @author TangYu
     */
    public function delete()
    {
        $sweepstakes_commodity_id = $this->input->post('id', TRUE);

        if (empty($sweepstakes_commodity_id) || intval($sweepstakes_commodity_id) < 1){
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            echo json_encode($data);
            exit;
        }

        $result = $this->jys_db_helper->soft_delete('sweepstakes_commodity', ['id' => intval($sweepstakes_commodity_id)], ['is_show' => 0]);
        if ($result){
            $data = ['success' => TRUE, 'msg' => '删除奖品成功'];
        }else{
            $data = ['success' => FALSE, 'msg' => '删除奖品失败'];
        }

        echo json_encode($data);
    }

    /**
     * 分页获取奖品结果表数据
     * @param int $sweepstakes_id
     * @param int $page
     * @param int $page_size
     * @author TangYu
     */
    public function get_all_prize($sweepstakes_id = 0, $page = 1, $page_size = 10)
    {
        if (intval($sweepstakes_id) < 1 || intval($page) < 1 || intval($page_size) < 1){
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            echo json_encode($data);
            exit;
        }
        $result = $this->Sweepstakes_commodity_model->get_scroll_prize($sweepstakes_id, $page, $page_size);

        //处理电话号码 和奖品名称
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
     * 获取当前活动抽奖奖品
     * @param int $page
     * @param int $page_size
     */
    public function get_sweepstakes_commodity($page = 1, $page_size = 10)
    {
        $data = $this->Sweepstakes_commodity_model->get_sweepstakes_commodity($page, $page_size, true);
        echo json_encode($data);
    }
}