<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Commodity_admin.php
 *
 *   Description: 商品管理
 *
 *       Created: 2016-11-21 10:49:41
 *
 *        Author: sunzuosheng
 *
 * =========================================================
 */

class Commodity_admin extends CI_Controller{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_db_helper']);
        $this->load->model(['Commodity_model', 'Common_model', 'User_model']);
    }

    /**
     * 分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function paginate($page = 1, $page_size = 10){
        $keyword = $this->input->post('keyword', TRUE);
        $is_point = $this->input->post('is_point', TRUE) ? intval($this->input->post('is_point', TRUE)) : 0;

        $data = $this->Commodity_model->admin_paginate($page, $page_size, array('commodity.is_point' => $is_point), $keyword);

        echo json_encode($data);
    }

    /**
     * 代理商分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function agent_paginate($page = 1, $page_size = 10){
        $keyword = $this->input->post('keywords', TRUE);
        $is_point = $this->input->post('is_point', TRUE) ? intval($this->input->post('is_point', TRUE)) : 0;

        $data = $this->Commodity_model->agent_paginate($page, $page_size, $keyword, $is_point);

        echo json_encode($data);
    }

    /**
     * 分页获取商品不同的规格
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param int $commodity_id 商品Id
     */
    public function paginate_for_specification($page = 1, $page_size = 10, $commodity_id = 0){
        $keyword = $this->input->post('keyword') ? $this->input->post('keyword', TRUE) : NULL;

        $data = $this->Commodity_model->paginate_for_specification($page, $page_size, array(), $keyword, $commodity_id);
        echo json_encode($data);
    }

    /**
     * 添加商品规格
     */
    public function add_commodity_specification()
    {
        //验证表单信息
        $this->form_validation->set_rules('id', '商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('packagetype_name', '规格名称', 'trim|required|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('market_price', '市场价格', 'trim|required|numeric');
        $this->form_validation->set_rules('selling_price', '销售价格', 'trim|required|numeric');
        $this->form_validation->set_rules('status_id', '规格状态', 'trim|required|in_list[0,1,2]');

        $this->form_validation->set_rules('packagetype', '包装类型', 'trim|required|in_list[1,2]');
        $this->form_validation->set_rules('points', '购买所得积分', 'trim|is_natural');
        $this->form_validation->set_rules('goodsunit', '基本单位', 'trim|required');
        $this->form_validation->set_rules('attachment_ids', '图片ID', 'trim');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $post['commodity_id']           = intval($this->input->post('id', TRUE));
            $post['name']                   = $this->input->post('packagetype_name', TRUE);
            $post['market_price']           = round(floatval($this->input->post('market_price', TRUE)), 2);
            $post['selling_price']          = round(floatval($this->input->post('selling_price', TRUE)), 2);
            $post['status_id']              = intval($this->input->post('status_id', TRUE));
            $post['packagetype']            = intval($this->input->post('packagetype', TRUE));
            $post['points']                 = $this->input->post('points') ? $this->input->post('points', TRUE) : NULL;
            $post['goodsunit']              = $this->input->post('goodsunit', TRUE);

            $commodity_info = $this->jys_db_helper->get('commodity', $post['commodity_id']);
            if (empty($commodity_info) || $commodity_info['type_id'] == 1) {
                $data['success'] = FALSE;
                $data['msg'] = '添加失败，基因商品只能通过ERP系统添加';
                echo json_encode($data);
                exit;
            }

            if ($this->check_name_is_exists($post['name'], $post['commodity_id'])){
                $data['success'] = FALSE;
                $data['msg'] = '该商品商品规格已存在';
            }else{
                $post['create_time'] = date('Y-m-d H:i:s');
                $post['update_time'] = $post['create_time'];

                //缩略图
                if (!empty($this->input->post('attachment_ids', TRUE))) {
                    $attachment_ids = explode(',', $this->input->post('attachment_ids', TRUE));
                }else {
                    $attachment_ids = array();
                }
                $data = $this->Commodity_model->add_specification($post, $attachment_ids);
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误，'.$res['msg'];
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 检验商品规格名字是否存在
     * @param null $name 商品规格名字
     * @param null $commodity_id 商品id
     * @return bool
     */
    public function check_name_is_exists($name = NULL, $commodity_id = NULL){
        if (empty($number) || empty($commodity_id)){
            return FALSE;
        }

        if ($this->jys_db_helper->get_total_num('commodity_specification', ['name'=>$name, 'commodity_id' => $commodity_id])){
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 修改商品规格
     */
    public function update_commodity_specification()
    {
        $data = array('success'=>FALSE, 'msg'=>'更新商品规格信息失败');
        //验证表单信息
        $this->form_validation->set_rules('id', '规格ID', 'trim|required|numeric');
        $this->form_validation->set_rules('commodity_id', '商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('selling_price', '销售价格', 'trim|required|numeric');
        $this->form_validation->set_rules('status_id', '规格状态', 'trim|required|numeric');

        $this->form_validation->set_rules('market_price', '市场价格', 'trim|required|numeric');
        $this->form_validation->set_rules('packagetype', '包装类型', 'trim|required|in_list[1,2]');
        $this->form_validation->set_rules('points', '购买所得积分', 'trim|is_natural');
        $this->form_validation->set_rules('goodsunit', '基本单位', 'trim|required');
        $this->form_validation->set_rules('name', '规格名称', 'trim|required|min_length[1]|max_length[100]');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $id          = intval($this->input->post('id', TRUE));

            if (!empty($commodity_info) && $commodity_info['type_id'] != 1) {
                $post['packagetype']            = intval($this->input->post('packagetype', TRUE));
                $post['goodsunit']              = $this->input->post('goodsunit', TRUE);
            }
            $post['name']                   = $this->input->post('name', TRUE);
            $post['market_price']           = round(floatval($this->input->post('market_price', TRUE)), 2);
            $post['selling_price']          = round(floatval($this->input->post('selling_price', TRUE)), 2);
            $post['status_id']              = intval($this->input->post('status_id', TRUE));
            $post['commodity_id']              = intval($this->input->post('commodity_id', TRUE));
            $post['points']                 = $this->input->post('points') ? $this->input->post('points', TRUE) : NULL;
            //缩略图
            $attachment_ids = $this->input->post('attachment_ids', TRUE) ? explode(',', $this->input->post('attachment_ids', TRUE)) : NULL;
            if (round(floatval($post['selling_price']), 2) < 0.01 && $post['status_id'] == jys_system_code::COMMODITY_SPECIFICATION_STATUS_ENABLED) {
                $data['msg'] = '当前商品的价格不支持上架操作';
            }else {
                $data = $this->Commodity_model->update_commodity_specification($id, $post, $attachment_ids);
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误，'.$res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除商品规格，基因商品无法删除，规格对应有订单的软删除
     */
    public function delete_commodity_specification()
    {
        $data = array('success' => FALSE, 'msg' => '删除规格失败');
        $specification_id = $this->input->post('specification_id');

        //获取商品信息
        $specification = $this->Commodity_model->get_commodity_list_by_condition(array('commodity_specification.id' => $specification_id), FALSE);
        if (!empty($specification)) {
            if ($specification['type_id'] == jys_system_code::COMMODITY_TYPE_GENE) {
                $data['msg'] = '基因商品无法删除规格';
                echo json_encode($data);
                exit;
            }

            //查询订单，有订单时软删除，无订单时直接删除
            $order = $this->jys_db_helper->get_where('order_commodity', array('commodity_specification_id' => $specification_id));
            if (!empty($order)) {
                $this->jys_db_helper->update('commodity_specification', $specification_id, array('status_id' => Jys_system_code::COMMODITY_SPECIFICATION_STATUS_DELETED));
            } else {
                $this->jys_db_helper->delete('commodity_specification', $specification_id);
            }

            $data['success'] = TRUE;
            $data['msg'] = '删除成功';
        } else {
            $data['msg'] = '无法删除规格，该规格未找到';
        }

        echo json_encode($data);
    }

    /**
     * 添加商品规格对应的检测模版
     */
    public function add_commodity_specification_template()
    {
        $this->form_validation->set_rules('specification_id', '商品规格ID', 'trim|required|integer');
        $this->form_validation->set_rules('template_id', '模板ID', 'trim|required|integer');
        $this->form_validation->set_rules('project_num', '检测项目数量', 'trim|required|integer');
        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            $input['specification_id'] = $this->input->post('specification_id', TRUE);
            $input['template_id']  = $this->input->post('template_id', TRUE);
            $input['project_num']  = $this->input->post('project_num');
            $input['create_at']  = date('Y-m-d H:i:s');
            $input['update_at']  = $input['create_at'];

            $commodity_info = $this->Commodity_model->get_commodity_list_by_condition(array('commodity_specification.id' => $input['specification_id']));
            if (empty($commodity_info) || intval($commodity_info[0]['id']) < 1) {
                $data['success'] = FALSE;
                $data['msg'] = '您所选择的规格信息不正确，添加检测模版失败';
                echo json_encode($data);
                exit;
            }
            $input['commodity_id'] = intval($commodity_info[0]['id']);

            if (!empty($this->jys_db_helper->get_where('commodity_specification_template', ['specification_id' => $input['specification_id'], 'template_id' => $input['template_id']]))) {
                $data['success'] = FALSE;
                $data['msg'] = '不能重复添加规格模板';
            } else {
                $data = $this->jys_db_helper->add('commodity_specification_template', $input);
            }
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入信息错误'.$res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除商品规格模板
     * @param int $id
     */
    public function delete_commodity_specification_template($id = 0)
    {
        if (empty($id) || intval($id) < 0) {
            echo json_encode(['success' => FALSE, 'msg' => '参数错误']);
            exit;
        }

        if ($this->jys_db_helper->delete('commodity_specification_template', $id)) {
            $data = ['success' => TRUE, 'msg' => '删除规格模板成功'];
        } else {
            $data = ['success' => TRUE, 'msg' => '删除规格模板失败'];
        }

        echo json_encode($data);
    }

    /**
     * 分页获取商品规格模板
     * @param int $page
     * @param int $page_size
     */
    public function paginate_commodity_specification_template($page = 1, $page_size = 10)
    {
        $id = $this->input->post('specification_id', TRUE);
        $data = $this->Commodity_model->paginate_commodity_specification_template($id, $page, $page_size);
        if ($data['success']) {
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key]['name'] = $data['data'][$key]['name'].'(已选'.$data['data'][$key]['project_num'].'项)';
            }
        }

        echo json_encode($data);
    }

    /**
     * 根据商品ID和规格Id获取商品剩余数据(缩略图)
     */
    public function show_specification_thumbnail(){
        $commodity_id = intval($this->input->post('commodity_id', TRUE));
        $commodity_specification_id = intval($this->input->post('commodity_specification_id', TRUE));

        $data = $this->Commodity_model->show_thumbnail($commodity_id, $commodity_specification_id);
        echo json_encode($data);
    }

    /**
     * 根据商品ID和规格Id获取商品剩余数据(缩略图)
     */
    public function get_specification_thumbnail(){
        $commodity_id = intval($this->input->post('commodity_id', TRUE));
        $commodity_specification_id = intval($this->input->post('commodity_specification_id', TRUE));

        $data = $this->Commodity_model->get_specification_thumbnail($commodity_id, $commodity_specification_id);
        echo json_encode($data);
    }

    /**
     * 获取全部现金商品或全部积分商品
     */
    public function get_all_commodity_by_is_point() {
        $is_point = $this->input->post('is_point', TRUE) ? intval($this->input->post('is_point', TRUE)) : 0;
        $result = array('success'=>FALSE, 'msg'=>'获取商品列表失败', 'data'=>array());

        $data = $this->Commodity_model->get_commodity_list_by_condition(['commodity.is_point'=>$is_point, 'commodity.status_id !='=>jys_system_code::COMMODITY_STATUS_DELETE]);

        if (!empty($data) && is_array($data)) {
            $commodity_list = array();
            foreach ($data as $item) {
                if (isset($item['category_name']) && !empty($item['category_name'])) {
                    $commodity_list[$item['category_name']][] = $item;
                }
            }
            if (!empty($commodity_list)) {
                $result['success'] = TRUE;
                $result['msg'] = '获取商品列表成功';
                $result['data'] = $commodity_list;
            }
        }

        echo json_encode($result);
    }

    /**
     * 添加商品
     */
    public function add()
    {
        //验证表单信息
        $this->form_validation->set_rules('name', '商品名称', 'trim|required|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('number', '商品编号', 'trim|required|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('agent_id', '供应商id', 'trim|numeric');
        $this->form_validation->set_rules('category_id', '商品分类ID', 'trim|required|numeric');
        $this->form_validation->set_rules('introduce', '商品介绍', 'trim|max_length[200]');
        $this->form_validation->set_rules('detail', '商品详情', 'trim|required|max_length[65535]');
        $this->form_validation->set_rules('recommend_commodity', '推荐商品ID', 'trim|numeric');
        $this->form_validation->set_rules('type_id', '商品类型', 'trim|required|numeric');
        $this->form_validation->set_rules('is_point', '是否积分商品', 'trim|in_list[0,1]');
        $this->form_validation->set_rules('level_id', '等级ID', 'trim|numeric');


        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $post['name']                   = $this->input->post('name', TRUE);
            $post['number']                 = $this->input->post('number', TRUE);
            $post['category_id']            = intval($this->input->post('category_id', TRUE));
            $post['introduce']              = htmlspecialchars($this->input->post('introduce', FALSE));
            $post['detail']                 = htmlspecialchars($this->input->post('detail', FALSE));
            $post['recommend_commodity']    = $this->input->post('recommend_commodity', TRUE) ? intval($this->input->post('recommend_commodity', TRUE)) : NULL;
            $post['type_id']                = intval($this->input->post('type_id', TRUE));
            $is_point                       = intval($this->input->post('is_point', TRUE));
            $level_id                       = intval($this->input->post('level_id', TRUE));

            if ($this->Commodity_model->check_number_is_exists($post['number'])){
                $data['success'] = FALSE;
                $data['msg'] = '商品编号已存在';
            }else{
                if ($is_point >= 0 && $is_point <= 1) {
                    $post['is_point'] = $is_point;
                }else {
                    $post['is_point'] = 0;
                }

                if (!empty($level_id)){
                    $post['level_id'] = $level_id;
                }

                $post['create_time'] = date('Y-m-d H:i:s');

                //缩略图
                // $attachment_ids = explode(',', $this->input->post('attachment_ids', TRUE));
                $data = $this->Commodity_model->add($post);
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 修改商品
     */
    public function update()
    {
        //验证表单信息
        $this->form_validation->set_rules('id', '商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('name', '商品名称', 'trim|required|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('number', '商品编号', 'trim|required|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('agent_id', '供应商id', 'trim|numeric');
        $this->form_validation->set_rules('category_id', '商品分类ID', 'trim|required|numeric');
        $this->form_validation->set_rules('introduce', '商品介绍', 'trim|max_length[200]');
        $this->form_validation->set_rules('detail', '商品详情', 'trim|required|max_length[65535]');
        $this->form_validation->set_rules('recommend_commodity', '推荐商品ID', 'trim|numeric');
        $this->form_validation->set_rules('type_id', '商品类型', 'trim|required|numeric');
        $this->form_validation->set_rules('level_id', '等级ID', 'trim|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $id                             = intval($this->input->post('id', TRUE));
            $post['name']                   = $this->input->post('name', TRUE);
            $post['number']                 = $this->input->post('number', TRUE);
            $post['category_id']            = intval($this->input->post('category_id', TRUE));
            $post['introduce']              = htmlspecialchars($this->input->post('introduce', FALSE));
            $post['detail']                 = htmlspecialchars($this->input->post('detail', FALSE));
            $post['recommend_commodity']    = $this->input->post('recommend_commodity', TRUE) ? intval($this->input->post('recommend_commodity', TRUE)) : NULL;
            $post['type_id']                = intval($this->input->post('type_id', TRUE));
            $post['update_time']            = date('Y-m-d H:i:s');
            $level_id                       = intval($this->input->post('level_id', TRUE));

            if (!empty($level_id)){
                $post['level_id'] = $level_id;
            }

            //缩略图
            // $attachment_ids = $this->input->post('attachment_ids', TRUE) ? explode(',', $this->input->post('attachment_ids', TRUE)) : NULL;

            $data = $this->Commodity_model->update($id, $post);
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除商品(软删除，商品下架)
     */
    public function delete()
    {
        $id = intval($this->input->post('id', true));
        $specifications = $this->jys_db_helper->get_where('commodity_specification', array('commodity_id' => $id));
        if (!empty($specifications)) {
            echo json_encode(array('success' => FALSE, 'msg' => '该商品下有规格，无法直接删除'));
            exit;
        }

        $modify['status_id'] = jys_system_code::COMMODITY_STATUS_DELETE;

        $data = $this->jys_db_helper->soft_delete('commodity', ['id' => $id], $modify);

        echo json_encode($data);
    }

    /**
     * 根据商品ID获取商品剩余数据(缩略图)
     */
    public function show_thumbnail(){
        $commodity_id = intval($this->input->post('commodity_id', TRUE));

        $data = $this->Commodity_model->show_thumbnail($commodity_id);

        echo json_encode($data);
    }

    /**
     * 删除商品缩略图
     */
    public function delete_thumbnail(){
        $id = $this->input->post('id', TRUE);

        $data = $this->jys_db_helper->delete('commodity_thumbnail', $id);
        if ($data) {
            echo json_encode(array('success' => TRUE, 'msg' => '删除图片成功'));
        } else {
            echo json_encode(array('success' => FALSE, 'msg' => '删除图片失败'));
        }
    }

    /**
     * 评价分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function evaluation_paginate($page = 1, $page_size = 10, $commodity_id = 0, $commodity_specification_id = 0){
        $data = $this->Commodity_model->evaluation_paginate($page, $page_size, $commodity_id, 0, NULL, $commodity_specification_id);
        $condition = array('commodity_id'=>$commodity_id);
        if (intval($commodity_specification_id) > 0) {
            $condition['commodity_specification_id'] = $commodity_specification_id;
        }
        $data['total'] = $this->jys_db_helper->get_total_num('commodity_evaluation', $condition);
        $data['total_page'] = $this->jys_db_helper->get_total_page('commodity_evaluation', $page_size, $condition);

        echo json_encode($data);
    }

    /**
     * 商品评价审核
     */
    public  function review_evaluation(){
        $id = $this->input->post('id', TRUE);
        $result = array('success' => FALSE, 'msg' => '商品评论审核还未通过');
        $data['reply_time'] = date("Y-m-d H:i:s");
        $data['reply_content'] = $this->input->post('replyContent',TRUE);
        $data['status']=1;
            if ($this->jys_db_helper->update('commodity_evaluation', $id, $data)) {
                $result['success'] = TRUE;
                $result['msg'] = '商品评论审核通过';
            }
            echo json_encode($result);
        }

    /**
     * 删除商品评价失败
     */
    public function delete_evaluation() {
        $id = $this->input->post('id', TRUE);
        $result = array('success'=>FALSE, 'msg'=>'删除商品评价失败');

        if (intval($id) < 1) {
            $result['msg'] = '请选择要删除的商品评价';
            echo json_encode($result);
        }

            if ($this->jys_db_helper->delete('commodity_evaluation', intval($id))) {
                $result['success'] = TRUE;
                $result['msg'] = '删除商品评价成功';
        }

        echo json_encode($result);
    }

    /**
     * 推荐商品分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function recommend_paginate($page = 1, $page_size = 10){
        $agent_id = $this->input->post('agent_id', TRUE);
        $type_id = $this->input->post('type_id', TRUE);
        $keywords = $this->input->post('keywords', TRUE);

        $data = $this->Commodity_model->recommend_paginate($page, $page_size, $agent_id, $type_id, $keywords);

        echo json_encode($data);
    }

    /**
     * 获取推荐商品
     */
    public function get_recommend(){
        $data = $this->Commodity_model->get_recommend();

        echo json_encode($data);
    }

    /**
     * 添加热卖或热换商品
     */
    public function add_recommend(){
        //验证表单信息
        $this->form_validation->set_rules('commodity_id', '商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('commodity_specification_id', '规格ID', 'trim|required|numeric');
        $this->form_validation->set_rules('start_time', '开始时间', 'trim|required');
        $this->form_validation->set_rules('end_time', '结束时间', 'trim|required');
        $this->form_validation->set_rules('type_id', '类型', 'trim|required|in_list[1,2]');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $commodity_id = intval($this->input->post('commodity_id', TRUE));
            $commodity_specification_id = intval($this->input->post('commodity_specification_id', TRUE));
            $start_time     = $this->input->post('start_time', TRUE);
            $end_time = $this->input->post('end_time', TRUE);
            if ($start_time >= $end_time){
                $data['success'] = FALSE;
                $data['msg'] = '结束时间大于开始时间，无法添加';
                echo json_encode($data);exit;
            }
            $type_id = intval($this->input->post('type_id', TRUE));
            $data = $this->Commodity_model->add_recommend($commodity_id, $start_time, $end_time, $type_id, $commodity_specification_id);

        }else{
            $data['success'] = FALSE;
            $data['msg'] = '参数错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 更新热卖或热换商品
     */
    public function update_recommend(){
        //验证表单信息
        $this->form_validation->set_rules('id', '推荐商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('commodity_id', '商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('start_time', '开始时间', 'trim|required');
        $this->form_validation->set_rules('end_time', '结束时间', 'trim|required');
        $this->form_validation->set_rules('type_id', '类型', 'trim|required');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $id                     = intval($this->input->post('id', TRUE));
            $post['commodity_id']   = intval($this->input->post('commodity_id', TRUE));
            $post['start_time']     = $this->input->post('start_time', TRUE);
            $post['end_time']       = $this->input->post('end_time', TRUE);
            $post['type_id']        = intval($this->input->post('type_id', TRUE));

            if ($post['start_time'] >= $post['end_time']){
                $data['success'] = FALSE;
                $data['msg'] = '结束时间大于开始时间，无法添加';
                echo json_encode($data);exit;
            }
            if ($this->jys_db_helper->update('recommend_commodity', $id, $post)){
                $data['success'] = TRUE;
                $data['msg'] = '更新成功';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除添加热卖或热换商品
     */
    public function delete_recommend(){
        $id = $this->input->post('id', TRUE);

        if ($this->jys_db_helper->delete('recommend_commodity', $id)){
            $data['success'] = TRUE;
            $data['msg'] = '删除成功';
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '删除失败';
        }

        echo json_encode($data);
    }

    /**
     * 限时折扣商品分页
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function flash_sale_paginate($page = 1, $page_size = 10){
        $agent_id = $this->input->post('agent_id', TRUE);
        $data = $this->Commodity_model->flash_sale_paginate($page, $page_size, $agent_id);

        echo json_encode($data);
    }

    /**
     * 添加限时折扣商品
     */
    public function add_flash_sale(){
        //验证表单信息
        $this->form_validation->set_rules('commodity_id', '商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('commodity_specification_id', '商品规格ID', 'trim|required|numeric');
        $this->form_validation->set_rules('price', '商品价格', 'trim|required|numeric');
        $this->form_validation->set_rules('start_time', '开始时间', 'trim|required');
        $this->form_validation->set_rules('end_time', '结束时间', 'trim|required');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $post['commodity_id']   = intval($this->input->post('commodity_id', TRUE));
            $post['commodity_specification_id']   = intval($this->input->post('commodity_specification_id', TRUE));
            $post['price']          = floatval($this->input->post('price', TRUE));
            $post['start_time']     = $this->input->post('start_time', TRUE);
            $post['end_time']       = $this->input->post('end_time', TRUE);
            $post['create_time']    = date('Y-m-d H:i:s');

            //判断是否重复添加
            $condition = array(
                'commodity_specification_id' => $post['commodity_specification_id'],
                'start_time <=' => date('Y-m-d H:i:s'),
                'end_time >=' => date('Y-m-d H:i:s')
            );
            $flash_sale = $this->jys_db_helper->get_where('flash_sale', $condition);
            if (!empty($flash_sale)) {
                echo json_encode(array('success' => FALSE, 'msg' => '添加失败，当前规格商品已添加，请勿重复添加'));
                exit;
            }

            $data = $this->jys_db_helper->add('flash_sale', $post);
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误'.$res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 更新限时折扣商品
     */
    public function update_flash_sale(){
        //验证表单信息
        $this->form_validation->set_rules('id', '限时折扣商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('commodity_id', '商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('price', '商品价格', 'trim|required|numeric');
        $this->form_validation->set_rules('start_time', '开始时间', 'trim|required');
        $this->form_validation->set_rules('end_time', '结束时间', 'trim|required');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $id                     = intval($this->input->post('id', TRUE));
            $post['commodity_id']   = intval($this->input->post('commodity_id', TRUE));
            $post['price']          = floatval($this->input->post('price', TRUE));
            $post['start_time']     = $this->input->post('start_time', TRUE);
            $post['end_time']       = $this->input->post('end_time', TRUE);

            if ($this->jys_db_helper->update('flash_sale', $id, $post)){
                $data['success'] = TRUE;
                $data['msg'] = '更新成功';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除限制折扣商品
     */
    public function delete_flash_sale(){
        $id = $this->input->post('id', TRUE);

        $data = $this->jys_db_helper->delete('flash_sale', $id);

        echo json_encode($data);
    }

    /**
     * 添加商品推荐商品
     */
    public function add_commodity_recommend_commodity()
    {
        $data = array('success' => FALSE, 'msg' => '添加推荐商品失败');

        $this->form_validation->set_rules('commodity_id', '商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('specification_id', '规格ID', 'trim|required|numeric');
        $this->form_validation->set_rules('recommend_commodity_id', '推荐商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('recommend_specification_id', '推荐商品规格ID', 'trim|required|numeric');

        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']){
            $post['commodity_id'] = $this->input->POST('commodity_id', TRUE);
            $post['specification_id'] = $this->input->POST('specification_id', TRUE);
            $post['recommend_commodity_id'] = $this->input->POST('recommend_commodity_id', TRUE);
            $post['recommend_specification_id'] = $this->input->POST('recommend_specification_id', TRUE);
            $post['create_time'] = date('Y-m-d H:i:s');

            $condition = array(
                'commodity_id' => $post['commodity_id'],
                'specification_id' => $post['specification_id'],
                'recommend_commodity_id' => $post['recommend_commodity_id'],
                'recommend_specification_id' => $post['recommend_specification_id']
            );
            if (!$this->jys_db_helper->get_where('managing_suggestions', $condition)) {
                if ($this->jys_db_helper->add('managing_suggestions', $post)) {
                    $data['success'] = TRUE;
                    $data['msg'] = '添加商品推荐商品成功';
                }
            } else {
                $data['msg'] = '该商品已经添加为推荐商品，请勿重复添加';
            }
        }else{
            $data['msg'] = $data['msg'].$res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 获取商品推荐商品
     */
    public function get_commodity_recommend_commodity()
    {
        $specification_id = $this->input->post('specification_id', TRUE);
        $data = $this->Commodity_model->get_commodity_recommend_commodity($specification_id);

        echo json_encode($data);
    }

    /**
     * 删除商品推荐商品
     */
    public function delete_commodity_recommend_commodity()
    {
        $id = $this->input->POST('id', TRUE);

        if ($this->jys_db_helper->delete('managing_suggestions', $id)){
            $data['success'] = TRUE;
            $data['msg'] = '删除商品推荐商品成功';
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '删除商品推荐商品失败';
        }

        echo json_encode($data);
    }

    /**
     * 添加一个商品到代理商
     */
    public function add_commodity_to_agent()
    {
        $this->form_validation->set_rules('commodity_id', '商品ID', 'trim|required|numeric');
        $this->form_validation->set_rules('agent_id', '代理商ID', 'trim|required|numeric');
        
        $res = $this->Common_model->deal_validation_errors();
        if ($res['success'])
        {
            $commodity_id = $this->input->post('commodity_id', TRUE);
            $agent_id = $this->input->post('agent_id', TRUE);
            $data = $this->Commodity_model->add_commodity_to_agent($commodity_id, $agent_id);
        }
        else
        {
            $data['success'] = FALSE;
            $data['msg'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除代理商商品
     */
    public function delete_commodity_from_agent()
    {
        $id = $this->input->POST('id', TRUE);

        if (empty($id))
        {
            $data['success'] = FALSE;
            $data['msg'] = '删除代理商品失败,id is null';
        }
        else
        {
            $data = $this->Commodity_model->remove_commodity_from_agent_by_id($id);
        }

        echo json_encode($data);
    }

    /**
     * 获取商品所有的代理商
     */
    public function get_agnets_for_commodity()
    {
        $commodity_id = $this->input->post('commodity_id', TRUE);

        if ( empty($commodity_id))
        {
            $data = ['success' => FALSE, 
                     'msg' => '获取代理商失败', 
                     'data' => NULL,];
        }
        else
        {
            $agents = $this->User_model->get_agents_detail_by_condition(['role_id' => Jys_system_code::ROLE_AGENT],
                                                                        FALSE, FALSE, $commodity_id);
             if ($agents)
             {
                $data = ['success' => TRUE, 
                         'msg' => '获取代理商成功', 
                         'data' => $agents];
            }
            else
            {
                $data = ['success' => FALSE, 
                         'msg' => '获取代理商失败', 
                         'data' => NULL,];
            }
        }

        echo json_encode($data);
    }

    public function upload_commodity_specification_attachment(){
        $data = array('success' => FALSE, 'msg' => '上传失败');
        
        $id = $this->input->post('id');
        $attachment = $this->input->post('attachment_id');
        $update = ['attachment' => $attachment];

        $result = $this->jys_db_helper->update('commodity_specification', $id, $update);
        if ($result) {
            $data = ['success' => TRUE, 'msg' => '上传成功'];
        }

        echo json_encode($data);
    }
}