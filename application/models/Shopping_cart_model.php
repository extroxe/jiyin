<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Shopping_cart_model.php
 *
 *     Description: 购物车模型
 *
 *         Created: 2016-11-24 16:22:54
 *
 *          Author: sunzuosheng
 *
 * =====================================================================================
 */

class Shopping_cart_model extends CI_Model{
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 根据用户ID获取该用户的购物车信息
     * @param int $user_id
     * @param array $condition 条件
     * @return mixed
     */
    public function all($user_id, $condition = [], $agent_id = NULL){
        if (!is_numeric($user_id) || intval($user_id) < 1) {
            $data['success'] = FALSE;
            $data['data'] = NULL;
            $data['msg'] = '用户ID不正确';
            return $data;
        }

        $current_time = date('Y-m-d H:i:s');
        $this->db->select('shopping_cart.id,
                           shopping_cart.user_id,
                           shopping_cart.commodity_id,
                           shopping_cart.specification_id,
                           commodity_specification.selling_price as price,
                           commodity_specification.market_price,
                           commodity_specification.packagetype,
                           commodity_specification.goodsunit,
                           commodity_specification.points,
                           commodity_specification.name as commodity_specification_name,
                           commodity_specification.id as specification_id,
                           commodity.name as commodity_name,
                           system_code.name as package_type_name,
                           attachment.path,
                           commodity_center.name as commodity_center_name,
                           commodity.is_point,
                           shopping_cart.amount,
                           shopping_cart.create_time,
                           flash_sale.price as flash_sale_price
                           ');
        if (!empty($agent_id)) {
            $this->db->select('agent_commodity.price as agent_price');
        }
        $this->db->join('commodity_specification', "commodity_specification.id = shopping_cart.specification_id AND commodity_specification.status_id = '".jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED."'", 'left');
        $this->db->join('commodity_center', "commodity_center.id = commodity_specification.commodity_center_id", 'left');
        $this->db->join('flash_sale', "flash_sale.commodity_specification_id = shopping_cart.specification_id AND `flash_sale`.`start_time` <= '{$current_time}' AND `flash_sale`.`end_time` >= '{$current_time}'", 'left');
        if (!empty($agent_id)) {
           $this->db->join('agent_commodity', 'agent_commodity.commodity_specification_id = commodity_specification.id', 'left');
           $this->db->where('agent_commodity.agent_id', $agent_id);
        }
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('commodity_thumbnail', 'commodity_thumbnail.commodity_specification_id = commodity_specification.id', 'left');
        $this->db->join('attachment', 'attachment.id = commodity_thumbnail.attachment_id', 'left');
        $this->db->join('system_code', "system_code.value = commodity_specification.packagetype AND system_code.type = '". jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE."'", 'left');
        $this->db->where('shopping_cart.user_id', $user_id);
        $this->db->where('commodity.is_point !=', 1);
        if (!empty($condition)){
            $this->db->where($condition);
        }

        $this->db->order_by('shopping_cart.create_time', 'DESC');
        $this->db->group_by('shopping_cart.id');
        $result = $this->db->get('shopping_cart');

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
     * 根据用户ID获取该用户购物车的商品数量
     *
     * @return bool
     */
    public function amount($user_id){
        if (empty($user_id)) {
            return FALSE;
        }
        $this->db->select('sum(shopping_cart.amount) as amount');
        $this->db->join('commodity', 'commodity.id = shopping_cart.commodity_id');
        $this->db->where('shopping_cart.user_id', $user_id);
        $this->db->where('commodity.is_point', 0);
        $this->db->where('commodity.status_id', Jys_system_code::COMMODITY_STATUS_PUTAWAY);
        $result = $this->db->get('shopping_cart');

        if ($result && $result->num_rows() > 0){
            return $result->row_array()['amount'];
        }

        return FALSE;
    }

    /**
     * 根据购物车id列表获取其对应的商品信息及购物车商品数量
     * @param array $id_list 购物车id列表
     */
    public function get_commodity_specification_by_shopping_cart_id_list($id_list) {
        if (empty($id_list) || !is_array($id_list)) {
            return FALSE;
        }
        $current_time = date('Y-m-d H:i:s');

        $this->db->select('
            commodity.id as commodity_id,
            commodity.name as commodity_name,
            commodity.number as commodity_number,
            commodity.category_id,
            category.name as category_name,
            commodity_center.name as commodity_center_name,
            commodity_specification.commodity_center_id,
            commodity_specification.id as commodity_specification_id,
            commodity_specification.market_price,
            commodity_specification.selling_price as price,
            shopping_cart.amount,
            flash_sale.price as flash_sale_price
        ');
        $this->db->join('commodity_specification', 'commodity_specification.id = shopping_cart.specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity', 'commodity.id = shopping_cart.commodity_id');
        $this->db->join('category', 'category.id = commodity.category_id', 'left');
        $this->db->join('flash_sale', "flash_sale.commodity_specification_id = commodity_specification.id and flash_sale.start_time <= '{$current_time}' and flash_sale.end_time >= '{$current_time}'", 'left');
        $this->db->where('commodity.status_id', jys_system_code::COMMODITY_STATUS_PUTAWAY);
        $this->db->where('commodity_specification.status_id', jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED);
        $this->db->where_in('shopping_cart.id', $id_list);
        $data = $this->db->get('shopping_cart');

        if ($data && $data->num_rows() > 0) {
            return $data->result_array();
        }else {
            return FALSE;
        }
    }

}