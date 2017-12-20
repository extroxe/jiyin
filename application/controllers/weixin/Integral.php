<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Integral.php
 *
 *     Description:  积分商城控制器
 *
 *         Created:  2016-12-19 16:01:22
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */

Class Integral extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->library(['form_validation']);
        $this->load->model(['Banner_model', 'Common_model', 'Commodity_model', 'Order_model']);
    }

    /**
     * 积分商城首页
     */
    public function index(){
        $data['title'] = "积分商城";
        $data['js'] = array('integral_index');
        $data['css'] = array('integral_index');
        $data['is_search'] = TRUE;
        $data['tab_nav'] = TRUE;
        $data['active_flag'] = 1;
        $data['integral'] = TRUE;
        $data['banner'] = $this->Common_model->deal_banner_url($this->Banner_model->get_home_banner(5, jys_system_code::BANNER_POSITION_WEIXIN_INTEGRAL_HOME));
        $data['main_content'] = 'integral_index';
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 兑换页
     */
    public function exchange(){
        $data['title'] = "兑换";
        $data['js'] = array('exchange_integral');
        $data['css'] = array('exchange_integral');
        $data['main_content'] = 'exchange_integral';
        $data['tab_nav'] = TRUE;
        $data['active_flag'] = 2;
        $data['integral'] = TRUE;
        $data['user_id'] = $_SESSION['user_id'];
        $data['banner'] = $this->Common_model->deal_banner_url($this->Banner_model->get_home_banner(5, jys_system_code::BANNER_POSITION_WEIXIN_INTEGRAL_EXCHANGE));
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 积分商品详情页
     * @param int $commodity_id
     * @param int $specification_id
     */
    public function commodity_detail($commodity_id = 0, $specification_id = 0){
        if (empty($commodity_id) || intval($commodity_id) < 0){
            redirect('weixin/index/show_404');
            exit;
        }

        $data['title'] = "积分兑换";
        $data['js'] = array('commodity_detail');
        $data['css'] = array('commodity_detail');
        $data['main_content'] = 'commodity_detail';
        $data['commodity_id'] = $commodity_id;
        $data['commodity_specification_id'] = $specification_id;
        $data['thumbnails'] = $this->Commodity_model->get_pic_by_commodity_id($specification_id, TRUE)['data'];

        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 确认订单(积分)
     */
    public function confirm_order(){
        $data['title'] = "确认订单";
        $data['js'] = array('confirm_order');
        $data['css'] = array('confirm_order');
        $data['main_content'] = 'confirm_order';
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 积分抽奖
     */
    public function draw(){
        $data['title'] = "积分抽奖";
        $data['js'] = array('awardRotate', 'integral_draw');
        $data['css'] = array('integral_draw');
        $data['main_content'] = 'integral_draw';
        $data['tab_nav'] = TRUE;
        $data['active_flag'] = 2;
        $data['integral'] = TRUE;
        $data['user_id'] = $_SESSION['user_id'];
        $data['banner'] = $this->Common_model->deal_banner_url($this->Banner_model->get_home_banner(5, jys_system_code::BANNER_POSITION_WEIXIN_INTEGRAL_SWEEPSTAKES));
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 积分夺宝
     */
    public function indiana(){
        $data['title'] = "积分夺宝";
        $data['js'] = array('Integral_indiana');
        $data['css'] = array('Integral_indiana');
        $data['main_content'] = 'Integral_indiana';
        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 领取奖品
     */
    public function receive_prize($id = 0, $insert_id = 0, $is_indiana = 1){
        if (empty($id) || intval($id) < 0 || empty($insert_id) || intval($insert_id) < 0 || !$this->Order_model->exist_prize_places($_SESSION['user_id'], $id, $is_indiana)){
            redirect('weixin/index/show_404');
            exit;
        }
        $data['title'] = "领取奖品";
        $data['js'] = array('receive_prize');
        $data['css'] = array('receive_prize');
        $data['main_content'] = 'receive_prize';
        if ($is_indiana == 1){
            $integral_indiana = $this->jys_db_helper->get('integral_indiana', $id);
            $data['commodity_id'] = $integral_indiana['commodity_id'];
            $data['id'] = $insert_id;
            $data['is_indiana'] = TRUE;
        }else{
            $sweepstakes_commodity = $this->jys_db_helper->get('sweepstakes_commodity', $id);
            $data['commodity_id'] = $sweepstakes_commodity['commodity_id'];
            $data['id'] = $insert_id;
            $data['is_indiana'] = FALSE;
        }

        $this->load->view('mobile/includes/template_view', $data);
    }

    /**
     * 添加奖品订单
     */
    public function add_prize_order(){
        $user_id = $_SESSION['user_id'];
        $commodity_id = $this->input->post('commodity_id', TRUE);
        $is_indiana = $this->input->post('is_indiana', TRUE);
        $address_id = $this->input->post('address_id', TRUE);

        if ($is_indiana == 1){
            $integral_indiana_id = $this->input->post('id', TRUE);
            $result = $this->Order_model->add_prize_order($user_id, $commodity_id, $address_id, Jys_system_code::TERMINAL_TYPE_WEIXIN, TRUE, $integral_indiana_id);
        }else{
            $sweepstakes_commodity_id = $this->input->post('id', TRUE);
            $result = $this->Order_model->add_prize_order($user_id, $commodity_id, $address_id, Jys_system_code::TERMINAL_TYPE_WEIXIN, FALSE, $sweepstakes_commodity_id);
        }


        echo json_encode($result);
    }

    /**
     * 夺宝详情
     * @param int $specification_id 商品规格ID
     */
    public function indiana_detail($specification_id = 0){
        $data['title'] = "夺宝详情";
        $data['js'] = array('indiana_detail');
        $data['css'] = array('indiana_detail');
        $data['main_content'] = 'indiana_detail';
        $data['specification_id'] = $specification_id;
        $this->load->view('mobile/includes/template_view', $data);
    }
}