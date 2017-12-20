<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Message_model.php
 *
 *     Description: 站内信模型
 *
 *         Created: 2017-2-16 11:23:51
 *
 *          Author: sunzuosheng
 *
 * =====================================================================================
 */

class Message_model extends CI_Model {
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 分页获取当前用户的站内信数据
     *
     * @param int $page
     * @param int $page_size
     * @param null $condition
     * @return array
     */
    public function paginate($page = 1, $page_size = 10, $condition = NULL) {
        $result = array('success'=>FALSE, 'msg'=>'查询失败', 'data'=>array(), 'total_page'=>0);
        if (intval($page_size) < 1 || intval($page) < 1) {
            $result['msg'] = '参数错误';
            return $result;
        }

        $this->db->select('
            message.*,
            send_user.id as send_user_id,
            send_user.username as send_user_username,
            send_user.gender as send_user_gender,
            receive_user.id as receive_user_id,
            receive_user.username as receive_user_username,
            receive_user.gender as receive_user_gender,
            status.name as status_name
        ');

        if (!empty($condition)){
            $this->db->where($condition);
        }

        $this->db->join('user as send_user', 'send_user.id = message.user_id', 'left');
        $this->db->join('user as receive_user', 'receive_user.id = message.receive_user_id', 'left');
        $this->db->join('system_code as status', "status.value = message.status_id AND status.type = '".jys_system_code::MESSAGE_STATUS."'", 'left');
        $this->db->order_by('message.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $message_list = $this->db->get('message');

        if ($message_list && $message_list->num_rows() > 0) {
            $result['success'] = TRUE;
            $result['msg'] = '获取站内信列表成功';
            $result['data'] = $message_list->result_array();

            $this->db->select('message.id');

            if (!empty($condition)){
                $this->db->where($condition);
            }

            $this->db->join('user as send_user', 'send_user.id = message.user_id', 'left');
            $this->db->join('user as receive_user', 'receive_user.id = message.receive_user_id', 'left');
            $total = $this->db->get('message');
            if ($total && $total->num_rows() > 0) {
                $total_count = $total->num_rows();
                $result['total_page'] = ceil($total_count / $page_size * 1.0);
            }else {
                $result['total_page'] = 1;
            }
        }else {
            $result['msg'] = '没有符合要求的信息';
        }

        return $result;
    }

    /**
     * 根据ID获取站内信数据
     *
     * @param int $id
     * @return array
     */
    public function get_message_by_id($id = 0){
        $result = array('success'=>FALSE, 'msg'=>'查询失败', 'data'=>array());
        if (intval($id) < 1) {
            $result['msg'] = '参数错误';
            return $result;
        }

        $this->db->select('
            message.*,
            send_user.id as send_user_id,
            send_user.username as send_user_username,
            send_user.gender as send_user_gender,
            receive_user.id as receive_user_id,
            receive_user.username as receive_user_username,
            receive_user.gender as receive_user_gender,
            status.name as status_name
        ');

        $this->db->join('user as send_user', 'send_user.id = message.user_id', 'left');
        $this->db->join('user as receive_user', 'receive_user.id = message.receive_user_id', 'left');
        $this->db->join('system_code as status', "status.value = message.status_id AND status.type = '".jys_system_code::MESSAGE_STATUS."'", 'left');

        $this->db->where('message.id', $id);

        $message = $this->db->get('message');

        if ($message && $message->num_rows() > 0) {
            $result['success'] = TRUE;
            $result['msg'] = '获取数据成功';
            $result['data'] = $message->row_array();
        }

        return $result;
    }
}