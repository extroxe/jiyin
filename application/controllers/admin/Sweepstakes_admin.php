<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Sweepstakes_admin.php
 *     Description: 积分抽奖后台管理端控制器
 *         Created: 2017-03-01 11:17:47
 *          Author: TangYu
 *
 * =====================================================================================
 */
class Sweepstakes_admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['form_validation']);
        $this->load->library('Jys_db_helper');
        $this->load->model(['Common_model', 'Sweepstakes_commodity_model']);
    }
    
    /**
     * 添加积分抽奖活动
     * @author TangYu
     */
    public function add()
    {
        $this->form_validation->set_rules('name', '活动名称', 'trim|required');
        $this->form_validation->set_rules('start_time', '开始时间', 'trim|required');
        $this->form_validation->set_rules('end_time', '结束时间', 'trim|required');
        $this->form_validation->set_rules('consume_points', '消费积分', 'trim|required|numeric');

        $result = $this->Common_model->deal_validation_errors();
        if ($result['success']){
            $date = date('Y-m-d H:i:s');

            //判断当前是否有抽奖活动
            if ($this->Sweepstakes_commodity_model->judge_sweepstakes()){
                $data = ['success' => FALSE, 'msg' => '当前已有抽奖活动，无法添加抽奖活动'];
                echo json_encode($data);
                exit;
            }
            $insert = [
                'name' => $this->input->post('name', TRUE),
                'start_time' => $this->input->post('start_time', TRUE),
                'end_time' => $this->input->post('end_time', TRUE),
                'consume_points' => $this->input->post('consume_points', TRUE),
                'create_time' => $date,
                'update_time' => $date
            ];

            $res = $this->jys_db_helper->add('sweepstakes', $insert);
            if ($res['success']){
                $data = ['success' => TRUE, 'msg' => '添加抽奖活动成功'];
            }else{
                $data = ['success' => FALSE, 'msg' => '添加抽奖活动失败'];
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '输入信息错误', 'error' => $result['msg']];
        }

        echo json_encode($data);
    }

    /**
     * 更新积分抽奖活动
     * @author TangYu
     */
    public function update()
    {
        $this->form_validation->set_rules('id', '活动ID', 'trim|required|numeric');
        $this->form_validation->set_rules('name', '活动名称', 'trim|required');
        $this->form_validation->set_rules('start_time', '开始时间', 'trim|required');
        $this->form_validation->set_rules('end_time', '结束时间', 'trim|required');
        $this->form_validation->set_rules('consume_points', '消费积分', 'trim|required|numeric');

        $result = $this->Common_model->deal_validation_errors();
        if ($result['success']){
            $date = date('Y-m-d H:i:s');
            $sweepstakes_id =  $this->input->post('id', TRUE);
            if (!$this->jys_db_helper->get('sweepstakes', $sweepstakes_id)){
                $data = ['success' => FALSE, 'msg' => '没有该条活动信息，更新失败'];
                echo json_encode($data);
                exit;
            }

            $update = [
                'name' => $this->input->post('name', TRUE),
                'start_time' => $this->input->post('start_time', TRUE),
                'end_time' => $this->input->post('end_time', TRUE),
                'consume_points' => $this->input->post('consume_points', TRUE),
                'update_time' => $date
            ];

            $res = $this->jys_db_helper->update('sweepstakes', $sweepstakes_id, $update);
            if ($res === TRUE){
                $data = ['success' => TRUE, 'msg' => '更新抽奖活动成功'];
            }else{
                $data = ['success' => FALSE, 'msg' => '更新抽奖活动失败'];
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '输入信息错误', 'error' => $result['msg']];
        }

        echo json_encode($data);
    }

    /**
     * 删除抽奖活动
     * @author TangYu
     */
    public function delete()
    {
        $sweepstakes_id = $this->input->post('id', TRUE);
        if (empty($sweepstakes_id) || intval($sweepstakes_id) < 1){
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            echo json_encode($data);
            exit;
        }

        //判断当前抽奖活动是否有人参与，有人参与则无法删除
        $result = $this->jys_db_helper->get_where('sweepstakes_result', ['sweepstakes_id' => $sweepstakes_id]);
        if ($result){
            $data = ['success' => FALSE, 'msg' => '当前活动已有用户参与，无法删除'];
            echo json_encode($data);
            exit;
        }

        $res = $this->jys_db_helper->delete('sweepstakes', $sweepstakes_id);
        if ($res){
            $data = ['success' => TRUE, 'msg' => '删除活动抽奖成功'];
        }else{
            $data = ['success' => FALSE, 'msg' => '删除活动抽奖失败'];
        }

        echo json_encode($data);
    }

    /**
     * 分页获取抽奖活动
     * @param int $page
     * @param int $page_size
     */
    public function get_sweepstakes_info($page = 1, $page_size = 10)
    {
        $result = $this->Sweepstakes_commodity_model->paginate_sweepstakes($page, $page_size);

        echo json_encode($result);
    }
}