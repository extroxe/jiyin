<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =========================================================
 *
 *      Filename: Category_model.php
 *
 *   Description: 商品分类模型
 *
 *       Created: 2016-11-16 20:23:54
 *
 *        Author: zourui
 *
 * =========================================================
 */
class Category_model extends CI_Model
{

    //分页数据条数，默认为10
    private $page_size = 10;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param integer $agent_id 代理商ID
     */
    public function paginate($page = 1, $page_size = 10)
    {
        $data = [
            'success' => FALSE,
            'msg' => '没有分类数据',
            'data' => NULL
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('category.id,
                           category.name,
                           category.parent_id,
                           category.type_id,
                           system_code.name as type_name,
                           father_category.name as father_name,');
        $this->db->join('category as father_category', 'father_category.id = category.parent_id', 'left');
        $this->db->join('system_code', 'system_code.value = category.type_id and system_code.type = "' . Jys_system_code::COMMODITY_TYPE . '"', 'left');

        $this->db->limit($page_size, ($page - 1) * $page_size);
        $this->db->order_by('category.id', 'DESC');
        $result = $this->db->get('category');

        if ($result && $result->num_rows() > 0) {
            $data = [
                'success' => TRUE,
                'msg' => '查询成功',
                'data' => $result->result_array()
            ];
        }

        return $data;
    }

    /**
     * 获取非父类商品分类
     * @param $id integer 父类id
     */
    public function get($parent_id = NULL)
    {
        $response = ['success' => FALSE, 'msg' => '查询分类失败', 'data' => NULL];


        $this->db->select('category.id as id,category.name as name,parent_id');
        $this->db->from('category');
        //$parend_id为空值，查询非父类商品
        if (empty($parent_id)) {
            $this->db->where('category.parent_id > 0');
        } else {//$parent_id不为空，根据id查询该分类下所有子类
            $this->db->where('category.parent_id', $parent_id);
        }
        $res = $this->db->get();

        if ($res && $res->num_rows() > 0) {
            $temp = $res->result_array();
            $response = ['success' => TRUE, 'msg' => '查询分类成功', 'data' => $temp];
        }

        return $response;
    }

    /**
     * 获取非父类对应父类分类
     * @param $id integer 父类id
     */
    public function get_info_by_id($id)
    {
        $response = ['success' => FALSE, 'msg' => '获取非父类对应父类分类失败'];

        if (empty($id) || intval($id) < 1) {
            $response['msg'] = '参数错误';
            return $response;
        }

        $this->db->select('*');
        $this->db->from('category');
        $this->db->where('parent_id', $id);
        $res = $this->db->get();

        if ($res && $res->num_rows() > 0) {
            $temp = $res->result_array();
            $response = ['success' => TRUE, 'msg' => '获取非父类对应父类分类成功', 'data' => $temp];
        }

        return $response;
    }

    /**
     * 获取父类商品分类
     * @param $id integer 父类id
     * @return mixed 数据库资源
     */
    public function get_father_category()
    {
        $response = ['success' => FALSE, 'msg' => '查询父类分类失败'];


        $this->db->select('category.id as id,category.name as name');
        $this->db->from('category');
        $this->db->where('category.parent_id', NULL);
        $res = $this->db->get();

        if ($res && $res->num_rows() > 0) {
            $temp = $res->result_array();
            $response = ['success' => TRUE, 'msg' => '查询父类分类成功', 'data' => $temp];
        }

        return $response;
    }

    /**
     * 查询全部商品分类
     * @param $id integer 父类id
     * @return mixed 数据库资源
     */
    public function get_all_category($page = 1)
    {
        if (empty($page) || intval($page) < 1) {
            return FALSE;
        }

        $response = ['success' => FALSE, 'msg' => '查询全部分类失败'];


        $this->db->select('category.id as id,
                           category.parent_id as parent_id, 
                           category.name as name');
        $this->db->join('category as parent_category', 'parent_category.id = category.parent_id', 'left');
        $this->db->limit($this->page_size, ($page - 1) * $this->page_size);
        $res = $this->db->get('category');

        if ($res && $res->num_rows() > 0) {
            $temp = $res->result_array();
            $response = ['success' => TRUE, 'msg' => '查询全部分类成功', 'data' => $temp];
        }

        return $response;
    }

    /**
     * 获取分类信息（父子类关联）
     */
    public function get_category()
    {
        $this->db->select('category.*, system_code.name as type_name');
        $this->db->join('system_code', 'system_code.value = category.type_id and system_code.type = "' . jys_system_code::COMMODITY_TYPE . '"', 'left');
        $this->db->where('category.parent_id', NULL);
        //$this->db->where('category.id !=', 13);
        $this->db->order_by('category.id', 'ASC');
        $result = $this->db->get('category');
        if ($result && $result->num_rows() > 0) {
            $collection = [];
            $parents_category = $result->result_array();

            $this->db->where('category.parent_id !=', NULL);
            $this->db->order_by('category.name');
            $res = $this->db->get('category');
            if ($res && $res->num_rows() > 0) {
                $children_category = $res->result_array();
            }

            foreach ($parents_category as $key => $parent) {
                $category = [];
                foreach ($children_category as $child) {
                    if ($child['parent_id'] == $parent['id']) {
                        $category[] = $child;
                    }
                }
                $parents_category[$key]['children_category'] = $category;

                if ($parent['type_id'] == 1) {
                    $collection[1]['type_name'] = $parent['type_name'];
                    $collection[1]['category'][] = $parents_category[$key];
                } else if ($parent['type_id'] == 2) {
                    $collection[2]['type_name'] = $parent['type_name'];
                    $collection[2]['category'][] = $parents_category[$key];
                } else if ($parent['type_id'] == 3) {
                    $collection[3]['type_name'] = $parent['type_name'];
                    $collection[3]['category'][] = $parents_category[$key];
                }
            }

            return $collection;
        } else {
            return [];
        }
    }

    /**
     * 获取会员分类
     *
     * @return array
     */
    public function get_member_category()
    {
        $this->db->select('id, name');
        $this->db->where('parent_id', 13);
        $result = $this->db->get('category');

        if ($result && $result->num_rows() > 0) {
            return $result->result_array();
        }

        return FALSE;
    }

}
