<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  System_setting.php
 *
 *     Description:  系统设置控制器
 *
 *         Created:  2017-3-8 14:44:36
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */

class System_setting extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->model('System_setting_model');
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

}