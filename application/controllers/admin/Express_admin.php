<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Express_admin.php
 *
 *   Description: 快递信息管理
 *
 *       Created: 2016-12-27 10:37:33
 *
 *        Author: wuhaohua
 *
 * =========================================================
 */
class Express_admin extends CI_Controller{
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation']);
    }

    /**
     * 获取所有快递公司
     */
    public function get_all_express_company() {
        $result = array('success' => FALSE, 'msg' => '获取快递公司失败', 'data' => array());
        $data = $this->jys_db_helper->all('express_company');
        if (!empty($data) && is_array($data) && isset($data['success']) && $data['success']) {
            $result['success'] = TRUE;
            $result['msg'] = '获取快递公司成功';
            $result['data'] = $data['data'];
        }

        echo json_encode($result);
    }

}