<?php
if (!defined('BASEPATH'))
 exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Category_admin.php
 *
 *   Description: 商品分类管理
 *
 *       Created: 2016-11-16 19:43:11
 *
 *        Author: zourui
 *
 * =========================================================
 */
 
class Category_admin extends CI_Controller {
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_db_helper']);
        $this->load->model(['Category_model', 'Common_model']);
    }

    /**
     * 分页获取数据
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function paginate($page = 1, $page_size = 10){
        $data = $this->Category_model->paginate($page, $page_size);
        $data['total_page'] = $this->jys_db_helper->get_total_page('category');

        echo json_encode($data);
    }

    /**
     * 添加分类
     * @return string 反馈信息
     */
    public function add()
    {
        //验证表单信息
        $this->form_validation->set_rules('parent_id', '父类ID', 'trim|required|numeric');
        $this->form_validation->set_rules('name', '分类名称', 'trim|required|min_length[1]|max_length[32]');
        $this->form_validation->set_rules('type_id', '类型ID', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $this->Common_model->deal_validation_errors();

        // 接收数据
        $data['parent_id'] = $this->input->post('parent_id', TRUE) ? intval($this->input->post('parent_id', TRUE)) : NULL ;
        $data['name']      = $this->input->post('name', TRUE);
        $data['type_id']   = $this->input->post('type_id', TRUE);

        $response = $this->jys_db_helper->add('category', $data);

        echo json_encode($response);
    }

    /**
     * 删除分类
     * @return string 反馈信息
     */
    public function delete()
    {
        $id = intval($this->input->post('id', TRUE));

        $commodity = $this->jys_db_helper->get_where('commodity', ['category_id'=>$id]);
        if (!empty($commodity) && is_array($commodity)) {
            $response['success'] = FALSE;
            $response['msg'] = '该分类被商品引用，无法删除'; 
            echo json_encode($response);
            exit;
        }

        if ($this->jys_db_helper->delete('category', $id)) {
            $response['success'] = TRUE;
            $response['msg'] = '删除成功';
        }else {
            $response['success'] = FALSE;
            $response['msg'] = '删除失败';
        }

        echo json_encode($response);
    }

    /**
     * 修改分类
     * @return string 反馈信息
     */
    public function update()
    {
        //验证表单信息
        $this->form_validation->set_rules('id', '分类ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('parent_id', '父类ID', 'trim|is_natural');
        $this->form_validation->set_rules('name', '分类名称', 'trim|required|min_length[1]|max_length[32]');
        $this->form_validation->set_rules('type_id', '类型ID', 'trim|required|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $this->Common_model->deal_validation_errors();

        // 接收数据
        $id                 = intval($this->input->post('id', TRUE));
        $data['parent_id']  = intval($this->input->post('parent_id', TRUE)) > 0 ? intval($this->input->post('parent_id', TRUE)) : NULL;
        $data['name']       = $this->input->post('name', TRUE);
        $data['type_id']    = $this->input->post('type_id', TRUE);

        if ($this->jys_db_helper->set_update('category', $id, $data)){
            $response['success'] = TRUE;
            $response['msg'] = '更新成功';
        }

        echo json_encode($response);
    }

    /**
     * 获取非父类对应父类分类
     * @return array 分类数组
     */
    public function get_categories()
    {
        // 获取父类分类
         $response = $this->Category_model->get_father_category();

        //获取父类的子类分类
         foreach($response['data'] as $key => $val){
            if(!empty($val) && !is_null($val) && $val != FALSE){
                $response['data'][$key]['cate'] = $this->Category_model->get_info_by_id($val['id']);
            }
        }

        echo json_encode($response);
    }

    /**
     * 获取非父类分类
     * @return string 分类数组
     */
    public function get()
    {
        $response = $this->Category_model->get();

        echo json_encode($response);
    }
    
    /**
     * 获取父类商品分类
     * @return string 分类数组
     */
    public function get_father_category()
    {
        $response = $this->Category_model->get_father_category();

        echo json_encode($response);
    }
    
    /**
     * 分页获取所有商品分类
     * @return string 分类数组
     */
    public function get_all_category()
    {
        
        $response = $this->Category_model->get_all_category();

        echo json_encode($response);
    }


}