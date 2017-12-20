<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Integral_indiana.php
 *     Description: 积分夺宝控制器
 *         Created: 2017-03-03 10:23:47
 *          Author: TangYu
 *
 * =====================================================================================
 */
class Integral_indiana extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['Jys_db_helper', 'form_validation']);
        $this->load->model(['Integral_indiana_model', 'Order_model', 'Common_model', 'Category_model', 'Commodity_model', 'System_setting_model']);
    }

    /**
     * @param int $page
     * @param int $page_size
     * @author SunZuoSheng
     */
    public function index($page = 1, $page_size = 10){
        $data['title'] = "赛安生物-积分夺宝";
        $data['js'] = array('integral_indiana');
        $data['css'] = array('integral_indiana');
        $data['main_content'] = 'integral_indiana';
        $data['isset_nav'] = FALSE;
        $data['all_indiana'] = $this->Integral_indiana_model->get_all_indiana_info($page, $page_size)['data'];
        if (!empty($data['all_indiana'])) {
            $data['all_indiana'] = $this->Common_model->format_commodity_name($data['all_indiana']);
        }
        $data['indiana_result_info'] = $this->Integral_indiana_model->get_result_info()['data'];
        if (!empty($data['indiana_result_info'])) {
            $data['indiana_result_info'] = $this->Common_model->format_commodity_name($data['indiana_result_info']);
        }
        $data['rules'] = $this->System_setting_model->get_indiana_rules()['data']['value'];
        $this->load->view('includes/template_view', $data);
    }

    /**
     * @param null $commodity_id
     * @param null $integral_indiana_id
     * @return bool
     * @author SunZuoSheng
     */
    public function detail($commodity_id = NULL, $integral_indiana_id = NULL){
        if (empty($commodity_id) && empty($integral_indiana_id)){
            return FALSE;
        }

        $data['title'] = "赛安生物-积分夺宝详情";
        $data['js'] = array('commodity_detail', 'jquery.imagezoom.min', 'integral_indiana_detail');
        $data['css'] = array('commodity_detail', 'integral_indiana_detail');
        $data['main_content'] = 'integral_indiana_detail';
        $data['isset_nav'] = FALSE;
        $data['collection'] = $this->Category_model->get_category();
        $data['recommend'] = $this->Commodity_model->get_home_recommend(4, 1);
        if (!empty($data['recommend'])) {
            $data['recommend'] = $this->Common_model->format_commodity_name($data['recommend']);
        }
        $data['commodity'] = $this->Commodity_model->get_commodity_by_condition(['commodity.id' => $commodity_id])['data'];
        if (!empty($data['commodity'])) {
            $data['commodity'] = $this->Common_model->format_commodity_name($data['commodity']);
        }
        $data['integral_indiana'] = $this->Integral_indiana_model->get_indiana_info($integral_indiana_id)['data'];
        if (!empty($data['integral_indiana'])) {
            $data['integral_indiana'] = $this->Common_model->format_commodity_name($data['integral_indiana']);
        }
        if (empty($data['commodity'])){
            show_404();
        }
        $data['commodity_thumbnail'] = $this->Commodity_model->show_thumbnail($commodity_id)['data'];
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 获取所有夺宝活动信息
     * @param int $page
     * @param int $page_size
     * @author TangYu
     */
    public function get_all_indiana_info($page = 1, $page_size = 10)
    {
        if (empty($page_size) || empty($page) || intval($page) < 1 || intval($page_size) < 1){
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            echo json_encode($data);
            exit;
        }

        $result = $this->Integral_indiana_model->get_all_indiana_info($page, $page_size);
        //计算剩余柱数
        if ($result['success']) {
            $result['data'] = $this->Common_model->format_commodity_name($result['data']);
            for ($i = 0; $i < count($result['data']); $i++) {
                $result['data'][$i]['remain_bet'] = ceil($result['data'][$i]['total_points'] / $result['data'][$i]['amount_bet']) - $result['data'][$i]['current_bet'];
            }
        }
        echo json_encode($result);
    }

    /**
     * 根据ID获取当前ID夺宝活动信息
     * @param int $indiana_id
     * @return mixed
     * @author TangYu
     */
    public function get_indiana_info($indiana_id = 0)
    {
        if (empty($indiana_id) || intval($indiana_id) < 1) {
            $data = array('success' => FALSE, 'msg' => '参数错误');
            return $data;
        }

        $result = $this->Integral_indiana_model->get_indiana_info($indiana_id);
        //计算剩余柱数
        if ($result['success']) {
            $result['data']['remain_bet'] = ceil($result['data']['total_points'] / $result['data']['amount_bet']) - $result['data']['current_bet'];
        }

        return $result['data'];
    }

    /**
     * 参与夺宝、判断活动状态、判断是否具备资格以及是否生成夺宝结果
     * @author TangYu
     */
    public function join_integral_indiana()
    {
        $indiana_id = $this->input->post('id', TRUE);

        if (intval($indiana_id) < 1) {
            $data = array('success' => FALSE, 'msg' => '参数错误');
            echo json_encode($data);
            exit;
        }

        $integral_indiana = $this->get_indiana_info($indiana_id);
        if ($integral_indiana['integral_indiana_status'] == jys_system_code::INTEGRAL_INDIANA_STATUS_DONE){
            $data = ['success' => FALSE, 'msg' => '参与人数已满，参与失败'];
            echo json_encode($data);
            exit;
        }
        //判断是否具有参与资格
        $qualifications_result = $this->has_qualifications($integral_indiana);
        if ($qualifications_result['success']) {
            //组装插入信息
            $insert_info = [
                'integral_indiana_id' => $indiana_id,
                'user_id' => $_SESSION['user_id'],
                'point' => $integral_indiana['amount_bet'],
                'create_time' => date('Y-m-d H:i:s')
            ];

            $res = $this->Integral_indiana_model->join_integral_indiana($indiana_id, $insert_info);
            if ($res['success']) {
                //判断参与人数是否已满，满了即生成夺宝结果
                if ($this->is_full($indiana_id)) {
                    $indiana_result = $this->generate_result($indiana_id);
                }
                if (isset($indiana_result) && $indiana_result['success']) {
                    $res['msg'] = "参与夺宝活动成功，" . $indiana_result['msg'];
                }
            }
        } else {
            $res = $qualifications_result;
        }

        echo json_encode($res);
    }

    /**
     * 获取夺宝结果
     * @author TangYu
     */
    public function get_result_info()
    {
        $result = $this->Integral_indiana_model->get_result_info();
        if ($result['success']) {
            $result['data'] = $this->Common_model->format_commodity_name($result['data']);
        }

        echo json_encode($result);
    }

    /**
     * 分页获取我参与的夺宝活动，并判断是否夺宝
     * @param int $page
     * @param int $page_size
     * @author TangYu
     */
    public function my_indiana($page = 1, $page_size = 10)
    {
        $result = $this->Integral_indiana_model->my_indiana($_SESSION['user_id'], $page, $page_size);
        if ($result['success']) {
            $result['data'] = $this->Common_model->format_commodity_name($result['data']);
            $win_result = $this->is_win();

            //判断是否夺宝
            if (!empty($win_result)){
                for ($i = 0; $i < count($result['data']); $i++){
                    $result['data'][$i]['is_win'] = FALSE;
                    for ($j = 0; $j < count($win_result); $j++){
                        if ($result['data'][$i]['integral_indiana_id'] == $win_result[$j]['integral_indiana_id']){
                            $result['data'][$i]['is_win'] = TRUE;
                        }
                    }
                }
            }
        }

        echo json_encode($result);
    }

    /**
     * 生成夺宝结果，从下注表中先随机取出一条符合的数据，然后再加入到结果表
     * @param int $indiana_id
     * @return array
     * @author TangYu
     */
    public function generate_result($indiana_id = 0)
    {
        if (empty($indiana_id) || intval($indiana_id) < 1) {
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            return $data;
        }

        $res = $this->get_indiana_info($indiana_id);
        $rand = rand(0, $res['current_bet']);

        $result = $this->Integral_indiana_model->rand_result($indiana_id, $rand);
        if ($result['success']) {
            $insert = [
                'integral_indiana_id' => $indiana_id,
                'integral_indiana_bet_id' => $result['data']['id'],
                'user_id' => $result['data']['user_id'],
                'create_time' => date('Y-m-d H:i:s')
            ];

            if ($this->Integral_indiana_model->add_data($insert)) {
                $data = ['success' => TRUE, 'msg' => '已生成结果'];
            } else {
                $data = ['success' => FALSE, 'msg' => '生成结果并加入数据表失败'];
            }
        } else {
            $data = ['success' => FALSE, 'msg' => '生成夺宝结果失败'];
        }
        return $data;
    }

    /**
     * 根据夺宝规则判断当前用户是否有参与夺宝资格
     * @param array $integral_indiana
     * @return array
     * @author TangYu
     */
    protected function has_qualifications($integral_indiana = array())
    {
        $data = ['success' => TRUE, 'msg' => '当前用户具有参与资格'];
        if (empty($integral_indiana) || !is_array($integral_indiana)){
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            return $data;
        }

        $user_info = $this->jys_db_helper->get('user', $_SESSION['user_id']);
        //获取已完成订单情况，计算消费总额
        $user_expenditure = 0;
        $user_order_info = $this->Order_model->get_user_expenditure($_SESSION['user_id']);
        if ($user_order_info['success']) {
            for ($i = 0; $i < count($user_order_info['data']); $i++) {
                $user_expenditure += $user_order_info['data'][$i]['total_price'];
            }
        } else {
            $user_expenditure = 0;
        }

        //判断是否有资格参与夺宝
        if (!empty($integral_indiana['user_total_point']) && $user_info['current_point'] <= $integral_indiana['user_total_point']) {
            $data = ['success' => FALSE, 'msg' => '您当前拥有积分小于活动规则参与积分底限，无法参与'];
            return $data;
        }
        if (!empty($integral_indiana['user_expenditure']) && $user_expenditure <= $integral_indiana['user_expenditure']) {
            $data = ['success' => FALSE, 'msg' => '您当前总消费金额小于活动规则参与消费金额底限，无法参与'];
            return $data;
        }
        if ($integral_indiana['register_start_time'] != NULL) {
            if ($user_info['create_time'] < $integral_indiana['register_start_time']) {
                $data = ['success' => FALSE, 'msg' => '您注册时间超出活动规则注册时间段，无法参与'];
                return $data;
            }
        }
        if ($integral_indiana['register_end_time'] != NULL) {
            if ($user_info['create_time'] > $integral_indiana['register_end_time']) {
                $data = ['success' => FALSE, 'msg' => '您注册时间超出活动规则注册时间段，无法参与'];
                return $data;
            }
        }

        return $data;
    }

    /**
     * 判断积分夺宝活动参与人数已满
     * @param int $indiana_id
     * @return bool
     * @author TangYu
     */
    public function is_full($indiana_id = 0)
    {
        if (empty($indiana_id) || intval($indiana_id) < 1) {
            return FALSE;
        }
        $integral_indiana = $this->get_indiana_info($indiana_id);
        $total_bet = $this->jys_db_helper->get_total_num('integral_indiana_bet', ['integral_indiana_id' => $indiana_id]);
        if ($integral_indiana['remain_bet'] == 0 && $integral_indiana['current_bet'] <= $total_bet) {
            $this->jys_db_helper->update('integral_indiana', $indiana_id, ['status'=>Jys_system_code::INTEGRAL_INDIANA_RESULT_STATUS_RECEIVED]);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 查看夺宝结果表中，当前用户的数据
     * @return array
     * @author TangYu
     */
    public function is_win()
    {
        $result = $this->jys_db_helper->get_where_multi('integral_indiana_result', ['user_id' => $_SESSION['user_id'], 'status !=' => Jys_system_code::INTEGRAL_INDIANA_RESULT_STATUS_SYSTEM_EXTRACTION]);
        if ($result) {
            return $result;
        } else {
            return [];
        }
    }
}