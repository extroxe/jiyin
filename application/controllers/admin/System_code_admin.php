<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: System_code_admin.php
 *
 *   Description: 系统字典表管理
 *
 *       Created: 2016-11-21 15:47:04
 *
 *        Author: sunzuosheng
 *
 * =========================================================
 */

class System_code_admin extends CI_Controller{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_db_helper', 'Jys_system_code']);
        $this->load->model(['Common_model']);
    }

    /**
     * 根据type类型获取字典数据
     *
     * @param string $type 字典类型
     */
    public function get_by_type($type = NULL){
        //如果角色是代理商，则只显示代理商能设置的位置
        if($type == 'banner_position' && $_SESSION['role_id'] == jys_system_code::ROLE_AGENT){
            $data = $this->jys_db_helper->get_where_multi('system_code', ['type'=>$type, 'value'=>jys_system_code::BANNER_POSITION_AGENT_HOME]);
        }else{
            $data = $this->jys_db_helper->get_where_multi('system_code', ['type'=>$type]);
        }
        echo json_encode($data);
    }
}