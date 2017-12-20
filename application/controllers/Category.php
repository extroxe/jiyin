<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Category.php
 *
 *     Description: 用户分类查询控制器
 *
 *         Created: 2016-11-24 21:21:14
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
class Category extends CI_Controller{
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_db_helper']);
        $this->load->model(['Category_model', 'Common_model', 'Commodity_model']);
    }

    /**
     * 获取父类商品分类
     * @return string 分类数组
     */
    public function get_father_category(){
        $response = $this->Category_model->get_father_category();
        echo json_encode($response);
    }

    /**
     * 获取非父类分类
     * @return string 分类数组
     */
    public function get(){
        $response = $this->Category_model->get();

        echo json_encode($response);
    }

    /**
     * 获取全部分类（父子类关联）
     */
    public function get_category(){
        $response = $this->Category_model->get_category();

        echo json_encode($response);
    }

    /**
     * 根据首页的分类进行选择商品并分页
     */
    public function get_commodity_by_category(){
        $result = [
            'success' => FALSE,
            'msg' => '查询分类商品数据失败',
            'data' => null
        ];
        $category = $this->input->post('category', true);
        $page = $this->input->post('page', true);
        $page_size = 10; //默认为每页10个

        $result = $this->Commodity_model->get_commodity_by_category($category,$page, $page_size);
        foreach($result['data'] as $key => $val){
            if(!empty($val) && !is_null($val) && $val != FALSE){
                $result['data'][$key]['evaluation_number'] = $this->Commodity_model->get_evaluation_number_by_commodity_id($val['id']);
                $result['data'][$key]['pic'] = $this->Commodity_model->get_pic_by_commodity_id($val['id']);
            }
        }
        echo json_encode($result);
    }

    /**
     * 根据父类id查询该父类下所有子类
     */
    public function get_child_category_by_id($category_id)
    {
        $data = [
            'success' => FALSE,
            'msg' => '查询子类失败',
            'data' => null
        ];
        if (empty($category_id) || intval($category_id) < 0){
            return $data;
        }
        $child_category = $this->Category_model->get($category_id)['data'];
        if (!empty($child_category)){
            $data = [
                'success' => TRUE,
                'msg' => '查询子类成功',
                'data' => $child_category
            ];
        }

        echo json_encode($data);
    }
}