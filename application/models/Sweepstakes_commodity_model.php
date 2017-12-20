<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Sweepstakes_commodity_model.php
 *     Description: 积分抽奖奖品模型
 *         Created: 2017-03-01 15:12:47
 *          Author: Tangyu
 *
 * =====================================================================================
 */
class Sweepstakes_commodity_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 分页获取抽奖活动
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function paginate_sweepstakes($page = 1, $page_size = 10)
    {
        $data = array('success' => FALSE, 'msg' => '获取抽奖活动失败', 'data' => array(), 'total_page' => 0);
        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '分页参数错误';
            return $data;
        }

        $this->db->select('sweepstakes.*');
        $this->db->order_by('create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('sweepstakes');

        if ($result && $result->num_rows() > 0) {
            $data['success'] = TRUE;
            $data['msg'] = '获取抽奖活动成功';
            $data['data'] = $result->result_array();

            $this->db->select('id');
            $page_result = $this->db->get('sweepstakes');
            if ($page_result && $page_result->num_rows() > 0) {
                $data['total_page'] = ceil($page_result->num_rows() / $page_size * 1.0);
            } else {
                $data['total_page'] = 1;
            }
        } else {
            $data['msg'] = '暂无抽奖活动';
        }

        return $data;
    }

    /**
     * 添加奖品,奖品数量>8无法添加
     * @param string $table
     * @param array $insert
     * @return array
     * @author TangYu
     */
    public function add_sweepstakes_commodity($table = '', $insert = [])
    {
        if (empty($table) || empty($insert) || !is_array($insert)){
            $data = ['success' => FALSE, 'msg' => '参数错误'];
            return $data;
        }

        $this->db->trans_begin();
        //判断活动的奖品数量是否大于8
        $sweepstakes_commodity = $this->jys_db_helper->get_where_multi($table, ['sweepstakes_id' => $insert['sweepstakes_id'], 'is_show' => 1]);
        if ($sweepstakes_commodity){
            $num = count($sweepstakes_commodity);
        }else{
            $num = 0;
        }

        if ($num < 8){
            $res = $this->jys_db_helper->add($table, $insert);
            if ($res['success']){
                $data = ['success' => TRUE, 'msg' => '添加活动奖品成功'];
                $this->db->trans_commit();
            }else{
                $data = ['success' => FALSE, 'msg' => '添加活动奖品失败'];
                $this->db->trans_rollback();
            }
        }else {
            $data = ['success' => FALSE, 'msg' => '当前活动奖品已满8，无法继续添加'];
        }

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        }else{
            $this->db->trans_commit();
        }

        return $data;
    }

    /**
     * 获取所有当前活动奖品（最多8）
     * @param int $page
     * @param int $page_size
     * @param bool $ignore_date
     * @return array
     */
    public function get_sweepstakes_commodity($page = 1, $page_size = 10, $ignore_date = false)
    {
        $data = array('success' => FALSE, 'msg' => '获取奖品失败', 'data' => NULL, 'total_page' => 0);

        $date = date('Y-m-d H:i:s');
        $this->db->select('commodity.id as commodity_id,
                           commodity.name as commodity_name,
                           commodity.introduce as commodity_introduce,
                           commodity_specification.name as commodity_specification_name,
                           commodity_specification.selling_price,
                           commodity_center.name as commodity_center_name,
                           attachment.path as commodity_path,
                           sweepstakes.name as sweepstakes_name,
                           sweepstakes.consume_points,
                           sweepstakes.start_time,
                           sweepstakes.end_time,
                           sweepstakes_result.id as result_id,
                           sweepstakes_commodity.id,
                           sweepstakes_commodity.sweepstakes_id,
                           sweepstakes_commodity.point,
                           sweepstakes_commodity.total_number,
                           sweepstakes_commodity.current_number,
                           sweepstakes_commodity.user_total_point,
                           sweepstakes_commodity.user_expenditure,
                           sweepstakes_commodity.register_start_time,
                           sweepstakes_commodity.register_end_time,
                           package_type.name as package_type_name
                            ');
        $this->db->join('commodity', 'commodity.id = sweepstakes_commodity.commodity_id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = sweepstakes_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.attachment', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('sweepstakes', 'sweepstakes.id = sweepstakes_commodity.sweepstakes_id', 'left');
        $this->db->join('sweepstakes_result', 'sweepstakes_result.sweepstakes_commodity_id = sweepstakes_commodity.id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype AND package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        if (!$ignore_date){
            $this->db->where('sweepstakes.start_time <=', $date);
            $this->db->where('sweepstakes.end_time >=', $date);
        }

        $this->db->where('sweepstakes_commodity.is_show', 1);
        $this->db->group_by('sweepstakes_commodity.id');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('sweepstakes_commodity');

        if ($result && $result->num_rows() > 0){
            $data['success'] = TRUE;
            $data['msg'] = '获取奖品成功';
            $data['data'] = $result->result_array();

            $this->db->select('sweepstakes_commodity.id');
            $this->db->join('sweepstakes', 'sweepstakes.id = sweepstakes_commodity.sweepstakes_id', 'left');
            if (!$ignore_date){
                $this->db->where('sweepstakes.start_time <=', $date);
                $this->db->where('sweepstakes.end_time >=', $date);
            }
            $this->db->where('sweepstakes_commodity.is_show', 1);
            $page_result = $this->db->get('sweepstakes_commodity');
            if ($page_result && $page_result->num_rows() > 0) {
                $data['total_page'] = ceil($page_result->num_rows() / $page_size * 1.0);
            } else {
                $data['total_page'] = 1;
            }
        }else{
            $data['msg'] = '暂无抽奖奖品信息';
        }

        return $data;
    }

    /**
     * 找出符合当前参与者的奖品、事务（扣除抽奖消耗积分、判断抽取奖品、加入result表、奖品表current_number字段-1）
     * @param array $condition
     * @return array
     * @author TangYu
     */
    public function find_one($condition = [])
    {
        if (empty($condition) || !is_array($condition)){
            $data = ['success' => FALSE, 'msg' => '参数错误', 'data' => NULL];
            return $data;
        }

        $this->db->trans_begin();
        //扣去抽奖消费积分
        $consume_res = $this->jys_db_helper->set_update('user', $_SESSION['user_id'], ['current_point' => 'current_point - '.$condition['consume_points']], FALSE);
        $this->db->select('commodity.id as commodity_id,
                           commodity.name as commodity_name,
                           commodity.introduce,
                           commodity_specification.name as commodity_specification_name,
                           commodity_center.name as commodity_center_name,
                           package_type.name as package_type_name,
                           attachment.path as commodity_path,
                           sweepstakes.consume_points,
                           sweepstakes_commodity.id,
                           sweepstakes_commodity.sweepstakes_id,
                           sweepstakes_commodity.point,
                           sweepstakes_commodity.total_number,
                           sweepstakes_commodity.current_number
                            ');

        $this->db->join('commodity_specification', 'commodity_specification.id = sweepstakes_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype AND package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.attachment', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('sweepstakes', 'sweepstakes.id = sweepstakes_commodity.sweepstakes_id', 'left');
        $this->db->group_start();
        $this->db->where('sweepstakes_commodity.user_total_point <', $condition['current_point']);
        $this->db->where('sweepstakes_commodity.user_expenditure <', $condition['user_expenditure']);
        $this->db->where('sweepstakes_commodity.register_start_time <=', $condition['register_time']);
        $this->db->where('sweepstakes_commodity.register_end_time >=', $condition['register_time']);
        $this->db->or_where('sweepstakes_commodity.user_total_point IS NULL');
        $this->db->or_where('sweepstakes_commodity.user_expenditure IS NULL');
        $this->db->or_where('sweepstakes_commodity.register_start_time IS NULL');
        $this->db->or_where('sweepstakes_commodity.register_end_time IS NULL');
        $this->db->group_end();
        $this->db->where('sweepstakes_commodity.current_number >=', 1);
        $this->db->where('sweepstakes_commodity.is_show', 1);
        $this->db->group_by('sweepstakes_commodity.id');
        $result = $this->db->get('sweepstakes_commodity');

        if ($result && $result->num_rows() > 0){
            //随机取一条
            $count = $result->num_rows();
            $rand = rand(1, $count) - 1;
            $result = $result->result_array()[$rand];

            if ($consume_res && $result['commodity_id'] == NULL && $result['point'] == NULL){
                $data = ['success' => FALSE, 'msg' => '抽取奖品为谢谢参与', 'data' => NULL];
                $this->db->trans_commit();
                return $data;
            }

            $insert = [
                'sweepstakes_commodity_id' => $result['id'],
                'sweepstakes_id' => $result['sweepstakes_id'],
                'user_id' => $condition['user_id'],
                'status' => jys_system_code::SWEEPSTAKES_RESULT_STATUS_NOT_RECEIVE,
                'create_time' => date('Y-m-d H:i:s')
            ];

            //并加入sweepstakes_result表
            $add_res = $this->jys_db_helper->add('sweepstakes_result', $insert, TRUE);

            //sweepstakes_commodity 表剩余数量-1
            $update_res = $this->jys_db_helper->set_update('sweepstakes_commodity', $result['id'], ['current_number' => 'current_number - 1'], FALSE);
            if ($consume_res && $add_res['success'] && $update_res){
                $data = ['success' => TRUE, 'msg' => '获取奖品成功', 'data' => $result, 'insert_id' => $add_res['insert_id']];
                $this->db->trans_commit();
            }else{
                $this->db->trans_rollback();
            }
        } else {
            $data = ['success' => FALSE, 'msg' => '没有符合条件奖品', 'data' => NULL];
            $this->db->trans_commit();
            return $data;
        }

        return $data;
    }

    /**
     * 领取积分奖品
     * @param int $result_id
     * @param int $user_id
     * @return array
     * @author TangYu
     */
    public function receive($result_id = 0, $user_id = 0)
    {
        $this->db->trans_start();
        $this->db->select('sweepstakes.consume_points,
                           sweepstakes_commodity.commodity_id,
                           sweepstakes_commodity.point,
                           commodity.name as commodity_name,
                           sweepstakes_result.user_id
                           ');
        $this->db->join('sweepstakes', 'sweepstakes.id = sweepstakes_result.sweepstakes_id', 'left');
        $this->db->join('sweepstakes_commodity', 'sweepstakes_commodity.id = sweepstakes_result.sweepstakes_commodity_id', 'left');
        $this->db->join('commodity', 'commodity.id = sweepstakes_commodity.commodity_id', 'left');
        $this->db->where('sweepstakes_result.id', $result_id);
        $this->db->where('sweepstakes_result.status', Jys_system_code::SWEEPSTAKES_RESULT_STATUS_NOT_RECEIVE);
        $this->db->where('sweepstakes_result.user_id', $user_id);
        $result = $this->db->get('sweepstakes_result');

        if ($result && $result->num_rows() > 0){
            //抽奖所得积分，加入用户当前积分
            $add_res = $this->jys_db_helper->set_update('user', $result->row_array()['user_id'], ['current_point' => 'current_point + '.$result->row_array()['point']], FALSE);
            //将sweepstakes_result表status置为1
            $update_res = $this->jys_db_helper->update_by_condition('sweepstakes_result', ['id' => $result_id], ['status' => Jys_system_code::SWEEPSTAKES_RESULT_STATUS_RECEIVED]);
            if ($add_res && $update_res){
                $data = ['success' => TRUE, 'msg' => '领取成功', 'point' => $result->row_array()['point']];
                $this->db->trans_commit();
            }else{
                $data = ['success' => FALSE, 'msg' => '领取失败，网络异常'];
                $this->db->trans_rollback();
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '领取失败，无该条数据'];
        }

        return $data;
    }

    /**
     * 分页获取我的所有奖品
     * @param int $user_id
     * @param int $page
     * @param int $page_size
     * @return array
     * @author TangYu
     */
    public function get_my_prize($user_id = 0, $page = 1, $page_size = 10)
    {
        $this->db->select('sweepstakes.name as sweepstakes_name,
                           sweepstakes_commodity.commodity_id,
                           sweepstakes_commodity.point,
                           commodity_specification.name as commodity_specification_name,
                           commodity_specification.selling_price as commodity_specification_name,
                           commodity_center.name as commodity_center_name,
                           commodity.name as commodity_name,
                           attachment.path as commodity_path,
                           sweepstakes_result.id as result_id,
                           sweepstakes_result.sweepstakes_commodity_id,
                           sweepstakes_result.status,
                           sweepstakes_result.create_time
                            ');
        $this->db->join('sweepstakes', 'sweepstakes.id = sweepstakes_result.sweepstakes_id', 'left');
        $this->db->join('sweepstakes_commodity', 'sweepstakes_commodity.id = sweepstakes_result.sweepstakes_commodity_id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = sweepstakes_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.attachment', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->where('sweepstakes_result.user_id', $user_id);
        $this->db->group_start();
        $this->db->where('sweepstakes_commodity.commodity_id !=', NULL);
        $this->db->or_where('sweepstakes_commodity.point !=', NULL);
        $this->db->group_end();
        $this->db->group_by('sweepstakes_result.id');
        $this->db->order_by('sweepstakes_result.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('sweepstakes_result');

        if ($result && $result->num_rows() > 0){
            $data = ['success' => TRUE, 'msg' => '获取我的奖品成功', 'data' => $result->result_array()];

            //分页
            $this->db->select('sweepstakes_result.id');
            $this->db->join('sweepstakes', 'sweepstakes.id = sweepstakes_result.sweepstakes_id', 'left');
            $this->db->join('sweepstakes_commodity', 'sweepstakes_commodity.id = sweepstakes_result.sweepstakes_commodity_id', 'left');
            $this->db->join('commodity_specification', 'commodity_specification.id = sweepstakes_commodity.commodity_specification_id', 'left');
            $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
            $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
            $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.attachment', 'left');
            $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
            $this->db->where('sweepstakes_result.user_id', $user_id);
            $this->db->group_start();
            $this->db->where('sweepstakes_commodity.commodity_id !=', NULL);
            $this->db->or_where('sweepstakes_commodity.point !=', NULL);
            $this->db->group_end();
            $this->db->group_by('sweepstakes_result.id');
            $this->db->order_by('sweepstakes_result.create_time', 'DESC');
            $page_result = $this->db->get('sweepstakes_result');
            if ($page_result && $page_result->num_rows() > 0){
                $data['total_page'] =ceil($page_result->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '奖品列表空空如也，快去抽奖吧', 'data' => NULL, 'total_page' => 0];
        }

        return $data;
    }

    /**
     * 获取滚动奖品（最新） 分页获取奖品结果表数据
     * @param int $sweepstakes_id
     * @param int $page
     * @param int $page_size
     * @return array
     * @author TangYu
     */
    public function get_scroll_prize($sweepstakes_id = 0, $page = 1, $page_size = 10)
    {
        $this->db->select('sweepstakes_result.id,
                           sweepstakes_commodity.commodity_id,
                           sweepstakes_commodity.point,
                           commodity.name as commodity_name,
                           commodity_specification.name as commodity_specification_name,
                           commodity_specification.selling_price as commodity_specification_name,
                           commodity_center.name as commodity_center_name,
                           user.phone,
                           user.nickname,
                           user.username
                            ');
        $this->db->join('sweepstakes_commodity', 'sweepstakes_commodity.id = sweepstakes_result.sweepstakes_commodity_id', 'left');
        $this->db->join('commodity', 'commodity.id = sweepstakes_commodity.commodity_id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = sweepstakes_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('user', 'sweepstakes_result.user_id = user.id', 'left');
        $this->db->where('sweepstakes_result.sweepstakes_id', $sweepstakes_id);
        $this->db->order_by('sweepstakes_result.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('sweepstakes_result');

        if ($result && $result->num_rows() > 0){
            $data = ['success' => TRUE, 'msg' => '获取滚动获奖信息成功', 'data' => $result->result_array()];
            //分页
            $this->db->select('sweepstakes_result.id');
            $this->db->join('sweepstakes_commodity', 'sweepstakes_commodity.id = sweepstakes_result.sweepstakes_commodity_id', 'left');
            $this->db->join('commodity', 'commodity.id = sweepstakes_commodity.commodity_id', 'left');
            $this->db->join('user', 'sweepstakes_result.user_id = user.id', 'left');
            $this->db->where('sweepstakes_result.sweepstakes_id', $sweepstakes_id);
            $this->db->order_by('sweepstakes_result.create_time', 'DESC');
            $page_result = $this->db->get('sweepstakes_result');

            if ($page_result && $page_result->num_rows() > 0){
                $data['total_page'] =ceil($page_result->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '暂无获奖信息', 'data' => NULL, 'total_page' => 0];
        }

        return $data;
    }

    /**
     * 默认查询当前时间段内是否有抽奖活动
     * 传入ID时，为判断该ID活动是否已经开始
     * @param int $id
     * @return bool
     * @author TangYu
     */
    public function judge_sweepstakes($id = 0)
    {
        $date = date('Y-m-d H:i:s');

        $this->db->select('id');
        if (!empty($id)){
            $this->db->where('id', $id);
        }
        $this->db->where('start_time <=', $date);
        $this->db->where('end_time >=', $date);
        $result = $this->db->get('sweepstakes');
        if ($result && $result->num_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }
}