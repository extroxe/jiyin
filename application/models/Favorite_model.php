<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Favorite_model.php
 *
 *     Description: 收藏夹模型
 *
 *         Created: 2017-1-3 17:57:08
 *
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
class Favorite_model extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->library(['Jys_kdniao']);
    }

    /**
     * 分页获取收藏信息
     * @param int $page 页数
     * @param int $page_size 页面大小
     * @param null $condition 其他条件
     * @return array
     */
    public function paginate($page = 1, $page_size = 10, $condition=NULL) {
        $result = array('success' => FALSE, 'msg' => '获取收藏列表失败', 'data' => array(), 'total_page' => 0);
        if (intval($page) < 1 || intval($page_size) < 1) {
            $result['msg'] = '分页信息不正确';
            return $result;
        }

        $this->db->select(
            'commodity.*,
             commodity.name as commodity_name,
             commodity_path.path,
             favorite.id as favorite_id,
             favorite.user_id,
             favorite.create_time as favorite_create_time,
             commodity_specification.id as commodity_specification_id,
             commodity_specification.selling_price as price,
             commodity_specification.name as commodity_specification_name,
             commodity_center.name as commodity_center_name,
             package_type.name as package_type_name
        ');
        $this->db->join('commodity_specification', "commodity_specification.id = favorite.commodity_specification_id", 'left');
        $this->db->join('commodity_center', "commodity_center.id = commodity_specification.commodity_center_id", 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.attachment', 'left');
        $this->db->join('attachment as commodity_path', 'commodity_path.id = commodity_thumbnail.attachment_id', 'left');
        if (!empty($condition) && is_array($condition)) {
            $this->db->where($condition);
        }
        $this->db->order_by('favorite.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $data = $this->db->get('favorite');

        if ($data && $data->num_rows() > 0) {
            $result['msg'] = '查询成功';
            $result['success'] = TRUE;
            $result['data'] = $data->result_array();

            $this->db->select('favorite.id');
            if (!empty($condition) && is_array($condition)) {
                $this->db->where($condition);
            }
            $page_result = $this->db->get('favorite');
            if ($page_result && $page_result->num_rows() > 0) {
                $result['total_page'] = ceil($page_result->num_rows() / $page_size * 1.0);
            }else {
                $result['total_page'] = 1;
            }
        } else {
            $result['msg'] = '未查询到符合要求的信息';
        }

        return $result;
    }

    /**
     * 添加收藏
     * @param $commodity_id 商品ID
     * @param $commodity_specification_id 商品规格ID
     * @param $user_id 用户ID
     * @return array
     */
    public function add($commodity_id, $user_id, $commodity_specification_id) {
        $data = array('success' => FALSE, 'msg' => '收藏失败');
        if (intval($commodity_id) < 1 || intval($user_id) < 1 || intval($commodity_specification_id) < 1) {
            $data['msg'] = '商品参数错误';
        }

        $favorite = $this->jys_db_helper->get_where('favorite', ['commodity_id' => $commodity_id, 'user_id' => $user_id, 'commodity_specification_id' => $commodity_specification_id]);
        if (!empty($favorite)) {
            // 该商品已经收藏过
            $data['msg'] = '该商品已收藏过了';
        } else {
            // 该商品还未收藏
            $insert = array(
                'commodity_id' => $commodity_id,
                'user_id' => $user_id,
                'create_time' => date('Y-m-d H:i:s'),
                'commodity_specification_id' => $commodity_specification_id
            );
            $result = $this->jys_db_helper->add('favorite', $insert);
            if ($result['success']) {
                $data['msg'] = '收藏成功';
                $data['success'] = TRUE;
            }
        }

        return $data;
    }
}