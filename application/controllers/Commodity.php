<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =====================================================================================
 *
 *        Filename:  Commodity.php
 *
 *     Description:  商品控制器
 *
 *         Created:  2016-11-24 16:43:24
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */
Class Commodity extends CI_Controller
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['form_validation', 'Jys_db_helper']);
        $this->load->model(['Commodity_model', 'Category_model', 'User_model']);
    }

    /**
     * 商品详情页
     *
     * @param int $commodity_id 商品ID
     * @return mixed
     */
    public function index($commodity_id = 0, $specification_id = 0){
        if (empty($commodity_id)) {
            return FALSE;
        }

        $data['title'] = "商品详情";
        $data['js'] = array('template', 'commodity_detail', 'jquery.imagezoom.min');
        $data['css'] = array('commodity_detail');
        $data['main_content'] = 'commodity_detail';
        $data['isset_search'] = TRUE;
        $data['isset_nav'] = TRUE;


        $data['collection'] = $this->Category_model->get_category();
        $user_info = array();

        if (!empty($_SESSION['user_id']) && !empty($_SESSION['role_id']) && intval($_SESSION['role_id']) == jys_system_code::ROLE_USER) {
            $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $_SESSION['user_id']]);
        }
        $data['commodity'] = $this->Commodity_model->get_commodity_by_condition(['commodity.id' => $commodity_id], FALSE, FALSE, FALSE, $user_info)['data'];
        if (empty($data['commodity'])) {
            show_404();
        } else {
            // 格式化商品名
            $data['commodity'] = $this->Common_model->format_commodity_name($data['commodity']);
        }

        //根据商品类型，获取相应热卖 $commodity_type == 1 或者热换商品 $commodity_type == 2
        $commodity_type = $data['commodity']['is_point'] == 1 ? jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_EXCHANGE : jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_SALE;
        $data['recommend'] = $this->Commodity_model->get_home_recommend(4, $commodity_type, $user_info);
        if (!empty($data['recommend'])) {
            // 格式化商品名
            $data['recommend'] = $this->Common_model->format_commodity_name($data['recommend']);
        }

        $data['commodity_thumbnail'] = $this->Commodity_model->show_thumbnail($commodity_id)['data'];
        $data['commodity']['specification_id_'] = $specification_id;
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 根据商品id获取规格
     * @param $commodity_id
     */
    public function commodity_specification_by_id($commodity_id)
    {
        $data = array('success' => false, 'msg' => '获取失败', 'data' => []);
        if (empty($commodity_id) || intval($commodity_id) < 0) {
            echo json_encode($data);
            exit;
        }
        if (isset($_SESSION['agent_id']) && intval($_SESSION['agent_id'] > 0)) {
            $agent_id = $_SESSION['agent_id'];
        }else{
            $agent_id = NULL;
        }
        $data['data'] = $this->Commodity_model->commodity_specification($commodity_id, $agent_id);
        if (count($data['data']) > 0) {
            $data['success'] = true;
            $data['msg'] = '获取成功';
        }
        echo json_encode($data);
    }

    /**
     * 根据分类ID获取商品信息
     *
     * @param null $category_id
     */
    public function get_commodity_by_category($category_id = NULL)
    {
        if (empty($category_id)) {
            echo json_encode([
                'data' => NULL,
                'success' => FALSE,
                'msg' => '参数错误'
            ]);
        }

        $data = $this->Commodity_model->get_commodity_by_condition(['commodity.category_id', $category_id], TRUE, TRUE);

        echo json_encode($data);
    }

    /**
     * 获取商品评价分页
     *
     * @param int $page 页数
     * @param int $page_siz 页大小
     * @param int $commodity_id 商品ID
     */
    public function evaluation_paginate($page = 1, $page_siz = 10, $commodity_id = 0, $evaluation_level = 0, $commodity_specification_id = 0)
    {
        $data = $this->Commodity_model->evaluation_paginate($page, $page_siz, $commodity_id, $evaluation_level, 1, $commodity_specification_id);
        $data['data'] = $this->Commodity_model->filter_evaluation($data['data']);
        if (intval($commodity_specification_id) > 0) {
            // 查询某一个规格的评价
            $data['total'] = $this->jys_db_helper->get_total_num('commodity_evaluation', ['commodity_id' => $commodity_id, 'commodity_specification_id' => $commodity_specification_id, 'status' => 1]);
        } else {
            // 查询某一个商品的评价
            $data['total'] = $this->jys_db_helper->get_total_num('commodity_evaluation', ['commodity_id' => $commodity_id, 'status' => 1]);
        }

        if ($data['total'] == 0) {
            $praise = 0;
        } else {
            $praise = $this->Commodity_model->get_praise_num($commodity_id) / $data['total'];
        }

        $data['praise_rate'] = round($praise, 2);

        $condition_prefix = "";
        if (intval($commodity_specification_id) > 0) {
            // 查询某一个规格的评价
            $condition_prefix = "commodity_id = {$commodity_id} AND commodity_specification_id = {$commodity_specification_id} AND status = 1 ";
        } else {
            // 查询某一个商品的评价
            $condition_prefix = "commodity_id = {$commodity_id} AND status = 1 ";
        }
        if ($evaluation_level == 1) {
            $data['total_page'] = $this->jys_db_helper->get_total_page('commodity_evaluation', $page_siz, $condition_prefix."AND score >= 4");
        } else if ($evaluation_level == 2) {
            $data['total_page'] = $this->jys_db_helper->get_total_page('commodity_evaluation', $page_siz, $condition_prefix."AND score >= 2 and score < 4");
        } else if ($evaluation_level == 3) {
            $data['total_page'] = $this->jys_db_helper->get_total_page('commodity_evaluation', $page_siz, $condition_prefix."AND score >= 1 and score < 2");
        } else {
            $data['total_page'] = $this->jys_db_helper->get_total_page('commodity_evaluation', $page_siz, $condition_prefix);
        }

        $data['total'] = $data['total'] ? $data['total'] : count($data['data']);
        $data['total_page'] = $data['total_page'] ? $data['total_page'] : 1;

        echo json_encode($data);
    }

    /**
     * 获取评价nav
     */
    public function evaluation_nav()
    {
        $commodity_id = intval($this->input->post('commodity_id', TRUE));
        $commodity_specification_id = intval($this->input->post('commodity_specification_id', TRUE));

        $condition = array('commodity_id' => $commodity_id, 'status' => 1);
        if ($commodity_specification_id > 0) {
            $condition['commodity_specification_id'] = $commodity_specification_id;
        }

        $all_evaluation = $this->jys_db_helper->get_where_multi('commodity_evaluation', $condition);

        $data = $this->Commodity_model->evaluation_nav($all_evaluation);
        echo json_encode($data);
    }

    /**
     * 检验积分是否足够
     */
    public function check_point_enough()
    {
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        } else {
            echo json_encode([
                'success' => FALSE,
                'msg' => '请先登录'
            ]);
            exit;
        }

        $specification_id = intval($this->input->post('specification_id', TRUE));
        $amount = intval($this->input->post('amount', TRUE));

        if ($this->Commodity_model->check_point_enough($user_id, $specification_id, $amount)) {
            $data['success'] = TRUE;
            $data['msg'] = '可兑换';
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '积分不足';
        }

        echo json_encode($data);
    }

    /**
     *  获取推荐现金商品
     */
    public function get_recommend()
    {
        $data = array(
            'success' => FALSE,
            'msg' => '没有推荐商品',
            'data' => NULL
        );
        if (!empty($_SESSION['user_id']) && !empty($_SESSION['role_id']) && intval($_SESSION['role_id']) == jys_system_code::ROLE_USER) {
            $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $_SESSION['user_id']]);
        } else {
            $user_info = array();
        }
        $recommend_commodity = $this->Commodity_model->get_home_recommend(10, jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_SALE, $user_info);
        if (!empty($recommend_commodity)) {
            // 格式化商品名字
            $recommend_commodity = $this->Common_model->format_commodity_name($recommend_commodity);

            $data = array(
                'success' => TRUE,
                'msg' => '找到推荐商品',
                'data' => $recommend_commodity
            );
        }

        echo json_encode($data);
    }

    /**
     * 获取热换商品
     */
    public function get_hot_exchange_commodity()
    {
        $data = array('success' => FALSE, 'msg' => '获取热换商品失败', 'data' => NULL);

        $hot_exchange_commodity = $this->Commodity_model->get_home_recommend();
        if (!empty($hot_exchange_commodity)) {
            $hot_exchange_commodity = $this->Common_model->format_commodity_name($hot_exchange_commodity);
            $data = array('success' => TRUE, 'msg' => '获取热换商品成功', 'data' => $hot_exchange_commodity);
        }

        echo json_encode($data);
    }

    /**
     * 根据商品获取推荐商品
     */
    public function get_commodity_recommend_commodity()
    {
        $commodity_id = $this->input->POST('commodity_id', TRUE);
        if (empty($commodity_id) || intval($commodity_id) < 1) {
            show_404();
            exit;
        }
        $commodity_recommend_commodity = $this->Commodity_model->get_commodity_recommend_commodity($commodity_id);
        if (!empty($commodity_recommend_commodity)) {
            $data = ['success' => TRUE, 'msg' => '获取推荐商品成功', 'data' => $commodity_recommend_commodity];
        } else {
            $data = ['success' => FALSE, 'msg' => '获取推荐商品失败', 'data' => NULL];
        }

        echo json_encode($data);
    }
}