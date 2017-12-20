<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Address_model.php
 *
 *     Description:
 *
 *         Created: 2016-12-11 23:23:56
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
Class Address_model extends CI_Model {
    /**
     * User_model 构造函数
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * 显示该用户下的所有地址信息
     */
    public function show_address($limit = FALSE){
        $user_id = $_SESSION['user_id'];

        $this->db->where('user_id', $user_id);
        $this->db->order_by('default, create_time', 'DESC');
        if ($limit){
            $this->db->limit($limit, 0);
        }

        $result = $this->db->get('address');

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
     * 添加地址
     *
     * @param array $address 地址信息
     * @return bool
     */
    public function add_address($address = []){
        $data['success'] = FALSE;
        $data['msg'] = '添加失败';

        if (empty($address)){
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->trans_begin();
        if ($address['default']){
            $this->jys_db_helper->update_by_condition('address', ['user_id'=>$_SESSION['user_id']], ['default'=>0]);
        }
        $check_repeat = $this->_check_address_repeat($address,$address['user_id']);
        if($check_repeat['msg'] == 'not_repeat'){
            if ($this->jys_db_helper->add('address', $address)['success']){
                $data['success'] = TRUE;
                $data['msg'] = '添加成功';
                $this->db->trans_commit();
            }else{
                $this->db->trans_rollback();
            }
        }else if($check_repeat['msg'] == 'repeat'){
            $data['msg'] = '您输入的地址已存在，请勿重复输入';
            $this->db->trans_rollback();
        }else{
            $data['msg'] = '检查地址时出错，请确认是否正确输入';
            $this->db->trans_rollback();
        }


        return $data;
    }

    /**
     * 检查地址是否重复
     * @param $address_str 地址信息数组
     * @param $user_id 用户Id
     * @return bool 如有完全一样的地址，返回FALSE，否则返回TRUE
     */
    private function _check_address_repeat($address = [], $user_id){
        $result['msg'] = 'repeat';
        if(empty($address) || is_null($address) || empty($user_id) || intval($user_id) < 1){
            return FALSE;
        }
        $number = $this->jys_db_helper->get_total_num('address', $address);
        if (intval($number) < 1) {
            $result['msg'] = 'not_repeat';
        }
        return $result;   //repeat代表地址重复了,not_repeat代表地址没有重复
    }

    /**
     * 更新地址
     *
     * @param int $id 地址ID
     * @param array $address 地址信息
     * @return mixed
     */
    public function update_address($id = 0, $user_id = 0, $address = []){
        $data['success'] = FALSE;
        $data['msg'] = '更新失败';

        if (empty($address) || !is_array($address) || count($address) < 1 || intval($id) < 1 || intval($user_id) < 1){
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->trans_begin();

        if ($address['default']){
            $this->jys_db_helper->update_by_condition('address', ['user_id' => $user_id], ['default'=>0]);
        }
        $check_repeat = $this->_check_address_repeat($address, $user_id);
        if($check_repeat['msg'] == 'not_repeat'){
            if ($this->jys_db_helper->update_by_condition('address', array('id' => $id, 'user_id' => $user_id), $address)){
                $data['success'] = TRUE;
                $data['msg'] = '更新成功';
                $this->db->trans_commit();
            }else{
                $this->db->trans_rollback();
            }
        }else if($check_repeat['msg'] == 'repeat'){
            $data['msg'] = '您输入的地址已存在，请勿重复输入';
            $this->db->trans_rollback();
        }else{
            $data['msg'] = '检查地址时出错，请确认是否正确输入';
            $this->db->trans_rollback();
        }
        return $data;
    }

    /**
     * 获取选择的地址（上次订单所选择的地址ID）
     *
     * @return bool
     */
    public function selected_address($user_id = NULL){
        if (empty($user_id) || intval($user_id) < 1){
            return FALSE;
        }

        $this->db->select('order.address_id');
        $this->db->where('order.user_id', $user_id);
        $this->db->order_by('order.create_time', 'DESC');
        $result = $this->db->get('order');

        if ($result && $result->num_rows() > 0){
            return $result->row_array()['address_id'];
        }

        return FALSE;
    }

}