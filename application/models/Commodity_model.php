<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Commodity_model.php
 *
 *     Description: 商品模型
 *
 *         Created: 2016-11-21 14:22:43
 *
 *          Author: sunzuosheng
 *
 * =====================================================================================
 */

class Commodity_model extends CI_Model{
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->library(['Jys_tool']);
    }

    /**
     * 添加商品
     *
     * @param array $commodity 商品信息
     * @param null $attachment_ids 缩略图IDS
     * @return mixed
     */
    public function add($commodity = []){
        $data['success'] = FALSE;
        $data['msg'] = '添加失败';

        if (empty($commodity)){
            $data['msg'] = '参数错误';
            return $data;
        }

        // $this->db->trans_begin();
        $data = $this->jys_db_helper->add('commodity', $commodity, TRUE);

        // if ($data['success']){
        //     $commodity_id = $data['insert_id'];
        //     $thumbnail_fail_flag = false;
        //     $thumbnail_arr = [];

        //     if (!empty($commodity['agent_id'])){
        //         $agent_home_data = [
        //             'commodity_id' => $commodity_id,
        //             'agent_id' => $commodity['agent_id'],
        //             'create_time' => date('Y-m-d H:i:s')
        //         ];
        //         $agent_home_data['update_time'] = $agent_home_data['create_time'];
        //         $add_agent_home = $this->jys_db_helper->add('agent_home', $agent_home_data);
        //     }

        //     if (is_array($attachment_ids)){
        //         foreach ($attachment_ids as $attachment_id){
        //             $thumbnail_arr[] = [
        //                 'attachment_id' => $attachment_id,
        //                 'commodity_id' => $commodity_id,
        //                 'create_time' => date('Y-m-d H:i:s')
        //             ];
        //         }

        //         $_data = $this->jys_db_helper->add_batch('commodity_thumbnail', $thumbnail_arr);
        //     }else{
        //         $thumbnail_arr = [
        //             'attachment_id' => $attachment_ids,
        //             'commodity_id' => $commodity_id,
        //             'create_time' => date('Y-m-d H:i:s')
        //         ];
        //         $_data = $this->jys_db_helper->add('commodity_thumbnail', $thumbnail_arr);
        //     }

        //     if (!$_data['success']){
        //         $thumbnail_fail_flag = true;
        //     }

        //     if ($thumbnail_fail_flag && (isset($add_agent_home) && !$add_agent_home['success'])){
        //         $data['success'] = FALSE;
        //         $data['msg'] = '添加失败，缩略图错误';
        //         $this->db->trans_rollback();
        //     }else{
        //         $this->db->trans_commit();
        //     }
        // }

        // if ($this->db->trans_status() === FALSE){
        //     $this->db->trans_rollback();
        // }
        // else{
        //     $this->db->trans_commit();
        // }

        return $data;
    }

    /**
     * 更新商品信息
     *
     * @param int $id 商品ID
     * @param array $commodity 商品信息
     * @param null $attachment_ids 商品缩略图IDS
     * @return mixed
     */
    public function update($id = 0, $commodity = []){
        $data['success'] = FALSE;
        $data['msg'] = '更新失败';

        if (empty($id) || empty($commodity) || intval($id) < 1){
            $data['msg'] = '参数错误';
            return $data;
        }
        // $this->db->trans_begin();
        if ($this->jys_db_helper->update('commodity', $id, $commodity)){
            $data['success'] = TRUE;
            $data['msg'] = '更新成功';
        }

        // if ($data['success']){
        //     $thumbnail_fail_flag = false;
        //     $thumbnail_arr = [];

        //判断agent是否修改。添加、修改、删除三种情况
        // if ($commodity['agent_id']){
        //     $is_update = $this->jys_db_helper->get_where('agent_home', ['commodity_id' => $id]);
        //     if ($is_update){
        //         $update_res = $this->jys_db_helper->update_by_condition('agent_home', ['commodity_id' => $id], ['agent_id' => $commodity['agent_id']]);
        //     }else{
        //         $add_data = [
        //             'commodity_id' => $id,
        //             'agent_id' => $commodity['agent_id'],
        //             'create_time' => date('Y-m-d H:i:s')
        //         ];
        //         $add_data['update_time'] = $add_data['create_time'];
        //         $add_res = $this->jys_db_helper->add('agent_home', $add_data);
        //     }
        // }else{
        //     $delete_res = $this->jys_db_helper->delete_by_condition('agent_home', ['commodity_id' => $id]);
        // }
        // if (!empty($attachment_ids) && is_array($attachment_ids)){
        //     foreach ($attachment_ids as $attachment_id){
        //         $thumbnail_arr[] = [
        //             'attachment_id' => $attachment_id,
        //             'commodity_id' => $id,
        //             'create_time' => date('Y-m-d H:i:s')
        //         ];
        //     }

        //     $_data = $this->jys_db_helper->add_batch('commodity_thumbnail', $thumbnail_arr);
        // }else if (!empty($attachment_ids) && is_string($attachment_ids)){
        //     $thumbnail_arr = [
        //         'attachment_id' => $attachment_ids,
        //         'commodity_id' => $id,
        //         'create_time' => date('Y-m-d H:i:s')
        //     ];
        //     $_data = $this->jys_db_helper->add('commodity_thumbnail', $thumbnail_arr);
        // }else{
        //     $_data['success'] = TRUE;
        // }

        // if (!$_data['success']){
        //     $thumbnail_fail_flag = true;
        // }
        // if ($thumbnail_fail_flag){
        //     $data['success'] = FALSE;
        //     $data['msg'] = '添加失败，缩略图错误';
        //     $this->db->trans_rollback();
        // }else{
        //     $this->db->trans_commit();
        // }
        // }

        // if ($this->db->trans_status() === FALSE){
        //     $this->db->trans_rollback();
        // }
        // else{
        //     $this->db->trans_commit();
        // }

        return $data;
    }

    /**
     * 分页获取商品规格
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param array $condition 条件
     * @param string $keyword 查询关键字
     * @param int $commodity_id 商品id
     * @return array
     */
    public function paginate_for_specification($page = 1, $page_size = 10, $condition = array(), $keyword = '', $commodity_id = 0){
        $data = array(
            'success' => FALSE,
            'msg' => '没有商规格品数据',
            'data' => null,
            'total_page' => 0
        );

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('commodity.name as commodity_name,
                           commodity_specification.*,
                           commodity.type_id as commodity_type,
                           commodity_specification_status.name as specification_status,
                           attachment.path,
                           commodity_center.name as commodity_specification_name,
                           package_type.name as package_type_name,
                           ');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_specification.attachment', 'left');
        $this->db->join('system_code as commodity_specification_status', 'commodity_specification_status.value = commodity_specification.status_id and commodity_specification_status.type = "'.jys_system_code::COMMODITY_SPECIFICATION_STATUS.'"', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        if(intval($commodity_id) > 0){
            $this->db->where('commodity_specification.commodity_id', $commodity_id);
        }
        if (!empty($condition)){
            $this->db->where($condition);
        }
        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('commodity.name', $keyword);
            $this->db->or_like('commodity_specification.name', $keyword);
            $this->db->or_like('commodity_center.name', $keyword);
            $this->db->or_like('package_type.name', $keyword);
            $this->db->group_end();
        }
        $this->db->group_by('commodity_specification.id');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('commodity_specification');

        if ($result && $result->num_rows() > 0){
            $data = array(
                'success' => TRUE,
                'msg' => '',
                'data' => $result->result_array()
            );
            $this->db->select('commodity.id');
            $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
            $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
            $this->db->join('attachment', 'attachment.id = commodity_specification.attachment', 'left');
            $this->db->join('system_code as commodity_specification_status', 'commodity_specification_status.value = commodity_specification.status_id and commodity_specification_status.type = "'.jys_system_code::COMMODITY_SPECIFICATION_STATUS.'"', 'left');
            $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
            $this->db->where('commodity.status_id !=', jys_system_code::COMMODITY_STATUS_DELETE);
            if(intval($commodity_id) > 0){
                $this->db->where('commodity_specification.commodity_id', $commodity_id);
            }
            if (!empty($condition)){
                $this->db->where($condition);
            }

            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('commodity.name', $keyword);
                $this->db->or_like('commodity_specification.name', $keyword);
                $this->db->or_like('commodity_center.name', $keyword);
                $this->db->or_like('package_type.name', $keyword);
                $this->db->group_end();
            }
            $this->db->group_by('commodity_specification.id');
            $res = $this->db->get('commodity_specification');

            if ($res && $res->num_rows() > 0){
                $data['total_page'] = ceil($res->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }

        return $data;
    }

    /**
     * 根据条件，获取商品列表
     * @param array $condition 条件数组
     */
    public function get_commodity_list_by_condition($condition = array(), $multiple = TRUE, $agent_id = NULL) {
        if (empty($condition) || !is_array($condition) || count($condition) < 1) {
            return FALSE;
        }

        $this->db->select('commodity.id,
                           commodity.name as commodity_name,
                           commodity.number,
                           commodity.category_id,
                           commodity_specification.id as commodity_specification_id,
                           commodity_specification.selling_price as price,
                           commodity_specification.market_price,
                           commodity_specification.packagetype,
                           commodity_specification.goodsunit,
                           commodity_specification.name as commodity_specification_name,
                           commodity.introduce,
                           commodity.detail,
                           commodity_specification.sales_volume,
                           commodity_specification.points,
                           commodity_center.name as commodity_center_name,
                           package_type.name as package_type_name,
                           flash_sale.price as flash_sale_price,
                           flash_sale.start_time as flash_sale_start_time,
                           flash_sale.end_time as flash_sale_end_time,
                           category.name as category_name,
                           r_commodity.id as recommend_id,
                           r_commodity.name as recommend_name,
                           recommend_commodity.start_time,
                           recommend_commodity.end_time,
                           commodity.status_id,
                           commodity_status.name as status,
                           commodity.type_id,
                           commodity_type.name as type,
                           commodity.is_point,
                           attachment.path');
        if (!empty($agent_id)) {
            $this->db->select('agent_commodity.price as agent_price');
        }
        $this->db->join('commodity_specification', "commodity_specification.commodity_id = commodity.id", 'left');
        $this->db->join('commodity_center', "commodity_center.id = commodity_specification.commodity_center_id", 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('recommend_commodity', 'recommend_commodity.id = commodity.recommend_commodity', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id', 'left');
        $this->db->join('commodity as r_commodity', 'r_commodity.id = recommend_commodity.commodity_id', 'left');
        $this->db->join('system_code as commodity_status', 'commodity_status.value = commodity.status_id and commodity_status.type = "'.jys_system_code::COMMODITY_STATUS.'"', 'left');
        $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        if (!empty($agent_id)) {
            $this->db->join('agent_commodity', 'agent_commodity.commodity_id = commodity.id', 'left');
            $this->db->where('agent_commodity.commodity_specification_id', $condition['commodity_specification.id']);
            $this->db->where('agent_commodity.agent_id', $agent_id);
        }
        if (!empty($condition) && is_array($condition)) {
            $this->db->where($condition);
        }
        $result = $this->db->get('commodity');

        if ($result && $result->num_rows() > 0){
            return $multiple ? $this->commodity_html_decode($result->result_array()) : $this->commodity_html_decode($result->row_array());
        }else {
            return FALSE;
        }
    }

    /**
     * 后台商品管理分页获取
     * @param int $page
     * @param int $page_size
     * @param null $condition  查询条件
     * @param string $keyword  查询关键字
     * @return array
     */
    public function admin_paginate($page = 1, $page_size = 10, $condition = NULL, $keyword = '')
    {
        $data = array(
            'success' => FALSE,
            'msg' => '没有商品数据',
            'data' => null,
            'total_page' => 0
        );

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('commodity.id,
                           commodity.name,
                           commodity.number,
                           commodity.category_id,
                           commodity.level_id,
                           commodity.introduce,
                           commodity.detail,
                           category.name as category_name,
                           commodity.status_id,
                           commodity.type_id,
                           commodity_type.name as type,
                           commodity.is_point
                           ');

        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->where('commodity.status_id !=', jys_system_code::COMMODITY_STATUS_DELETE);
        if (!empty($condition)){
            $this->db->where($condition);
        }

        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('commodity.name', $keyword);
            $this->db->or_like('commodity.introduce', $keyword);
            $this->db->or_like('category.name', $keyword);
            $this->db->or_like('commodity_type.name', $keyword);
            $this->db->group_end();
        }
        $this->db->order_by('commodity.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('commodity');
        if ($result && $result->num_rows() > 0){
            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $this->commodity_html_decode($result->result_array())
            ];

            $this->db->select('commodity.id');
            $this->db->join('category', 'category.id = commodity.category_id', 'left');
            $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
            if (!empty($condition)){
                $this->db->where($condition);
            }

            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('commodity.name', $keyword);
                $this->db->or_like('commodity.introduce', $keyword);
                $this->db->or_like('category.name', $keyword);
                $this->db->or_like('commodity_type.name', $keyword);
                $this->db->group_end();
            }
            $this->db->order_by('commodity.create_time', 'DESC');
            $res = $this->db->get('commodity');
            if ($res && $res->num_rows() > 0){
                $data['total_page'] = ceil($res->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }
        return $data;
    }

    /**
     * 前台分页显示规格商品
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param array $condition 条件
     * @param string $keyword 查询关键字
     * @param string $agent_id 代理商ID
     * @return array
     */
    public function paginate($page = 1, $page_size = 10, $condition = NULL, $keyword = '', $agent_id = ''){
        $data = array(
            'success' => FALSE,
            'msg' => '没有商品数据',
            'data' => null,
            'total_page' => 0
        );

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('commodity.id,
                           commodity.name as commodity_name,
                           commodity.number,
                           commodity.category_id,
                           commodity.level_id,
                           commodity_specification.selling_price as price,
                           commodity_specification.market_price,
                           commodity_specification.id as specification_id,
                           commodity_specification.name as commodity_specification_name,
                           commodity_specification.goodsunit,
                           commodity.introduce,
                           commodity.detail,
                           commodity_specification.points,
                           commodity_specification.sales_volume,
                           package_type.name as package_type_name,
                           commodity_center.name as commodity_center_name,
                           flash_sale.price as flash_sale_price,
                           flash_sale.start_time as flash_sale_start_time,
                           flash_sale.end_time as flash_sale_end_time,
                           category.name as category_name,
                           r_commodity.id as recommend_id,
                           r_commodity.name as recommend_name,
                           recommend_commodity.start_time,
                           recommend_commodity.end_time,
                           commodity.status_id,
                           commodity_status.name as status,
                           commodity.type_id,
                           commodity_type.name as type,
                           commodity.is_point,
                           attachment.path');

        $this->db->join('commodity_specification', "commodity_specification.commodity_id = commodity.id AND commodity_specification.status_id = '".jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED."'", 'left');
        $this->db->join('commodity_center', "commodity_center.id = commodity_specification.commodity_center_id", 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('recommend_commodity', 'recommend_commodity.commodity_id = commodity.id', 'left');
        $this->db->join('commodity as r_commodity', 'r_commodity.id = recommend_commodity.commodity_id', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id', 'left');
        $this->db->join('system_code as commodity_status', 'commodity_status.value = commodity.status_id and commodity_status.type = "'.jys_system_code::COMMODITY_STATUS.'"', 'left');
        $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        $this->db->where('commodity.status_id !=', jys_system_code::COMMODITY_STATUS_DELETE);
//        if (is_numeric($agent_id) && intval($agent_id) > 0){
//            $this->db->where('commodity.agent_id', intval($agent_id));
//        }else if (empty($agent_id)){
//            $this->db->where('commodity.agent_id', NULL);
//        }
        if (!empty($condition)){
            $this->db->where($condition);
        }

        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('commodity.name', $keyword);
            $this->db->or_like('commodity.introduce', $keyword);
            $this->db->or_like('category.name', $keyword);
            $this->db->or_like('commodity_center.name', $keyword);
            $this->db->group_end();
        }
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('commodity');
        if ($result && $result->num_rows() > 0){
            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $this->commodity_html_decode($result->result_array())
            ];

            $this->db->select('commodity.id');
            $this->db->join('commodity_specification', "commodity_specification.commodity_id = commodity.id AND commodity_specification.status_id = '".jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED."'", 'left');
            $this->db->join('commodity_center', "commodity_center.id = commodity_specification.commodity_center_id", 'left');
            $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.id', 'left');
            $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
            $this->db->join('category', 'category.id = commodity.category_id', 'left');
            $this->db->join('recommend_commodity', 'recommend_commodity.commodity_id = commodity.id', 'left');
            $this->db->join('commodity as r_commodity', 'r_commodity.id = recommend_commodity.commodity_id', 'left');
            $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id', 'left');
            $this->db->join('system_code as commodity_status', 'commodity_status.value = commodity.status_id and commodity_status.type = "'.jys_system_code::COMMODITY_STATUS.'"', 'left');
            $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
            $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
            $this->db->where('commodity.status_id !=', jys_system_code::COMMODITY_STATUS_DELETE);
            if (!empty($condition)){
                $this->db->where($condition);
            }

            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('commodity.name', $keyword);
                $this->db->or_like('commodity.introduce', $keyword);
                $this->db->or_like('category.name', $keyword);
                $this->db->or_like('commodity_center.name', $keyword);
                $this->db->group_end();
            }
            $this->db->group_by('commodity_specification.id');
            $res = $this->db->get('commodity');

            if ($res && $res->num_rows() > 0){
                $data['total_page'] = ceil($res->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }
        return $data;
    }

    /**
     * PC端 首页搜索商品
     * @param int $page
     * @param int $page_size
     * @param null $condition
     * @return array
     */
    public function search_paginate($page = 1, $page_size = 10, $condition = NULL){
        $data = array(
            'success' => FALSE,
            'msg' => '没有商品数据',
            'data' => null,
            'total_page' => 0
        );

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $date = date('Y-m-d H:i:s');
        $this->db->select('commodity.id,
                           commodity.name as commodity_name,
                           commodity.number,
                           commodity.category_id,
                           commodity.level_id,
                           commodity_specification.selling_price as price,
                           commodity_specification.market_price,
                           commodity_specification.id as specification_id,
                           commodity_specification.name as commodity_specification_name,
                           commodity_specification.goodsunit,
                           commodity.introduce,
                           commodity.detail,
                           commodity_specification.points,
                           commodity_specification.sales_volume,
                           package_type.name as package_type_name,
                           commodity_center.name as commodity_center_name,
                           flash_sale.price as flash_sale_price,
                           flash_sale.start_time as flash_sale_start_time,
                           flash_sale.end_time as flash_sale_end_time,
                           category.name as category_name,
                           commodity.status_id,
                           commodity.type_id,
                           commodity_type.name as type,
                           commodity.is_point,
                           attachment.path');

        $this->db->join('flash_sale', 'flash_sale.commodity_specification_id = commodity_specification.id AND "'.$date.'" >= flash_sale.start_time and "'.$date.'" <= flash_sale.end_time', 'left');
        $this->db->join('commodity_center', "commodity_center.id = commodity_specification.commodity_center_id", 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('recommend_commodity', 'recommend_commodity.commodity_specification_id = commodity_specification.id', 'left');
        $this->db->join('commodity', "commodity.id = commodity_specification.commodity_id", 'left');
        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        $this->db->where('commodity_specification.status_id', jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED);
        if (!empty($condition)){
            $this->db->group_start();
            $this->db->where($condition);
            $this->db->group_end();
        }
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $this->db->group_by('commodity_specification.id');
        $result = $this->db->get('commodity_specification');
        if ($result && $result->num_rows() > 0){
            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $this->commodity_html_decode($result->result_array())
            ];

            $this->db->select('commodity_specification.id');
            $this->db->join('flash_sale', 'flash_sale.commodity_specification_id = commodity_specification.id', 'left');
            $this->db->join('commodity_center', "commodity_center.id = commodity_specification.commodity_center_id", 'left');
            $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.id', 'left');
            $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
            $this->db->join('recommend_commodity', 'recommend_commodity.commodity_specification_id = commodity_specification.id', 'left');
            $this->db->join('commodity', "commodity.id = commodity_specification.commodity_id", 'left');
            $this->db->join('category', 'category.id = commodity.category_id', 'left');
            $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
            $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
            $this->db->where('commodity_specification.status_id', jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED);
            if (!empty($condition)){
                $this->db->where($condition);
            }
            $this->db->group_by('commodity_specification.id');
            $res = $this->db->get('commodity_specification');

            if ($res && $res->num_rows() > 0){
                $data['total_page'] = ceil($res->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }
        return $data;
    }

    /**
     * 获取总页数
     *
     * @param int $page_size 页大小
     * @param null $condition 条件
     * @return array|int
     */
    public function get_total_page($page_size = 10, $condition = NULL){
        if (empty($page_size)){
            return FALSE;
        }

        $total_page = 0;

        $this->db->select('commodity.id');
        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id', 'left');
        $this->db->join('recommend_commodity', 'recommend_commodity.commodity_id = commodity.id', 'left');
        $this->db->where('commodity.status_id !=', jys_system_code::COMMODITY_STATUS_DELETE);
        if (!empty($condition)){
            $this->db->where($condition);
        }



        $result = $this->db->get('commodity');

        if ($result && $result->num_rows() > 0){
            $total_page = ceil($result->num_rows()/$page_size);
        }

        return $total_page;
    }

    /**
     * 根据商品ID获取商品剩余数据(缩略图)
     *
     * @param int $commodity_id
     * @return array
     */
    public function show_thumbnail($commodity_id = 0){
        $data = array(
            'success' => FALSE,
            'msg' => '暂无该商品图片',
            'data' => null
        );

        if (empty($commodity_id) || intval($commodity_id) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('commodity_thumbnail.id,
                           attachment.id as attachment_id,
                           attachment.path,
                           user.id as user_id,
                           user.name as user_name');

        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('attachment_user', 'attachment_user.attachment_id = attachment.id', 'left');
        $this->db->join('user', 'user.id = attachment_user.user_id', 'left');
        $this->db->where('commodity_thumbnail.commodity_id', $commodity_id);
        $this->db->group_by('commodity_thumbnail.id');
        $result = $this->db->get('commodity_thumbnail');

        if ($result && $result->num_rows() > 0){
            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $result->result_array()
            ];
        }

        return $data;
    }

    /**
     * 根据商品ID获取商品剩余数据(缩略图)
     *
     * @param int $commodity_id
     * @return array
     */
    public function get_specification_thumbnail($commodity_id = 0, $commodity_specification_id = 0){
        $data = array(
            'success' => FALSE,
            'msg' => '暂无该商品图片',
            'data' => null
        );

        if (empty($commodity_specification_id) || intval($commodity_specification_id) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('commodity_thumbnail.id,
                           attachment.id as attachment_id,
                           attachment.path');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->where('commodity_thumbnail.commodity_specification_id', $commodity_specification_id);
        $this->db->group_by('commodity_thumbnail.id');
        $result = $this->db->get('commodity_thumbnail');

        if ($result && $result->num_rows() > 0){
            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $result->result_array()
            ];
        }

        return $data;
    }

    /**
     * 根据商品ID获取商品评价分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param int $commodity_id 商品ID
     * @param int $evaluation_level 评价等级(0=>无,1=>好评,2=>中评,3=>差评)
     * @return array 商品评价分页数据
     */
    public function evaluation_paginate($page = 1, $page_size = 10, $commodity_id = 0, $evaluation_level = 0, $status = NULL, $commodity_specification_id = 0){
        $data = [
            'success' => FALSE,
            'msg' => '没有商品评价数据',
            'data' => null
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1 || empty($commodity_id) || intval($commodity_id) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('commodity_evaluation.id,
                           commodity_evaluation.commodity_id,
                           commodity_evaluation.commodity_specification_id,
                           commodity_evaluation.score,
                           commodity_evaluation.content,
                           commodity_evaluation.create_time,
                           commodity_evaluation.status,
                           commodity_evaluation.reply_time,
                           commodity_evaluation.reply_content,
                           commodity.name as commodity_name,
                           commodity_specification.name as commodity_specification_name,
                           commodity_center.name as commodity_center_name,
                           package_type.name as package_type_name,
                           user.name as user_name,
                           user.username as user_username,
                           user.nickname as user_nickname,
                           user.phone as user_phone,
                           user.gender as user_gender,
                           attachment.path as user_avatar_path,
                           order_commodity.id as order_commodity_id,
                           order_commodity.number as order_commodity_number,
                           level.id as level_id,
                           level.name as level_name');

        $this->db->join('user', 'user.id = commodity_evaluation.user_id', 'left');
        $this->db->join('attachment', 'attachment.id = user.avatar', 'left');
        $this->db->join('order_commodity', 'order_commodity.id = commodity_evaluation.order_commodity_id', 'left');
        $this->db->join('level', 'level.id = user.level', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = commodity_evaluation.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        $this->db->where('commodity_evaluation.commodity_id', $commodity_id);
        if (!is_null($status)) {
            $this->db->where('commodity_evaluation.status', intval($status));
        }
        if (intval($commodity_specification_id) > 0) {
            $this->db->where('commodity_evaluation.commodity_specification_id', intval($commodity_specification_id));
        }
        if ($evaluation_level == 1) {
            $this->db->where('commodity_evaluation.score >=', 4);
        } else if ($evaluation_level == 2) {
            $this->db->where('commodity_evaluation.score <', 4);
            $this->db->where('commodity_evaluation.score >=', 2);
        } else if ($evaluation_level == 3) {
            $this->db->where('commodity_evaluation.score <=', 1);
        }
        $this->db->order_by('commodity_evaluation.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('commodity_evaluation');




        if ($result && $result->num_rows() > 0) {
            $evaluation_arr = $result->result_array();

            $evaluation_arr = $this->get_evaluation_pic($evaluation_arr);

            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $evaluation_arr
            ];
        }

        return $data;
    }

    /**
     * 统计评价nav
     *
     * @param array $all_evaluation 所有评价
     * @return array 统计结果
     */
    public function evaluation_nav($all_evaluation = []){
        $data['all_eva'] = 0;
        $data['good_eva'] = 0;
        $data['mid_eva'] = 0;
        $data['bad_eva'] = 0;
        if (empty($all_evaluation)) {
            return $data;
        }

        $data['all_eva'] = count($all_evaluation);
        foreach ($all_evaluation as $evaluation){
            if ($evaluation['score'] >= 4){
                $data['good_eva']++;
            }else if ($evaluation['score'] >= 2){
                $data['mid_eva']++;
            }else{
                $data['bad_eva']++;
            }
        }

        return $data;
    }

    /**
     * 推荐商品分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param string $agent_id 代理商ID
     * @param int $type_id 推荐类型（热卖/热换）
     * @param string $agent_id 关键字查询
     * @return array 推荐商品分页数据
     */
    public function recommend_paginate($page = 1, $page_size = 10, $agent_id = '', $type_id = 1, $keywords = ''){
        $data = array(
            'success' => FALSE,
            'msg' => '没有推荐商品数据',
            'data' => array(),
            'total_page' => 0
        );
        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('recommend_commodity.id,
                           recommend_commodity.commodity_id,
                           recommend_commodity.commodity_specification_id,
                           recommend_commodity.type_id,
                           recommend_commodity.start_time,
                           recommend_commodity.end_time,
                           recommend_commodity.create_time,
                           commodity.name as commodity_name,
                           commodity_specification.packagetype,
                           commodity_specification.name as commodity_specification_name,
                           commodity_specification.selling_price as price,
                           commodity_center.name as commodity_center_name,
                           attachment.path,
                           system_code.name as type,
                           package_type.name as package_type_name,
                           ');
        $this->db->join('commodity_specification', 'commodity_specification.id = recommend_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_specification.attachment', 'left');
        $this->db->join('system_code', 'system_code.value = recommend_commodity.type_id and system_code.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
//        if (is_numeric($agent_id) && intval($agent_id) > 0){
//            $this->db->where('commodity.agent_id', intval($agent_id));
//        }else if (empty($agent_id)){
//            $this->db->where('commodity.agent_id', NULL);
//        }
        $this->db->where('recommend_commodity.type_id', $type_id);
        $this->db->where('commodity_specification.status_id !=', jys_system_code::COMMODITY_SPECIFICATION_STATUS_DELETED);
        if (!empty($keywords)) {
            $this->db->group_start();
            $this->db->like('commodity.name', $keywords);
            $this->db->or_like('commodity_specification.name', $keywords);
            $this->db->or_like('commodity_center.name', $keywords);
            $this->db->or_like('package_type.name', $keywords);
            $this->db->group_end();
        }
        $this->db->order_by('commodity_specification.sales_volume', 'DESC');
        $this->db->order_by('attachment.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('recommend_commodity');

        if ($result && $result->num_rows() > 0){
            $data = array(
                'success' => TRUE,
                'msg' => '',
                'data' => $result->result_array()
            );
        }
        $this->db->select('recommend_commodity.id,recommend_commodity.commodity_id,commodity.name');
        $this->db->join('commodity_specification', 'commodity_specification.id = recommend_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_specification.attachment', 'left');
        $this->db->join('system_code', 'system_code.value = recommend_commodity.type_id and system_code.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
//        if (is_numeric($agent_id) && intval($agent_id) > 0){
//            $this->db->where('commodity.agent_id', intval($agent_id));
//        }else if (empty($agent_id)){
//            $this->db->where('commodity.agent_id', NULL);
//        }
        $this->db->where('recommend_commodity.type_id', $type_id);
        $this->db->where('commodity_specification.status_id !=', jys_system_code::COMMODITY_SPECIFICATION_STATUS_DELETED);
        if (!empty($keywords)) {
            $this->db->group_start();
            $this->db->like('commodity.name', $keywords);
            $this->db->or_like('commodity_specification.name', $keywords);
            $this->db->or_like('commodity_center.name', $keywords);
            $this->db->or_like('package_type.name', $keywords);
            $this->db->group_end();
        }
        $result = $this->db->get('recommend_commodity');
        if ($result && $result->num_rows() > 0){
            $total_num = $result->num_rows();
            $data['total_page'] = ceil($total_num / $page_size * 1.0);
        } else {
            $data['total_page'] = 1;
        }

        return $data;
    }

    /**
     * 添加热换或热卖商品
     * @param int $commodity_id 商品ID
     * @param string $start_time 开始时间
     * @param string $end_time 结束时间
     * @param int $type_id 推荐类型
     */
    public function add_recommend($commodity_id = 0, $start_time = "", $end_time = "", $type_id = 0, $commodity_specification_id = 0) {
        $result = array('success'=>FALSE, 'msg'=>'添加失败');
        if (intval($commodity_id) < 0 || empty($start_time) || empty($end_time) || intval($type_id) < 1 || intval($commodity_id) < 0) {
            $result['msg'] = '添加失败，参数错误';
            return $result;
        }

        $this->db->trans_start();
        $commodity = $this->jys_db_helper->get('commodity_specification', $commodity_specification_id);
        if (!empty($commodity) && isset($commodity['status_id']) && intval($commodity['status_id']) != jys_system_code::COMMODITY_SPECIFICATION_STATUS_DELETED) {
            $condition = ['end_time >' => $start_time, 'type_id' => $type_id, 'commodity_id' => $commodity_id, 'commodity_specification_id' => $commodity_specification_id];
            $recommend = $this->jys_db_helper->get_where('recommend_commodity', $condition);
            if (!empty($recommend)) {
                $result['msg'] = '当前商品在当前时间已有推荐，请不要重复添加';
            }else {
                $insert['commodity_id'] = $commodity_id;
                $insert['commodity_specification_id'] = $commodity_specification_id;
                $insert['start_time'] = $start_time;
                $insert['end_time'] = $end_time;
                $insert['type_id'] = $type_id;
                $insert['create_time']    = date('Y-m-d H:i:s');
                $result = $this->jys_db_helper->add('recommend_commodity', $insert);
            }
        }else {
            $result['msg'] = '该商品已被删除，无法添加';
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $result['success'] = FALSE;
            $result['msg'] = '添加失败，事务执行失败';
        }

        return $result;
    }

    /**
     * 管理员端获取获取推荐商品
     *
     * @return array
     */
    public function get_recommend($nums = NULL){
        $date_now = date('Y-m-d H:i:s');
        $data = [
            'success' => FALSE,
            'msg' => '没有推荐商品数据',
            'data' => null
        ];

        $this->db->select('recommend_commodity.id,
                           recommend_commodity.start_time,
                           recommend_commodity.end_time,
                           recommend_commodity.create_time,
                           recommend_commodity.commodity_id,
                           commodity.name,
                           commodity_thumbnail.attachment_id,
                           attachment.path,
                           recommend_commodity.type_id,
                           system_code.name as type');

        $this->db->join('commodity', 'commodity.id = recommend_commodity.commodity_id', 'left');
        $this->db->join('system_code', 'system_code.value = recommend_commodity.type_id and system_code.type = "'.jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_SALE.'"', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_id = recommend_commodity.commodity_id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->where('recommend_commodity.start_time <=', $date_now);
        $this->db->where('recommend_commodity.end_time >=', $date_now);
        $this->db->where('commodity.status_id !=', '0');
        $this->db->where('commodity.is_point =', '0');
        $this->db->group_by('commodity.id');
        $this->db->order_by('commodity.sales_volume', 'DESC');
        $this->db->order_by('attachment.create_time', 'ASC');
        if(!empty($nums)){
            $this->db->limit($nums, 0);
        }
        $result = $this->db->get('recommend_commodity');

        if ($result && $result->num_rows() > 0){
            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $result->result_array()
            ];
        }

        return $data;
    }

    /**
     * 获取热换商品
     *
     * @param int $new_num 热换条数
     * @param int $type_id 推荐类型
     * @param  array $user_info 用户信息
     * @param  string $agent_id 代理商ID
     * @return array
     */
    public function get_home_recommend($new_num = NULL, $type_id = jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_EXCHANGE, $user_info = [], $agent_id = ''){
        $date_now = date('Y-m-d H:i:s');

        $this->db->select('recommend_commodity.id,
                           recommend_commodity.commodity_id,
                           recommend_commodity.commodity_specification_id,
                           commodity.name as commodity_name,
                           commodity.is_point,
                           commodity_specification.selling_price as price,
                           commodity_specification.market_price,
                           commodity_specification.name as commodity_specification_name,
                           commodity_specification.points,
                           commodity_specification.sales_volume,
                           commodity_specification.packagetype,
                           commodity_center.name as commodity_center_name,
                           attachment.path,
                           flash_sale.price as flash_sale_price,
                           flash_sale.start_time as flash_sale_start_time,
                           flash_sale.end_time as flash_sale_end_time,
                           system_code.name as type,
                           package_type.name as package_type_name');

        $this->db->join('commodity_specification', 'commodity_specification.id = recommend_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_specification.attachment', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_specification_id = commodity_specification.id and "'.$date_now.'" >= flash_sale.start_time and "'.$date_now.'" <= flash_sale.end_time', 'left');
        $this->db->join('system_code', 'system_code.value = commodity.type_id and system_code.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
//        if (is_numeric($agent_id) && intval($agent_id) > 0){
//            $this->db->where('commodity.agent_id', intval($agent_id));
//        }else if (empty($agent_id)){
//            $this->db->where('commodity.agent_id', NULL);
//        }
        $this->db->where('recommend_commodity.start_time <=', $date_now);
        $this->db->where('recommend_commodity.end_time >=', $date_now);
        $this->db->where('recommend_commodity.type_id', $type_id);
        $this->db->order_by('recommend_commodity.create_time', 'DESC');
        $this->db->group_by('recommend_commodity.id');

        if(!empty($new_num)){
            $this->db->limit($new_num, 0);
        }

        $result = $this->db->get('recommend_commodity');

        if ($result && $result->num_rows() > 0){
            if ($type_id == jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_SALE && isset($user_info['price_discount']) && floatval($user_info['price_discount']) > 0) {
                return $this->calculate_discount_price($result->result_array(), $user_info['price_discount']);
            }else {
                return $result->result_array();
            }
        }else{
            return array();
        }
    }

    /**
     * 获取限时折扣商品
     * @param int $limit  获取条数
     * @param int $erp_user_id  代理商ID
     * @return array
     */
    public function get_flash_sale($limit = 0, $erp_user_id = 0)
    {
        $date_now = date('Y-m-d, H:i:s');
        $data = array(
            'success' => FALSE,
            'msg' => '没有限时折扣商品',
            'data' => NULL
        );
        $this->db->select('flash_sale.id, 
                           flash_sale.commodity_id,
                           flash_sale.price as flash_sale_price, 
                           flash_sale.start_time, 
                           flash_sale.end_time, 
                           commodity.name as commodity_name,
                           commodity_specification.id as specification_id,
                           commodity_specification.selling_price as price,
                           commodity_specification.market_price,
                           commodity_specification.name as commodity_specification_name,
                           commodity_specification.sales_volume,
                           commodity_specification.points,
                           commodity_center.name as commodity_center_name,
                           package_type.name as package_type_name,
                           attachment.path,
                           system_code.name as type');
        $this->db->join('commodity', 'flash_sale.commodity_id = commodity.id', 'left');
        $this->db->join('commodity_specification', "commodity_specification.id = flash_sale.commodity_specification_id AND commodity_specification.status_id = '". jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED ."'", 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_specification.attachment', 'left');
        $this->db->join('system_code', 'system_code.value = commodity.type_id and system_code.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
//        if (is_numeric($agent_id) && intval($agent_id) > 0){
//            $this->db->where('commodity.agent_id', intval($agent_id));
//        }else if (empty($agent_id)){
//            $this->db->where('commodity.agent_id', NULL);
//        }
        $this->db->where('flash_sale.start_time <=', $date_now);
        $this->db->where('flash_sale.end_time >=', $date_now);
        $this->db->order_by('flash_sale.create_time', 'DESC');
        $this->db->group_by('flash_sale.id');
        if(!empty($limit)){
            $this->db->limit($limit, 0);
        }

        $result = $this->db->get('flash_sale');

        if ($result && $result->num_rows() > 0){
            $data = array(
                'success' => TRUE,
                'msg' => '查找到限时折扣商品',
                'data' => $result->result_array()

            );
        }
        return $data;
    }
    /**
     * 限时折扣商品分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param string $agent_id 代理商ID
     * @return array 限时折扣商品分页数据
     */
    public function flash_sale_paginate($page = 1, $page_size = 10, $agent_id = ''){
        $data = array(
            'success' => FALSE,
            'msg' => '没有限时折扣商品数据',
            'data' => null,
            'total_page' => 0
        );

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('flash_sale.id,
                           flash_sale.price,
                           flash_sale.start_time,
                           flash_sale.end_time,
                           flash_sale.create_time,
                           flash_sale.commodity_id,
                           commodity_specification.id as commodity_specification_id,
                           commodity_specification.selling_price,
                           commodity_specification.market_price,
                           commodity_specification.name as commodity_specification_name,
                           commodity_center.name as commodity_center_name,
                           commodity.name as commodity_name,
                           package_type.name as package_type_name');
        $this->db->join('commodity_specification', 'commodity_specification.id = flash_sale.commodity_specification_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype AND package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
//        if (is_numeric($agent_id) && intval($agent_id) > 0){
//            $this->db->where('commodity.agent_id', intval($agent_id));
//        }else if (empty($agent_id)){
//            $this->db->where('commodity.agent_id', NULL);
//        }
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('flash_sale');
        if ($result && $result->num_rows() > 0){
            $data = array(
                'success' => TRUE,
                'msg' => '',
                'data' => $result->result_array()
            );
            $this->db->select('flash_sale.id');
            $this->db->join('commodity_specification', 'commodity_specification.id = flash_sale.commodity_specification_id', 'left');
            $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
            $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
            $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype AND package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
            $result = $this->db->get('flash_sale');
            if ($result && $result->num_rows() > 0){
                $data['total_page'] = ceil($result->num_rows() / $page_size * 1.0);
            } else {
                $data['total_page'] = 1;
            }
        }

        return $data;
    }

    /**
     * 评价数据中添加评价图片
     *
     * @param array $evaluation_arr
     * @return array
     */
    public function get_evaluation_pic($evaluation_arr = []){
        if (!is_array($evaluation_arr) || empty($evaluation_arr)){
            return [];
        }

        foreach ($evaluation_arr as $key => $evaluation){
            $evaluation_arr[$key]['evaluation_pic'] = $this->get_evaluation_pic_path_by_evaluation_id($evaluation['id']);
        }

        return $evaluation_arr;
    }

    /**
     * 根据评价ID获取评价图片
     *
     * @param int $evaluation_id
     * @return null
     */
    public function get_evaluation_pic_path_by_evaluation_id($evaluation_id = 0){
        if (intval($evaluation_id) < 1 || empty($evaluation_id)){
            return null;
        }

        $this->db->select('commodity_evaluation_pic.id,
                           attachment.path');
        $this->db->join('attachment', 'attachment.id = commodity_evaluation_pic.attachment_id', 'left');
        $this->db->where('commodity_evaluation_pic.commodity_evaluation_id', $evaluation_id);
        $result = $this->db->get('commodity_evaluation_pic');

        if ($result && $result->num_rows() > 0){
            return $result->result_array();
        }else{
            return null;
        }
    }

    /**
     * 根据条件获取商品详情
     *
     * @param array $condition 条件（数组）
     * @param bool $multiple 返回多条数据
     * @param bool $thumb 返回缩略图
     * @param bool $limit 数据数量
     * @param array $user_info 用户信息（数组）
     * @return array 商品详情
     */
    public function get_commodity_by_condition($condition = [], $multiple = FALSE, $thumb = FALSE, $limit = FALSE, $user_info = [], $agent_id = ''){
        $data = [
            'success' => FALSE,
            'msg' => '没有商品数据',
            'data' => null
        ];

        $date = date('Y-m-d H:i:s');

        $this->db->select('commodity.id,
                           commodity.number,
                           commodity.is_point,
                           commodity.category_id,
                           commodity.name as commodity_name,
                           commodity.level_id,
                           commodity_specification.id as specification_id,
                           commodity_specification.selling_price as price,
                           commodity_specification.market_price,
                           commodity_specification.packagetype,
                           commodity_specification.goodsunit,
                           commodity_specification.name as commodity_specification_name,
                           commodity.introduce,
                           commodity.detail,
                           commodity_specification.sales_volume,
                           commodity_specification.points,
                           commodity_center.name as commodity_center_name,
                           package_type.name as package_type_name,
                           category.name as category_name,
                           flash_sale.price as flash_sale_price,
                           flash_sale.start_time as flash_sale_start_time,
                           flash_sale.end_time as flash_sale_end_time,
                           commodity.status_id,
                           commodity_status.name as status,
                           commodity.type_id,
                           commodity_type.name as type'.($thumb ? ', attachment.id as attachment_id, attachment.path' : NULL));

        $this->db->join('commodity_center', "commodity_center.id = commodity_specification.commodity_center_id", 'left');
        $this->db->join('commodity', "commodity.id = commodity_specification.commodity_id", 'left');
        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_specification_id = commodity_specification.id and "'.$date.'" >= flash_sale.start_time and "'.$date.'" <= flash_sale.end_time', 'left');
        $this->db->join('system_code as commodity_status', 'commodity_status.value = commodity.status_id and commodity_status.type = "'.jys_system_code::COMMODITY_STATUS.'"', 'left');
        $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        if ($thumb){
            $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.attachment', 'left');
            $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        }
        $this->db->where($condition);
        $this->db->order_by('commodity_specification.create_time', 'DESC');

        if ($thumb){
            $this->db->group_by('commodity_specification.id');
        }
        if ($limit){
            $this->db->limit($limit, 0);
        }
        $result = $this->db->get('commodity_specification');

        if ($result && $result->num_rows() > 0){
            if (isset($user_info['price_discount']) && floatval($user_info['price_discount']) > 0) {
                if ($multiple) {
                    $result_data = $result->result_array();
                }else {
                    $result_data = $result->row_array();
                }
                $result_data = $this->calculate_discount_price($result_data, $user_info['price_discount']);
                $response_data = $this->commodity_html_decode($result_data);
            }else {
                $response_data = $this->commodity_html_decode($multiple ? $result->result_array() : $result->row_array());
            }

            $data = [
                'success' => TRUE,
                'msg' => '',
                'data' => $response_data
            ];
        }

        return $data;
    }

    /**
     * 根据商品Id获取评价数量
     * @param $id
     * @return mixed
     */
    public function get_evaluation_number_by_commodity_id($id){
        $this->db->select('count(*) as num');
        $this->db->where('commodity_id',$id);
        $result = $this->db->get('commodity_evaluation');

        if ($result && $result->num_rows() > 0){
            return $result->row_array();
        }
    }

    /**
     * 更具商品ID获取商品图片
     *
     * @param int $specification_id 商品规格ID
     * @param bool $multiple 复数选择
     * @return mixed
     */
    public function get_pic_by_commodity_id($specification_id, $multiple = FALSE){
        $data = array(
            'success' => FALSE,
            'msg' => '没有该商品的图片',
            'data' => null
        );
        
        $this->db->select('attachment.path as pic_path');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->where('commodity_thumbnail.commodity_specification_id', $specification_id);
        $result = $this->db->get('commodity_thumbnail');
        if($result && $result -> num_rows()>0){
            $data = array(
                'success' => TRUE,
                'msg' => '成功获取该商品的图片',
                'data' => $multiple ? $result->result_array() :$result->row_array()
            );
        }
        return $data;
    }

    /**
     * 商品类容html解码
     *
     * @param array $commodities 商品数据（解码前）
     * @return array 商品数据（解码后）
     */
    public function commodity_html_decode($commodities = []){
        if (empty($commodities) || !is_array($commodities)){
            return [];
        }

        foreach ($commodities as $key => $commodity){
            if (is_array($commodity)){
                $commodities[$key]['introduce'] = htmlspecialchars_decode($commodity['introduce']);
                $commodities[$key]['detail'] = htmlspecialchars_decode($commodity['detail']);
            }else{
                $commodities['introduce'] = htmlspecialchars_decode($commodities['introduce']);
                $commodities['detail'] = htmlspecialchars_decode($commodities['detail']);
                return $commodities;
            }
        }

        return $commodities;
    }

    /**
     * 过滤用户评论
     *
     * @param array $evaluations 评论
     * @return array|bool
     */
    public function filter_evaluation($evaluations = []){
        if (empty($evaluations)){
            return FALSE;
        }

        foreach ($evaluations as $key => $evaluation){
            $evaluations[$key]['user_username'] = NULL;
            $evaluations[$key]['user_name'] = NULL;
            $evaluations[$key]['user_nickname'] = mb_substr($evaluation['user_nickname'], 0, 1).'***'.mb_substr($evaluation['user_nickname'], -1);
            $evaluations[$key]['user_phone'] = substr($evaluation['user_phone'], 0, 3).'*****'.substr($evaluation['user_phone'], -3);
            $evaluations[$key]['order_commodity_number'] = NULL;
        }

        return $evaluations;
    }

    /**
     * 获取好评数目
     *
     * @param int $commodity_id 商品ID
     * @return bool|int
     */
    public function get_praise_num($commodity_id = 0){
        if (empty($commodity_id) || intval($commodity_id) < 1){
            return FALSE;
        }

        $this->db->where('commodity_id', $commodity_id);
        $this->db->where('score >=', 4);
        $result = $this->db->get('commodity_evaluation');

        if ($result && $result->num_rows() > 0){
            return count($result->result_array());
        }else{
            return 0;
        }
    }

    /**
     * 搜索条件（处理后）
     *
     * @param array $search 搜索条件
     * @return bool
     */
    public function search($search = []){
        if (empty($search)){
            return FALSE;
        }
        $search_str = '';
        $date_now = date('Y-m-d, H:i:s');

        // 处理限时折扣/热卖/热换
        if (isset($search['flash_sale']) && $search['flash_sale'] == true){
            $search_str .= 'flash_sale.start_time <= "'.$date_now.'" and flash_sale.end_time >= "'.$date_now.'"';
        } else if (isset($search['hot_sale']) && $search['hot_sale'] == true){
            $search_str .= 'recommend_commodity.start_time <= "'.$date_now.'" and recommend_commodity.end_time >= "'.$date_now.'" and recommend_commodity.type_id = '.jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_SALE;
        } else if (isset($search['hot_exchange']) && $search['hot_exchange'] == true){
            $search_str .= 'recommend_commodity.start_time <= "'.$date_now.'" and recommend_commodity.end_time >= "'.$date_now.'" and recommend_commodity.type_id = '.jys_system_code::RECOMMEND_COMMODITY_STATUS_HOT_EXCHANGE;
        }

        // 处理关键字(商品名、商品类型、商品分类名、规格名、包装名)
        if (isset($search['key_words']) && !empty($search['key_words'])){
            $search['result'] = $search['key_words'];
            foreach (explode(' ', $search['key_words']) as $key => $row){
                if (empty($row)){
                    header('Location:'.$_SERVER['HTTP_REFERER']);
                    return;
                }

                if ($key == 0){
                    $search_str .= '(commodity.name like "%'. $row .'%" or category.name like "%'. $row .'%" or commodity_specification.name like "%'. $row . '%" or commodity_center.name like "%'. $row .'%" or commodity_type.name like "%'. $row .'%" or package_type.name like "%'. $row .'%"';
                } else {
                    $search_str .= ' or commodity.name like "%'. $row .'%" or category.name like "%'. $row .'%" or commodity_specification.name like "%'. $row . '%" or commodity_center.name like "%'. $row .'%" or commodity_type.name like "%'. $row .'%" or package_type.name like "%'. $row .'%"';
                }

                if ($key == count(explode(' ', $search['key_words']))- 1){
                    $search_str .= ')';
                }
            }
        }

        // 商品分类ID查询
        if (isset($search['category']) && !empty($search['category'])){
            if (strlen($search_str) > 0){
                $search_str .= ' and ';
            }
            $search_str .= '(category.id = '.$search['category'].' or category.parent_id = '.$search['category'].')';
        }

        // 商品类型ID
        if (isset($search['type']) && !empty($search['type'])){
            if (strlen($search_str) > 0){
                $search_str .= ' and ';
            }
            $search_str .= 'commodity.type_id = '.$search['type'];
        }

        // 价格区间
        if (isset($search['price']) && !empty($search['price'])){
            $price = explode('-', $search['price']);
            if ($price[0] == 'min_p'){
                $search_str = '('.$search_str.') and commodity_specification.selling_price >= '.$price[1];
            }else if ($price[0] == 'max_p'){
                $search_str = '('.$search_str.') and commodity_specification.selling_price <= '.$price[1];
            }else{
                $search_str = '('.$search_str.') and commodity_specification.selling_price >= '.$price[0].' and commodity_specification.selling_price <= '.$price[1];
            }
        }

        return $search_str;
    }

    /**
     * 检验用户积分是否足够兑换该商品
     *
     * @param int $user_id 用户ID
     * @param int $specification_id 商品规格ID
     * @param int $amount 数量
     * @return bool
     */
    public function check_point_enough($user_id, $specification_id, $amount){
        if (empty($user_id) || empty($specification_id) || empty($amount) || intval($user_id) < 1 || intval($specification_id) < 1 || intval($amount) < 1){
            return FALSE;
        }

        $user = $this->jys_db_helper->get('user', $user_id);
        $commodity = $this->jys_db_helper->get_where('commodity_specification', array('id' => $specification_id));

        if ($commodity && $user['current_point'] >= ($amount * $commodity['selling_price'])){
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 根据商品ID获取商品评价信息
     *
     * @param null $commodities 商品数据
     * @return array|null
     */
    public function get_commodity_evaluation_info($commodities = NULL){
        if (empty($commodities) || !is_array($commodities)){
            return NULL;
        }

        foreach ($commodities as $key => $commodity){
            $total_num = $this->jys_db_helper->get_total_num('commodity_evaluation', ['commodity_id'=>$commodity['id']]);
            $commodities[$key]['evaluation_num'] = $this->get_evaluation_number_by_commodity_id($commodity['id'])['num'];
            if ($total_num == 0){
                $commodities[$key]['good_evaluation'] = 0;
            }else{
                $commodities[$key]['good_evaluation'] = round($this->get_praise_num($commodity['id']) / $total_num, 2);
            }
        }

        return $commodities;
    }

    /**
     * 获取商品推荐商品
     */
    public function get_commodity_recommend_commodity($specification_id, $agent_id = '')
    {
        $data = array('success' => FALSE, 'msg' => '获取商品推荐商品失败', 'data' => array(), 'total_page' => 0);

        $this->db->select('managing_suggestions.id,
                           managing_suggestions.commodity_id,
                           managing_suggestions.specification_id,
                           managing_suggestions.recommend_commodity_id,
                           managing_suggestions.recommend_specification_id,
                           commodity.name as commodity_name,
                           commodity_specification.selling_price,
                           commodity_specification.market_price,
                           commodity_specification.packagetype,
                           commodity_specification.name as specification_name,
                           commodity_center.name,
                           attachment.path,
                           system_code.name as package_type_name
                           ');
        $this->db->join('commodity', 'commodity.id = managing_suggestions.recommend_commodity_id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = managing_suggestions.recommend_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_specification.attachment', 'left');
        $this->db->join('system_code', "system_code.value = commodity_specification.packagetype AND system_code.type = '".jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE."'");
        if (!empty($specification_id)){
            $this->db->where('managing_suggestions.specification_id', $specification_id);
        }
//        if (is_numeric($agent_id) && intval($agent_id) > 0){
//            $this->db->where('commodity.agent_id', intval($agent_id));
//        }else if (empty($agent_id)){
//            $this->db->where('commodity.agent_id', NULL);
//        }
        $result = $this->db->get('managing_suggestions');

        if ($result && $result->num_rows() > 0){
            $data['success'] = TRUE;
            $data['msg'] = '获取商品推荐商品成功';
            $data['data'] = $result->result_array();
        }else{
            $data['msg'] = '无商品推荐商品信息';
        }

        return $data;
    }

    /**
     * 检验商品编号是否存在
     *
     * @param null $number 商品编号
     * @return bool
     */
    public function check_number_is_exists($number = NULL){
        if (empty($number)){
            return FALSE;
        }

        if ($this->jys_db_helper->get_total_num('commodity', ['number'=>$number])){
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 把一个商品添加给指定的代理商
     * @param integer $commodity_id 商品ID
     * @param integer $agent_id     代理商ID
     * @param string $category 主页分类
     */
    public function add_commodity_to_agent($commodity_id = 0, $agent_id = 0, $category = 'tumour')
    {
        $result = array(
            'success' => FALSE,
            'msg' => '添加失败',
        );
        if ($commodity_id < 0 OR $agent_id < 0)
        {
            $result['mgs'] = '添加失败，参数错误';
            return $result;
        }

        $this->db->trans_start();
        if ( ! $this->_commodity_exsits($commodity_id))
        {
            if ($this->_commodity_exsits_in_agent($commodity_id, $agent_id))
            {
                $result['msg'] = '当前商品已分配给该代理商，请不要重复添加';
            }
            else
            {
                $result = $this->_insert_commodity_to_agent($commodity_id, $agent_id);
            }
        }
        else
        {
            $result['msg'] = '该商品已被删除，无法添加';
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $result['success'] = FALSE;
            $result['msg'] = '添加失败，事务执行失败';
        }
        else {}

        return $result;
    }

    /**
     * 把一个商品从代理商中删除
     * @param  integer $commodity_id 商品ID
     * @param  integer $agent_id     代理商ID
     * @return [type]                [description]
     */
    public function remove_commodity_from_agent($commodity_id = 0, $agent_id = 0)
    {
        $result = array(
            'success' => FALSE,
            'msg' => '添加失败',
        );
        if ($commodity_id < 0 OR $agent_id < 0)
        {
            $result['mgs'] = '添加失败，参数错误';
            return $result;
        }
        $this->db->trans_start();
        if ($this->_commodity_exsits_in_agent($commodity_id, $agent_id))
        {
            $delete_condition = array(
                'commodity_id' => $commodity_id,
                'agent_id' => $agent_id,
            );
            $result = $this->jys_db_helper->delete_by_condition(delete_condition);
        }
        else
        {
            $result['msg'] = '该商品未分配给该代理商，无法删除';
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $result['success'] = FALSE;
            $result['msg'] = '添加失败，事务执行失败';
        }
        else {}
        return $result;
    }

    /**
     * 检查商品是否存在（未删除）
     * @param  integer $commodity_id 商品ID
     * @return BOOL                存在TRUE,不存在FLASE
     */
    private function _commodity_exsits($commodity_id = 0)
    {
        $commodity = $this->jys_db_helper->get('commodity', $commodity_id);
        if (!empty($commodity)
            && isset($commodity['status_id'])
            && intval($commodity['status_id']) > 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * 检查商品是否已经分配给代理商
     * @param  integer $commodity_id 商品ID
     * @param  integer $agent_id     代理商ID
     * @return BOOL               存在TRUE，不存在FALSE
     */
    private function _commodity_exsits_in_agent($commodity_id, $agent_id)
    {
        $commodity = $this->jys_db_helper->get_where('agent_home',
            ['commodity_id' => $commodity_id,
                'agent_id'=>$agent_id]);
        return ! empty($commodity);
    }

    /**
     * 添加一个商品到代理商
     * @param  integer $commodity_id 商品ID
     * @param  interger $agent_id     代理商ID
     * @param  string $category 主页分类
     * @return BOOL
     */
    private function _insert_commodity_to_agent($commodity_id, $agent_id, $category = 'tumour')
    {
        $insert['commodity_id'] = $commodity_id;
        $insert['agent_id'] = $agent_id;
        $insert['create_time'] = date('Y-m-d H:i:s');
        $insert['update_time'] = date('Y-m-d H:i:s');
        $result = $this->jys_db_helper->add('agent_home', $insert);
        return $result;
    }

    /**
     * @param int $agent_id  代理商ID
     * @param int $page
     * @param int $page_size
     * @param null $condition  查询条件
     * @param string $keyword  关键字
     * @param array $user_info
     * @return array
     */
    public function paginate_by_agent_id($agent_id = 0, $page = 1, $page_size = 10, $condition = NULL, $keyword = '', $user_info = []) {
        $response = array('success'=>FALSE, 'msg'=>'获取商品列表失败', 'data'=>[], 'total_page'=>0);
        if (intval($page) < 1 || intval($page_size) < 1 || empty($agent_id) || intval($agent_id) < 0) {
            $response['msg'] = '参数错误，获取商品列表失败';
            return $response;
        }

        $this->db->select('commodity.id,
                           commodity.name as commodity_name,
                           commodity.number,
                           commodity.category_id,
                           commodity.level_id,
                           commodity.status_id,
                           commodity.type_id,
                           commodity.is_point,
                           commodity_specification.id as commodity_specification_id,
                           commodity_specification.selling_price,
                           commodity_specification.name as packagetype_name,
                           commodity_specification.points,
                           commodity_specification.sales_volume,
                           commodity_center.name,
                           agent_index.agent_id,
                           agent_index.name as agent_home_category,
                           agent_index.color as agent_home_color,
                           agent_home.rank as agent_home_rank,
                           agent_commodity.id as agent_commodity_id,
                           agent_commodity.price as agent_price,
                           flash_sale.price as flash_sale_price,
                           flash_sale.start_time as flash_sale_start_time,
                           flash_sale.end_time as flash_sale_end_time,
                           category.name as category_name,
                           recommend_commodity.start_time,
                           recommend_commodity.end_time,
                           commodity_status.name as status,
                           commodity_type.name as type,
                           attachment.path');
        $this->db->join('agent_index', 'agent_index.id = agent_home.agent_index_id', 'left');
        $this->db->join('agent_commodity', 'agent_commodity.id = agent_home.agent_commodity_id', 'left');
        $this->db->join('commodity', 'commodity.id = agent_commodity.commodity_id', 'left');
        $this->db->join('commodity_specification', "commodity_specification.id = agent_commodity.commodity_specification_id AND commodity_specification.status_id = '". jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED ."'", 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        // $this->db->join('attachment', 'attachment.id = commodity_specification.attachment', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('recommend_commodity', 'recommend_commodity.commodity_id = commodity.id', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_id = commodity.id', 'left');
        $this->db->join('system_code as commodity_status', 'commodity_status.value = commodity.status_id and commodity_status.type = "'.jys_system_code::COMMODITY_STATUS.'"', 'left');
        $this->db->join('system_code as commodity_type', 'commodity_type.value = commodity.type_id and commodity_type.type = "'.jys_system_code::COMMODITY_TYPE.'"', 'left');
        $this->db->where('agent_index.agent_id', $agent_id);
        $this->db->group_by('agent_commodity.id');
        $this->db->group_by('agent_commodity.commodity_specification_id');
        $this->db->order_by('agent_home.rank');
        if (!empty($condition)){
            $this->db->where($condition);
        }
        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('commodity.name', $keyword);
            $this->db->or_like('commodity.introduce', $keyword);
            $this->db->or_like('category.name', $keyword);
            $this->db->group_end();
        }

        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('agent_home');
        if ($result && $result->num_rows() > 0) {
            $response['success'] = TRUE;
            $commodity_list = $result->result_array();
            if (isset($user_info['price_discount']) && floatval($user_info['price_discount']) > 0) {
                $commodity_list = $this->calculate_discount_price($commodity_list, $user_info['price_discount'], $agent_id);
            }
            $response['data'] = $commodity_list;
            $response['msg'] = '获取成功';

            $this->db->select('COUNT(commodity.id) AS count');
            $this->db->join('agent_index', 'agent_index.id = agent_home.agent_index_id', 'left');
            $this->db->join('agent_commodity', 'agent_commodity.id = agent_home.agent_commodity_id', 'left');
            $this->db->join('commodity', 'commodity.id = agent_commodity.commodity_id', 'left');
            $this->db->join('commodity_specification', "commodity_specification.id = agent_commodity.commodity_specification_id AND commodity_specification.status_id = '". jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED ."'", 'left');
            $this->db->join('category', 'category.id = commodity.category_id', 'left');
            $this->db->where('agent_commodity.agent_id', $agent_id);
            $this->db->group_by('agent_commodity.id');
            $this->db->group_by('agent_commodity.commodity_specification_id');
            if (!empty($condition)){
                $this->db->where($condition);
            }
            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('commodity.name', $keyword);
                $this->db->or_like('commodity.introduce', $keyword);
                $this->db->or_like('category.name', $keyword);
                $this->db->group_end();
            }

            $data_result = $this->db->get('agent_home');
            if ($data_result && $data_result->num_rows() > 0) {
                $data_result = $data_result->row_array();
                if (intval($data_result['count']) > 0) {
                    $response['total_page'] = ceil(intval($data_result['count']) / $page_size);
                }else {
                    $response['total_page'] = 1;
                }
            }else {
                $response['total_page'] = 1;
            }
        }else {
            $response['msg'] = '未找到符合要求的商品';
        }

        return $response;
    }

    /**
     * 添加商品规格信息
     * @param array $commodity 商品规格信息
     * @param array $attachment_ids 缩略图IDS
     * @return mixed
     */
    public function add_specification($commodity = [], $attachment_ids = array()){
        $data['success'] = FALSE;
        $data['msg'] = '添加失败';

        if (empty($commodity)){
            $data['msg'] = '请输入要添加的商品规格信息';
            return $data;
        }
        $this->db->trans_begin();
        $data = $this->jys_db_helper->add('commodity_specification', $commodity, TRUE);

        if ($data['success']){
            $commodity_specification_id = $data['insert_id'];
            $thumbnail_fail_flag = false;
            $thumbnail_arr = array();

            if (!empty($attachment_ids) && is_array($attachment_ids)){
                foreach ($attachment_ids as $attachment_id){
                    if (intval($attachment_id) > 0) {
                        $thumbnail_arr[] = [
                            'attachment_id' => intval($attachment_id),
                            'commodity_id' => intval($commodity['commodity_id']),
                            'commodity_specification_id' => intval($commodity_specification_id),
                            'create_time' => date('Y-m-d H:i:s')
                        ];
                    }
                }

                $_data = $this->jys_db_helper->add_batch('commodity_thumbnail', $thumbnail_arr);
                if (!$_data['success']){
                    $thumbnail_fail_flag = true;
                }
            }

            if ($thumbnail_fail_flag){
                $data['success'] = FALSE;
                $data['msg'] = '添加失败，缩略图错误';
                $this->db->trans_rollback();
            }else{
                $this->db->trans_commit();
            }
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        }
        else{
            $this->db->trans_commit();
        }
        return $data;
    }

    /**
     * 更新商品规格信息
     *
     * @param int $id 商品ID
     * @param array $update 修改规格信息
     * @param null $attachment_ids 商品缩略图IDS
     * @return mixed
     */
    public function update_commodity_specification($id = 0, $update = [], $attachment_ids = NULL){
        $data['success'] = FALSE;
        $data['msg'] = '更新失败';

        if (empty($id) || empty($update) || intval($id) < 1){
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->trans_start();
        $this->jys_db_helper->update('commodity_specification', $id, $update);
        $thumbnail_arr = [];
        //上传图片
        if (!empty($attachment_ids) && is_array($attachment_ids) && count($attachment_ids) > 1) {
            foreach ($attachment_ids as $attachment_id){
                $thumbnail_arr[] = [
                    'attachment_id' => $attachment_id,
                    'commodity_id' => $update['commodity_id'],
                    'commodity_specification_id' => $id,
                    'create_time' => date('Y-m-d H:i:s')
                ];
            }
            $this->jys_db_helper->add_batch('commodity_thumbnail', $thumbnail_arr);
        } elseif (!empty($attachment_ids) && count($attachment_ids) == 1) {
            $thumbnail_arr = [
                'attachment_id' => $attachment_ids[0],
                'commodity_id' => $update['commodity_id'],
                'commodity_specification_id' => $id,
                'create_time' => date('Y-m-d H:i:s')
            ];
            $this->jys_db_helper->add('commodity_thumbnail', $thumbnail_arr);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            $data['msg'] = '更新失败，事务错误';
        } else {
            $data['success'] = TRUE;
            $data['msg'] = '更新成功';
        }

        return $data;
    }

    /**
     * 计算折扣价格
     * @param array $commodity_list 商品信息列表
     * @param $price_discount 折扣率
     * @return array 修改价格后的商品信息列表
     */
    public function calculate_discount_price($commodity_list = [], $price_discount, $agent_id = NULL) {
        if ((empty($commodity_list) || floatval($price_discount) <= 0) && empty($agent_id)) {
            return $commodity_list;
        }

        if (isset($commodity_list['agent_price']) && floatval($commodity_list['agent_price']) > 0) {
            // 商品信息是一维数组，价格字段是agent_price
            if (floatval($price_discount) < 1) {
                $commodity_list['original_price'] = round(floatval($commodity_list['agent_price']), 2);
            }
            $commodity_list['price'] = floatval($commodity_list['agent_price']);
            if ($commodity_list['price'] < 0.01) {
                $commodity_list['price'] = 0.01;
            }else {
                $commodity_list['price'] = round($commodity_list['price'], 2);
            }
        }else if (isset($commodity_list['price']) && floatval($commodity_list['price']) > 0) {
            // 商品信息是一维数组，价格字段是price
            if (floatval($price_discount) < 1) {
                $commodity_list['original_price'] = round(floatval($commodity_list['price']), 2);
            }
            $commodity_list['price'] = floatval($price_discount) * floatval($commodity_list['price']);
            if ($commodity_list['price'] < 0.01) {
                $commodity_list['price'] = 0.01;
            }else {
                $commodity_list['price'] = round($commodity_list['price'], 2);
            }
        }else if (isset($commodity_list['selling_price']) && floatval($commodity_list['selling_price']) > 0) {
            // 商品信息是一维数组，价格字段是selling_price
            if (floatval($price_discount) < 1) {
                $commodity_list['original_price'] = round(floatval($commodity_list['selling_price']), 2);
            }
            $commodity_list['price'] = floatval($price_discount) * floatval($commodity_list['selling_price']);
            if ($commodity_list['price'] < 0.01) {
                $commodity_list['price'] = 0.01;
            }else {
                $commodity_list['price'] = round($commodity_list['price'], 2);
            }
            unset($commodity_list['selling_price']);
        }else {
            // 商品信息是二维数组
            for ($i = 0; $i < count($commodity_list); $i++) {
                if (floatval($price_discount) < 1) {
                    if (isset($commodity_list[$i]['price'])) {
                        $commodity_list[$i]['original_price'] = round(floatval($commodity_list[$i]['price']), 2);
                    }else if (isset($commodity_list[$i]['selling_price'])) {
                        $commodity_list[$i]['original_price'] = round(floatval($commodity_list[$i]['selling_price']), 2);
                    }
                }
                if (isset($commodity_list[$i]['agent_price']) && floatval($commodity_list[$i]['agent_price']) > 0) {
                    $commodity_list[$i]['price'] = floatval($commodity_list[$i]['agent_price']);
                    if ($commodity_list[$i]['price'] < 0.01) {
                        $commodity_list[$i]['price'] = 0.01;
                    }else {
                        $commodity_list[$i]['price'] = round($commodity_list[$i]['price'], 2);
                    }
                }else if (isset($commodity_list[$i]['price']) && floatval($commodity_list[$i]['price']) > 0) {
                    $commodity_list[$i]['price'] = floatval($price_discount) * floatval($commodity_list[$i]['price']);
                    if ($commodity_list[$i]['price'] < 0.01) {
                        $commodity_list[$i]['price'] = 0.01;
                    }else {
                        $commodity_list[$i]['price'] = round($commodity_list[$i]['price'], 2);
                    }
                }else if (isset($commodity_list[$i]['selling_price']) && floatval($commodity_list[$i]['selling_price']) > 0) {
                    $commodity_list[$i]['price'] = floatval($price_discount) * floatval($commodity_list[$i]['selling_price']);
                    if ($commodity_list[$i]['price'] < 0.01) {
                        $commodity_list[$i]['price'] = 0.01;
                    }else {
                        $commodity_list[$i]['price'] = round($commodity_list[$i]['price'], 2);
                    }
                    unset($commodity_list[$i]['selling_price']);
                }
            }
        }

        return $commodity_list;
    }

    /**
     * 批量从ERP插入或更新商品信息
     * @param array $insert_array 需要插入的数组
     * @param array $update_commodity_array 需要更新的数组
     * @param array $update_center_array 需要更新的数组
     * @param array $update_array 需要更新的数组
     * @return mixed
     */
    public function add_update_commodity_from_erp($insert_array = [], $update_commodity_array = [], $update_center_array = [], $update_array = [])
    {
        $insert_status['success'] = FALSE;
        $update_commodity_status['success'] = FALSE;
        $update_commodity_center_status['success'] = FALSE;
        $update_commodity_specification_status['success'] = FALSE;
        $this->db->trans_begin();
        if(!empty($insert_array)){
            $insert_status = $this->jys_db_helper->add_batch('commodity', $insert_array);
        }
        if(!empty($update_commodity_array)){
            $update_commodity_status = $this->jys_db_helper->update_batch('commodity', $update_commodity_array, 'id');
        }
        if(!empty($update_center_array)){
            $update_commodity_center_status = $this->jys_db_helper->update_batch('commodity_center', $update_center_array, 'id');
        }
        if(!empty($update_array)){
            $update_commodity_specification_status = $this->jys_db_helper->update_batch('commodity_specification', $update_array, 'erp_commodity_id');
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $data['success'] = FALSE;
            $data['msg']     = '同步失败';
        } else {
            $this->db->trans_commit();
            $data['success'] = TRUE;
            $data['msg']     = '同步成功';
        }
//        if($insert_status['success'] || $update_commodity_status['success'] || $update_commodity_center_status['success'] || $update_commodity_specification_status['success']){
//            $data['success'] = TRUE;
//            $data['msg']     = '同步成功';
//        } else{
//            $data['success'] = FALSE;
//            $data['msg']     = '同步失败';
//        }
        return $data;
    }

    /**
     * 通过比较商品名字，决定是否更新整个商品
     * @param string $name
     * @param array $value_array
     * @return mixed
     */
    public function compare_commodity_name($name = '', $value_array = [])
    {
        $i = 0;   //临时循环变量
        $j = 0;   //临时循环变量
        foreach ($value_array as $commodity_key => $commodity_value){
            $ids[] = $commodity_value['goodsid'];
        }
        $this->db->select('commodity_specification.id as commodity_specification_id, commodity_specification.commodity_id, 
                           commodity_specification.commodity_center_id, commodity_specification.erp_commodity_id,
                           commodity.name as commodity_name');
        $this->db->join('commodity', 'commodity_specification.commodity_id = commodity.id');
        $this->db->where_in('commodity_specification.erp_commodity_id', $ids);
        $data_result = $this->db->get('commodity_specification')->result_array();
        foreach ($data_result as $item => $value){
            $commodity_specification_id[] = $value['commodity_specification_id'];
            $commodity_center_id[] = $value['commodity_center_id'];
            $commodity_id[] = $value['commodity_id'];
        }
//        $commodity_specification_id = array_unique($commodity_specification_id,SORT_REGULAR);
//        $commodity_center_id = array_unique($commodity_center_id,SORT_REGULAR);
        $commodity_id = array_unique($commodity_id,SORT_REGULAR);
//        $temp = $this->jys_db_helper->is_exist('commodity', ['name' => $name]);
//        if ($temp) {  //如果已存在，则直接返回Id
//            $temp_result['success'] = TRUE;
//            $temp_result['insert_id'] = $temp;
//        } else {
//        }
        if($data_result[0]['commodity_name'] != $name){
            $update_commodity = [];
            foreach ($commodity_id as $commodity_id_key => $commodity_id_value){
                //这里是把商品的分类独立出来，也就是商城商品一级
                $update_commodity[$i]['name'] = $name;
                $update_commodity[$i]['id'] = $commodity_id_value;
                $update_commodity[$i]['update_time'] = date("Y-m-d H:i:s", time());
                $i++;
            }
            $changed_update_commodity_center_array = [];
            $changed_update_commodity_specification_array = [];
            foreach ($value_array as $key => $value){
                foreach ($data_result as $id_key => $id_value){
                    if($value['goodsid'] == $id_value['erp_commodity_id']){
                        //组装商品中间表数组
                        $changed_update_commodity_center_array[$j]['name']   = $value['goodsname'];
                        $changed_update_commodity_center_array[$j]['id']   = $id_value['commodity_center_id'];
                        //组装商品规格表数组
                        $changed_update_commodity_specification_array[$j]['packagetype'] = $value['packagetype'];
                        $changed_update_commodity_specification_array[$j]['erp_commodity_id'] = intval($value['goodsid']);
                        $changed_update_commodity_specification_array[$j]['market_price'] = floatval($value['refpricre']);
                        $changed_update_commodity_specification_array[$j]['goodsunit'] = $value['goodsunit'];
                        $changed_update_commodity_specification_array[$j]['erp_user_id'] = $value['customerid'];
                        $changed_update_commodity_specification_array[$j]['erp_user_name'] = $value['customname'];
                        $changed_update_commodity_specification_array[$j]['update_time'] = $value['updatetime'];
                        $j++;
                    }
                }
            }
            $result = $this->add_update_commodity_from_erp([], $update_commodity, $changed_update_commodity_center_array, $changed_update_commodity_specification_array);
            if(!$result['success']){
                //添加日志
                $add = [
                    'id' => $this->jys_tool->uuid(),
                    'success' => Jys_system_code::ERP_STATUS_FAIL,
                    'msg' => '从ERP同步更新商品分类名为'.$name.'的操作失败',
                    'interface_name' => jys_system_code::ERP_NAME_GOODS_INCREASE_ERP_DS,
                    'code' => jys_system_code::ERP_CODE_BASE02,
                    'create_time' => date("Y-m-d H:i:s"),
                    'level' => jys_system_code::ERP_RETURN_STATUS_FAIL
                ];
                $log_res = $this->jys_db_helper->add('log', $add);
            }
            //插入商品表
        }else{
            $update_commodity_center_array = [];
            $update_commodity_specification_array = [];
            foreach ($value_array as $key => $value){
                foreach ($data_result as $id_key => $id_value){
                    if($value['goodsid'] == $id_value['erp_commodity_id']){
                        //组装商品中间表数组
                        $update_commodity_center_array[$j]['name']   = $value['goodsname'];
                         $update_commodity_center_array[$j]['id']   = $id_value['commodity_center_id'];
                        //组装商品规格表数组
                        $update_commodity_specification_array[$j]['packagetype'] = $value['packagetype'];
                        $update_commodity_specification_array[$j]['erp_commodity_id'] = intval($value['goodsid']);
                        $update_commodity_specification_array[$j]['market_price'] = floatval($value['refpricre']);
                        $update_commodity_specification_array[$j]['goodsunit'] = $value['goodsunit'];
                        $update_commodity_specification_array[$j]['erp_user_id'] = $value['customerid'];
                        $update_commodity_specification_array[$j]['erp_user_name'] = $value['customname'];
                        $update_commodity_specification_array[$j]['update_time'] = $value['updatetime'];
                        $j++;
                    }
                }
            }
            $result = $this->add_update_commodity_from_erp([], [], $update_commodity_center_array, $update_commodity_specification_array);
            if(!$result['success']){
                //添加日志
                $add = [
                    'id' => $this->jys_tool->uuid(),
                    'success' => Jys_system_code::ERP_STATUS_FAIL,
                    'msg' => '从ERP同步更新商品分类名为'.$name.'的操作失败',
                    'interface_name' => jys_system_code::ERP_NAME_GOODS_INCREASE_ERP_DS,
                    'code' => jys_system_code::ERP_CODE_BASE02,
                    'create_time' => date("Y-m-d H:i:s"),
                    'level' => jys_system_code::ERP_RETURN_STATUS_FAIL
                ];
                $log_res = $this->jys_db_helper->add('log', $add);
            }
        }
        return $result;
    }

    /**
     * 分页获取商品规格模板
     * @param int $id
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function paginate_commodity_specification_template($id = 0, $page = 1, $page_size = 10)
    {
        $data = ['success' => FALSE, 'msg' => '暂无商品规格模板', 'data' => [], 'total_page' => 0];
        if (empty($page) || intval($page) < 0 || empty($page_size) || intval($page_size) < 0) {
            $data['msg'] = '分页参数错误';
            return $data;
        }

        $this->db->select(' commodity_specification_template.*,
                            detection_template.name,
                            detection_template.description,
                            detection_project.name as project_name,
                            detection_project.description as project_description,
                            count(detection_project.id) as project_count
                            ');
        $this->db->join('detection_template', 'detection_template.id = commodity_specification_template.template_id', 'left');
        $this->db->join('detection_project', 'detection_project.template_id = detection_template.id', 'left');
        $this->db->where('commodity_specification_template.specification_id', $id);
        $this->db->group_by('commodity_specification_template.template_id');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('commodity_specification_template');
        if ($result && $result->num_rows() > 0) {
            $data['success'] = TRUE;
            $data['msg'] = '获取商品规格模板成功';
            $data['data'] = $result->result_array();

            $this->db->select('id');
            $this->db->where('commodity_specification_template.specification_id', $id);
            $page_result = $this->db->get('commodity_specification_template');
            if ($page_result && $page_result->num_rows() > 0) {
                $data['total_page'] = ceil($page_result->num_rows() / $page_size * 1.0);
            } else {
                $data['total_page'] = 1;
            }
        }

        return $data;
    }

    /**
     * 获取商品的规格信息
     * @param int $commodity_id
     * @return array
     */
    public function commodity_specification($commodity_id = 0, $agent_id = NULL)
    {
        $data = array();
        if (empty($commodity_id) || intval($commodity_id) < 0) {
            return $data;
        }

        $date = date('Y-m-d H:i:s');
        $this->db->select('commodity_center.name,
                            commodity_specification.market_price,
                            commodity_specification.id,
                            commodity_specification.selling_price,
                            commodity_specification.goodsunit,
                            commodity_specification.name as commodity_specification_name,
                            commodity_specification.commodity_center_id,
                            attachment.path,
                            package_type.name as package_type_name,
                            flash_sale.price as flash_sale_price,
                            flash_sale.end_time as end_time
                            ');
        if (!empty($agent_id)) {
            $this->db->select('agent_commodity.price as agent_price');   
        }
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_specification.attachment', 'left');
        $this->db->join('flash_sale', 'flash_sale.commodity_specification_id = commodity_specification.id and "'.$date.'" >= flash_sale.start_time and "'.$date.'" <= flash_sale.end_time', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype AND package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        if (!empty($agent_id)) {
            $this->db->join('agent_commodity', 'agent_commodity.commodity_specification_id = commodity_specification.id', 'left');
            $this->db->where('agent_commodity.agent_id', $agent_id);
            $this->db->where('agent_commodity.commodity_id', $commodity_id);  
        }
        $this->db->where('commodity_specification.commodity_id', $commodity_id);
        $this->db->where('commodity_specification.status_id', jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED);
        $this->db->order_by('commodity_specification.commodity_center_id');
        $result = $this->db->get('commodity_specification');
        if ($result && $result->num_rows() > 0) {
            $result = $result->result_array();
            for ($i = 0; $i < count($result); $i++) {
                $data[$result[$i]['commodity_center_id']][] = $result[$i];
            }
        }

        return array_values($data);
    }

    /**
     * 添加代理商商品时，分页获取商品
     * @param int $page
     * @param int $page_size
     * @param string $keyword
     * @param int $is_point
     * @return array
     */
    public function agent_paginate($page = 1, $page_size = 10, $keyword = '', $is_point = 0){
        $data = array('success' => FALSE, 'msg' => '没有商品数据', 'data' => null, 'total_page' => 0);
        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('commodity_specification.id,
                           commodity.id as commodity_id,
                           commodity.name as commodity_name,
                           commodity_center.name as commodity_center_name,
                           commodity_specification.selling_price as price,
                           commodity_specification.market_price,
                           commodity_specification.name as commodity_specification_name,
                           commodity_specification.commodity_center_id,
                           commodity_specification.goodsunit,
                           system_code.name as package_type_name
                           ');
        $this->db->join('commodity', "commodity.id = commodity_specification.commodity_id", 'left');
        $this->db->join('commodity_center', "commodity_center.id = commodity_specification.commodity_center_id", 'left');
        $this->db->join('system_code', "system_code.value = commodity_specification.packagetype AND system_code.type = '". jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE."'", 'left');
        $this->db->where('commodity_specification.status_id', jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED);
        $this->db->where('commodity.is_point', $is_point);
        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('commodity.name', $keyword);
            $this->db->or_like('commodity_specification.name', $keyword);
            $this->db->or_like('commodity_center.name', $keyword);
            $this->db->or_like('system_code.name', $keyword);
            $this->db->group_end();
        }
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('commodity_specification');
        if ($result && $result->num_rows() > 0){
            $commodity_info = $result->result_array();
            $data = ['success' => TRUE, 'msg' => '查询成功', 'data' => $commodity_info];
            $this->db->select('commodity_specification.id');
            $this->db->join('commodity', "commodity.id = commodity_specification.commodity_id", 'left');
            $this->db->join('commodity_center', "commodity_center.id = commodity_specification.commodity_center_id", 'left');
            $this->db->join('system_code', "system_code.value = commodity_specification.packagetype AND system_code.type = '". jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE."'", 'left');
            $this->db->where('commodity_specification.status_id', jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED);
            $this->db->where('commodity.is_point', $is_point);
            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('commodity.name', $keyword);
                $this->db->or_like('commodity_specification.name', $keyword);
                $this->db->or_like('commodity_center.name', $keyword);
                $this->db->or_like('system_code.name', $keyword);
                $this->db->group_end();
            }
            $res = $this->db->get('commodity_specification');
            if ($res && $res->num_rows() > 0){
                $data['total_page'] = ceil($res->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }

        return $data;
    }

}