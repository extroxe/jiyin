<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  System_setting_admin.php
 *
 *     Description:  系统设置控制器
 *
 *         Created:  2016-12-09 10:03:56
 *
 *          Author:  zourui
 *
 * =====================================================================================
 */

Class System_setting_admin extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->library(['form_validation']);
        $this->load->model('System_setting_model');
    }

    /**
     * 订单预计完成天数设置
     */
    public function set_load_status(){
        $this->form_validation->set_rules('value', '系统项值', 'trim|required');

        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $array['name']        = 'orderExpectedCompletionDays';
            $array['value']        = $this->input->post('value', TRUE);
            $array['update_time']  = date('Y-m-d h:i:s');
            $array['create_time']  = date('Y-m-d h:i:s');

            $status = $this->System_setting_model->set_load_status($array);
            if($status){
                $data['success'] = TRUE;
                $data['mag'] = '操作成功';
            }else{
                $data['success'] = FALSE;
                $data['mag'] = '操作失败';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '数据输入错误';
        }

        echo json_encode($data);

    }

    /**
     * 获取订单预计完成天数
     */
     public function get_system_info(){
        $result = $this->System_setting_model->get_system_info();

        echo json_encode($result);

     }

    /**
     * 积分抽奖规则设置
     */
    public function sweepstakes_rules(){
        $this->form_validation->set_rules('content', '规则', 'required');

        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $array['name'] = 'sweepstakesRules';
            $array['value'] = htmlspecialchars($this->input->post('content', FALSE));
            $array['update_time']  = date('Y-m-d h:i:s');
            $array['create_time']  = date('Y-m-d h:i:s');

            $status = $this->System_setting_model->set_load_status($array);
            if($status){
                $data['success'] = TRUE;
                $data['mag'] = '操作成功';
            }else{
                $data['success'] = FALSE;
                $data['mag'] = '操作失败';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '数据输入错误';
        }

        echo json_encode($data);
    }

    /**
     * 积分夺宝规则设置
     */
    public function indiana_rules(){
        $this->form_validation->set_rules('content', '规则', 'required');

        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $array['name'] = 'indianaRules';
            $array['value'] = htmlspecialchars($this->input->post('content', FALSE));
            $array['update_time']  = date('Y-m-d h:i:s');
            $array['create_time']  = date('Y-m-d h:i:s');

            $status = $this->System_setting_model->set_load_status($array);
            if($status){
                $data['success'] = TRUE;
                $data['mag'] = '操作成功';
            }else{
                $data['success'] = FALSE;
                $data['mag'] = '操作失败';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '数据输入错误';
        }

        echo json_encode($data);
    }

    /**
     * 获取积分抽奖规则
     */
    public function get_sweepstakes_rules(){
        $result = $this->System_setting_model->get_sweepstakes_rules();

        echo json_encode($result);
    }

    /**
     * 获取积分夺宝规则
     */
    public function get_indiana_rules(){
        $result = $this->System_setting_model->get_indiana_rules();

        echo json_encode($result);
    }

    /**
     * 热卖商品封面设置
     */
    public function hot_sale_cover(){
        $this->form_validation->set_rules('path', '路径', 'trim|required');

        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $array['name'] = 'hotSaleCover';
            $array['value'] = $this->input->post('path', FALSE);
            $array['update_time']  = date('Y-m-d h:i:s');
            $array['create_time']  = date('Y-m-d h:i:s');

            $status = $this->System_setting_model->set_load_status($array);
            if($status){
                $data['success'] = TRUE;
                $data['mag'] = '操作成功';
            }else{
                $data['success'] = FALSE;
                $data['mag'] = '操作失败';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '数据输入错误';
        }

        echo json_encode($data);
    }

    /**
     * 热换商品封面设置
     */
    public function hot_exchange_cover(){
        $this->form_validation->set_rules('path', '路径', 'trim|required');

        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $array['name'] = 'hotExchangeCover';
            $array['value'] = $this->input->post('path', FALSE);
            $array['update_time']  = date('Y-m-d h:i:s');
            $array['create_time']  = date('Y-m-d h:i:s');

            $status = $this->System_setting_model->set_load_status($array);
            if($status){
                $data['success'] = TRUE;
                $data['mag'] = '操作成功';
            }else{
                $data['success'] = FALSE;
                $data['mag'] = '操作失败';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '数据输入错误';
        }

        echo json_encode($data);
    }

    /**
     * 获取热卖商品封面
     */
    public function get_hot_sale_cover(){
        $result = $this->System_setting_model->get_hot_sale_cover();

        echo json_encode($result);
    }

    /**
     * 获取热换商品封面
     */
    public function get_hot_exchange_cover(){
        $result = $this->System_setting_model->get_hot_exchange_cover();

        echo json_encode($result);
    }

}