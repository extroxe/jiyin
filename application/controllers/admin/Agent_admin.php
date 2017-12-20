<?php
if (!defined('BASEPATH'))
 exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Agent_admin.php
 *
 *   Description: 代理商管理
 *
 *       Created: 2016-11-16 19:43:11
 *
 *        Author: zourui
 *
 * =========================================================
 */
 
class Agent_admin extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_db_helper']);
        $this->load->model(['Agent_model', 'Common_model']);
    }

    /**
     * 获取代理商所有商品分类
     */
    public function get_all_category_for_agent()
    {
        $role_id       = $_SESSION['role_id'];
        $user_id       = $_SESSION['user_id'];
        $page          = intval($this->input->post('page', TRUE)) > 0 ? intval($this->input->post('page', TRUE)) : 1;
        $page_size     = intval($this->input->post('page_size')) > 0 ? intval($this->input->post('page_size', TRUE)) : 10;
        $start_time    = $this->input->post('start_time');
        $end_time      = $this->input->post('end_time');
        $keyword       = $this->input->post('keyword');
        $name          = $this->input->post('name');
        
        $response = $this->Agent_model->get_all_category_for_agent($page, $page_size, $role_id, $user_id, $start_time, $end_time, $keyword, $name);

        echo json_encode($response);
    }

    /**
     * 删除代理商分类
     */
    public function delete_agent_index()
    {
        $id = $this->input->post('id');
        $result = $this->Agent_model->delete_agent_index($id);

        echo json_encode($result);
    }

    /**
     * 添加代理商主页
     */
    public function add_agent_category()
    {
        $time = date('Y-m-d H:i:s');
        $array = $this->input->post('commodity_category');
        $array = json_decode($array, TRUE);

        if (is_array($array) && !empty($array)) {
            $this->db->trans_start();
            $category_info = $this->Agent_model->check_agent_index(trim($array[0]['name']), $array[0]['agent_id']);
            if (!empty($category_info) && is_array($category_info)) {
                $result = ['success' => 'FALSE', 'msg' => '当前主页已经存在，请勿重复添加'];
                echo json_encode($result);die;
            }else{
                //添加代理商主页
                $add_agent_index = [
                    'name' => $array[0]['name'],
                    'color' => $array[0]['color'],
                    'agent_id' => $array[0]['agent_id'],
                    'create_time' => $time,
                    'update_time' => $time
                ];
                $agent_index_result = $this->Agent_model->add_agent_index($add_agent_index);
                foreach ($array as $key => $value) {
                    //判断排序是否符合要求
                    $rank_result = $this->Agent_model->get_agent_rank('agent_home', ['rank' => $value['rank']]);
                    if (!empty($rank_result) || $value['rank'] < 0) {
                        $result = ['success' => FALSE, 'msg' => '商品排序已存在,请勿重复添加'];
                        echo json_encode($result);die;
                    }
                    $add_agent_home[$key]['rank'] = $value['rank'];
                    $add_agent_home[$key]['agent_commodity_id'] = $value['agent_commodity_id'];
                    $add_agent_home[$key]['agent_index_id'] = $agent_index_result['insert_id'];
                    $add_agent_home[$key]['update_time'] = $time;
                    $add_agent_home[$key]['create_time'] = $time;
                }
                //添加主页商品
                $agent_home_result = $this->Agent_model->add_agent_home($add_agent_home);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['msg'] = '添加失败';
                $result['success'] = FALSE;
            } else {
                $this->db->trans_commit();
                $result['success'] = TRUE;
                $result['msg'] = '添加成功';
            }
        }else {
            $result['msg'] = '请选择要添加的商品';
            $result['success'] = FALSE;
        }


        echo json_encode($result);
    }

    /**
     * 批量添加代理商商品
     */
    public function add_agent_commodity_category()
    {
        $time = date('Y-m-d H:i:s');
        $agent_id = $_SESSION['user_id'];
        $array = $this->input->post('commodity_category');
        $array = json_decode($array, TRUE);
        if (is_array($array) && !empty($array)) {
            foreach ($array as $key => $value) {
                $category_info = $this->Agent_model->get_agent_commodity_by_category($value);
                if (!empty($category_info)) {
                    $result = ['success' => 'FALSE', 'msg' => '当前商品已经存在或未选择商品!'];
                    echo json_encode($result);exit;
                }else{
                    $array[$key]['update_time'] = $time;
                    $array[$key]['create_time'] = $time;
                }
            }
            $data = $this->Agent_model->add_agent_home($array);
            if ($data) {
                $result = ['success' => 'TRUE', 'msg' => '添加成功'];
            }else{
                $result = ['success' => 'FALSE', 'msg' => '添加失败'];
            }
        }

        echo json_encode($result);
    }

    /**
     * 根据名称获取代理商分类
     */
    public function get_category_by_name()
    {
        $index_id = $this->input->post('index_id');
        $result = $this->Agent_model->get_category_by_name($index_id);

        echo json_encode($result);
    }

    /**
     * 根据id删除代理商主页商品
     */
    public function delete_agent_home_by_id()
    {
        $id = $this->input->post('id');
        $agent_index_id = $this->input->post('agent_index_id');

        $result = $this->Agent_model->delete_agent_home_by_id($id, $agent_index_id);

        echo json_encode($result);
    }

    //修改代理商主页
    public function update_agent_index()
    {
        $update['color'] = $this->input->post('color');
        $update['name'] = $this->input->post('name');
        $update['id'] = $this->input->post('index_id');

        $result = $this->Agent_model->update_agent_index($update);

        echo json_encode($result);
    }

    /**
     * 调整排序
     */
    public function adjust_rank()
    {
        $data['success'] = FALSE;
        $data['msg'] = '更改商品排序失败';

        $level_post = $this->input->post('id');
        $level_ids = explode(',', $level_post);
        if (empty($level_ids) || !is_array($level_ids))
        {
            $data['msg'] = '接收参数有误！';
            return $data;
        }
        $data['data'] = $level_ids;
        $this->db->trans_start();
        $level_rank = array();
        foreach ($level_ids as $key=>$val)
        {
            $level_rank['rank'] = $key + 1;
            if (!empty($val)){
                $res = $this->Agent_model->update_rank($val, $level_rank);
                if (!$res)
                {
                    $this->db->trans_rollback();
                }
                else
                {
                    $this->db->trans_commit();
                    $data['success'] = TRUE;
                    $data['msg'] = '更改商品排序成功';
                }
            }
        }
        $this->db->trans_complete();

        echo json_encode($data);
    }

    /**
     * 获取所有的代理商接口
     */
    public function get_all_agent_code()
    {
        $result = ['success' => FALSE, 'msg' => '获取代理商接口失败', 'data' => array()];
        $data = $this->jys_db_helper->get_where_multi('system_code', ['type' => 'interface_name']);
        if (!empty($data)) {
            $result['success'] = TRUE;
            $result['msg'] = '获取代理商接口成功';
            $result['data'] = $data;
        }

        echo json_encode($result);
    }

    /**
     * 获取所有代理商
     */
    public function get_all_agent()
    {
        $page = $this->input->post('page') ? $this->input->post('page') : 1;
        $page_size = $this->input->post('page_size') ? $this->input->post('page_size') : 10; 
        $keywords = $this->input->post('keywords');
        $result = $this->Agent_model->get_all_agent($page, $page_size, $keywords);

        echo json_encode($result);
    }

    /**
     * 分页获取代理商商品列表
     */
    public function get_agent_commodity_page() 
    {
        $this->form_validation->set_rules('page', '页数', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('page_size', '页内数据个数', 'trim|required|greater_than[0]');

        $this->Common_model->deal_validation_errors();

        $page       = $this->input->get('page');
        $page_size  = $this->input->get('page_size');
        $keyword    = $this->input->post('keyword', TRUE);
        $name       = $this->input->post('name', TRUE);
        $role_id    = $_SESSION['role_id'];
        $user_id    = $_SESSION['user_id'];

        $result = $this->Agent_model->get_agent_commodity_page($page, $page_size, $keyword, $role_id, $user_id, $name);
        
        echo json_encode($result);
    }

    /**
     * 添加代理商商品
     */
    public function add_agent_commodity() 
    {
        $now_date = date('Y-m-d H:i:s');
        $array = $this->input->post('commodity_category');
        $array = json_decode($array, TRUE);
        if (is_array($array) && !empty($array)) {
            foreach ($array as $key => $value) {
                $array[$key]['create_time'] = $now_date;
                $array[$key]['update_time'] = $now_date;
            }
        }
        $result = $this->Agent_model->add_agent_commodity($array);
        
        echo json_encode($result);
    }

    /**
     * 更新代理商商品
     */
    public function update_agent_commodity() 
    {
        $this->form_validation->set_rules('id', 'ID', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('price', '主页名称', 'trim|required');

        $this->Common_model->deal_validation_errors();

        $id = $this->input->post('id');
        $price = $this->input->post('price');
        $now_date = date('Y-m-d H:i:s');
        if ($price < 0.01) {
            $price = 0.01;
        }
        $update = [
            'id' => $id,
            'price' => $price,
            'update_time' => $now_date
        ];

        $result = $this->Agent_model->update_agent_commodity($id, $update);

        echo json_encode($result);
    }

    /**
     * 删除代理商商品
     */
    public function delete_agent_commodity() 
    {
        $this->form_validation->set_rules('id', 'ID', 'trim|required|greater_than[0]');

        $this->Common_model->deal_validation_errors();

        $id = $this->input->post('id');

        $result = $this->Agent_model->delete_agent_commodity($id);
        echo json_encode($result);
    }

    /**
     * 根据代理商id获取代理商分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function agent_paginate_by_id($page = 1, $page_size = 10)
    {
        $keyword = $this->input->post('keywords', TRUE);
        $agent_id = $this->input->post('agent_id', TRUE);

        $data = $this->Agent_model->agent_paginate_by_id($page, $page_size, $keyword, $agent_id);

        echo json_encode($data);
    }

}