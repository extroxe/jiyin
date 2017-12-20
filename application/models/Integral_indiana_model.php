<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Integral_indiana_model.php
 *     Description: 积分夺宝模型
 *         Created: 2017-03-03 10:23:47
 *          Author: TangYu
 *
 * =====================================================================================
 */
class Integral_indiana_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 分页获取所有夺宝活动信息
     * @param int $page
     * @param int $page_size
     * @return array
     * @author TangYu
     */
    public function get_all_indiana_info($page = 1, $page_size = 10)
    {
        $data = array('success' => FALSE, 'msg' => '获取夺宝活动失败', 'data' => array(), 'total_page' => 0);
        if (intval($page) < 1 || intval($page_size) < 1){
            $data['msg'] = '分页参数错误';
            return $data;
        }

        $this->db->select('commodity.name as commodity_name,
                           commodity_specification.name as commodity_specification_name,
                           commodity_specification.commodity_id,
                           commodity_specification.id as commodity_specification_id,
                           commodity_specification.selling_price,
                           commodity_center.name as commodity_center_name,
                           package_type.name as package_type_name,
                           attachment.path,
                           integral_indiana.id,
                           integral_indiana.total_points,
                           integral_indiana.amount_bet,
                           integral_indiana.current_bet,
                           integral_indiana.user_total_point,
                           integral_indiana.user_expenditure,
                           integral_indiana.register_start_time,
                           integral_indiana.register_end_time,
                           integral_indiana.status as integral_indiana_status
                           ');
        $this->db->join('commodity_specification', 'commodity_specification.id = integral_indiana.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.attachment', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype AND package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        $this->db->where('commodity.status_id != ', jys_system_code::COMMODITY_STATUS_DELETE);
        $this->db->where('integral_indiana.status != ', jys_system_code::INTEGRAL_INDIANA_STATUS_DELETED);
        $this->db->group_by('integral_indiana.id');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('integral_indiana');

        if ($result && $result->num_rows() > 0){
            $data['success'] = TRUE;
            $data['msg'] = '获取夺宝活动成功';
            $data['data'] = $result->result_array();

            //分页
            $this->db->select('integral_indiana.id');
            $this->db->join('commodity', 'commodity.id = integral_indiana.commodity_id', 'left');
            $this->db->where('commodity.status_id != ', jys_system_code::COMMODITY_STATUS_DELETE);
            $this->db->where('integral_indiana.status != ', jys_system_code::INTEGRAL_INDIANA_STATUS_DELETED);
            $this->db->group_by('integral_indiana.id');
            $page_result = $this->db->get('integral_indiana');
            if ($page_result->num_rows() > 0){
                $data['total_page'] = ceil($page_result->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }else{
            $data['msg'] = '暂无夺宝活动信息';
        }

        return $data;
    }

    /**
     * 获取单个夺宝活动信息
     * @param int $indiana_id
     * @return array
     * @author TangYu
     */
    public function get_indiana_info($indiana_id = 0)
    {
        $this->db->select('commodity.name as commodity_name,
                           commodity.id as commodity_id,
                           commodity_specification.name as commodity_specification_name,
                           commodity_center.name as commodity_center_name,
                           package_type.name as package_type_name,
                           commodity_thumbnail.attachment_id,
                           attachment.path,
                           integral_indiana.id,
                           integral_indiana.total_points,
                           integral_indiana.amount_bet,
                           integral_indiana.current_bet,
                           integral_indiana.user_total_point,
                           integral_indiana.user_expenditure,
                           integral_indiana.register_start_time,
                           integral_indiana.register_end_time,
                           integral_indiana.status as integral_indiana_status
                           ');
        $this->db->join('commodity_specification', 'commodity_specification.id = integral_indiana.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype AND package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        $this->db->where('integral_indiana.id', $indiana_id);
        $this->db->limit(1);
        $result = $this->db->get('integral_indiana');

        if ($result && $result->num_rows() > 0){
            $data = array('success' => TRUE, 'msg' => '获取夺宝活动成功', 'data' => $result->row_array());
        }else{
            $data = array('success' => FALSE, 'msg' => '获取夺宝活动失败', 'data' => NULL);
        }

        return $data;
    }

    /**
     * 参与夺宝，开启事务，增加integral_indiana_bet表数据、修改integral_indiana表current_bet字段、修改user表current_point字段
     * @param int $indiana_id
     * @param array $insert_info
     * @return array
     * @author TangYu
     */
    public function join_integral_indiana($indiana_id = 0, $insert_info = array())
    {
        if (empty($indiana_id) || intval($indiana_id) < 1 || empty($insert_info) || !is_array($insert_info)){
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            return $data;
        }

        $point = $insert_info['point'];
        $user_id = $insert_info['user_id'];

        $this->db->trans_start();
        $add_res = $this->jys_db_helper->add('integral_indiana_bet', $insert_info);
        $update_current_bet_res = $this->jys_db_helper->set_update('integral_indiana', $indiana_id, ['current_bet' => 'current_bet + 1'], FALSE);
        $update_user_current_points = $this->jys_db_helper->set_update('user', $user_id, ['current_point' => 'current_point - '.$point], FALSE);

        if ($add_res['success'] && $update_current_bet_res && $update_user_current_points){
            $data = ['success' => TRUE, 'msg' => '参与活动成功，请等待活动结果'];
            $this->db->trans_commit();
        }else{
            $data = ['success' => FALSE, 'msg' => '系统繁忙，参与活动失败'];
            $this->db->trans_rollback();
        }

        if ($this->db->trans_status() === FALSE){
            $data = ['success' => FALSE, 'msg' => '数据库事务执行失败'];
            $this->db->trans_rollback();
        }

        return $data;
    }

    /**
     * 随机生成结果
     * @param int $indiana_id
     * @param int $rand
     * @return array
     * @author TangYu
     */
    public function rand_result($indiana_id = 0, $rand = 0)
    {
        if (intval($rand) < 0 || empty($indiana_id) || intval($indiana_id) < 1){
            $data = ['success' => FALSE, 'msg' => '参数错误', 'data' => NULL];
            return $data;
        }

        $this->db->select('id, integral_indiana_id, user_id');
        $this->db->where('integral_indiana_id', $indiana_id);
        $this->db->limit(1, $rand);
        $result = $this->db->get('integral_indiana_bet');
        if ($result && $result->num_rows() > 0){
            $data = ['success' => TRUE, 'msg' => '生成结果成功', 'data' => $result->row_array()];
        }else{
            $data = ['success' => FALSE, 'msg' => '生成结果失败', 'data' => NULL];
        }

        return $data;
    }

    /**
     * 将生成结果插入数据库，并改变夺宝活动状态
     * @param array $insert
     * @return bool
     * @author TangYu
     */
    public function add_data($insert = array())
    {
        if (empty($insert) || !is_array($insert)){
            return FALSE;
        }
        $this->db->trans_start();
        $add_res = $this->jys_db_helper->add('integral_indiana_result', $insert);
        $update_res = $this->jys_db_helper->update('integral_indiana', $insert['integral_indiana_id'], ['status' => Jys_system_code::INTEGRAL_INDIANA_STATUS_DONE]);

        if ($add_res['success'] && $update_res){
            $this->db->trans_commit();
            return TRUE;
        }else{
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    /**
     * 分页查询夺宝结果
     * @param int $page
     * @param int $page_size
     * @return array
     * @author TangYu
     */
    public function get_result_info($page = 1, $page_size = 10)
    {
        if (empty($page_size) || empty($page) || intval($page) < 1 || intval($page_size) < 1){
            $data = array('success' => FALSE, 'msg' => '参数错误', 'data' => NULL, 'total_page' => 0);
            return $data;
        }

        $this->db->select('integral_indiana_result.user_id,
                           integral_indiana_result.integral_indiana_bet_id as user_bet_id,
                           user.username,
                           user.nickname,
                           integral_indiana.total_points,
                           integral_indiana.amount_bet,
                           integral_indiana.commodity_id,
                           commodity.name as commodity_name,
                           commodity_specification.name as commodity_specification_name,
                           commodity_center.name as commodity_center_name,
                           package_type.name as package_type_name,
                           commodity_thumbnail.attachment_id,
                           attachment.path
                            ');
        $this->db->join('integral_indiana', 'integral_indiana.id = integral_indiana_result.integral_indiana_id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = integral_indiana.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.attachment', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype AND package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        $this->db->join('user', 'user.id = integral_indiana_result.user_id', 'left');
        $this->db->where('integral_indiana_result.status', jys_system_code::INTEGRAL_INDIANA_RESULT_STATUS_PASS);
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('integral_indiana_result');

        if ($result && $result->num_rows() > 0){
            $data = array('success' => TRUE, 'msg' => '查询夺宝结果成功', 'data' => $result->result_array());

            //分页
            $this->db->select('integral_indiana_result.id');
            $this->db->where('integral_indiana_result.status', jys_system_code::INTEGRAL_INDIANA_RESULT_STATUS_PASS);
            $page_result = $this->db->get('integral_indiana_result');
            if ($page_result->num_rows() > 0){
                $data['total_page'] = ceil($page_result->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }else{
            $data = array('success' => FALSE, 'msg' => '查询夺宝结果失败', 'data' => NULL, 'total_page' => 0);
        }

        return $data;
    }

    /**
     * 分页获取我参与的夺宝活动情况
     * @param int $user_id
     * @param int $page
     * @param int $page_size
     * @return array
     * @author TangYu
     */
    public function my_indiana($user_id = 0, $page = 1, $page_size = 10)
    {
        if (empty($user_id) || intval($user_id) < 1 || intval($page) < 1 || intval($page_size) < 1){
            $data = array('success' => FALSE, 'msg' => '参数错误', 'data' => NULL, 'total_page' => 0);
            return $data;
        }

        $this->db->select('
                           integral_indiana.id as integral_indiana_id,
                           integral_indiana.amount_bet,
                           integral_indiana.total_points,
                           integral_indiana.current_bet,
                           integral_indiana.status as integral_indiana_status,
                           integral_indiana_bet.id as integral_indiana_bet_id,
                           integral_indiana_bet.create_time,
                           integral_indiana_result.status as integral_indiana_result_status,
                           count(integral_indiana_bet.id) as count_bet,
                           commodity.id as commodity_id,
                           commodity.name as commodity_name,
                           commodity_specification.name as commodity_specification_name,
                           commodity_center.name as commodity_center_name,
                           package_type.name as package_type_name,
                           commodity_thumbnail.attachment_id,
                           attachment.path
                           ');
        $this->db->join('integral_indiana', 'integral_indiana.id = integral_indiana_bet.integral_indiana_id', 'left');
        $this->db->join('integral_indiana_result', 'integral_indiana_result.integral_indiana_bet_id = integral_indiana_bet.id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = integral_indiana.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.attachment', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype AND package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        $this->db->where('integral_indiana_bet.user_id', $user_id);
        $this->db->group_by('integral_indiana_bet.integral_indiana_id');
        $result = $this->db->get('integral_indiana_bet');

        if ($result && $result->num_rows() > 0){
            $data = array('success' => TRUE, 'msg' => '获取我的夺宝信息成功', 'data' => $result->result_array());

            //分页
            $this->db->select('integral_indiana_bet.id');
            $this->db->where('integral_indiana_bet.user_id', $user_id);
            $this->db->group_by('integral_indiana_bet.integral_indiana_id');
            $page_result = $this->db->get('integral_indiana_bet');
            if ($page_result && $page_result->num_rows() > 0){
                $data['total_page'] = ceil($page_result->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }else{
            $data = array('success' => FALSE, 'msg' => '获取我的夺宝信息失败', 'data' => NULL, 'total_page' => 0);
        }

        return $data;
    }

    /**
     * 获取当前夺宝活动，夺宝者下注信息
     * @param int $indiana_id
     * @param int $page
     * @param int $page_size
     * @return array
     * @author TangYu
     */
    public function indiana_bet_info($indiana_id = 0, $page = 1, $page_size = 10)
    {
        $this->db->select('
                           integral_indiana_bet.id as bet_id,
                           integral_indiana_bet.point,
                           integral_indiana_bet.integral_indiana_id,
                           count(integral_indiana_bet.user_id) as count_bet,
                           integral_indiana_bet.user_id,
                           integral_indiana_result.id as result_id,
                           integral_indiana_result.status as integral_indiana_result_status,
                           commodity.name as commodity_name,
                           user.nickname,
                           user.phone,
                            ');
        $this->db->join('user', 'user.id = integral_indiana_bet.user_id', 'left');
        $this->db->join('integral_indiana', 'integral_indiana.id = integral_indiana_bet.integral_indiana_id', 'left');
        $this->db->join('commodity', 'commodity.id = integral_indiana.commodity_id', 'left');
        $this->db->join('integral_indiana_result', 'integral_indiana_result.integral_indiana_bet_id = integral_indiana_bet.id', 'left');
        $this->db->where('integral_indiana_bet.integral_indiana_id', $indiana_id);
        $this->db->group_by('integral_indiana_bet.user_id');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('integral_indiana_bet');

        if ($result && $result->num_rows() > 0){
            $data = ['success' => TRUE, 'msg' => '已获取到夺宝者下注信息', 'data' => $result->result_array()];
            $this->db->select('integral_indiana_bet.id');
            $this->db->join('user', 'user.id = integral_indiana_bet.user_id', 'left');
            $this->db->where('integral_indiana_bet.integral_indiana_id', $indiana_id);
            $this->db->group_by('integral_indiana_bet.user_id');
            $page_result = $this->db->get('integral_indiana_bet');
            if ($page_result && $page_result->num_rows() > 0){
                $data['total_page'] = ceil($page_result->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '获取到夺宝者下注信息失败', 'data' => NULL, 'total_page' => 0];
        }

        return $data;
    }

    /**
     * 修改夺宝中奖者
     * @param int $indiana_id
     * @param array $update_info
     * @return array
     * @author TangYu
     */
    public function modify_winner($indiana_id = 0, $update_info = array())
    {
        if ($indiana_id < 1 || empty($update_info) || !is_array($update_info)){
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            return $data;
        }

        $this->db->trans_start();
        $delete = $this->jys_db_helper->delete_by_condition('integral_indiana_result', ['integral_indiana_id' => $indiana_id]);
        $add = $this->jys_db_helper->add('integral_indiana_result', $update_info);

        if ($delete && $add['success']){
            $data = ['success' => TRUE, 'msg' => '修改夺宝中奖者成功'];
            $this->db->trans_commit();
        }else{
            $data = ['success' => FALSE, 'msg' => '修改夺宝中奖者失败'];
            $this->db->trans_rollback();
        }

        return $data;
    }
}