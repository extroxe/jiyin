<?php
/**
 * =====================================================================================
 *
 *         Filename:  Jys_system_switch.php
 *
 *      Description:  系统开关类
 *
 *          Created:  2016-11-8 16:52:16
 *
 *           Author:  wuhaohua
 *
 * =====================================================================================
 */
class Jys_system_switch {
    private $_CI;

    public function __construct() {
        $this -> _CI = & get_instance();
    }

    public function index() {
        $this -> _switch();
    }

    /**
     * 控制系统开关
     */
    private function _switch() {
        $switch = $this -> _CI->config->item('system_switch');
        if (strtolower($switch) == "off") {
            echo "系统正在维护中...";
            exit ;
        }
    }

}
