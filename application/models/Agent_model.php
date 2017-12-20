<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =========================================================
 *
 *      Filename: Agent_model.php
 *
 *   Description: 商品分类模型
 *
 *       Created: 2016-11-16 20:23:54
 *
 *        Author: zourui
 *
 * =========================================================
 */
class Agent_model extends CI_Model
{

    //分页数据条数，默认为10
    private $page_size = 10;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取代理商主页信息
     * @param $page integer 页数
     * @param $page_size integer 偏移量
     * @return mixed 数据库资源
     */
    public function get_all_category_for_agent($page = 1, $page_size = 10, $role_id = '', $user_id = '', $start_time, $end_time, $keyword, $name)
    {
        $response = ['success' => FALSE, 'msg' => '获取代理商主页信息失败', 'data' => [], 'total' => 0, 'total_page' => 1];
        if (empty($page) || intval($page) < 1) {
            $response['msg'] = '参数错误';
            return $response;
        }

        $this->db->select('agent_index.id,
                            agent_index.color,
                            agent_index.agent_id,
                            agent_index.update_time,
                            user.name as agent_name,
                            agent_index.name as index_name');
        $this->db->join('user', 'user.id = agent_index.agent_id', 'left');
        //判断是否是代理商本人
        if ($role_id == jys_system_code::ROLE_AGENT) {
            $this->db->where('agent_index.agent_id', $user_id);
        }
        if (!empty($start_time)) {
            $this->db->where('agent_index.update_time >', $start_time);
        }
        if (!empty($end_time)) {
            $this->db->where('agent_index.update_time <', $end_time);
        }
        if (!empty($keyword)) {
            $this->db->like('agent_index.name', $keyword);
        }
        if (!empty($name)) {
            $this->db->like('user.name', $name);
        }
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $this->db->order_by('agent_index.update_time', 'DESC');
        $res = $this->db->get('agent_index');
        if ($res && $res->num_rows() > 0) {
            $data = $res->result_array();

            $this->db->select('agent_index.id');
            $this->db->join('user', 'user.id = agent_index.agent_id', 'left');
            //判断是否是代理商本人
            if ($role_id == jys_system_code::ROLE_AGENT) {
                $this->db->where('agent_index.agent_id', $user_id);
            }
            $this->db->order_by('agent_index.create_time', 'DESC');
            if (!empty($start_time)) {
                $this->db->where('agent_index.update_time >', $start_time);
            }
            if (!empty($end_time)) {
                $this->db->where('agent_index.update_time <', $end_time);
            }
            if (!empty($keyword)) {
                $this->db->like('agent_index.name', $keyword);
            }
            if (!empty($name)) {
                $this->db->like('user.name', $name);
            }
            $temp = $this->db->get('agent_index');
            $total = intval($temp->num_rows());
            $total_page = ceil($temp->num_rows() / $page_size * 1.0);
            $response = ['success' => TRUE, 'msg' => '获取代理商主页信息成功', 'data' => $data, 'total' => $total, 'total_page' => $total_page, 'role_id' => $role_id];
        } else {
            $response['msg'] = '暂无代理商主页信息';
        }

        return $response;
    }

    /**
     * 根据分类名称获取代理商分类
     *
     * @return array
     */
    public function get_category_by_name($agent_index_id = '')
    {
        $data = array('success' => 'FALSE', 'msg' => '没有代理商商品', 'data' => []);
        if (empty($index_id)) {
            $data = ['success' => FALSE, 'msg' => '没有代理商商品'];
        }
        $this->db->select('
            agent_home.id, 
            agent_home.rank,
            agent_home.agent_commodity_id,
            agent_home.agent_index_id,
            agent_commodity.commodity_id,
            agent_commodity.commodity_specification_id,
            agent_commodity.price,
            package_type.name as package_type_name,
            commodity.name as commodity_name,
            commodity_center.name as commodity_center_name
        ');
        $this->db->join('agent_commodity', 'agent_commodity.id = agent_home.agent_commodity_id', 'left');
        $this->db->join('commodity', 'commodity.id = agent_commodity.commodity_id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = agent_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
        $this->db->where('agent_home.agent_index_id', $agent_index_id);
        $this->db->order_by('agent_home.rank');
        $agent_info = $this->db->get('agent_home');
        if ($agent_info && $agent_info->num_rows() > 0) {
            $data = $agent_info->result_array();
            if (!empty($data)) {
                $data = ['success' => TRUE, 'msg' => '获取代理商商品成功', 'data' => $data];
            }
        }

        return $data;
    }

    //获取代理商商品分类
    public function check_agent_index($name = '', $agent_id){
        $result = $this->jys_db_helper->get_where('agent_index',['name' => $name, 'agent_id' => $agent_id]);

        return $result;
    }

    //获取代理商商品排序
    public function get_agent_rank($rank = ''){
        $result = $this->jys_db_helper->get_where('agent_home', ['rank' => $rank]);

        return $result;
    }
    
    //添加主页商品
    public function add_agent_home($add_agent_home = []){
        $result = $this->jys_db_helper->add_batch('agent_home', $add_agent_home);

        return $result;
    }

    //添加主页
    public function add_agent_index($add_agent_index = []){
        $result = $this->jys_db_helper->add('agent_index', $add_agent_index, TRUE);

        return $result;
    }

    //按条件获取代理商商品
    public function get_agent_commodity_by_category($value = []){
        $this->db->select('id');
        $this->db->where('agent_home.agent_commodity_id', $value['agent_commodity_id']);
        $this->db->where('agent_home.agent_index_id', $value['agent_index_id']);
        $agent_info = $this->db->get('agent_home');
        if ($agent_info && $agent_info->num_rows() > 0) {
            return $agent_info->result_array()[0];
        }

        return FALSE;
    }
                
    /**
     * 删除代理商分类
     */
    public function delete_agent_index($id = '')
    {
        if (empty($id)) {
            $data = ['success' => FALSE, 'msg' => '没有要删除的代理商主页'];
        }
        $index_info = $this->jys_db_helper->get_where('agent_home', ['agent_index_id' => $id]);
        if (!empty($index_info)) {
            $data = ['success' => FALSE, 'msg' => '该主页下存在商品不能删除!'];
            return $data;
        }
        $result = $this->jys_db_helper->delete('agent_index', $id);
        if ($result) {
            $data = ['success' => TRUE, 'msg' => '删除成功'];
        }else{
            $data = ['success' => FALSE, 'msg' => '删除失败'];
        }

        return $data;
    }

    //根据id删除代理商商品
    public function delete_agent_home_by_id($id = '', $agent_index_id= '')
    {
        $data = array('success' => FALSE, 'msg' => '删除失败');
        if (empty($id) || empty($delete_agent_home_by_id)) {
            $data['msg'] = '没有要删除的代理商商品';
        }
        $this->db->trans_start();
        $rank = $this->jys_db_helper->get('agent_home', $id)['rank'];
        $result = $this->jys_db_helper->delete('agent_home', $id);
        if ($result) {
            //调整代理商商品顺序
            $rank_info = $this->adjust_rank($id, $agent_index_id, $rank);
            if ($rank_info['success']) {
                $this->db->trans_commit();
                $data = ['success' => TRUE, 'msg' => '删除成功'];   
            }else{
                $this->db->trans_rollback();
            }
        }else{
            $this->db->trans_rollback();
            $data = ['success' => FALSE, 'msg' => '删除失败'];
        }
        $this->db->trans_complete();

        return $data;
    }

    //调整代理商商品顺序
    public function adjust_rank($id = '', $agent_index_id = '', $rank = '')
    {
        if (empty($id) || empty($agent_index_id)) {
            $data = ['success' => FALSE, 'msg' => '输入错误'];
            return $data;
        }
        $this->db->set('rank', 'rank-1', FALSE);
        $this->db->where('rank >', $rank);
        $this->db->where('agent_index_id', $agent_index_id);
        $result = $this->db->update('agent_home');
        if ($result) {
            $data = ['success' => TRUE, 'msg' => '调整成功'];
        }

        return $data;
    }

    //修改排序
    public function update_rank($val, $level_rank)
    {
        $data = $this->jys_db_helper->update('agent_home', $val, $level_rank);

        return $data;   
    }
    
    //修改代理商主页
    public function update_agent_index($update = [])
    {
        if (empty($update) && !is_array($update)) {
            $data = ['success' => FALSE, 'msg' => '没有要更新的代理商主页'];
        }
        $result = $this->jys_db_helper->update('agent_index', $update['id'], $update);
        if ($result) {
            $data = ['success' => TRUE, 'msg' => '更新成功'];
        }else{
            $data = ['success' => FALSE, 'msg' => '更新失败'];
        }

        return $data;
    }

    /**
     * 根据代理商ID和页面名称，获取该代理商的配色
     * @param int $agent_id 代理商ID
     * @param string $name 页面名称
     */
    public function get_agent_index_color($agent_id = 0, $name = "") {
        $result = array('success' => FALSE, 'msg' => '获取代理商配色失败', 'data'=>NULL);

        if (intval($agent_id) < 1) {
            $result['msg'] = '请选择代理商';
            return $result;
        }

        $condition = array('agent_id'=>intval($agent_id));
        if (!empty($name)) {
            $condition['name'] = $name;
        }

        $data = $this->jys_db_helper->get_where('agent_index', $condition);
        if ($data) {
            $result['success'] = TRUE;
            $result['msg'] = '获取代理商配色成功';
            $result['data'] = $data['color'];
        }

        return $result;
    }

    /**
     *
     * 根据页数获取数据
     * @param $page
     * @param int $page 页数
     * @param int $pagesize
     * @param $keyword
     * @return array
     */
    public function get_agent_commodity_page($page = 1, $pagesize = 10, $keyword, $role_id, $user_id, $name) {
        $result = array('success' => FALSE, 'msg' => '查询失败', 'data' => [], 'total_page' => 1);

        if (intval($page) < 1 || intval($pagesize) < 1) {
            $result['msg'] = "页数或页内数据个数不得小于1";
            return $result;
        }
        $this->db->select("agent_commodity.*, 
                            user.name,
                            commodity.name as commodity_name,
                            package_type.name as package_type_name,
                            commodity_specification.market_price,
                            commodity_specification.selling_price,
                            commodity_specification.name as commodity_specification_name,
                            commodity_specification.goodsunit,
                            commodity_center.name as commodity_center_name,
                            ");
        $this->db->join('user', 'user.id = agent_commodity.agent_id', 'left');
        $this->db->join('commodity', 'commodity.id = agent_commodity.commodity_id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = agent_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');

        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('user.name', $keyword);
            $this->db->or_like('commodity.name', $keyword);
            $this->db->or_like('commodity_center.name', $keyword);
            $this->db->or_like('commodity_specification.name', $keyword);
            $this->db->or_like('package_type.name', $keyword);
            $this->db->group_end();
        }
        if (!empty($name)) {
            $this->db->where('user.name', $name);
        }
        if ($role_id == jys_system_code::ROLE_AGENT) {
            $this->db->where('agent_commodity.agent_id', $user_id);
        }
        $this->db->order_by('agent_commodity.create_time', 'DESC');
        $this->db->limit($pagesize, ($page - 1) * $pagesize);
        $data = $this->db->get('agent_commodity');
        if ($data && $data->num_rows() > 0) {

            $this->db->select("agent_commodity.id");
            $this->db->join('user', 'user.id = agent_commodity.agent_id', 'left');
            $this->db->join('commodity', 'commodity.id = agent_commodity.commodity_id', 'left');
            $this->db->join('commodity_specification', 'commodity_specification.id = agent_commodity.commodity_specification_id', 'left');
            $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
            $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "'.jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE.'"', 'left');
            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('user.name', $keyword);
                $this->db->or_like('commodity.name', $keyword);
                $this->db->or_like('commodity_center.name', $keyword);
                $this->db->or_like('commodity_specification.name', $keyword);
                $this->db->or_like('package_type.name', $keyword);
                $this->db->group_end();
            }
            if (!empty($name)) {
                $this->db->where('user.name', $name);
            }
            if ($role_id == jys_system_code::ROLE_AGENT) {
                $this->db->where('agent_commodity.agent_id', $user_id);
            }
            $_data = $this->db->get('agent_commodity');
            $result['total_page'] = ceil($_data->num_rows()/$pagesize);
            $result['success'] = TRUE;
            $result['msg'] = "查询成功";
            $result['data'] = $data->result_array();
            $result['role_id'] = $role_id;
        }else {
            $result['msg'] = "未查询到相关数据";
            $result['role_id'] = $role_id;
        }

        return $result;
    }

    /**
     * 获取所有代理商
     */
    public function get_all_agent($page = 1, $page_size = 10, $keywords = '')
    {
        $result = ['success' => FALSE, 'msg' => '获取代理商失败', 'data' => array(), 'total_page' => 1];
        $this->db->select("user.id, user.name, user.username");
        $this->db->where('role_id', jys_system_code::ROLE_AGENT);
        if (!empty($keywords)) {
            $this->db->like('user.name', $keywords);
        }
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $data = $this->db->get('user');
        if ($data && $data->num_rows() > 0) {
            $user_info = $data->result_array();
            $result['success'] = TRUE;
            $result['msg'] = '获取成功';
            $result['data'] = $user_info;

            $this->db->select("user.id");
            $this->db->where('role_id', jys_system_code::ROLE_AGENT);
            if (!empty($keywords)) {
                $this->db->like('user.name', $keywords);
            }
            $res = $this->db->get('user');
            if ($res && $res->num_rows() > 0) {
                $result['total_page'] = ceil($res->num_rows() / $page_size * 1.0);
            }
        }   

        return $result;
    }

    /**
     * 添加代理商商品
     */
    public function add_agent_commodity($array = []) {
        $result = array('success' => FALSE, 'msg' => '添加失败');
        $i = 0;
        $j = 0;
        $add_data = array();
        //判断是否有重复的代理商商品
        foreach ($array as $key => $value) {
            $condition = ['commodity_specification_id' => $value['commodity_specification_id'], 'agent_id' => $value['agent_id']];
            $agent_commodity_info = $this->jys_db_helper->get_where('agent_commodity', $condition);
            if (!empty($agent_commodity_info)) {
                $i++;
            }else{
                $add_data[$j] = $value;
                $j++;
            }
        }
        //美誉没有重复的批量添加
        if (!empty($add_data)) {
            $result = $this->jys_db_helper->add_batch('agent_commodity', $add_data);
        }
        if ($i > 0) {
            $result = array('success' => TRUE, 'msg' => '注意！有'.$i.'条数据重复');
        }
        
        return $result;
    }

    /**
     * 更新代理商主页接口
     */
    public function update_agent_commodity($id = '', $array = []) {
        $result = array('success' => FALSE, 'msg' => '更新失败');

        if (empty($id)) {
            $result = array('success' => FALSE, 'msg' => '没有要更新的代理商商品');
            return $result;
        }
        $data = $this->jys_db_helper->update('agent_commodity', $id, $array);
        if ($data) {
            $result = array('success' => TRUE, 'msg' => '更新成功');
        }

        return $result;
    }

    /**
     * 删除代理商商品
     */
    public function delete_agent_commodity($id = '') {
        $result = array('success' => FALSE, 'msg' => '删除失败');

        if (empty($id)) {
            $result = array('success' => FALSE, 'msg' => '没有要删除的代理商商品');
            return $result;
        }

        $data = $this->jys_db_helper->delete('agent_commodity', $id);
        if ($data) {
            $result = array('success' => TRUE, 'msg' => '删除成功');
        }
        return $result;
    }

    /**
     * 分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param string $keyword 查询关键字
     * @param string $agent_id 代理商ID
     */
    public function agent_paginate_by_id($page = 1, $page_size = 10, $keyword = '', $agent_id){
        $data = [
            'success' => FALSE,
            'msg' => '没有商品数据,请联系管理员添加',
            'data' => null,
            'total_page' => 0
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }
        $this->db->select('agent_commodity.id,
                           agent_commodity.price,
                           agent_commodity.agent_id,
                           commodity.id as commodity_id,
                           commodity.name as commodity_name,
                           commodity_center.name as commodity_center_name,
                           commodity_specification.name as commodity_specification_name,
                           commodity_specification.id as commodity_specification_id,
                           commodity_specification.goodsunit,
                           package_type.name as package_type_name
                           ');

        $this->db->join('commodity', "commodity.id = agent_commodity.commodity_id", 'left');
        $this->db->join('commodity_specification', "commodity_specification.id = agent_commodity.commodity_specification_id", 'left');
        $this->db->join('commodity_center', "commodity_center.id = commodity_specification.commodity_center_id", 'left');
        $this->db->join('system_code as package_type', "package_type.value = commodity_specification.packagetype AND package_type.type = '".jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE."'", 'left');
        $this->db->where('agent_commodity.agent_id', $agent_id);
        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('commodity.name', $keyword);
            $this->db->or_like('commodity_center.name', $keyword);
            $this->db->or_like('commodity_specification.name', $keyword);
            $this->db->or_like('package_type.name', $keyword);
            $this->db->group_end();
        }
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('agent_commodity');
        if ($result && $result->num_rows() > 0){
            $commodity_info = $result->result_array();
            $data = ['success' => TRUE, 'msg' => '查询成功', 'data' => $commodity_info];

            $this->db->select('commodity_specification.id');
            $this->db->join('commodity', "commodity.id = agent_commodity.commodity_id", 'left');
            $this->db->join('commodity_specification', "commodity_specification.id = agent_commodity.commodity_specification_id", 'left');
            $this->db->join('commodity_center', "commodity_center.id = commodity_specification.commodity_center_id", 'left');
            $this->db->join('system_code as package_type', "package_type.value = commodity_specification.packagetype AND package_type.type = '".jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE."'", 'left');
            $this->db->where('agent_commodity.agent_id', $agent_id);
            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('commodity.name', $keyword);
                $this->db->or_like('commodity_center.name', $keyword);
                $this->db->or_like('commodity_specification.name', $keyword);
                $this->db->or_like('package_type.name', $keyword);
                $this->db->group_end();
            }
            $res = $this->db->get('agent_commodity');

            if ($res && $res->num_rows() > 0){
                $data['total_page'] = ceil($res->num_rows() / $page_size * 1.0);
            }else{
                $data['total_page'] = 1;
            }
        }

        return $data;
    }

}
