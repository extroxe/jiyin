<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Discount_coupon_model.php
 *
 *     Description: 优惠券模型
 *
 *         Created: 2016-12-26 10:27:54
 *
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
class Discount_coupon_model extends CI_Model {
    public function __construct(){
        parent::__construct();
    }

    public function paginate($page = 1, $page_size = 10, $condition = array())
    {
        $this->check_user_discount_coupon_expire();
        $data = array('success' => FALSE, 'data' => '获取优惠券信息失败', 'data' => NULL, 'total_page' => 0, 'total' => 0);
        if (empty($page) || empty($page_size) || intval($page) < 0 || intval($page_size) < 0) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('discount_coupon.*');
        $this->db->where('discount_coupon.status_id !=', jys_system_code::DISCOUNT_COUPON_STATUS_DELETE);
        if (!empty($condition)) {
            $this->db->where($condition);
        }
        $this->db->order_by('discount_coupon.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('discount_coupon');

        if ($result && $result->num_rows() > 0) {
            $data['success'] = TRUE;
            $data['msg'] = '获取优惠券信息成功';
            $data['data'] = $result->result_array();

            $this->db->select('id');
            $this->db->where('discount_coupon.status_id !=', jys_system_code::DISCOUNT_COUPON_STATUS_DELETE);
            if (!empty($condition)) {
                $this->db->where($condition);
            }
            $page_result = $this->db->get('discount_coupon');
            if ($page_result && $page_result->num_rows()) {
                $data['total'] = $page_result->num_rows();
                $data['total_page'] = ceil($page_result->num_rows() / $page_size * 1.0);
            } else {
                $data['total'] = count($data['data']);
                $data['total_page'] = 1;
            }
        } else {
            $data['msg'] = '没有优惠券信息';
        }

        return $data;
    }

    /**
     * 根据user_id和优惠价全状态获取用户优惠券列表
     * @param int $user_id 用户ID
     * @param int $status_id 优惠券状态
     * @param int $order_price  订单价格，当这个值不为0时，为订单结算页面的优惠券，需要查出符合当前订单价格的优惠券
     * @return array
     */
    public function get_user_discount_coupon_list_by_user_id($user_id = 0, $status_id = 0, $order_price = 0) {
        $this->check_user_discount_coupon_expire();
        if (intval($user_id) < 1) {
            $data['success'] = FALSE;
            $data['data'] = NULL;
            $data['msg'] = '获取失败，参数不正确';
            return $data;
        }

        $this->db->select('user_discount_coupon.*,
                           discount_coupon.name,
                           discount_coupon.condition,
                           discount_coupon.privilege,
                           user_discount_coupon_status.name as status');

        $this->db->join('discount_coupon', 'discount_coupon.id = user_discount_coupon.discount_coupon_id', 'left');
        $this->db->join('system_code as user_discount_coupon_status', 'user_discount_coupon_status.value = user_discount_coupon.status_id AND user_discount_coupon_status.type = "'.jys_system_code::USER_DISCOUNT_COUPON_STATUS.'"', 'left');
        $this->db->where('user_discount_coupon.status_id', jys_system_code::USER_DISCOUNT_COUPON_STATUS_UNUSED);
        $this->db->where('user_discount_coupon.user_id', $user_id);
        if (!empty($status_id)) {
            $this->db->where('user_discount_coupon.status_id', $status_id);
        }
        if (!empty($order_price)) {
            $this->db->where('discount_coupon.condition <= ', $order_price);
        }
        $result = $this->db->get('user_discount_coupon');

        if ($result && $result->num_rows() > 0){
            $data['success'] = TRUE;
            $data['data'] = $result->result_array();
            $data['msg'] = '获取成功';
        }else{
            $data['success'] = FALSE;
            $data['data'] = NULL;
            $data['msg'] = '获取失败';
        }

        return $data;
    }

    /**
     * 根据用户优惠券ID获取用户优惠券详情
     * @param $user_discount_coupon_id 用户优惠券ID
     */
    public function get_user_discount_coupon_by_id($user_discount_coupon_id) {
        $this->check_user_discount_coupon_expire();
        if (intval($user_discount_coupon_id) < 1) {
            $data['success'] = FALSE;
            $data['data'] = NULL;
            $data['msg'] = '获取失败，参数不正确';
            return $data;
        }

        $this->db->select('user_discount_coupon.*,
                           discount_coupon.name,
                           discount_coupon.condition,
                           discount_coupon.privilege,
                           user_discount_coupon_status.name as status');

        $this->db->where('user_discount_coupon.id', $user_discount_coupon_id);
        $this->db->where('user_discount_coupon_status.type', jys_system_code::USER_DISCOUNT_COUPON_STATUS);
        $this->db->join('discount_coupon', 'discount_coupon.id = user_discount_coupon.discount_coupon_id', 'left');
        $this->db->join('system_code as user_discount_coupon_status', 'user_discount_coupon_status.value = user_discount_coupon.status_id', 'left');
        $result = $this->db->get('user_discount_coupon');

        if ($result && $result->num_rows() > 0){
            $data['success'] = TRUE;
            $data['data'] = $result->result_array();
            $data['msg'] = '获取成功';
        }else{
            $data['success'] = FALSE;
            $data['data'] = NULL;
            $data['msg'] = '获取失败';
        }

        return $data;
    }

    /**
     * 获取已发布的的优惠券
     * @param status_id 发布状态
     */
    public function get_discount_coupon_by_status_id($user_id, $status_id = jys_system_code::DISCOUNT_COUPON_STATUS_PULISHED)
    {
        if (intval($status_id) < 1){
            $data = ['success' => FALSE, 'msg' => '获取失败，参数不正确', 'data' => NULL];
            return $data;
        }

        $date_now = date('Y-m-d H:i:s');
        $this->db->select('discount_coupon.*, user_discount_coupon.id as user_discount_coupon_id');
        $this->db->join('user_discount_coupon', 'user_discount_coupon.discount_coupon_id = discount_coupon.id and user_discount_coupon.user_id = '.$user_id, 'left');
        $this->db->where('discount_coupon.status_id', $status_id);
        $this->db->where('discount_coupon.start_time <=', $date_now);
        $this->db->where('discount_coupon.end_time >=', $date_now);
        $this->db->group_by('discount_coupon.id');
        $this->db->order_by('user_discount_coupon_id');
        $result = $this->db->get('discount_coupon');

        if ($result && $result->num_rows() > 0){
            $data = ['success' => TRUE, 'msg' => '获取可领取优惠券成功', 'data' => $result->result_array()];
        }else{
            $data = ['success' => FALSE, 'msg' => '获取可领取优惠券失败', 'data' => NULL];
        }

        return $data;
    }

    /**
     * 根据优惠券ID 查看该用户是否已经领取过该优惠券
     * @param  $discount_coupon_id 优惠券ID
     */
    public function check_user_discount_coupon_by_id($discount_coupon_id = 0, $user_id = 0)
    {
        if (intval($user_id) < 0 || intval($discount_coupon_id) < 0){
            $data = ['success' => FALSE, 'msg' => '查询失败，参数错误'];
            return $data;
        }
        $condition = ['user_id' => $user_id, 'discount_coupon_id' => $discount_coupon_id];

        $result = $this->jys_db_helper->get_where('user_discount_coupon', $condition);
        if (!empty($result)){
            $data['success'] = TRUE;
        }else{
            $data['success'] = FALSE;
        }

        return $data;
    }

    /**
     * 根据优惠券ID 获取该优惠券的信息
     * @param  $discount_coupon_id 优惠券ID
     */
    public function get_discount_coupon_info_by_id($discount_coupon_id = 0)
    {
        $this->check_user_discount_coupon_expire();
        $result = $this->jys_db_helper->get('discount_coupon', $discount_coupon_id);

        if (!empty($result)){
            return $result;
        }
    }

    /**
     * 根据优惠券ID获取发放情况
     *
     * @param $id 优惠券ID
     */
    public function get_distribution_by_coupon_id($id = 0) {
        $result = array('success'=>FALSE, 'msg'=>'获取优惠券发放情况失败', 'data'=>array());
        if (intval($id) < 1) {
            $result['msg'] = '优惠券ID不正确';
            return $result;
        }

        $this->check_user_discount_coupon_expire();
        $this->db->select('
            user.*,
            discount_coupon.id as discount_coupon_id,
            discount_coupon.name as discount_coupon_name,
            discount_coupon.condition,
            discount_coupon.privilege,
            user_discount_coupon.id as user_discount_coupon_id,
            user_discount_coupon.start_time,
            user_discount_coupon.end_time,
            user_discount_coupon.status_id as user_discount_coupon_status_id,
            user_discount_coupon.create_time as user_discount_coupon_create_time,
            user_discount_coupon_status.name as user_discount_coupon_status_name,
            user_avatar.path as avatar_path
        ');

        $this->db->join('user', 'user.id = user_discount_coupon.user_id');
        $this->db->join('discount_coupon', 'discount_coupon.id = user_discount_coupon.discount_coupon_id');
        $this->db->join('system_code as user_discount_coupon_status', "user_discount_coupon_status.value = user_discount_coupon.status_id AND user_discount_coupon_status.type ='".jys_system_code::USER_DISCOUNT_COUPON_STATUS."'", 'left');
        $this->db->join('attachment as user_avatar', 'user_avatar.id = user.avatar', 'left');

        $this->db->where('discount_coupon.id', $id);
        $this->db->order_by('user_discount_coupon.create_time', 'DESC');

        $data = $this->db->get('user_discount_coupon');

        if ($data && $data->num_rows() > 0) {
            $data = $data->result_array();
            $result['success'] = TRUE;
            $result['msg'] = '查询成功';
            $result['data'] = $data;
        }else {
            $result['msg'] = '当前优惠券无人领取';
        }
        return $result;
    }

    /**
     * 检查所有人已领取的优惠券是否过期
     */
    public function check_user_discount_coupon_expire() {
        $this->jys_db_helper->update_by_condition('user_discount_coupon', ['end_time <= '=>date('Y-m-d H:i:s'), 'status_id'=>jys_system_code::USER_DISCOUNT_COUPON_STATUS_UNUSED], ['status_id'=>jys_system_code::USER_DISCOUNT_COUPON_STATUS_EXPIRED]);
    }
}