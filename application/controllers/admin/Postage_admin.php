<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =========================================================
 *
 *      Filename: Postage_admin.php
 *
 *   Description: 邮费规则管理
 *
 *       Created: 2017-09-08 11:16:45
 *
 *        Author: zhangcl
 *
 * =========================================================
 */
class Postage_admin extends CI_Controller
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_db_helper']);
        $this->load->model(['Common_model', 'Postage_model']);
    }

    /**
     * 设置运费接口
     */
    public function set_postage()
    {
        $postage = $this->input->post('postage', TRUE);

        if (is_null(json_decode($postage)) || empty(json_decode($postage, TRUE))) {
            $data['success'] = FALSE;
            $data['msg'] = '请输入正确的邮费信息';
            echo json_decode($data);
            exit;
        }

        $postage = json_decode($postage, TRUE);
        $data = $this->Postage_model->set($postage);

        echo json_encode($data);
    }

    /**
     * 获取所有的运费
     */
    public function get_all()
    {
        $data = $this->jys_db_helper->all('freight');

        echo json_encode($data);
    }

    /**
     * 分页获取免邮规则
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function paginate($page = 1, $page_size = 10)
    {
        $data = $this->Postage_model->paginate($page, $page_size);

        echo json_encode($data);
    }

    /**
     * 添加包邮规则
     */
    public function add()
    {
        $data = array('success' => FALSE, 'msg' => '添加免邮规则失败');

        $this->form_validation->set_rules('name', '包邮名称', 'trim|required');
        $this->form_validation->set_rules('role_id', '角色', 'trim|required');
        $this->form_validation->set_rules('type', '券类型', 'trim|required|in_list[1,2]');   // 1为满金额包邮，2为满数量包邮
        $this->form_validation->set_rules('order_cost', '订单总金额', 'trim|numeric');
        $this->form_validation->set_rules('order_commodity_amount', '订单内商品数量', 'trim|is_natural');
        $this->form_validation->set_rules('commodity_scope', '可使用商品', 'trim|required|in_list[1,2,3]');
        $this->form_validation->set_rules('commodity_list', '商品列表', 'trim');
        $this->form_validation->set_rules('category_list', '分类列表', 'trim');
        $this->form_validation->set_rules('level_scope', '参加会员等级', 'trim|required|in_list[0,1]');
        $this->form_validation->set_rules('level_list', '会员等级列表', 'trim');
        $this->form_validation->set_rules('terminal_type_scope', '参加终端类型', 'trim|required|in_list[0,1]');
        $this->form_validation->set_rules('terminal_list', '终端类型列表', 'trim');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $result = $this->Common_model->deal_validation_errors();

        if ($result['success']) {
            $type = $this->input->post('type', TRUE);
            $new_info['name'] = $this->input->post('name', TRUE);
            $new_info['role_id'] = $this->input->post('role_id', TRUE);
            if (intval($type) == 1) {
                // 满金额包邮
                $new_info['order_cost'] = $this->input->post('order_cost', TRUE);
                $new_info['order_commodity_amount'] = 0;
            } else {
                // 满数量包邮
                $new_info['order_cost'] = 0;
                $new_info['order_commodity_amount'] = $this->input->post('order_commodity_amount', TRUE);
            }
            $new_info['commodity_scope'] = $this->input->post('commodity_scope', TRUE);
            switch (intval($new_info['commodity_scope'])) {
                case 1:
                    // 全部
                    $new_info['commodity_list'] = array();
                    $new_info['category_list'] = array();
                    break;
                case 2:
                    // 按分类
                    $new_info['commodity_list'] = array();
                    $new_info['category_list'] = json_decode($this->input->post('category_list', TRUE), TRUE);
                    break;
                case 3:
                    // 按商品
                    $new_info['commodity_list'] = json_decode($this->input->post('commodity_list', TRUE), TRUE);
                    $new_info['category_list'] = array();
                    break;
                default:
                    $data['msg'] = '可使用商品范围不正确';
                    echo json_encode($data);
                    exit;
                    break;
            }
            $new_info['level_scope'] = $this->input->post('level_scope', TRUE);
            if (intval($new_info['level_scope']) == 1) {
                // 所有用户等级可用
                $new_info['level_list'] = array();
            } else {
                // 部分用户等级可用
                $new_info['level_list'] = json_decode($this->input->post('level_list', TRUE), TRUE);
            }
            $new_info['terminal_type_scope'] = $this->input->post('terminal_type_scope', TRUE);
            if (intval($new_info['terminal_type_scope']) == 1) {
                // 所有用户等级可用
                $new_info['terminal_list'] = array();
            } else {
                // 部分用户等级可用
                $new_info['terminal_list'] = json_decode($this->input->post('terminal_list', TRUE), TRUE);
            }
            $new_info['status_id'] = 1; // 包邮规则状态：1为开启，0为关闭

            $data = $this->Postage_model->add($new_info);
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误，'.$result['msg'];
            $data['error'] = $result['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 修改包邮规则
     */
    public function update()
    {
        $data = array('success' => FALSE, 'msg' => '添加免邮规则失败');

        $this->form_validation->set_rules('id', 'ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('name', '包邮名称', 'trim|required');
        $this->form_validation->set_rules('role_id', '角色', 'trim|required');
        $this->form_validation->set_rules('type', '券类型', 'trim|required|in_list[1,2]');   // 1为满金额包邮，2为满数量包邮
        $this->form_validation->set_rules('order_cost', '订单总金额', 'trim|numeric');
        $this->form_validation->set_rules('order_commodity_amount', '订单内商品数量', 'trim|is_natural');
        $this->form_validation->set_rules('commodity_scope', '可使用商品', 'trim|required|in_list[1,2,3]');
        $this->form_validation->set_rules('commodity_list', '商品列表', 'trim');
        $this->form_validation->set_rules('category_list', '分类列表', 'trim');
        $this->form_validation->set_rules('level_scope', '参加会员等级', 'trim|required|in_list[0,1]');
        $this->form_validation->set_rules('level_list', '会员等级列表', 'trim');
        $this->form_validation->set_rules('terminal_type_scope', '参加终端类型', 'trim|required|in_list[0,1]');
        $this->form_validation->set_rules('terminal_list', '终端类型列表', 'trim');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $result = $this->Common_model->deal_validation_errors();

        if ($result['success']) {
            $id = $this->input->post('id', TRUE);
            $type = $this->input->post('type', TRUE);
            $new_info['name'] = $this->input->post('name', TRUE);
            $new_info['role_id'] = $this->input->post('role_id', TRUE);
            if (intval($type) == 1) {
                // 满金额包邮
                $new_info['order_cost'] = $this->input->post('order_cost', TRUE);
                $new_info['order_commodity_amount'] = 0;
            } else {
                // 满数量包邮
                $new_info['order_cost'] = 0;
                $new_info['order_commodity_amount'] = $this->input->post('order_commodity_amount', TRUE);
            }
            $new_info['commodity_scope'] = $this->input->post('commodity_scope', TRUE);
            switch (intval($new_info['commodity_scope'])) {
                case 1:
                    // 全部
                    $new_info['commodity_list'] = array();
                    $new_info['category_list'] = array();
                    break;
                case 2:
                    // 按分类
                    $new_info['commodity_list'] = array();
                    $new_info['category_list'] = json_decode($this->input->post('category_list', TRUE), TRUE);
                    break;
                case 3:
                    // 按商品
                    $new_info['commodity_list'] = json_decode($this->input->post('commodity_list', TRUE), TRUE);
                    $new_info['category_list'] = array();
                    break;
                default:
                    $data['msg'] = '可使用商品范围不正确';
                    echo json_encode($data);
                    exit;
                    break;
            }
            $new_info['level_scope'] = $this->input->post('level_scope', TRUE);
            if (intval($new_info['level_scope']) == 1) {
                // 所有用户等级可用
                $new_info['level_list'] = array();
            } else {
                // 部分用户等级可用
                $new_info['level_list'] = json_decode($this->input->post('level_list', TRUE), TRUE);
            }
            $new_info['terminal_type_scope'] = $this->input->post('terminal_type_scope', TRUE);
            if (intval($new_info['terminal_type_scope']) == 1) {
                // 所有用户等级可用
                $new_info['terminal_list'] = array();
            } else {
                // 部分用户等级可用
                $new_info['terminal_list'] = json_decode($this->input->post('terminal_list', TRUE), TRUE);
            }

            $data = $this->Postage_model->update($id, $new_info);
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $result['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除包邮规则
     */
    public function delete()
    {
        $this->form_validation->set_rules('id', 'id', 'trim|required');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $result = $this->Common_model->deal_validation_errors();

        if ($result['success']) {
            $id = $this->input->post('id', TRUE);
            $data = $this->Postage_model->delete($id);
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误，' . $result['msg'];
            $data['error'] = $result['msg'];
        }
        echo json_encode($data);
    }
}
