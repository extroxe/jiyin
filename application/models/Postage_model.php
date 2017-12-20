<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =====================================================================================
 *
 *        Filename: Postage_model.php
 *
 *     Description:
 *
 *         Created: 2017-09-08 11:23:25
 *
 *          Author: zhangcl
 *
 * =====================================================================================
 */
class Postage_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 设置运费
     * @param $data 省市区信息
     * @param $price 运费
     */
    public function set($data)
    {
        $result = array('success' => FALSE, 'msg' => '设置运费失败');

        // 格式化价格数据
        for ($i = 0; $i < count($data); $i++) {
            // $data[$i]['condition'] = round(floatval($data[$i]['condition']), 2) < 0.01 ? 0 : round(floatval($data[$i]['condition']), 2);
            $data[$i]['price'] = round(floatval($data[$i]['price']), 2) < 0.01 ? 0 : round(floatval($data[$i]['price']), 2);
        }

        // 对数据进行分类
        $this->db->trans_start();
        $postage_list = $this->db->get('freight');
        $insert_list = array();
        $update_list = array();
        if ($postage_list && $postage_list->num_rows() > 0) {
            $postage_list = $postage_list->result_array();
            foreach ($data as $data_item) {
                $insert_flag = TRUE;
                foreach ($postage_list as $postage_list_item) {
                    if ($postage_list_item['province_code'] == $data_item['province_code'] && $postage_list_item['city_code'] == $data_item['city_code'] && $postage_list_item['district_code'] == $data_item['district_code']) {
                        // 更新
                        $data_item['update_time'] = date("Y-m-d H:i:s");
                        $update_list[] = $data_item;
                        $insert_flag = FALSE;
                        break;
                    }
                }
                if ($insert_flag) {
                    $data_item['create_time'] = date("Y-m-d H:i:s");
                    $data_item['update_time'] = date("Y-m-d H:i:s");
                    $insert_list[] = $data_item;
                }
            }
        } else {
            foreach ($data as $item) {
                $item['create_time'] = date("Y-m-d H:i:s");
                $item['update_time'] = date("Y-m-d H:i:s");
                $insert_list[] = $item;
            }
        }

        //区分是否有district_code，从而区分是否按照district_code来更新
        $update_district_code = array();
        $update_city_code = array();
        foreach ($update_list as $value) {
            if (!empty($value['district_code'])) {
                $update_district_code[] = $value;
            } else {
                $update_city_code[] = $value;
            }
        }

        //插入邮费
        if (!empty($insert_list)) {
            $this->db->insert_batch('freight', $insert_list);
        }
        //按district_code更新
        if (!empty($update_district_code)) {
            $this->db->update_batch('freight', $update_district_code, 'district_code');
        }
        //按city_code更新
        if (!empty($update_city_code)) {
            $this->db->update_batch('freight', $update_city_code, 'city_code');
        }
        $this->db->trans_complete();
        $result['success'] = TRUE;
        $result['msg'] = '设置运费成功';

        if ($this->db->trans_status() === FALSE) {
            $result['success'] = FALSE;
            $result['msg'] = '设置运费失败';
        }

        return $result;
    }

    /**
     * 分页获取免邮规则
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param $condition 附加条件
     */
    public function paginate($page = 1, $page_size = 10, $condition = NULL)
    {
        $result = array('success' => FALSE, 'msg' => '获取免邮规则失败', 'data' => array(), 'total_page' => 0, 'total' => 0);
        if (intval($page_size) < 1 || intval($page) < 1) {
            $result['msg'] = '请输入正确的页码及页面大小';
            return $result;
        }

        $this->db->select('
            freight_rule.id
        ');
        $this->db->from('freight_rule');
        if (!empty($condition)) {
            $this->db->where($condition);
        }
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $this->db->order_by('freight_rule.create_time', 'DESC');

        $freight_rule_data = $this->db->get();
        if ($freight_rule_data && $freight_rule_data->num_rows() > 0) {
            $freight_rule_list = $freight_rule_data->result_array();
            // 向包邮规则中追加包邮规则条件信息
            $freight_rule_ids = array();
            foreach ($freight_rule_list as $item) {
                $freight_rule_ids[] = $item['id'];
            }
            $result['data'] = $this->_get_freight_rule_option_by_rules_id_arr($freight_rule_ids);

            if (!empty($result['data'])) {
                $result['success'] = TRUE;
                $result['msg'] = '获取包邮规则列表成功';
            } else {
                $result['msg'] = '获取包邮规则列表失败，包邮规则条件配置错误';
            }

            // 获取分页信息
            $this->db->select('freight_rule.id');
            $this->db->from('freight_rule');
            if (!empty($condition)) {
                $this->db->where($condition);
            }
            $freight_rule_total_data = $this->db->get();
            if ($freight_rule_total_data && $freight_rule_total_data->num_rows() > 0) {
                $result['total'] = $freight_rule_total_data->num_rows();
                $result['total_page'] = ceil($result['total'] / $page_size);
            } else {
                $result['total'] = count($result['data']);
                $result['total_page'] = 1;
            }
        } else {
            $result['msg'] = '当前没有包邮规则';
        }
        return $result;
    }

    /**
     * 添加免邮规则
     */
    public function add($new_info)
    {
        $result = array('success' => FALSE, 'msg' => '添加免邮规则失败');

        $freight_rule_insert_data = array(
            'name' => $new_info['name'],
            'role_id' => $new_info['role_id'],
            'order_cost' => $new_info['order_cost'],
            'order_commodity_amount' => $new_info['order_commodity_amount'],
            'commodity_scope' => $new_info['commodity_scope'],
            'terminal_type_scope' => $new_info['terminal_type_scope'],
            'level_scope' => $new_info['level_scope'],
            'status' => 1,// 状态（1为开启，0为关闭）
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        );

        $this->db->trans_start();
        $freight_rule_insert_result = $this->jys_db_helper->add('freight_rule', $freight_rule_insert_data, TRUE);
        $this->db->trans_complete();
        if ($freight_rule_insert_result['success'] && isset($freight_rule_insert_result['insert_id']) && intval($freight_rule_insert_result['insert_id']) > 0) {
            $freight_rule_id = intval($freight_rule_insert_result['insert_id']);
        } else {
            $result['msg'] = '添加免邮规则时，数据库事务失败';
            return $result;
        }

        $freight_rule_option_insert_data = array();

        // 商品列表
        $this->_insert_info_to_insert_data($freight_rule_option_insert_data, $new_info['commodity_list'], $freight_rule_id, jys_system_code::FREIGHT_RULE_OPTION_TYPE_COMMODITY);
        // 分类列表
        $this->_insert_info_to_insert_data($freight_rule_option_insert_data, $new_info['category_list'], $freight_rule_id, jys_system_code::FREIGHT_RULE_OPTION_TYPE_CATEGORY);
        // 会员等级列表
        $this->_insert_info_to_insert_data($freight_rule_option_insert_data, $new_info['level_list'], $freight_rule_id, jys_system_code::FREIGHT_RULE_OPTION_TYPE_LEVEL);
        // 终端类型列表
        $this->_insert_info_to_insert_data($freight_rule_option_insert_data, $new_info['terminal_list'], $freight_rule_id, jys_system_code::FREIGHT_RULE_OPTION_TYPE_TERMINAL);

        if (!empty($freight_rule_option_insert_data) && is_array($freight_rule_option_insert_data)) {
            $this->db->trans_start();
            $this->jys_db_helper->delete_by_condition('freight_rule_option', array('freight_rule_id' => $freight_rule_id));
            $freight_rule_option_insert_result = $this->jys_db_helper->add_batch('freight_rule_option', $freight_rule_option_insert_data);
            $this->db->trans_complete();
            if ($freight_rule_option_insert_result['success']) {
                $result['success'] = TRUE;
                $result['msg'] = '添加免邮规则成功';
            } else {
                $result['msg'] = '添加免邮规则失败，添加免邮规则配置项失败';
            }
        } else {
            $this->jys_db_helper->delete_by_condition('freight_rule_option', array('freight_rule_id' => $freight_rule_id));
            $result['success'] = TRUE;
            $result['msg'] = '添加免邮规则成功';
        }
        return $result;
    }

    /**
     * 修改免邮规则
     */
    public function update($id = 0, $new_info = [])
    {
        $result = array('success' => FALSE, 'msg' => '更新免邮规则失败');

        if (intval($id) < 1) {
            $result['msg'] = '请选择要更新的免邮规则';
            return $result;
        }

        if (empty($new_info) || !is_array($new_info)) {
            $result['msg'] = '请输入要更新的免邮规则内容';
            return $result;
        }

        $freight_rule_update_data = array(
            'name' => $new_info['name'],
            'role_id' => $new_info['role_id'],
            'order_cost' => $new_info['order_cost'],
            'order_commodity_amount' => $new_info['order_commodity_amount'],
            'commodity_scope' => $new_info['commodity_scope'],
            'terminal_type_scope' => $new_info['terminal_type_scope'],
            'level_scope' => $new_info['level_scope'],
            'update_time' => date('Y-m-d H:i:s')
        );
        $freight_rule_update_result = $this->jys_db_helper->update('freight_rule', intval($id), $freight_rule_update_data);
        if (!$freight_rule_update_result) {
            $result['msg'] = '更新免邮规则失败';
            return $result;
        }

        $freight_rule_option_insert_data = array();

        // 商品列表
        $this->_insert_info_to_insert_data($freight_rule_option_insert_data, $new_info['commodity_list'], intval($id), jys_system_code::FREIGHT_RULE_OPTION_TYPE_COMMODITY);
        // 分类列表
        $this->_insert_info_to_insert_data($freight_rule_option_insert_data, $new_info['category_list'], intval($id), jys_system_code::FREIGHT_RULE_OPTION_TYPE_CATEGORY);
        // 会员等级列表
        $this->_insert_info_to_insert_data($freight_rule_option_insert_data, $new_info['level_list'], intval($id), jys_system_code::FREIGHT_RULE_OPTION_TYPE_LEVEL);
        // 终端类型列表
        $this->_insert_info_to_insert_data($freight_rule_option_insert_data, $new_info['terminal_list'], intval($id), jys_system_code::FREIGHT_RULE_OPTION_TYPE_TERMINAL);

        if (!empty($freight_rule_option_insert_data) && is_array($freight_rule_option_insert_data)) {
            $this->db->trans_start();
            $this->jys_db_helper->delete_by_condition('freight_rule_option', array('freight_rule_id' => intval($id)));
            $freight_rule_option_insert_result = $this->jys_db_helper->add_batch('freight_rule_option', $freight_rule_option_insert_data);
            $this->db->trans_complete();
            if ($freight_rule_option_insert_result['success']) {
                $result['success'] = TRUE;
                $result['msg'] = '更新免邮规则成功';
            } else {
                $result['msg'] = '更新免邮规则失败，更新免邮规则配置项失败';
            }
        } else {
            $this->jys_db_helper->delete_by_condition('freight_rule_option', array('freight_rule_id' => intval($id)));
            $result['success'] = TRUE;
            $result['msg'] = '更新免邮规则成功';
        }
        return $result;
    }

    /**
     * 删除免邮规则
     */
    public function delete($id = 0)
    {
        $result = array('success' => FALSE, 'msg' => '删除免邮规则失败');

        if (intval($id) < 1) {
            $result['msg'] = '请选择要删除的免邮规则';
            return $result;
        }

        if ($this->jys_db_helper->delete('freight_rule', $id)) {
            $result['success'] = TRUE;
            $result['msg'] = '删除成功';
        } else {
            $result['msg'] = '删除免邮规则失败';
        }
        return $result;
    }

    /**
     * 计算某一个订单的运费
     * @param $address_info
     * @param $commodity_list
     * @param $user_info
     * @param $terminal_type
     */
    public function get_postage_by_order($address_info, $commodity_list, $user_info, $terminal_type)
    {
        $result = array('success' => FALSE, 'msg' => '获取订单运费失败', 'data' => 0);

        $freight_price = 0;
        $freight_condition = array();
        // 获取当前区域的基本运费
        if (!isset($address_info['province_code']) || empty($address_info['province_code'])) {
            $result['msg'] = '收货地址省份信息不正确';
            return $result;
        } else {
            $freight_condition['province_code'] = $address_info['province_code'];
        }
        if (!isset($address_info['city_code']) || empty($address_info['city_code'])) {
            $result['msg'] = '收货地址城市信息不正确';
            return $result;
        } else {
            $freight_condition['city_code'] = $address_info['city_code'];
        }
        if (!isset($address_info['district_code']) || empty($address_info['district_code'])) {
            $freight_condition['district_code'] = NULL;
        } else {
            $freight_condition['district_code'] = $address_info['district_code'];
        }
        $freight_data = $this->jys_db_helper->get_where('freight', $freight_condition);
        if ($freight_data && is_array($freight_data)) {
            $freight_price = floatval($freight_data['price']);
        }
        if ($freight_price == 0) {
            $result['success'] = TRUE;
            $result['msg'] = '获取订单运费成功';
            $result['data'] = $this->config->item('postage_price');;
            return $result;
        }

        // 按照用户信息及下单终端类型获取所有符合条件的包邮规则
        $this->db->select('
            freight_rule.*,
            freight_rule_option.option_id,
            freight_rule_option.type as option_type,
            freight_rule_option.option_name
        ');
        $this->db->join('freight_rule_option', 'freight_rule_option.freight_rule_id = freight_rule.id', 'left');

        $this->db->where('freight_rule.status', 1);     // 状态
        $this->db->where('freight_rule.role_id', $user_info['role_id']);    // 角色
        // 下单终端
        $this->db->group_start();
        $this->db->where('freight_rule.terminal_type_scope', 1);
        $this->db->or_group_start();
        $this->db->where('freight_rule_option.option_id', $terminal_type);
        $this->db->where('freight_rule_option.type', jys_system_code::FREIGHT_RULE_OPTION_TYPE_TERMINAL);
        $this->db->group_end();
        $this->db->group_end();
        // 用户等级
        $this->db->group_start();
        $this->db->where('freight_rule.level_scope', 1);
        if (intval($user_info['level']) > 0) {
            $this->db->or_group_start();
            $this->db->where('freight_rule_option.option_id', $user_info['level']);
            $this->db->where('freight_rule_option.type', jys_system_code::FREIGHT_RULE_OPTION_TYPE_LEVEL);
            $this->db->group_end();
        }
        $this->db->group_end();
        $freight_rule_result = $this->db->get('freight_rule');
        $result['success'] = TRUE;
        $result['msg'] = '获取订单运费成功';
        $result['data'] = $freight_price;   // 默认邮费
        if ($freight_rule_result && $freight_rule_result->num_rows() > 0) {
            // 存在满足条件的包邮规则，计算当前订单是否符合包邮条件
            $freight_rule_list = $this->_assemble_freight_rule_data($freight_rule_result->result_array());
            foreach ($freight_rule_list as $item) {
                if ($this->_check_commodity_list_satisfied_freight_rule($item, $commodity_list)) {
                    // 符合包邮条件
                    $result['data'] = 0;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * 将需要增加的包邮规则配置项加入到数据项数组中
     * @param $data_arr 数据项数组
     * @param $info_arr 需要加入到数据项数组中的数据
     * @param $freight_rule_id 免邮规则ID
     * @param $freight_rule_option_type 数据类型
     */
    private function _insert_info_to_insert_data(&$data_arr, $info_arr, $freight_rule_id, $freight_rule_option_type)
    {
        if (empty($data_arr) || !is_array($data_arr)) {
            $data_arr = array();
        }

        if (!empty($info_arr) && is_array($info_arr)) {
            foreach ($info_arr as $value) {
                $value['freight_rule_id'] = intval($freight_rule_id);
                $value['type'] = intval($freight_rule_option_type);
                $value['create_time'] = date('Y-m-d H:i:s');
                $data_arr[] = $value;
            }
        }
    }

    /**
     * 组装免邮规则数据
     * @param $data 待组装的数据
     */
    private function _assemble_freight_rule_data($data)
    {
        // 重新组装数据
        $freight_rule_result = array();     // 免邮规则

        if (empty($data) || !is_array($data)) {
            return $freight_rule_result;
        }

        foreach ($data as $item) {
            if (!array_key_exists($item['id'], $freight_rule_result)) {
                // 组装免邮规则数组
                $freight_rule_result[$item['id']] = array(
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'role_id' => $item['role_id'],
                    'order_cost' => $item['order_cost'],
                    'order_commodity_amount' => $item['order_commodity_amount'],
                    'commodity_scope' => $item['commodity_scope'],
                    'level_scope' => $item['level_scope'],
                    'terminal_type_scope' => $item['terminal_type_scope'],
                    'status' => $item['status'],
                    'create_time' => $item['create_time'],
                    'update_time' => $item['update_time'],
                    'commodity_list' => array(),
                    'category_list' => array(),
                    'level_list' => array(),
                    'terminal_list' => array()
                );
                if (isset($item['role_name'])) {
                    $freight_rule_result[$item['id']]['role_name'] = $item['role_name'];
                }
            }

            switch (intval($item['option_type'])) {
                case jys_system_code::FREIGHT_RULE_OPTION_TYPE_COMMODITY:
                    // 商品数组
                    $freight_rule_result[$item['id']]['commodity_list'][] = array(
                        'option_id' => $item['option_id'],
                        'option_name' => $item['option_name']
                    );
                    break;
                case jys_system_code::FREIGHT_RULE_OPTION_TYPE_CATEGORY:
                    // 分类数组
                    $freight_rule_result[$item['id']]['category_list'][] = array(
                        'option_id' => $item['option_id'],
                        'option_name' => $item['option_name']
                    );
                    break;
                case jys_system_code::FREIGHT_RULE_OPTION_TYPE_LEVEL:
                    // 用户等级数组
                    $freight_rule_result[$item['id']]['level_list'][] = array(
                        'option_id' => $item['option_id'],
                        'option_name' => $item['option_name']
                    );
                    break;
                case jys_system_code::FREIGHT_RULE_OPTION_TYPE_TERMINAL:
                    // 客户端类型数组
                    $freight_rule_result[$item['id']]['terminal_list'][] = array(
                        'option_id' => $item['option_id'],
                        'option_name' => $item['option_name']
                    );
                    break;
                default:
                    break;
            }
        }

        return $freight_rule_result;
    }

    /**
     * 检查订单中的商品列表是否符合某一条包邮规则
     * @param $freight_rule 包邮规则信息
     * @param $commodity_list 商品列表
     */
    private function _check_commodity_list_satisfied_freight_rule($freight_rule, $commodity_list)
    {
        switch (intval($freight_rule['commodity_scope'])) {
            case 1:
                // 全部商品
                if (round(floatval($freight_rule['order_cost']), 2) > 0) {
                    // 按金额包邮
                    $total_price = 0;
                    foreach ($commodity_list as $commodity) {
                        if (isset($commodity['flash_sale_price'])) {
                            $total_price += round(floatval($commodity['flash_sale_price']), 2);
                        }else {
                            $total_price += round(floatval($commodity['price']), 2);
                        }

                    }
                    if ($total_price >= round(floatval($freight_rule['order_cost']), 2)) {
                        // 订单内符合要求商品总金额大于包邮金额限制，满足包邮条件
                        return TRUE;
                    }
                } else if (intval($freight_rule['order_commodity_amount']) > 0) {
                    // 按数量包邮
                    $total = 0;
                    foreach ($commodity_list as $commodity) {
                        $total += intval($commodity['amount']);
                    }
                    if ($total >= intval($freight_rule['order_commodity_amount'])) {
                        // 订单内符合要求的商品数量大于包邮商品数量限制，满足包邮条件
                        return TRUE;
                    }
                } else {
                    return FALSE;
                }
                break;
            case 2:
                // 按分类
                if (!isset($freight_rule['category_list']) || empty($freight_rule['category_list']) || !is_array($freight_rule['category_list'])) {
                    // 没有指定免邮的分类列表
                    break;
                }

                if (round(floatval($freight_rule['order_cost']), 2) > 0) {
                    // 按金额包邮
                    $total_price = 0;
                    foreach ($freight_rule['category_list'] as $category) {
                        foreach ($commodity_list as $commodity) {
                            if (intval($commodity['category_id']) == intval($category['option_id'])) {
                                // 该商品分类在免邮规则之内
                                if (isset($commodity['flash_sale_price'])) {
                                    $total_price += round(floatval($commodity['flash_sale_price']), 2);
                                }else {
                                    $total_price += round(floatval($commodity['price']), 2);
                                }
                            }
                        }
                    }
                    if ($total_price >= round(floatval($freight_rule['order_cost']), 2)) {
                        // 订单符合要求商品的总金额大于包邮金额限制，满足包邮条件
                        return TRUE;
                    }
                } else if (intval($freight_rule['order_commodity_amount']) > 0) {
                    // 按数量包邮
                    $total = 0;
                    foreach ($freight_rule['category_list'] as $category) {
                        foreach ($commodity_list as $commodity) {
                            if (intval($commodity['category_id']) == intval($category['option_id'])) {
                                // 该商品分类在免邮规则之内
                                $total += intval($commodity['amount']);
                            }
                        }
                    }
                    if ($total >= intval($freight_rule['order_commodity_amount'])) {
                        // 订单内符合要求商品数量大于包邮商品数量限制，满足包邮条件
                        return TRUE;
                    }
                } else {
                    return FALSE;
                }
                break;
            case 3:
                // 按商品规格
                if (!isset($freight_rule['commodity_list']) || empty($freight_rule['commodity_list']) || !is_array($freight_rule['commodity_list'])) {
                    // 没有指定免邮的商品规格列表
                    break;
                }

                if (round(floatval($freight_rule['order_cost']), 2) > 0) {
                    // 按金额包邮
                    $total_price = 0;
                    foreach ($freight_rule['commodity_list'] as $commodity_specification) {
                        foreach ($commodity_list as $commodity) {
                            if (intval($commodity['commodity_specification_id']) == intval($commodity_specification['option_id'])) {
                                // 该商品分类在免邮规则之内
                                if (isset($commodity['flash_sale_price'])) {
                                    $total_price += round(floatval($commodity['flash_sale_price']), 2);
                                }else {
                                    $total_price += round(floatval($commodity['price']), 2);
                                }
                            }
                        }
                    }
                    if ($total_price >= round(floatval($freight_rule['order_cost']), 2)) {
                        // 订单符合要求商品的总金额大于包邮金额限制，满足包邮条件
                        return TRUE;
                    }
                } else if (intval($freight_rule['order_commodity_amount']) > 0) {
                    // 按数量包邮
                    $total = 0;
                    foreach ($freight_rule['commodity_list'] as $commodity_specification) {
                        foreach ($commodity_list as $commodity) {
                            if (intval($commodity['commodity_specification_id']) == intval($commodity_specification['option_id'])) {
                                // 该商品分类在免邮规则之内
                                $total += intval($commodity['amount']);
                            }
                        }
                    }
                    if ($total >= intval($freight_rule['order_commodity_amount'])) {
                        // 订单内符合要求商品数量大于包邮商品数量限制，满足包邮条件
                        return TRUE;
                    }
                } else {
                    return FALSE;
                }
                break;
            default:
                break;
        }

        return FALSE;
    }

    /**
     * 根据包邮规则ID数组，获取其具体包邮条件
     * @param array $freight_rule_id_arr
     */
    private function _get_freight_rule_option_by_rules_id_arr($freight_rule_id_arr = array())
    {
        if (empty($freight_rule_id_arr) || !is_array($freight_rule_id_arr)) {
            return array();
        }

        $this->db->select('
            freight_rule.*,
            freight_rule_option.option_id,
            freight_rule_option.option_name,
            freight_rule_option.type as option_type,
            role.name as role_name
        ');
        $this->db->from('freight_rule');
        $this->db->join('freight_rule_option', 'freight_rule_option.freight_rule_id = freight_rule.id', 'left');
        $this->db->join('system_code as role', "role.value = freight_rule.role_id and role.type = '" . jys_system_code::ROLE . "'", 'left');
        $this->db->where_in('freight_rule.id', $freight_rule_id_arr);
        $this->db->order_by('freight_rule.create_time', 'DESC');

        $freight_rule_data = $this->db->get();
        if ($freight_rule_data && $freight_rule_data->num_rows() > 0) {
            $freight_rule_list = $freight_rule_data->result_array();
            return $this->_assemble_freight_rule_data($freight_rule_list);
        } else {
            return array();
        }
    }
}