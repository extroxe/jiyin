<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Common_model.php
 *     Description: 公共模型类
 *         Created: 2016-11-12 20:20:56
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
Class Common_model extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->library(['encrypt']);
    }

    /**
     * 根据attachment_id获取附件信息
     *
     * @param $attachment_id
     * @return bool
     */
    public function get_attachment($attachment_id){
        $attachment = $this->jys_db_helper->get($attachment_id, 'attachment');
        if (!empty($attachment)){
            $attachment['path'] = base_url().$attachment['path'];
            return $attachment;
        }

        return FALSE;
    }

    /**
     * 根据附件id获取附件信息
     * @param $attachment_id
     * @return bool
     */
    public function get_attachment_by_id($attachment_id){
        $attachment = $this->jys_db_helper->get($attachment_id, 'attachment');

        if (!empty($attachment)){
            $attachment['path'] = base_url().$attachment['path'];
            return $attachment;
        }

        return FALSE;
    }


    /**
     * 将对象转化为数组并去掉id
     *
     * @param $obj
     * @return array $arr
     */
    public function obj_to_arr_except_id($obj){
        if (!is_object($obj)){
            return FALSE;
        }

        $arr = get_object_vars($obj);

        foreach ($arr as $key => $value) {
            if ($key != 'id'){
                $new_arr[$key] = $value;
            }
        }

        return $new_arr;
    }

    

    /**
     * 表单验证错误信息处理
     * @return array   错误信息
     */
    public function deal_validation_errors() {
        $res['success'] = FALSE;
        $res['msg'] = '';
        // var_dump(validation_errors());exit;
        if (!$this->form_validation->run()) {

            preg_match_all("/<p>(.*?)<\/p>/i", validation_errors(), $out);

            foreach ($out[1] as $error) {
                $res['msg'] .= trim($error).'<br/>';
            }
        }else{
            $res['success'] = TRUE;
        }

        return $res;
    }

    /**
     * 判断session是否过期或无效
     * @return bool  有效返回FASLE，无效返回TRUE
     */
    public function session_timeout(){
        if (!isset($_SESSION['username']) || empty($_SESSION['username'])){
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 根据条件给数组去重（二维数组）
     *
     * @param array $arr 需去重的数据
     * @param null $condition 条件
     * @return array
     */
    public function unique_queue($arr = [], $condition = NULL){
        if (empty($arr)){
            return [];
        }

        foreach ($arr as $key => $row){
            if (empty($condition)){
                $row = join(',', $row);
            }else{
                $rows = [];
                foreach ($condition as $v){
                    $rows[] = $row[$v];
                }
                $row = join(',', $rows);
            }

            $temp[] = $row;
        }
        $temp = array_unique($temp);
        foreach ($temp as $key => $row){
            $row = explode(',', $row);
            foreach ($condition as $k => $v){
                $queue[$key][$v] = $row[$k];
            }
        }

        return $queue;
    }


    /**
     * 分页模板
     *
     * @param int $page 当前页
     * @param int $total_page 总页数
     * @param bool $jump 是否具有跳转模块
     * @return bool|string
     */
    public function render($page = 1, $total_page = 1, $page_num = 7, $jump = TRUE){
        if (empty($page) || empty($total_page)){
            return FALSE;
        }
        $render = '<span>';

        if ($page != 1){
            $render .= '<a class="prev-page">上一页</a>';
        }

        for ($i = 1; $i <= $total_page; $i++){
            if ($i == $page){
                $render .= '<a class="current-page">'.$i.'</a>';
            }else{
                if ($total_page >= $page_num){
                    if ($i == 2){
                        $render .= '<b class="page-break">...</b>';
                    }

                    if (abs($i - $page) <= 2){
                        $render .= '<a class="point-page">'.$i.'</a>';
                    }else if ($i == 1 || $i == 2){
                        $render .= '<a class="point-page">'.$i.'</a>';
                    }
                }else{
                    $render .= '<a class="point-page">'.$i.'</a>';
                }
            }
        }

        if ($page < $total_page){
            if ($total_page >= $page_num){
                $render .= '<b class="page-break">...</b>';
            }
            $render .= '<a class="next-page">下一页</a>';
        }

        $render .= '</span>';

        if ($jump){
            $render .= '<span class="skip"><em>共<b>'.$total_page.'</b>页 到第</em><input type="text" value="'.$page.'"><em>页</em><a class="skip-btn">确定</a></span>';
        }

        return $render;
    }


    /**
     * 处理banner URL问题
     * @param array $banner
     * @return array
     */
    public function deal_banner_url($banner = array())
    {
        if (empty($banner) || !is_array($banner)){
            return [];
        }
        //处理banner URL问题
        for ($i = 0; $i < count($banner); $i++){
            if (strpos($banner[$i]['url'], 'http') === FALSE){
                $banner[$i]['url'] = site_url().$banner[$i]['url'];
            }
        }

        return $banner;
    }

    /**
     * 格式化商品名称，组成(商品名 商品规格名 包装名)，前台方便显示
     * @param array $commodity
     * @return array
     */
    public function format_commodity_name($commodity = array())
    {
        if (empty($commodity) || !is_array($commodity)) {
            return array();
        }

        // 根据是否有commodity_center.name组装商品的commodity_name字段
        if (count($commodity) == count($commodity, 1)) {
            //一维数组
            if (!empty($commodity['commodity_center_name'])) {
                $specification_name = $commodity['commodity_center_name'];
            } elseif (!empty($commodity['commodity_specification_name'])) {
                $specification_name = $commodity['commodity_specification_name'];
            } else {
                $specification_name = '';
            }
            $commodity['commodity_name'] = $commodity['commodity_name'].' '.$specification_name.' '.$commodity['package_type_name'];
        } else {
            //二维数组
            for ($i = 0; $i < count($commodity); $i++) {
                if (!empty($commodity[$i]['commodity_center_name'])) {
                    $specification_name = $commodity[$i]['commodity_center_name'];
                } elseif (!empty($commodity[$i]['commodity_specification_name'])) {
                    $specification_name = $commodity[$i]['commodity_specification_name'];
                } else {
                    $specification_name = '';
                }
                $commodity[$i]['commodity_name'] = $commodity[$i]['commodity_name'].' '.$specification_name.' '.$commodity[$i]['package_type_name'];
            }
        }

        return $commodity;
    }
}