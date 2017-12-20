<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =========================================================
 *
 *      Filename: Report_model.php
 *
 *   Description: 查询报告管理
 *
 *       Created: 2016-11-24 22:14:23
 *
 *        Author: zourui
 *
 * =========================================================
 */
Class Report_model extends CI_Model
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['Jys_db_helper', 'Jys_soap', 'jys_tool', 'Jys_message']);
    }

    /**
     * 根据用户身份证号后六位和手机号查询报告
     * @param null $identity_card 身份证号后六位
     * @param null $phone 手机号
     */
    public function get_report_by_condition($identity_card = NULL, $phone = NULL)
    {
        $data['success'] = FALSE;
        $data['msg'] = '没有数据';
        $data['data'] = null;

        if (empty($phone)) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('order_commodity.id,
                           order_commodity.number as order_commodity_number,
                           order_commodity.commodity_id,
                           order_commodity.total_price,
                           order_commodity.amount,
                           commodity_specification.selling_price as price,
                           commodity_specification.name as commodity_specification_name,
                           erp_commodity_specification.name as erp_commodity_specification_name,
                           report.name,
                           report.age,
                           report.gender,
                           report.number,
                           report.update_time,
                           detection_template.name as template_name,
                           attachment.id as attachment_id,
                           attachment.path');
        $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = order_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity_specification as erp_commodity_specification', 'erp_commodity_specification.erp_commodity_id = order_commodity.erp_commodity_id', 'left');
        $this->db->join('attachment', 'attachment.id = report.attachment_id', 'left');
        $this->db->join('order_commodity_template', 'order_commodity_template.order_commodity_id = order_commodity.id', 'left');
        $this->db->join('detection_template', 'detection_template.id = order_commodity_template.template_id', 'left');
        $this->db->where('report.phone', $phone);
        if (isset($identity_card) && !empty($identity_card)) {
            $this->db->like('report.identity_card', $identity_card, 'before');
        }
        $this->db->order_by('report.create_time', 'DESC');
        $this->db->group_by('report.id');
        $result = $this->db->get('report');

        if ($result && $result->num_rows() > 0) {
            $data['success'] = TRUE;
            $data['msg'] = '查询到报告数据';
            $data['data'] = $result->result_array();
        }

        return $data;
    }


    /**
     * 根据attachment_id获取附件路径
     * @param int $id 报告Id
     * @return string || bool
     */
    public function get_path_by_report_id($id = 1)
    {
        if (empty($id)) {
            return FALSE;
        }

        $this->db->select('attachment.path');
        $this->db->where('report.id', $id);
        $this->db->join('attachment', 'report.attachment_id = attachment.id', 'left');
        $result = $this->db->get('report');
        if ($result && $result->num_rows() > 0) {
            return $result->row_array();
        } else {
            return FALSE;
        }
    }

    /**
     * 获取当前用户的检测报告
     */
    public function get_report_by_user_id($user_id)
    {
        $data['success'] = FALSE;
        $data['msg'] = '没有数据';

        if (empty($user_id)) {
            $data['msg'] = '用户不存在';
            return $data;
        }
        $this->db->select('order_commodity.id,
                           order_commodity.number as order_commodity_number,
                           order_commodity.commodity_id,
                           commodity.name as commodity_name,
                           commodity_specification.selling_price,
                           commodity_specification.name as commodity_specification_name,
                           commodity.number,
                           order_commodity.total_price,
                           order_commodity.amount,
                           report.name,
                           report.age,
                           report.phone,
                           report.number,
                           report.identity_card,
                           report.gender,
                           report.update_time,
                           commodity.category_id,
                           category.name as category_name,
                           category.parent_id,
                           attachment.id as attachment_id,
                           attachment.path,
                           commodity_center.name as commodity_center_name,
                           package_type.name as package_type_name');
        $this->db->where('order.user_id', $user_id);
        $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
        $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = order_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype and package_type.type = "' . jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE . '"', 'left');
        $this->db->join('category', 'commodity.category_id = category.id and category.id = category.parent_id', 'left');
        $this->db->join('attachment', 'attachment.id = report.attachment_id', 'left');
        $this->db->where('attachment_id >', 0);
        $this->db->order_by('report.create_time', 'DESC');
        $result = $this->db->get('report');

        if ($result && $result->num_rows() > 0) {
            $report_result = $result->result_array();
            foreach ($report_result as $key => $value) {
                if (!empty($value['commodity_center_name'])) {
                    $report_result[$key]['commodity_name'] = $value['commodity_name'] . '+' . $value['commodity_center_name'] . '+' . $value['package_type_name'];
                } else {
                    $report_result[$key]['commodity_name'] = $value['commodity_name'] . '+' . $value['commodity_specification_name'] . '+' . $value['package_type_name'];
                }
            }

            $data['success'] = TRUE;
            $data['msg'] = '获取数据成功';
            $data['data'] = $report_result;
        }

        return $data;
    }

    /**
     * 分页获取用户检测报告
     * @param int $page 页数
     * @param int $page_size 页面大小
     * @param int $user_id 用户ID
     * @return array
     */
    public function paginate_for_report($page = 1, $page_size = 10, $user_id = 0)
    {
        $result = array('success' => TRUE, 'msg' => '获取报告列表失败', 'data' => array(), 'total_page' => 0);
        if (intval($page) < 1 || intval($page_size) < 1) {
            $result['msg'] = '分页参数错误';
            return $result;
        }

        $this->db->select('
            order.id as order_id,
            order.number as order_number,
            order_commodity.id as order_commodity_id,
            order_commodity.number as order_commodity_number,
            report_path.path,
            report.*,
            system_code.name as system_code_name
        ');
        $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
        $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
        $this->db->join('attachment as report_path', 'report_path.id = report.attachment_id', 'left');
        $this->db->join('system_code', 'system_code.value = report.blood_relationship and system_code.type = "' . jys_system_code::RELATION . '"', 'left');
        if (intval($user_id) > 0) {
            $this->db->where('order.user_id', $user_id);
        }
        $this->db->order_by('report.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $data = $this->db->get('report');

        if ($data && $data->num_rows() > 0) {
            $data = $data->result_array();
            $result['success'] = TRUE;
            $result['msg'] = '查询报告成功';
            $result['data'] = $data;

            if (intval($user_id) > 0) {
                $this->db->select('order.id');
                $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
                $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
                $this->db->where('order.user_id', $user_id);
                $page_data = $this->db->get('report');
                if ($page_data && $page_data->num_rows() > 0) {
                    $total_num = $page_data->num_rows();
                    $result['total_page'] = ceil($total_num / $page_size * 1.0);
                } else {
                    $result['total_page'] = 1;
                }
            } else {
                $total_page = $this->jys_db_helper->get_total_page('report', $page_size);
                if (intval($total_page) > 0) {
                    $result['total_page'] = intval($total_page);
                } else {
                    $result['total_page'] = 1;
                }
            }
        } else {
            $result['msg'] = '未查询到相关报告';
        }
        return $result;
    }

    /**
     * 根据子订单ID获取其下所有报告
     * @param $order_commodity_id
     * @return bool
     */
    public function get_report_list_by_order_commodity_id($order_commodity_id)
    {
        if (intval($order_commodity_id) < 1) {
            return FALSE;
        }

        $this->db->select('
            order.id as order_id,
            order.number as order_number,
            order_commodity.id as order_commodity_id,
            order_commodity.number as order_commodity_number,
            report_path.path,
            report.*
        ');
        $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
        $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
        $this->db->join('attachment as report_path', 'report_path.id = report.attachment_id', 'left');
        $this->db->where('order_commodity.id', $order_commodity_id);
        $this->db->order_by('report.update_time', 'DESC');
        $data = $this->db->get('report');

        if ($data && $data->num_rows() > 0) {
            return $data->result_array();
        } else {
            return FALSE;
        }
    }

    /**
     * 添加检测报告
     * @param $order_commodity_id 子订单ID
     * @param $number 报告编号
     * @param $name 姓名
     * @param $age 年龄
     * @param $gender 性别
     * @param $phone 手机号
     * @param $identity_card 身份证号
     * @param $attachment_id 报告ID
     */
    public function add($post = [])
    {
        $result = array('success' => FALSE, 'msg' => '添加报告失败');

        if (empty($post['number'])) {
            $result['msg'] = '参数不正确';
            return $result;
        }

        // 判断报告编号是否重复
        if ($post['number']) {
            $this->db->trans_start();
            $res = $this->jys_db_helper->is_exist('report', ['report.number' => $post['number']]);
            $this->db->trans_complete();

            if ($res) {
                $result['msg'] = '报告编号重复';
                return $result;
            }
        }

        // 判断该订单的报告是否已经上传
        if ($post['order_commodity_id']) {
            $this->db->trans_start();
            $order_commodity = $this->jys_db_helper->get_where('order_commodity', ['id' => $post['order_commodity_id']]);
            $total = $this->jys_db_helper->get_total_num('report', ['order_commodity_id' => $post['order_commodity_id']]);
            $this->db->trans_complete();

            if (intval($total) >= intval($order_commodity['amount'])) {
                // 报告数量已达上限
                $result['msg'] = '报告数量已达上限';
                return $result;
            }
        }

        $current_time = date('Y-m-d H:i:s');
        $insert = array(
            'order_commodity_id' => $post['order_commodity_id'],
            'number' => $post['number'],
            'name' => $post['name'],
            'birth' => $post['birth'],
            'gender' => $post['gender'],
            'phone' => $post['phone'],
            'height' => $post['height'],
            'weight' => $post['weight'],
            'smoking' => $post['smoking'],
            'identity_card' => $post['identity_card'],
            'attachment_id' => $post['attachment_id'],
//            'template_id' => $post['template_id'],
//            'project_num' => $post['project_num'],
            'province' => $post['province'],
            'province_code' => $post['province_code'],
            'city' => $post['city'],
            'city_code' => $post['city_code'],
            'district' => $post['district'],
            'district_code' => $post['district'],
            'address' => $post['address'],
            'personal_history' => $post['personal_history'],
            'family_history' => $post['family_history'],
//            'blood_relationship'    => $post['blood_relationship'],
            'create_time' => $current_time,
            'update_time' => $current_time,
            'operation_status' => jys_system_code::OPERATION_STATUS_CREATE_NOT
        );
        if (isset($post['identity_card'])) {
            if (strlen($post['identity_card']) == 18) {
                $insert['identity_card'] = $post['identity_card'];
                //根据身份号码截取出生日期
                $insert['birth'] = substr($post['identity_card'], 6, 4) . '-' . substr($post['identity_card'], 10, 2) . '-' . substr($post['identity_card'], 12, 2);
                //根据身份证号码判断性别
                $insert['gender'] = substr($post['identity_card'], -2, 1) % 2 ? '1' : '0';
            } else {
                $insert['identity_card'] = NULL;
            }
        } else {
            $insert['identity_card'] = NULL;
        }

        $this->db->trans_start();
        $insert_result = $this->jys_db_helper->add('report', $insert, true);

        if ($insert_result['success']) {
            $result['success'] = TRUE;
            $result['msg'] = '添加报告成功';
            $result['report_id'] = $insert_result['insert_id'];
        } else {
            $result['msg'] = '添加报告失败';
        }
        $this->db->trans_complete();

        return $result;
    }

    /**
     * 更新报告
     * @param $id 报告ID
     * @param string $number 报告编号
     * @param string $name 姓名
     * @param string $phone 电话号码
     * @param string $identity_card 身份证号
     * @param string $attachment_id 报告ID
     */
    public function update($post = [])
    {
        $result = array('success' => FALSE, 'msg' => '更新报告失败');
        if (intval($post['id']) < 1) {
            $result['msg'] = '请选择要更新的报告';
            return $result;
        }

        $update = array();
        if (isset($post['number']) && !empty($post['number'])) {
            $update['number'] = $post['number'];
        }
        if (isset($post['name']) && !empty($post['name'])) {
            $update['name'] = $post['name'];
            $update['enter_information_time'] = date('Y-m-d H:i:s');
            $update['report_status'] = Jys_system_code::REPORT_STATUS_COMMITTED;
        }
        if (isset($post['birth']) && !empty($post['birth'])) {
            $update['birth'] = $post['birth'];
            $update['enter_information_time'] = date('Y-m-d H:i:s');
            $update['report_status'] = Jys_system_code::REPORT_STATUS_COMMITTED;
        }
        if (isset($post['smoking'])) {
            $update['smoking'] = $post['smoking'];
        }
        if (isset($post['gender'])) {
            $update['gender'] = $post['gender'];
        }
        if (isset($post['phone']) && !empty($post['phone'])) {
            $update['phone'] = $post['phone'];
            $update['enter_information_time'] = date('Y-m-d H:i:s');
            $update['report_status'] = Jys_system_code::REPORT_STATUS_COMMITTED;
        }
        if (isset($post['identity_card'])) {
            if (strlen($post['identity_card']) == 18) {
                $update['identity_card'] = $post['identity_card'];
                //根据身份号码截取出生日期
                $update['birth'] = substr($post['identity_card'], 6, 4) . '-' . substr($post['identity_card'], 10, 2) . '-' . substr($post['identity_card'], 12, 2);
                //根据身份证号码判断性别
                $update['gender'] = substr($post['identity_card'], -2, 1) % 2 ? '1' : '0';
                $update['enter_information_time'] = date('Y-m-d H:i:s');
                $update['report_status'] = Jys_system_code::REPORT_STATUS_COMMITTED;
            }
        }
        if (isset($post['attachment_id']) && intval($post['attachment_id']) > 0) {
            $update['attachment_id'] = $post['attachment_id'];
            $update['report_attachment_upload_time'] = date('Y-m-d H:i:s');
            $update['erp_report_back_status'] = Jys_system_code::ERP_REPORT_BACK_STATUS_IN;
            //$update['report_status'] = Jys_system_code::REPORT_STATUS_COMMITTED;
        }
        if (isset($post['height']) && floatval($post['height']) > 0) {
            $update['height'] = $post['height'];
        }
        if (isset($post['weight']) && floatval($post['height']) > 0) {
            $update['weight'] = $post['weight'];
        }
        if (isset($post['address']) && !empty($post['address'])) {
            $update['address'] = $post['address'];
        }
        if (isset($post['personal_history']) && !empty($post['personal_history'])) {
            $update['personal_history'] = $post['personal_history'];
        }
        if (isset($post['family_history']) && !empty($post['family_history'])) {
            $update['family_history'] = $post['family_history'];
        }
        if (isset($post['province']) && !empty($post['province'])) {
            $update['province'] = $post['province'];
        }
        if (isset($post['province_code']) && !empty($post['province_code'])) {
            $update['province_code'] = $post['province_code'];
        }
        if (isset($post['city']) && !empty($post['city'])) {
            $update['city'] = $post['city'];
        }
        if (isset($post['city_code']) && !empty($post['city_code'])) {
            $update['city_code'] = $post['city_code'];
        }
        if (isset($post['district_code']) && !empty($post['district_code'])) {
            $update['district_code'] = $post['district_code'];
        }
        if (isset($post['district']) && !empty($post['district'])) {
            $update['district'] = $post['district'];
        }
        if (isset($post['project']) && !empty($post['project'])) {
            $update['project'] = $post['project'];
            $update['enter_information_time'] = date('Y-m-d H:i:s');
            $update['report_status'] = Jys_system_code::REPORT_STATUS_COMMITTED;
        }
//        if (isset($post['blood_relationship'])) {
//            $update['blood_relationship'] = $post['blood_relationship'];
//        }
//        if (isset($post['template_id']) && intval($post['template_id']) > 0) {
//            $update['template_id'] = intval($post['template_id']);
//        }
//        if (isset($post['project_num']) && intval($post['project_num']) > 0) {
//            $update['project_num'] = $post['project_num'];
//        }
        if (empty($update['gender'])) {
            $update['gender'] = NULL;
        }
        if (!empty($update) && is_array($update)) {
            $this->db->trans_start();
            $old_report_info = $this->jys_db_helper->get_where('report', array('id' => $post['id']));
            if ($old_report_info) {
                if (is_array($old_report_info) && intval($old_report_info['report_status']) == 1) {
                    unset($update['enter_information_time']);
                }
                //如果同步状态是创建已同步 更新为更新未同步
                if ($old_report_info['operation_status'] == jys_system_code::OPERATION_STATUS_CREATE_IN) {
                    $update['operation_status'] = jys_system_code::OPERATION_STATUS_UPDATE_NOT;
                }
            } else {
                $this->db->trans_complete();
                $result['msg'] = '您要更新的报告信息不存在';
                return $result;
            }
            $update['update_time'] = date('Y-m-d H:i:s');

            if ($this->jys_db_helper->update('report', $post['id'], $update)) {
                if (isset($update['attachment_id']) && intval($update['attachment_id']) > 0) {
                    $number[0] = $update['number'];
                    //调用短息发送接口
                    $this->send_report_status_information_to_user($number);
                }
                $result['success'] = TRUE;
                $result['msg'] = '更新报告成功';
            } else {
                $result['msg'] = '更新报告失败';
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $result['success'] = FALSE;
                $result['msg'] = '更新报告失败';
            }
        }

        return $result;
    }

    /**
     *删除报告
     */
    public function delete_report_by_id($report_id)
    {
        $result['success'] = FALSE;

        if ($this->jys_db_helper->delete('report', $report_id)) {
            $result['success'] = TRUE;
        }
        return $result;
    }

    /**
     * 填写报告的个人信息
     * @param $id 报告ID
     * @param string $number 报告编号
     * @param string $name 姓名
     * @param string $phone 电话号码
     * @param string $identity_card 身份证号
     * @param string $attachment_id 报告ID
     */
    public function add_report_userInfo($number, $name, $birth, $gender = 0, $phone, $smoking = 0, $identity_card, $province, $province_code, $city, $city_code, $district, $district_code, $address, $height, $weight, $personal_history, $family_history, $project, $order_commodity_id)
    {
        $result = array('success' => FALSE, 'msg' => '添加报告个人信息失败');
        $update = array();
        if (empty($number)) {
            $result['msg'] = '请选择要添加的报告';
            return $result;
        } else {
            $data = $this->get_report_id_by_number($number);
            if ($data['data']) {
                $report_id = $data['data']['id'];
                if ($data['data']['operation_status'] == jys_system_code::OPERATION_STATUS_CREATE_IN) {
                    $update['operation_status'] = jys_system_code::OPERATION_STATUS_UPDATE_NOT;
                }
//                $report_project_num = $data['data']['project_num'];
            } else {
                $result['msg'] = $data['msg'];
                return $result;
            }
        }
//        //判断选择的项目数是否超过最大值
//        if (!empty($project) && count(explode(',', $project)) > $report_project_num) {
//            $result['msg'] = '选择的项目数超过最大值';
//            return $result;
//        }

        if (!empty($name)) {
            $update['name'] = $name;
        }
        if (isset($gender)) {
            $update['gender'] = $gender;
        }
        if (!empty($birth)) {
            $update['birth'] = $birth;
        }
        if (isset($smoking)) {
            $update['smoking'] = $smoking;
        }
        if (!empty($phone)) {
            $update['phone'] = $phone;
        }
        if (!empty($identity_card)) {
            $update['identity_card'] = $identity_card;
            //根据身份号码截取出生日期
            $update['birth'] = substr($identity_card, 6, 4) . '-' . substr($identity_card, 10, 2) . '-' . substr($identity_card, 12, 2);
            //根据身份证号码判断性别
            $update['gender'] = substr($identity_card, -2, 1) % 2 ? '1' : '0';
        }
        if (!empty($height)) {
            $update['height'] = $height;
        }
        if (!empty($weight)) {
            $update['weight'] = $weight;
        }
        if (!empty($address)) {
            $update['address'] = $address;
        }
        if (!empty($personal_history)) {
            $update['personal_history'] = $personal_history;
        }
        if (!empty($family_history)) {
            $update['family_history'] = $family_history;
        }
        if (!empty($province)) {
            $update['province'] = $province;
        }
        if (!empty($province_code)) {
            $update['province_code'] = $province_code;
        }
        if (!empty($city)) {
            $update['city'] = $city;
        }
        if (!empty($city_code)) {
            $update['city_code'] = $city_code;
        }
        if (!empty($district_code)) {
            $update['district_code'] = $district_code;
        }
        if (!empty($district)) {
            $update['district'] = $district;
        }
//        if (!empty($blood_relationship)) {
//            $update['blood_relationship'] = $blood_relationship;
//        }
        if (!empty($project)) {
            $update['project'] = $project;
        }
        if (!empty($order_commodity_id)) {
            $update['order_commodity_id'] = $order_commodity_id;
        }
        if (!empty($number)) {
            $update['number'] = $number;
        }
        if (!empty($update) && is_array($update)) {
            $update['update_time'] = date('Y-m-d H:i:s');
            $update['enter_information_time'] = date('Y-m-d H:i:s');
            $update['report_status'] = Jys_system_code::REPORT_STATUS_COMMITTED;
            if ($this->jys_db_helper->update('report', $report_id, $update)) {
                $result['success'] = TRUE;
                $result['msg'] = '添加报告成功';
                if (!empty($phone) && !empty($name) && !empty($number)) {
                    $this->send_report_status_information_to_user(array($number));
                }
                //将报告信息回传到erp
                $erp_info_result = $this->insert_report_to_erp($update);
                //将报告信息会传到海云
                // $rest_info_result = $this->update_report_to_rest($update);
            } else {
                $result['msg'] = '添加报告失败';
            }
        }

        return $result;
    }

    /*
     * 根据订单编号获取报告
     */
    public function get_report_by_order_number($order_number)
    {
        if (intval($order_number) < 1) {
            return FALSE;
        }

        $this->db->select('report.*,                          
                           report_path.path'
        );

        $this->db->join('attachment as report_path', 'report_path.id = report.attachment_id', 'left');
        $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
        $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
        $this->db->where('order.number', $order_number);
        $data = $this->db->get('report');

        if ($data && $data->num_rows() > 0) {
            return $data->row_array();
        } else {
            return FALSE;
        }
    }

    /*
     * 根据报告编号获取报告信息
     */
    public function get_report_id_by_number($number = '')
    {
        $data = [
            'success' => FALSE,
            'msg' => '没有报告数据',
            'data' => null
        ];
        if (empty($number)) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('report.*');
        $this->db->where('number', $number);
        $result = $this->db->get('report');
        if ($result && $result->num_rows() > 0) {
            $this->db->select('report.*');
            $this->db->where('report_status', Jys_system_code::REPORT_STATUS_UNCOMMITTED);
            $this->db->where('number', $number);
            $res = $this->db->get('report');
            if ($res && $res->num_rows() > 0) {
                $data['data'] = $res->row_array();
                return $data;
            } else {
                $data['msg'] = '该报告的基本信息已经提交过了，不能重复提交！';
                return $data;
            }
        } else {
            $data['msg'] = '报告编号不存在，请输入正确的报告编号！';
            return $data;
        }
    }

    /*
     * 分页获取所有的检测报告
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param string $start_create_time 创建订单的开始时间
     * @param string $end_create_time 创建订单的结束时间
     * @param int $attachment (1 已经上传报告附件 2 未上传报告附件)
     * @param string $keyword 模糊查询的关键字
     * @param int $is_online 1为线上报告/2为线下报告
     */
    public function get_all_report($page = 1, $page_size = 10, $start_create_time = '', $end_create_time = '', $attachment = 2, $keyword = '', $has_written = 2, $is_online = 0, $agent_id)
    {
        $data = [
            'success' => FALSE,
            'msg' => '没有报告数据',
            'data' => null,
            'total_page' => 0
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('report.*,
                           order.id as order_id,
                           order.number as order_number,
                           order.create_time as order_create_time,
                           commodity_specification.name as commodity_specification_name,
                           attachment.path as path');
        $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
        if ($is_online == 1) {
            $this->db->join('commodity_specification', 'commodity_specification.id = order_commodity.commodity_specification_id', 'left');
        } else if ($is_online == 2) {
            $this->db->join('commodity_specification', 'commodity_specification.erp_commodity_id = order_commodity.erp_commodity_id', 'left');
        }
        $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
        $this->db->join('attachment', 'attachment.id = report.attachment_id', 'left');
        if ($agent_id != FALSE) {
            $this->db->join('user_agent', 'user_agent.user_id = order.user_id', 'left');
        }
        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('order.number', $keyword);
            $this->db->or_like('report.name', $keyword);
            $this->db->or_like('report.number', $keyword);
            $this->db->or_like('report.age', $keyword);
            $this->db->or_like('report.phone', $keyword);
            $this->db->or_like('report.identity_card', $keyword);
            $this->db->group_end();
        }
        if (!empty($start_create_time)) {
            $this->db->where('order.create_time >=', $start_create_time);
        }
        if (!empty($end_create_time)) {
            $this->db->where('order.create_time <=', $end_create_time);
        }
        //是否上传了PDF报告
        if (isset($attachment) && intval($attachment) == 1) {
            $this->db->where('report.attachment_id >', 0);
        } else if (isset($attachment) && intval($attachment) == 0) {
            $this->db->where('report.attachment_id =', NULL);
        }
        //是否已经填写了用户信息
        if (isset($has_written) && intval($has_written) == 1) {
            $this->db->where('report.report_status', '1');
        } else if (isset($has_written) && intval($has_written) == 0) {
            $this->db->where('report.report_status', '0');
        }
        //是否为线上还是线下报告
        if ($is_online == 1) {
            $this->db->where('order.terminal_type !=', Jys_system_code::TERMINAL_TYPE_LINE);
        } else if ($is_online == 2) {
            $this->db->where('order.terminal_type', Jys_system_code::TERMINAL_TYPE_LINE);
        }
        // 获取代理商旗下用户的订单报告
        if ($agent_id != FALSE) {
            $this->db->where('user_agent.agent_id', $agent_id);
        }
        $this->db->order_by('report.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('report');

        if ($result && $result->num_rows() > 0) {
            $data['success'] = TRUE;
            $data['msg'] = '查询报告成功';
            $result = $result->result_array();
            // 获取对应的检测模版
            $order_commodity_ids = array();
            foreach ($result as $value) {
                $order_commodity_ids[] = $value['order_commodity_id'];
            }
            $template_list = $this->get_template_by_order_commodity_ids($order_commodity_ids);
            foreach ($result as $key => $item) {
                if (isset($template_list[$item['order_commodity_id']]) && !empty($template_list[$item['order_commodity_id']])) {
                    $result[$key]['template_list'] = $template_list[$item['order_commodity_id']];
                } else {
                    $result[$key]['template_list'] = array();
                }
            }

            // 获取所有的检测项目
            $project_result = $this->jys_db_helper->all('detection_project');
            $projects = [];
            if ($project_result['success'] && !empty($project_result['data'])) {
                foreach ($project_result['data'] as $item) {
                    $projects[$item['id']] = $item;
                }
            }
            //将所选择的检测项目名称拼接成字符串
            foreach ($result as $index => $value) {
                $repo_pro = explode(",", $value['project']);
                $result[$index]['project_list'] = [];
                $project_text = '';
                if (!empty($repo_pro) && is_array($repo_pro)) {
                    foreach ($repo_pro as $pro_id) {
                        if (!empty($pro_id) && intval($pro_id) > 0) {
                            $result[$index]['project_list'][] = $projects[$pro_id];
                        }
                    }
                }
                foreach ($result[$index]['project_list'] as $pro) {
                    $project_text .= $pro['name'] . " ,";
                }
                //去除拼接成的检测项目名称结尾多余的逗号
                if (strlen($project_text) > 0) {
                    $project_text = substr($project_text, 0, strlen($project_text) - 1);
                }
                $result[$index]['project'] = $project_text;
            }

            $data['data'] = $result;
            if (empty($start_create_time) && empty($end_create_time) && !empty($attachment) && intval($attachment) == 2 && empty($keyword) && !empty($has_written)) {
                $this->db->select('COUNT(report.id) AS count');
                $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
                $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
                if ($agent_id != FALSE) {
                    $this->db->join('user_agent', 'user_agent.user_id = order.user_id', 'left');
                }
                if (!empty($keyword)) {
                    // 关键字模糊查找
                    $this->db->group_start();
                    $this->db->like('order.number', $keyword);
                    $this->db->or_like('report.name', $keyword);
                    $this->db->or_like('report.number', $keyword);
                    $this->db->or_like('report.age', $keyword);
                    $this->db->or_like('report.phone', $keyword);
                    $this->db->or_like('report.identity_card', $keyword);
                    $this->db->group_end();
                }
                if (!empty($start_create_time)) {
                    $this->db->where('order.create_time >=', $start_create_time);
                }
                if (!empty($end_create_time)) {
                    $this->db->where('order.create_time <=', $end_create_time);
                }
                //是否上传了PDF报告
                if (isset($attachment) && intval($attachment) == 1) {
                    $this->db->where('report.attachment_id >', 0);
                } else if (isset($attachment) && intval($attachment) == 0) {
                    $this->db->where('report.attachment_id =', NULL);
                }
                //是否已经填写了用户信息
                if (isset($has_written) && intval($has_written) == 1) {
                    $this->db->where('report.report_status', '1');
                } else if (isset($has_written) && intval($has_written) == 0) {
                    $this->db->where('report.report_status', '0');
                }
                //是否为线上还是线下报告
                if ($is_online == 1) {
                    $this->db->where('order.terminal_type !=', Jys_system_code::TERMINAL_TYPE_LINE);
                } else if ($is_online == 2) {
                    $this->db->where('order.terminal_type', Jys_system_code::TERMINAL_TYPE_LINE);
                }
                // 获取代理商旗下用户的订单报告
                if ($agent_id != FALSE) {
                    $this->db->where('user_agent.agent_id', $agent_id);
                }
                $res = $this->db->get('report');
                if ($res && $res->num_rows() > 0) {
                    $res = $res->row_array();
                    $data['total_page'] = ceil($res['count'] / $page_size * 1.0);
                    $data['total'] = $res['count'];
                } else {
                    $data['total_page'] = 1;
                    $data['total'] = count($data['data']);
                }
            }
        }
        return $data;
    }

    /*
     * 分页获取所有的检测报告（样本管理）
     * @param int $page 页数
     * @param int $page_size 页大小
     * @param string $start_create_time 创建订单的开始时间
     * @param string $end_create_time 创建订单的结束时间
     * @param int $attachment (1 已经上传报告附件 2 未上传报告附件)
     * @param string $keyword 模糊查询的关键字
     * @param int $is_online 1为线上报告/2为线下报告
     */
    public function get_all_report_for_sample($page = 1, $page_size = 10, $start_create_time = '', $end_create_time = '', $attachment = 2, $keyword = '', $has_written = 2, $is_online = 0, $agent_id)
    {
        $data = [
            'success' => FALSE,
            'msg' => '没有报告数据',
            'data' => null,
            'total_page' => 0,
            'total' => 0
        ];

        if (empty($page) || intval($page) < 1 || empty($page_size) || intval($page_size) < 1) {
            $data['msg'] = '参数错误';
            return $data;
        }

        $this->db->select('report.*,
                           order_commodity.commodity_specification_id,
                           order_commodity.erp_commodity_id,
                           commodity_specification.name as commodity_specification_name,
                           erp_commodity_specification.name as erp_commodity_specification_name,
                           order.id as order_id,
                           order.number as order_number,
                           order.create_time as order_create_time');
        $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = order_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity_specification as erp_commodity_specification', 'erp_commodity_specification.erp_commodity_id = order_commodity.erp_commodity_id', 'left');
        $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
        if ($agent_id != FALSE) {
            $this->db->join('user_agent', 'user_agent.user_id = order.user_id', 'left');
        }
        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('order.number', $keyword);
            $this->db->or_like('report.name', $keyword);
            $this->db->or_like('report.number', $keyword);
            $this->db->or_like('report.age', $keyword);
            $this->db->or_like('report.phone', $keyword);
            $this->db->or_like('report.identity_card', $keyword);
            $this->db->group_end();
        }
        if (!empty($start_create_time)) {
            $this->db->where('order.create_time >=', $start_create_time);
        }
        if (!empty($end_create_time)) {
            $this->db->where('order.create_time <=', $end_create_time);
        }
        //是否上传了PDF报告
        if (isset($attachment) && intval($attachment) == 1) {
            $this->db->where('report.attachment_id >', 0);
        } else if (isset($attachment) && intval($attachment) == 0) {
            $this->db->where('report.attachment_id =', NULL);
        }
        //是否已经填写了用户信息
        if (isset($has_written) && intval($has_written) == 1) {
            $this->db->where('report.report_status', '1');
        } else if (isset($has_written) && intval($has_written) == 0) {
            $this->db->where('report.report_status', '0');
        }
        //是否为线上还是线下报告
        if ($is_online == 1) {
            $this->db->where('order.terminal_type !=', Jys_system_code::TERMINAL_TYPE_LINE);
        } else if ($is_online == 2) {
            $this->db->where('order.terminal_type', Jys_system_code::TERMINAL_TYPE_LINE);
        }
        // 获取代理商旗下用户的订单报告
        if ($agent_id != FALSE) {
            $this->db->where('user_agent.agent_id', $agent_id);
        }
        $this->db->order_by('report.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $result = $this->db->get('report');

        if ($result && $result->num_rows() > 0) {
            $data['success'] = TRUE;
            $data['msg'] = '查询报告成功';
            $result = $result->result_array();
            // 获取对应的检测模版
            $order_commodity_ids = array();
            foreach ($result as $value) {
                $order_commodity_ids[] = $value['order_commodity_id'];
            }
            $template_list = $this->get_template_by_order_commodity_ids($order_commodity_ids);
            foreach ($result as $key => $item) {
                if (isset($template_list[$item['order_commodity_id']]) && !empty($template_list[$item['order_commodity_id']])) {
                    $result[$key]['template_list'] = $template_list[$item['order_commodity_id']];
                } else {
                    $result[$key]['template_list'] = array();
                }
            }

            // 获取所有的检测项目
            $project_result = $this->jys_db_helper->all('detection_project');
            $projects = [];
            if ($project_result['success'] && !empty($project_result['data'])) {
                foreach ($project_result['data'] as $item) {
                    $projects[$item['id']] = $item;
                }
            }

            foreach ($result as $key => $value) {
                //判断是否为ERP订单
                if (!empty($result[$key]['commodity_specification_name'])) {
                    $result[$key]['erp_order'] = FALSE;
                } else if (!empty($result[$key]['erp_commodity_specification_name'])) {
                    $result[$key]['erp_order'] = TRUE;
                }

            }
            //将所选择的检测项目名称拼接成字符串
            foreach ($result as $index => $value) {
                $repo_pro = explode(",", $value['project']);
                $result[$index]['project_list'] = [];
                $project_text = '';
                if (!empty($repo_pro) && is_array($repo_pro)) {
                    foreach ($repo_pro as $pro_id) {
                        if (!empty($pro_id) && intval($pro_id) > 0) {
                            $result[$index]['project_list'][] = $projects[$pro_id];
                        }
                    }
                }
                foreach ($result[$index]['project_list'] as $pro) {
                    $project_text .= $pro['name'] . " ,";
                }
                //去除拼接成的检测项目名称结尾多余的逗号
                if (strlen($project_text) > 0) {
                    $project_text = substr($project_text, 0, strlen($project_text) - 1);
                }
                $result[$index]['project'] = $project_text;
            }

            $data['data'] = $result;

            $this->db->select('COUNT(report.id) AS count');
            $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
            $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
            if ($agent_id != FALSE) {
                $this->db->join('user_agent', 'user_agent.user_id = order.user_id', 'left');
            }
            if (!empty($keyword)) {
                // 关键字模糊查找
                $this->db->group_start();
                $this->db->like('order.number', $keyword);
                $this->db->or_like('report.name', $keyword);
                $this->db->or_like('report.number', $keyword);
                $this->db->or_like('report.age', $keyword);
                $this->db->or_like('report.phone', $keyword);
                $this->db->or_like('report.identity_card', $keyword);
                $this->db->group_end();
            }
            if (!empty($start_create_time)) {
                $this->db->where('order.create_time >=', $start_create_time);
            }
            if (!empty($end_create_time)) {
                $this->db->where('order.create_time <=', $end_create_time);
            }
            //是否上传了PDF报告
            if (isset($attachment) && intval($attachment) == 1) {
                $this->db->where('report.attachment_id >', 0);
            } else if (isset($attachment) && intval($attachment) == 0) {
                $this->db->where('report.attachment_id =', NULL);
            }
            //是否已经填写了用户信息
            if (isset($has_written) && intval($has_written) == 1) {
                $this->db->where('report.report_status', '1');
            } else if (isset($has_written) && intval($has_written) == 0) {
                $this->db->where('report.report_status', '0');
            }
            //是否为线上还是线下报告
            if ($is_online == 1) {
                $this->db->where('order.terminal_type !=', Jys_system_code::TERMINAL_TYPE_LINE);
            } else if ($is_online == 2) {
                $this->db->where('order.terminal_type', Jys_system_code::TERMINAL_TYPE_LINE);
            }
            // 获取代理商旗下用户的订单报告
            if ($agent_id != FALSE) {
                $this->db->where('user_agent.agent_id', $agent_id);
            }
            $res = $this->db->get('report');
            if ($res && $res->num_rows() > 0) {
                $res = $res->row_array();
                $data['total_page'] = ceil($res['count'] / $page_size * 1.0);
                $data['total'] = $res['count'];
            } else {
                $data['total_page'] = 1;
                $data['total'] = count($data['data']);
            }
        }
        return $data;
    }

    /**
     * 依据子订单分页获取报告信息
     * @param int $page
     * @param int $page_size
     * @param array $condition 查询条件
     * @param string $keywords 关键字
     * @return array
     */
    public function get_report_by_suborder($page = 1, $page_size = 10, $condition = array(), $keywords = '')
    {
        $data = array('success' => FALSE, 'msg' => '没有报告信息', 'data' => array(), 'total_page' => 0, 'total_num' => 0);
        if (empty($page) || intval($page) < 0 || empty($page_size) || intval($page_size) < 0) {
            $data['msg'] = '分页参数错误';
            return $data;
        }

        $this->db->select(' order_commodity.number as order_commodity_number,
                            order_commodity.id as order_commodity_id,
                            order_commodity.order_id,
                            order_commodity.erp_commodity_id,
                            order_commodity.commodity_specification_id,
                            order_commodity.amount,
                            order.number as order_number,
                            order.address,
                            order.terminal_type,
                            commodity.name as commodity_name,
                            commodity_specification.name as commodity_specification_name,
                            commodity_center.name as commodity_center_name,
                            package_type.name as package_type_name,
                            erp_commodity.name as erp_commodity_name,
                            erp_commodity_specification.name as erp_commodity_specification_name,
                            erp_commodity_center.name as erp_commodity_center_name,
                            erp_package_type.name as erp_package_type_name,
                            user.name as agent_name,
                            ');
        $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = order_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id', 'left');
        $this->db->join('system_code as package_type', 'package_type.value = commodity_specification.packagetype AND package_type.type = "' . jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE . '"', 'left');
        $this->db->join('commodity_specification as erp_commodity_specification', 'erp_commodity_specification.erp_commodity_id = order_commodity.erp_commodity_id', 'left');
        $this->db->join('commodity_center as erp_commodity_center', 'erp_commodity_center.id = erp_commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity as erp_commodity', 'erp_commodity.id = erp_commodity_specification.commodity_id', 'left');
        $this->db->join('system_code as erp_package_type', 'erp_package_type.value = erp_commodity_specification.packagetype AND erp_package_type.type = "' . jys_system_code::COMMODITY_SPECIFICATION_PACKAGETYPE . '"', 'left');
        $this->db->join('user_agent', 'user_agent.user_id = order.user_id', 'left');
        $this->db->join('user', 'user.id = user_agent.agent_id', 'left');
        if (!empty($condition)) {
            $this->db->where($condition);
        }
        if (!empty($keywords)) {
            $this->db->group_start();
            $this->db->like('order_commodity.number', $keywords);
            $this->db->or_like('order.number', $keywords);
            $this->db->group_end();
        }
        $this->db->group_by('order_commodity.id');
        $this->db->order_by('order_commodity.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $res = $this->db->get('order_commodity');
        if ($res && $res->num_rows() > 0) {
            $data['success'] = TRUE;
            $data['msg'] = '获取报告信息成功';
            $data['data'] = $res->result_array();
            foreach ($data['data'] as $key => $value) {
                //判断是否为ERP订单
                if (!empty($data['data'][$key]['commodity_specification_id'])) {
                    $data['data'][$key]['erp_order'] = FALSE;
                } else {
                    $data['data'][$key]['erp_order'] = TRUE;
                }

                $data['data'][$key]['report_list'] = array();
                $data['data'][$key]['template_list'] = array();
                $data['data'][$key]['address'] = json_decode($value['address'], TRUE);
                $order_commodity_ids[] = $value['order_commodity_id'];
            }

            //组装报告
            $data['data'] = $this->push_report_suborder_ids($data['data'], $order_commodity_ids);
            //组装模板
            $data['data'] = $this->push_template_suborder_ids($data['data'], $order_commodity_ids);

            $this->db->select('order_commodity.id');
            $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
            $this->db->join('user_agent', 'user_agent.user_id = order.user_id', 'left');
            if (!empty($condition)) {
                $this->db->where($condition);
            }
            if (!empty($keywords)) {
                $this->db->group_start();
                $this->db->like('order_commodity.number', $keywords);
                $this->db->or_like('order.number', $keywords);
                $this->db->group_end();
            }
            $page_res = $this->db->get('order_commodity');
            if ($page_res && $page_res->num_rows() > 0) {
                $data['total_page'] = ceil($page_res->num_rows() / $page_size * 1.0);
                $data['total_num'] = $page_res->num_rows();
            } else {
                $data['total_page'] = 1;
                $data['total_num'] = count($data['data']);
            }
        }

        return $data;
    }

    /*
     * 根据子订单ids组装报告信息
     */
    private function push_report_suborder_ids($report = array(), $order_commodity_ids = array())
    {
        if (empty($report) || !is_array($report) || empty($order_commodity_ids) || !is_array($order_commodity_ids) || count($report) != count($order_commodity_ids)) {
            return $report;
        }

        $this->db->select(' report.number as report_number,
                            report.id,
                            report.order_commodity_id,
                            report.name,
                            report.gender,
                            report.age,
                            report.birth,
                            report.phone,
                            report.smoking,
                            report.identity_card,
                            report.personal_history,
                            report.family_history,
                            report.blood_relationship,
                            report.enter_information_time,
                            report.report_attachment_upload_time,
                            report.report_status,
                            report.height,
                            report.weight,
                            report.project,
                            report.attachment_id,
                            attachment.path as report_attachment');
        $this->db->join('attachment', 'attachment.id = report.attachment_id', 'left');
        $this->db->where_in('report.order_commodity_id', $order_commodity_ids);
        $res = $this->db->get('report');
        if ($res && $res->num_rows() > 0) {
            $data = $res->result_array();

            for ($i = 0; $i < count($report); $i++) {
                $report[$i]['report_num'] = 0;
                for ($j = 0; $j < count($data); $j++) {
                    if ($report[$i]['order_commodity_id'] == $data[$j]['order_commodity_id']) {
                        $report[$i]['report_list'][] = $data[$j];
                        $report[$i]['report_num']++;
                    }
                }
            }
        }

        return $report;
    }

    /*
     * 根据子订单ids组装模板信息
     */
    private function push_template_suborder_ids($report = array(), $order_commodity_ids = array())
    {
        if (empty($report) || !is_array($report) || empty($order_commodity_ids) || !is_array($order_commodity_ids) || count($report) != count($order_commodity_ids)) {
            return $report;
        }

        $this->db->select(' order_commodity_template.order_commodity_id,
                            order_commodity_template.template_id,
                            order_commodity_template.project_num,
                            detection_template.name,
                            detection_template.id,
                            detection_template.description
                            ');
        $this->db->join('detection_template', 'detection_template.id = order_commodity_template.template_id', 'left');
        $this->db->where_in('order_commodity_template.order_commodity_id', $order_commodity_ids);
        $this->db->order_by('detection_template.id');
        $res = $this->db->get('order_commodity_template');
        if ($res && $res->num_rows() > 0) {
            $data = $res->result_array();

            for ($i = 0; $i < count($report); $i++) {
                $report[$i]['total_project_num'] = 0;
                for ($j = 0; $j < count($data); $j++) {
                    if ($report[$i]['order_commodity_id'] == $data[$j]['order_commodity_id']) {
                        $data[$j]['name'] = $data[$j]['name'] . ' (' . $data[$j]['project_num'] . '项)';
                        $report[$i]['template_list'][] = $data[$j];
                        $report[$i]['total_project_num'] = $report[$i]['total_project_num'] + $data[$j]['project_num'];
                    }
                }
            }
        }

        return $report;
    }

    /*
     * 根据报告编号生成条形码
     */
    public function create_barcode_by_number($numbers)
    {
        if (empty($numbers) || !is_array($numbers)) {
            return FALSE;
        }
        $path = FCPATH . 'source/download/barcode/';

        foreach ($numbers as $number) {
            $this->jys_barcode->create_barcode($number, $path);
        }

        return $path;
    }

    /**
     * 根据报告编号查询报告是否存在,存在则获取报告信息、可选择的检测项目
     */
    public function check_report($number)
    {
        $data = ['success' => FALSE, 'msg' => '查询失败'];
        if (empty($number)) {
            return FALSE;
        }
        $report_id = $this->jys_db_helper->is_exist('report', ['number' => $number]);

        if ($report_id) {
            $this->db->select('detection_project.*, detection_template.name as template_name, order_commodity_template.project_num');
            $this->db->join('order_commodity_template', 'order_commodity_template.order_commodity_id = report.order_commodity_id', 'left');
            $this->db->join('detection_project', 'detection_project.template_id = order_commodity_template.template_id', 'left');
            $this->db->join('detection_template', 'detection_project.template_id = detection_template.id', 'left');
            $this->db->where('report.id', $report_id);
            $result = $this->db->get('report');
            $new_result = array();
            if ($result && $result->num_rows() > 0) {
                foreach ($result->result_array() as $key => $value) {
                    $new_result[$value['template_id']][] = $value;
//                    $new_result[$value['template_id']]['project_num'] = $value['project_num'];
                }

                rsort($new_result);
                //检测项目信息
                $data['success'] = TRUE;
                $data['msg'] = '查询成功';
                $data['data']['project'] = $new_result;

                //报告信息
                $this->db->select('report.*,
                                   attachment.path');
//                $this->db->join('detection_template', 'detection_template.id= report.template_id', 'left');
                $this->db->join('attachment', 'attachment.id= report.attachment_id', 'left');
                $this->db->where('report.id', $report_id);
                $num = $this->db->get('report');
                if ($num && $num->num_rows() > 0) {
                    $data['data']['report'] = $num->row_array();
                    //选中的项目id
                    $data['data']['report']['project'] = explode(',', $num->row_array()['project']);
                }
            }
        }
        return $data;
    }

    /**
     * 按照条件获取需要导出的报告信息
     */
    public function get_report_info($start_create_time = '', $end_create_time = '', $has_written = '', $keyword = '', $report_id_array = [], $number_array = [])
    {
        $data = ['success' => FALSE, 'msg' => '没有订单数据'];
        $this->db->select('report.number,
                            report.id,
                           report.name,
                           gender_type.name as gender,
                           report.identity_card,
                           report.birth,
                           report.order_commodity_id,
                           smoking_type.name as smoking,
                           report.height,
                           report.weight,
                           report.phone,
                           report.address,
                           report.province,
                           report.city,
                           report.district,
                          
                           relation_type.name as blood_relationship_name,                     
                           commodity.name as commodity_name,
                           commodity_specification.name as packagetype_name,
                           commodity_center.name as commodity_specification_name,
                           erp_commodity_specification.name as erp_packagetype_name,
                           erp_commodity_center.name as erp_commodity_specification_name,
                           report.project,
                           order.create_time as order_create_time,
                           report.attachment_id,
                           order_commodity.number as order_commodity_number,
                           order_commodity.express_number,
                           order.number as order_number,
                           order.terminal_type,
                           order.address as order_address,
                           express_company.name as express_company_name');
//        $this->db->select('GROUP_CONCAT(detection_template.name) as template_name');
        $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
        $this->db->join('commodity_specification', 'commodity_specification.id = order_commodity.commodity_specification_id', 'left');
        $this->db->join('commodity_center', 'commodity_center.id = commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity_specification as erp_commodity_specification', 'erp_commodity_specification.erp_commodity_id = order_commodity.erp_commodity_id', 'left');
        $this->db->join('commodity_center as erp_commodity_center', 'erp_commodity_center.id = erp_commodity_specification.commodity_center_id', 'left');
        $this->db->join('commodity', 'commodity.id = commodity_specification.commodity_id OR commodity.id = erp_commodity_specification.commodity_id', 'left');

//        $this->db->join('commodity', 'commodity.id = order_commodity.commodity_id', 'left');
        $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
        $this->db->join('express_company', 'express_company.id = order.express_company_id', 'left');
        $this->db->join('system_code as relation_type', 'relation_type.value = report.blood_relationship and relation_type.type = "' . jys_system_code::RELATION . '"', 'left');
        $this->db->join('system_code as gender_type', "gender_type.value = report.gender AND gender_type.type = '" . jys_system_code::GENDER . "'", 'left');
        $this->db->join('system_code as smoking_type', "smoking_type.value = report.gender AND smoking_type.type = '" . jys_system_code::SMOKING . "'", 'left');
        $this->db->join('system_code as report_status_type', "report_status_type.value = report.gender AND report_status_type.type = '" . jys_system_code::REPORT_STATUS . "'", 'left');
        $this->db->join('order_commodity_template', 'order_commodity_template.order_commodity_id = report.order_commodity_id', 'left');
//        $this->db->join('detection_template', 'detection_template.id = order_commodity_template.template_id', 'left');
//        $this->db->join('detection_template', 'detection_template.id = report.template_id', 'left');

        $this->db->order_by('report.create_time', 'DESC');

        if (!empty($keyword)) {
            // 关键字模糊查找
            $this->db->group_start();
            $this->db->like('order.number', $keyword);
            $this->db->or_like('report.name', $keyword);
            $this->db->or_like('report.number', $keyword);
            $this->db->or_like('commodity.name', $keyword);
            $this->db->or_like('report.age', $keyword);
            $this->db->or_like('report.phone', $keyword);
            $this->db->or_like('report.identity_card', $keyword);
            $this->db->group_end();
        }
        if (!empty($start_create_time)) {
            $this->db->where('order.create_time >=', $start_create_time);
        }
        if (!empty($end_create_time)) {
            $this->db->where('order.create_time <=', $end_create_time);
        }
        if (!empty($report_id_array) && $report_id_array[0] != "") {
            $this->db->where_in('report.id', $report_id_array);
        }
        if (!empty($number_array) && $number_array[0] != "") {
            $this->db->where_in('report.number', $number_array);
        }
        if (isset($has_written) && intval($has_written) == 1) {
            $this->db->where('report.report_status', '1');
        } else if (isset($has_written) && intval($has_written) == 0) {
            $this->db->where('report.report_status', '0');
        }
        $this->db->group_by('report.number');
        $result = $this->db->get('report');
        if ($result && $result->num_rows() > 0) {
            $result = $result->result_array();
            // 获取对应的模版
            $ids = array();
            foreach ($result as $item) {
                $ids[] = $item['order_commodity_id'];
            }

            $template_list = $this->get_template_by_order_commodity_ids($ids);
            foreach ($result as $key => $item) {
                $template_name = "";
                if (isset($template_list[$item['order_commodity_id']]) && !empty($template_list[$item['order_commodity_id']])) {
                    $result[$key]['template_list'] = $template_list[$item['order_commodity_id']];
                    foreach ($template_list[$item['order_commodity_id']] as $value) {
                        $template_name .= $value['name'] . ",";
                    }
                    if (strlen($template_name) > 0) {
                        $template_name = substr($template_name, 0, strlen($template_name) - 1);
                    }
                    $result[$key]['template_name'] = $template_name;
                } else {
                    $result[$key]['template_list'] = array();
                    $result[$key]['template_name'] = "";
                }
            }


            // 获取所有的检测项目
            $project_result = $this->jys_db_helper->all('detection_project');
            $projects = [];
            if ($project_result['success'] && !empty($project_result['data'])) {
                foreach ($project_result['data'] as $item) {
                    $projects[$item['id']] = $item;
                }
            }

            foreach ($result as $index => $value) {
                //判断是否为ERP订单
                if (!empty($result[$index]['commodity_specification_name']) && !empty($result[$index]['packagetype_name'])) {
                    $result[$index]['commodity_name'] = $value['commodity_name'] . '+' . $value['commodity_specification_name'] . '+' . $value['packagetype_name'];
                } else if (!empty($result[$index]['erp_commodity_specification_name']) && !empty($result[$index]['erp_packagetype_name'])) {
                    $result[$index]['commodity_name'] = $value['commodity_name'] . '+' . $value['erp_commodity_specification_name'] . '+' . $value['erp_packagetype_name'];
                }

                $result[$index]['addr'] = $result[$index]['province'] . $result[$index]['city'];
                if (!empty($result[$index]['district'])) {
                    $result[$index]['addr'] .= $result[$index]['district'];
                }
                $result[$index]['addr'] .= $result[$index]['address'];

                $repo_pro = explode(",", $value['project']);
                if (!empty($repo_pro) && is_array($repo_pro)) {
                    foreach ($repo_pro as $pro_id) {
                        if (!empty($pro_id) && intval($pro_id) > 0) {
                            $result[$index]['project_list'][] = $projects[$pro_id];
                        }
                    }
                }
            }
            if (!empty($report_id_array) && $report_id_array[0] != "") {
                $result = $this->sort_report_list_by_id_list($result, $report_id_array);
            } else if (!empty($number_array) && $number_array[0] != "") {
                $result = $this->sort_report_list_by_number_list($result, $number_array);
            }

            $data['success'] = TRUE;
            $data['data'] = $result;
            $data['msg'] = '查询成功';
        }
        return $data;
    }

    /**
     * 根据检测码获取报告信息
     * @param null $report_number 检测码
     */
    public function get_report_by_number($report_number = NULL)
    {
        $result = array('success' => FALSE, 'msg' => '获取检测码信息失败', 'data' => array());

        if (empty($report_number)) {
            $result['msg'] = '请输入要查询的检测码';
            return $result;
        }

        $this->db->select('report.*,
                           order.id as order_id,
                           commodity.name as commodity_name,
                           order.number as order_number,
                           relation_type.name as blood_relationship_name,
                           order.create_time as order_create_time,
                           attachment.path as path');
        $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
//        $this->db->join('detection_template', 'detection_template.id = order_commodity_template.template_id', 'left');
//        $this->db->join('order_commodity_template', 'order_commodity_template.order_commodity_id = order_commodity.id', 'left');
        $this->db->join('commodity', 'commodity.id = order_commodity.commodity_id', 'left');
        $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
        $this->db->join('attachment', 'attachment.id = report.attachment_id', 'left');
        $this->db->join('system_code as relation_type', 'relation_type.value = report.blood_relationship and relation_type.type = "' . jys_system_code::RELATION . '"', 'left');
        $this->db->where('report.number', $report_number);
        $data = $this->db->get('report');
        if ($data && $data->num_rows() > 0) {
            $result['data'] = $data->row_array();
            // 获取模版信息

            $template_list = $this->get_template_by_order_commodity_ids(array($result['data']['order_commodity_id']));
            $template_name = "";
            foreach ($template_list[$result['data']['order_commodity_id']] as $value) {
                $template_name .= $value['name'] . ",";
            }
            if (strlen($template_name) > 0) {
                $result['data']['template_name'] = substr($template_name, 0, strlen($template_name) - 1);
            } else {
                $result['data']['template_name'] = "";
            }

            $result['success'] = TRUE;
            $result['msg'] = '获取报告信息成功';

        } else {
            $result['msg'] = '您所查询的报告编号不存在';
            $result['data']['number'] = $report_number;
        }

        return $result;
    }

    /**
     * 按照报告编号列表的顺序对报告列表的顺序进行排序
     * @param $report_list 报告列表
     * @param $number_list 报告编号列表
     */
    public function sort_report_list_by_number_list($report_list, $number_list = array())
    {
        if (empty($report_list) || empty($number_list) || !is_array($report_list) || !is_array($number_list)) {
            return $report_list;
        }

        $result = array();
        foreach ($number_list as $number) {
            foreach ($report_list as $report_key => $report) {
                if ($report['number'] == $number) {
                    $result[] = $report;
                }
            }
        }

        return $result;
    }

    /**
     * 按照报告编号列表的顺序对报告列表的顺序进行排序
     * @param array $report_list
     * @param array $id_list
     * @return array
     */
    public function sort_report_list_by_id_list($report_list, $id_list = array())
    {
        if (empty($report_list) || empty($id_list) || !is_array($report_list) || !is_array($id_list)) {
            return $report_list;
        }

        $result = array();
        foreach ($id_list as $id) {
            foreach ($report_list as $report_key => $report) {
                if ($report['id'] == $id) {
                    $result[] = $report;
                }
            }
        }

        return $result;
    }

    /**
     * 添加erp检测码
     */
    public function insert_report_number_to_erp($data = [])
    {
        if (empty($data)) {
            $result = ['success' => FALSE, 'msg' => '没有要插入的数据'];
            return $result;
        }
        $add_data = array();
        //获取erp订单id和erp子订单id
        $this->db->select('order_commodity.order_id as dssadocid,
                          order_commodity.id as dssadtlid,
                          order_commodity.erp_docid as erpdocid,
                          order_commodity.erp_dtlid as erpdtlid
                          ');
        $this->db->where('order_commodity.id', $data[0]['order_commodity_id']);
        $report_result = $this->db->get('order_commodity');
        if ($report_result && $report_result->num_rows() > 0) {
            $add_info = $report_result->result_array();
            foreach ($data as $key => $value) {
                $add_data[$key]['dssadocid'] = $add_info[0]['dssadocid'];
                $add_data[$key]['dssadtlid'] = $add_info[0]['dssadtlid'];
                $add_data[$key]['erpdocid'] = $add_info[0]['erpdocid'];
                $add_data[$key]['erpdtlid'] = $add_info[0]['erpdtlid'];
                $add_data[$key]['test_code'] = $value['number'];
                $add_data[$key]['usestatus'] = 2;
                $add_data[$key]['updatetime'] = date("Y-m-d H:i:s");
            }
        }
        //添加报告
        if (!empty($add_data)) {
            $report_info = $this->jys_soap->inspection_information_to_erp($add_data, 0);
        }
        if (isset($report_info['returnCode']) && $report_info['returnCode'] == 1) {
            $result = ['success' => TRUE, 'msg' => '插入数据成功'];
            if (!empty($add_data)) {
                foreach ($add_data as $key => $value) {
                    $add[$key] = [
                        'success' => jys_system_code::ERP_STATUS_SUCCESS,
                        'msg' => '检测码为：' . $value['test_code'] . '的检测码(DS-ERP)添加成功',
                        'interface_name' => jys_system_code::ERP_NAME_DETECTION_CODE_DS_ERP,
                        'code' => jys_system_code::ERP_CODE_DS03,
                        'level' => jys_system_code::ERP_RETURN_STATUS_SUCCESS,
                        'create_time' => date("Y-m-d H:i:s"),
                        'id' => $this->jys_tool->uuid()
                    ];
                }
                //添加日志
                $log_res = $this->jys_db_helper->add_batch('log', $add);
            }
        } else {
            $result = ['success' => FALSE, 'msg' => $report_info['returnMsg']];
            if (!empty($add_data)) {
                foreach ($add_data as $key => $value) {
                    $add[$key] = [
                        'success' => jys_system_code::ERP_STATUS_FAIL,
                        'msg' => '检测码为：' . $data['test_code'] . '的检测码(DS-ERP)添加失败。(' . $report_info['returnMsg'] . ')',
                        'interface_name' => jys_system_code::ERP_NAME_DETECTION_CODE_DS_ERP,
                        'code' => jys_system_code::ERP_CODE_DS03,
                        'level' => jys_system_code::ERP_RETURN_STATUS_SUCCESS,
                        'create_time' => date("Y-m-d H:i:s"),
                        'id' => $this->jys_tool->uuid()
                    ];
                }
                //添加日志
                $log_res = $this->jys_db_helper->add_batch('log', $add);
            }
        }

        return $result;
    }

    /**
     * 更新erp检测信息
     */
    public function insert_report_to_erp($data = [])
    {
        if (empty($data) || !is_array($data)) {
            $result = ['success' => FALSE, 'msg' => '没有要插入的数据'];
            return $result;
        }
        $result = ['success' => FALSE, 'msg' => '插入数据失败'];
        //获取订单id
        $order_info = $this->jys_db_helper->get_where('order_commodity', ['id' => $data['order_commodity_id']]);
        $update_data[0] = [
            'dssadocid' => $order_info['order_id'],
            'dssadtlid' => $order_info['id'],
            'erpdocid' => $order_info['erp_docid'],
            'erpdtlid' => $order_info['erp_dtlid'],
            'test_code' => $data['number'],
            // 'ds_cusname' => $data['name'],
            // 'height' => $data['height'],
            // 'weight' => $data['weight'],
            // 'birthday' => $data['birth'],
            // 'address' => $data['address'],
            // 'phoneno' => $data['phone'],
            // 'idcardno' => $data['identity_card'],
            'usestatus' => 2,
            'updatetime' => date("Y-m-d H:i:s")
        ];
        //姓名
        if (!empty($data['name']) && isset($data['name'])) {
            $update_data[0]['ds_cusname'] = $data['name'];
        } else {
            $update_data[0]['ds_cusname'] = '';
        }
        //身高
        if (!empty($data['height']) && isset($data['height'])) {
            $update_data[0]['height'] = $data['height'];
        } else {
            $update_data[0]['height'] = '';
        }
        //体重
        if (!empty($data['weight']) && isset($data['weight'])) {
            $update_data[0]['weight'] = $data['weight'];
        } else {
            $update_data[0]['weight'] = '';
        }
        //生日
        if (!empty($data['birth']) && isset($data['birth'])) {
            $update_data[0]['birthday'] = $data['birth'];
        } else {
            $update_data[0]['birthday'] = '';
        }
        //地址
        if (!empty($data['address']) && isset($data['address'])) {
            $update_data[0]['address'] = $data['address'];
        } else {
            $update_data[0]['address'] = '';
        }
        //电话
        if (!empty($data['phone']) && isset($data['phone'])) {
            $update_data[0]['phoneno'] = $data['phone'];
        } else {
            $update_data[0]['phoneno'] = '';
        }
        //身份证
        if (!empty($data['identity_card']) && isset($data['identity_card'])) {
            $update_data[0]['idcardno'] = $data['identity_card'];
        } else {
            $update_data[0]['idcardno'] = '';
        }
        //年龄
        if (!empty($data['age']) && isset($data['age'])) {
            $update_data[0]['age'] = $data['age'];
        } else {
            $update_data[0]['age'] = '';
        }
        //性别
        if ($data['gender'] == jys_system_code::GENDER_FEMALE) {
            $update_data[0]['sex'] = jys_system_code::GENDER_FEMALE;
        } elseif ($data['gender'] == jys_system_code::GENDER_MALE) {
            $update_data[0]['sex'] = jys_system_code::GENDER_MALE;
        }
        //判断检测项目
        if (isset($data['project']) && !empty($data['project'])) {
            $ids = explode(',', $data['project']);
            $this->db->select('id, name');
            $this->db->where_in('id', $ids);
            $detection_project_result = $this->db->get('detection_project');
            if ($detection_project_result && $detection_project_result->num_rows() > 0) {
                $project_info = $detection_project_result->result_array();
                $flag = TRUE;
                foreach ($project_info as $key => $value) {
                    if ($flag) {
                        $update_data[0]['testing_content'] = $value['name'];
                        $flag = FALSE;
                    } else {
                        $update_data[0]['testing_content'] .= ',' . $value['name'];
                    }
                }
            }
        } else {
            $update_data[0]['testing_content'] = '';
        }
        //更新检测信息
        $report_info = $this->jys_soap->inspection_information_to_erp($update_data, 1);
        if ($report_info['returnCode'] == 1) {
            $result = ['success' => TRUE, 'msg' => '更新数据成功'];
            //添加日志
            foreach ($update_data as $key => $value) {
                $add[$key] = [
                    'success' => jys_system_code::ERP_STATUS_SUCCESS,
                    'msg' => '检测码为：' . $update_data[0]['test_code'] . '的报告检测信息(DS-ERP)添加成功',
                    'interface_name' => jys_system_code::ERP_NAME_DETECTION_INFORMATION_DS_ERP,
                    'code' => jys_system_code::ERP_CODE_DS03,
                    'create_time' => date("Y-m-d H:i:s"),
                    'id' => $this->jys_tool->uuid()
                ];
            }
            $log_res = $this->jys_db_helper->add_batch('log', $add);
        } else {
            $result = ['success' => FALSE, 'msg' => $report_info['returnMsg']];
            //添加日志
            foreach ($update_data as $key => $value) {
                $add[$key] = [
                    'success' => jys_system_code::ERP_STATUS_FAIL,
                    'msg' => '检测码为：' . $data['test_code'] . '的报告检测信息(DS-ERP)添加失败。(' . $report_info['returnMsg'] . ')',
                    'interface_name' => jys_system_code::ERP_NAME_DETECTION_INFORMATION_DS_ERP,
                    'code' => jys_system_code::ERP_CODE_DS03,
                    'create_time' => date("Y-m-d H:i:s"),
                    'id' => $this->jys_tool->uuid()
                ];
            }
            $log_res = $this->jys_db_helper->add_batch('log', $add);
        }

        return $result;
    }

    /**
     * 更新erp报告上传人
     */
    public function insert_report_user_info_to_erp($number = [])
    {
        $time = date("Y-m-d H:i:s");
        $result = ['success' => FALSE, 'msg' => '插入数据失败'];
        if (empty($number) || !is_array($number)) {
            $result = ['success' => FALSE, 'msg' => '没有要插入的数据'];
            return $result;
        }
        $this->db->select('order_commodity.order_id as said,
                          order_commodity.id as sadtlid,
                          report.number as test_code,
                          order.terminal_type
                          ');
        $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
        $this->db->join('order', 'order.id = order_commodity.order_id', 'left');
        $this->db->where_in('report.number', $number);
        $report_result = $this->db->get('report');
        if ($report_result && $report_result->num_rows() > 0) {
            $data = $report_result->result_array();
            foreach ($data as $terminal_key => $terminal_value) {
                if ($terminal_value['terminal_type'] == jys_system_code::TERMINAL_TYPE_PC || $terminal_value['terminal_type'] == jys_system_code::TERMINAL_TYPE_WEIXIN) {
                    $data[$terminal_key]['ordertype'] = 1;
                } elseif ($terminal_value['terminal_type'] == jys_system_code::TERMINAL_TYPE_LINE || empty($terminal_value['terminal_type'])) {
                    $data[$terminal_key]['ordertype'] = 2;
                }
                $data[$terminal_key]['uploadtime'] = $time;
                $data[$terminal_key]['reportmanid'] = $_SESSION['user_id'];
                $data[$terminal_key]['reportman'] = $_SESSION['username'];
                unset($data[$terminal_key]['terminal_type']);
            }

        }
        //报告回传
        $report_info = $this->jys_soap->report_status_to_erp($data);
        if ($report_info['returnCode'] == 1) {
            $result = ['success' => TRUE, 'msg' => '更新数据成功'];
            //添加日志
            foreach ($data as $key => $value) {
                $add[$key] = [
                    'success' => jys_system_code::ERP_STATUS_SUCCESS,
                    'msg' => '检测码为：' . $value['test_code'] . '的上传报告人信息(DS-ERP)添加成功',
                    'interface_name' => jys_system_code::ERP_NAME_REPORT_RETURN_DS_ERP,
                    'code' => jys_system_code::ERP_CODE_DS04,
                    'create_time' => date("Y-m-d H:i:s"),
                    'id' => $this->jys_tool->uuid()
                ];
            }
            $log_res = $this->jys_db_helper->add_batch('log', $add);
        } else {
            $result = ['success' => FALSE, 'msg' => $report_info['returnMsg']];
            //添加日志
            foreach ($data as $key => $value) {
                $add[$key] = [
                    'success' => jys_system_code::ERP_STATUS_FAIL,
                    'msg' => '检测码为：' . $value['test_code'] . '的上传报告检测信息(DS-ERP)添加失败。(' . $report_info['returnMsg'] . ')',
                    'interface_name' => jys_system_code::ERP_NAME_REPORT_RETURN_DS_ERP,
                    'code' => jys_system_code::ERP_CODE_DS04,
                    'create_time' => date("Y-m-d H:i:s"),
                    'id' => $this->jys_tool->uuid()
                ];
            }
            $log_res = $this->jys_db_helper->add_batch('log', $add);
        }

        return $result;
    }

    /**
     * 删除erp报告
     */
    public function delete_report_to_erp($number = [])
    {
        $result = ['success' => FALSE, 'msg' => '删除数据失败'];
        $report_info = $this->jys_soap->delete_report_to_erp($number);
        if ($report_info['returnCode'] == 1) {
            $result = ['success' => TRUE];
            //添加日志
            foreach ($number as $key => $value) {
                $add[$key] = [
                    'success' => jys_system_code::ERP_STATUS_SUCCESS,
                    'msg' => '检测码为：' . $value['test_code'] . '的报告(DS-ERP)删除成功',
                    'interface_name' => jys_system_code::ERP_NAME_DETECTION_REPORT_CODE_DS_ERP,
                    'code' => jys_system_code::ERP_CODE_DS05,
                    'create_time' => date("Y-m-d H:i:s"),
                    'id' => $this->jys_tool->uuid()
                ];
            }
            $log_res = $this->jys_db_helper->add_batch('log', $add);
        } else {
            //添加日志
            foreach ($number as $key => $value) {
                $add[$key] = [
                    'success' => jys_system_code::ERP_STATUS_FAIL,
                    'msg' => '检测码为：' . $value['test_code'] . '的报告(DS-ERP)删除失败。(' . $report_info['returnMsg'] . ')',
                    'interface_name' => jys_system_code::ERP_NAME_DETECTION_REPORT_CODE_DS_ERP,
                    'code' => jys_system_code::ERP_CODE_DS05,
                    'create_time' => date("Y-m-d H:i:s"),
                    'id' => $this->jys_tool->uuid()
                ];
            }
            $log_res = $this->jys_db_helper->add_batch('log', $add);
        }

        return $result;
    }

    /**
     * 删除erp检测码
     */
    public function delete_report_number_to_erp($number = [])
    {
        $result = ['success' => FALSE, 'msg' => '删除数据失败'];
        $report_info = $this->jys_soap->delete_report_number_to_erp($number);
        if ($report_info['returnCode'] == 1) {
            $result = ['success' => TRUE];
            //添加日志
            foreach ($number as $key => $value) {
                $add[$key] = [
                    'success' => jys_system_code::ERP_STATUS_SUCCESS,
                    'msg' => '检测码为：' . $value['test_code'] . '的报告(DS-ERP)删除成功',
                    'interface_name' => jys_system_code::ERP_NAME_DETECTION_DELCT_CODE_DS_ERP,
                    'code' => jys_system_code::ERP_CODE_DS06,
                    'create_time' => date("Y-m-d H:i:s"),
                    'id' => $this->jys_tool->uuid()
                ];
            }
            $log_res = $this->jys_db_helper->add_batch('log', $add);
        } else {
            //添加日志
            foreach ($number as $key => $value) {
                $add[$key] = [
                    'success' => jys_system_code::ERP_STATUS_FAIL,
                    'msg' => '检测码为：' . $value['test_code'] . '的报告(DS-ERP)删除失败。(' . $report_info['returnMsg'] . ')',
                    'interface_name' => jys_system_code::ERP_NAME_DETECTION_DELCT_CODE_DS_ERP,
                    'code' => jys_system_code::ERP_CODE_DS06,
                    'create_time' => date("Y-m-d H:i:s"),
                    'id' => $this->jys_tool->uuid()
                ];
            }
            $log_res = $this->jys_db_helper->add_batch('log', $add);
        }

        return $result;
    }

    /**
     * 根据检测码获取报告信息
     * @param null $report_number 检测码
     */
    public function get_temlate_by_order_commodity_id($ids = [])
    {

        $data = $this->get_template_by_order_commodity_ids($ids);
        if (empty($data)) {
            return array();
        }
        $result = array();
        foreach ($data as $key => $value) {
            foreach ($value as $item) {
                $result[$key][] = $item['hy_template_id'];
            }
        }
        foreach ($result as $key => $value) {
            $result[$key] = implode(',', $value);
        }

        return $result;
    }

    /**
     * 根据模板id获取erp模板id
     * @param null $report_number 检测码
     */
    public function get_report_info_for_rest($project_id)
    {
        $project_ids = explode(',', $project_id);
        $this->db->select('detection_project.hy_project_id');
        $this->db->where_in('id', $project_ids);
        $report_result = $this->db->get('detection_project');
        if ($report_result && $report_result->num_rows() > 0) {
            $template_info = $report_result->result_array();
            foreach ($template_info as $key => $value) {
                $data[] = $value['hy_project_id'];
            }
            $data = implode(',', $data);
        } else {
            $data = '';
        }

        return $data;
    }

    /**
     * 根据子订单ID数组获取子订单对应的模版信息
     * @param null $order_commodity_ids 子订单ID数组
     */
    public function get_template_by_order_commodity_ids($order_commodity_ids = NULL)
    {
        if (empty($order_commodity_ids) || !is_array($order_commodity_ids)) {
            return array();
        }

        $this->db->select("detection_template.*, order_commodity_template.order_commodity_id");
        $this->db->where_in("order_commodity_template.order_commodity_id", $order_commodity_ids);
        $this->db->join("detection_template", "order_commodity_template.template_id = detection_template.id");
        $this->db->group_by("order_commodity_template.order_commodity_id");
        $this->db->group_by("detection_template.id");
        $data = $this->db->get("order_commodity_template");

        $result = array();
        if ($data && $data->num_rows() > 0) {
            $data = $data->result_array();
            foreach ($data as $value) {
                $result[$value['order_commodity_id']][] = $value;
            }
        }
        return $result;
    }

    /**
     * 发送报告状态信息给被检测人
     * @param null $report_number_list
     */
    public function send_report_status_information_to_user($report_number_list = NULL)
    {
        if (empty($report_number_list) && !is_array($report_number_list)) {
            return FALSE;
        }
        $report_number_list = array_unique($report_number_list);

        $this->db->select("report.*");
        $this->db->where_in("report.number", $report_number_list);
        $report_list = $this->db->get("report");
        if ($report_list && $report_list->num_rows() > 0) {
            $report_list = $report_list->result_array();

            $report_id_list = array();
            foreach ($report_list as $item) {
                $report_id_list[] = $item['id'];
            }
            // 获取发送记录
            $this->db->select("report_inform.*, report.number");
            $this->db->join('report', 'report.id = report_inform.report_id', 'left');
            $this->db->where_in("report.number", $report_number_list);
            $report_inform_list = $this->db->get('report_inform');
            if ($report_inform_list && $report_inform_list->num_rows() > 0) {
                $report_inform_list = $report_inform_list->result_array();
                $report_informs = array();
                foreach ($report_inform_list as $item) {
                    $report_informs[$item['report_id']][$item['status_id']][$item['channel']] = $item;
                }
            } else {
                $report_informs = array();
            }
            // 对比数据
            $message_list = array();
            foreach ($report_list as $report) {
                if (!is_null($report['attachment_id']) && intval($report['attachment_id']) > 0) {
                    // 报告已发布
                    $this->structure_message_list($message_list, $report_informs, $report, jys_system_code::REPORT_INFORM_STATUS_PUBLISHED, jys_system_code::REPORT_INFORM_CHANNEL_SMS);
                }
                if (intval($report['erp_back_up_inspection_status']) == 1) {
                    // 启用备检
                    $this->structure_message_list($message_list, $report_informs, $report, jys_system_code::REPORT_INFORM_STATUS_BACK_UP, jys_system_code::REPORT_INFORM_CHANNEL_SMS);
                }
                if (intval($report['erp_collect_status']) == jys_system_code::ERP_COLLECT_STATUS_INVALID) {
                    // 收样作废
                    $this->structure_message_list($message_list, $report_informs, $report, jys_system_code::REPORT_INFORM_STATUS_SCRAP, jys_system_code::REPORT_INFORM_CHANNEL_SMS);
                }
                if (intval($report['erp_collect_status']) == jys_system_code::ERP_COLLECT_STATUS_FINISH) {
                    // 收样送检
                    $this->structure_message_list($message_list, $report_informs, $report, jys_system_code::REPORT_INFORM_STATUS_DETECTION, jys_system_code::REPORT_INFORM_CHANNEL_SMS);
                }
//                if (intval($report['erp_collect_status']) == jys_system_code::ERP_COLLECT_STATUS_SUBMIT) {
//                    // 收样已提交
//                    $this->structure_message_list($message_list, $report_informs, $report, jys_system_code::REPORT_INFORM_STATUS_SUBMIT, jys_system_code::REPORT_INFORM_CHANNEL_SMS);
//                }
                if (intval($report['report_status']) == jys_system_code::REPORT_STATUS_COMMITTED) {
                    // 样本信息已登记
                    $this->structure_message_list($message_list, $report_informs, $report, jys_system_code::REPORT_INFORM_STATUS_REGISTERED, jys_system_code::REPORT_INFORM_CHANNEL_SMS);
                }
            }

            // 发送短信
            $msm_result = $this->send_report_status_inform_by_sms($message_list);

            // 存储发送结果
            $this->jys_db_helper->add_batch('report_inform', $msm_result);

        }
    }

    /**
     * 比对报告状态已发送数据，构建本次需要发送的通知列表
     * @param $message_list 通知列表
     * @param $report_informs 当前已经发送过的报告通知记录
     * @param $report_info 报告信息
     * @param $report_inform_status 报告通知状态
     * @param $channel 通知渠道
     */
    private function structure_message_list(&$message_list, $report_informs, $report_info, $report_inform_status, $channel)
    {
        if (is_null($message_list) || !is_array($message_list) || !is_array($report_informs) || empty($report_info) || !is_array($report_info) || empty($report_inform_status) || empty($channel)) {
            return TRUE;
        }

        if (!isset($report_informs[$report_info['id']][$report_inform_status][$channel])) {
            // 当前报告，在当前状态的当前渠道，之前没有发送过短信
            $message_list[$report_inform_status][$channel][] = $report_info;
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 通过短信发送报告状态通知
     * @param $message_list 发送信息列表
     * @return array 已经发送成功的报告通知信息，做为插入数据库的依据
     */
    private function send_report_status_inform_by_sms($message_list)
    {
        $result = array();
        if (empty($message_list) || !is_array($message_list)) {
            return $result;
        }
        $channel = jys_system_code::REPORT_INFORM_CHANNEL_SMS;

        if (isset($message_list[jys_system_code::REPORT_INFORM_STATUS_REGISTERED][$channel])) {
            // 样本信息已登记
            $sms_template = '【赛安基因城】您的检测信息已登记。登记姓名：{$var}，样本编号：{$var}。温馨提醒：请妥善保存此短信，以便日后查询报告。因检测项目不同，赛安基因将在收样后2-5周出具电子检测报告。搜索公众号“赛安基因城”点击“检测服务”进行查询。';
            $sms_content = "";
            foreach ($message_list[jys_system_code::REPORT_INFORM_STATUS_REGISTERED][$channel] as $item) {
                if (!empty($item['phone'])) {
                    $inform['report_id'] = $item['id'];
                    if (!empty($item['enter_information_time'])) {
                        $time = $item['enter_information_time'];
                    } else {
                        $time = date("y-m-d H:i:s");
                    }
                    $inform['status_id'] = jys_system_code::REPORT_INFORM_STATUS_REGISTERED;
                    $inform['time'] = $time;
                    $inform['channel'] = $channel;
                    $inform['create_time'] = date("y-m-d H:i:s");
                    $result[] = $inform;
                    $sms_content .= $item['phone'] . ',' . $item['name'] . ',' . $item['number'] . ',';
                }
            }

            if (strlen($sms_content) > 0) {
                $sms_content = substr($sms_content, 0, strlen($sms_content) - 1);
                $this->jys_message->send_variable_message($sms_template, $sms_content);
            }
        }
        if (isset($message_list[jys_system_code::REPORT_INFORM_STATUS_SUBMIT][$channel])) {
            // 收样已提交
            $sms_template = "";
            $sms_content = "";
            foreach ($message_list[jys_system_code::REPORT_INFORM_STATUS_SUBMIT][$channel] as $item) {
                if (!empty($item['phone'])) {
                    $inform['report_id'] = $item['id'];
                    if (!empty($item['erp_collect_time'])) {
                        $time = $item['erp_collect_time'];
                    } else {
                        $time = date("y-m-d H:i:s");
                    }
                    $inform['status_id'] = jys_system_code::REPORT_INFORM_STATUS_SUBMIT;
                    $inform['time'] = $time;
                    $inform['channel'] = $channel;
                    $inform['create_time'] = date("y-m-d H:i:s");
                    $result[] = $inform;
                    $sms_content .= $item['phone'] . ',' . $item['name'] . ',' . $time . ',';
                }
            }

            if (strlen($sms_content) > 0) {
                $sms_content = substr($sms_content, 0, strlen($sms_content) - 1);
                $this->jys_message->send_variable_message($sms_template, $sms_content);
            }
        }
        if (isset($message_list[jys_system_code::REPORT_INFORM_STATUS_DETECTION][$channel])) {
            // 收样送检
            $sms_template = '【赛安基因城】您的样本：{$var}已收到，样本将进行初步质检后进行检测。实验室会在2-5周出具电子检测报告，请耐心等待。';
            $sms_content = "";
            foreach ($message_list[jys_system_code::REPORT_INFORM_STATUS_DETECTION][$channel] as $item) {
                if (!empty($item['phone'])) {
                    $inform['report_id'] = $item['id'];
                    if (!empty($item['erp_inspection_time'])) {
                        $time = $item['erp_inspection_time'];
                    } else {
                        $time = date("y-m-d H:i:s");
                    }
                    $inform['status_id'] = jys_system_code::REPORT_INFORM_STATUS_DETECTION;
                    $inform['time'] = $time;
                    $inform['channel'] = $channel;
                    $inform['create_time'] = date("y-m-d H:i:s");
                    $result[] = $inform;
                    $sms_content .= $item['phone'] . ',' . $item['number'] . ',';
                }
            }

            if (strlen($sms_content) > 0) {
                $sms_content = substr($sms_content, 0, strlen($sms_content) - 1);
                $this->jys_message->send_variable_message($sms_template, $sms_content);
            }

        }
        if (isset($message_list[jys_system_code::REPORT_INFORM_STATUS_SCRAP][$channel])) {
            // 收样作废
            $sms_template = '【赛安基因城】因运输意外或取样方式操作不当，您的样本：{$var}无法进行检测。客服人员将会在1~2个工作日内联系您，安排新的基因检测取样，造成不便万分抱歉。详情请拨打：400-100-3908电话客服咨询。';
            $sms_content = "";
            foreach ($message_list[jys_system_code::REPORT_INFORM_STATUS_SCRAP][$channel] as $item) {
                if (!empty($item['phone'])) {
                    $inform['report_id'] = $item['id'];
                    if (!empty($item['erp_back_up_cancel_time'])) {
                        $time = $item['erp_back_up_cancel_time'];
                    } else {
                        $time = date("y-m-d H:i:s");
                    }
                    $inform['status_id'] = jys_system_code::REPORT_INFORM_STATUS_SCRAP;
                    $inform['time'] = $time;
                    $inform['channel'] = $channel;
                    $inform['create_time'] = date("y-m-d H:i:s");
                    $result[] = $inform;
                    $sms_content .= $item['phone'] . ',' . $item['number'] . ',';
                }
            }

            if (strlen($sms_content) > 0) {
                $sms_content = substr($sms_content, 0, strlen($sms_content) - 1);
                $this->jys_message->send_variable_message($sms_template, $sms_content);
            }
        }
        if (isset($message_list[jys_system_code::REPORT_INFORM_STATUS_BACK_UP][$channel])) {
            // 启用备检
            $sms_template = '【赛安基因城】您的样本：{$var}初检失败，我们将调取备用样本进行检测。项目检测周期将延长1至3周，造成不便万分抱歉。';
            $sms_content = "";
            foreach ($message_list[jys_system_code::REPORT_INFORM_STATUS_BACK_UP][$channel] as $item) {
                if (!empty($item['phone'])) {
                    $inform['report_id'] = $item['id'];
                    if (!empty($item['erp_back_up_inspection_time'])) {
                        $time = $item['erp_back_up_inspection_time'];
                    } else {
                        $time = date("y-m-d H:i:s");
                    }
                    $inform['status_id'] = jys_system_code::REPORT_INFORM_STATUS_BACK_UP;
                    $inform['time'] = $time;
                    $inform['channel'] = $channel;
                    $inform['create_time'] = date("y-m-d H:i:s");
                    $result[] = $inform;
                    $sms_content .= $item['phone'] . ',' . $item['number'] . ',';
                }
            }

            if (strlen($sms_content) > 0) {
                $sms_content = substr($sms_content, 0, strlen($sms_content) - 1);
                $this->jys_message->send_variable_message($sms_template, $sms_content);
            }
        }
        if (isset($message_list[jys_system_code::REPORT_INFORM_STATUS_PUBLISHED][$channel])) {
            // 报告已发布
            $sms_template = '【赛安基因城】感谢您选择我们的服务，您的基因检测报告已经出具（{$var}）。您可关注的公众号“赛安基因城”点击“检测服务-报告查询”进行在线查询。快速查询地址： http://suo.im/3QOsfp';
            $sms_content = "";
            foreach ($message_list[jys_system_code::REPORT_INFORM_STATUS_PUBLISHED][$channel] as $item) {
                if (!empty($item['phone'])) {
                    $inform['report_id'] = $item['id'];
                    if (!empty($item['report_attachment_upload_time'])) {
                        $time = $item['report_attachment_upload_time'];
                    } else {
                        $time = date("y-m-d H:i:s");
                    }
                    $inform['status_id'] = jys_system_code::REPORT_INFORM_STATUS_PUBLISHED;
                    $inform['time'] = $time;
                    $inform['channel'] = $channel;
                    $inform['create_time'] = date("y-m-d H:i:s");
                    $result[] = $inform;
                    $sms_content .= $item['phone'] . ',' . $item['number'] . ',' ;
                }
            }

            if (strlen($sms_content) > 0) {
                $sms_content = substr($sms_content, 0, strlen($sms_content) - 1);
                $this->jys_message->send_variable_message($sms_template, $sms_content);
            }
        }


        return $result;
    }

     /*
     * 根据附件id获取文件路径
     */
    public function get_attachment_path_by_id($ids = [])
    {   
        if (empty($ids)) {
            return FALSE;
        }
        $this->db->select('id, path');
        $this->db->where_in('id', $ids);
        $res = $this->db->get('attachment');
        if ($res && $res->num_rows() > 0) {
            $data = $res->result_array();
        }else{
            $data = [];
        }

        return $data;
    }

    //批量删除报告
    public function batch_delete_report($numbers){
        if (empty($numbers)) {
            return ;
        }
        $this->db->where_in('number', $numbers);
        if ($this->db->delete('report')) {
            return TRUE;
        }else{
            return FALSE;
        }
    }


    /**
     * 分页获取报告状态列表
     * @param int $page 页数
     * @param int $page_size 页面大小
     * @param int $user_id 用户ID
     * @return array
     */
    public function get_report_list_by_status($page = 1, $page_size = 10, $status_id = '')
    {
        $result = array('success' => FALSE, 'msg' => '获取报告列表失败', 'data' => array(), 'total_page' => 0);

        $today =  date("Y-m-d" . " 00:00:00");
        $yesterday = date("Y-m-d" . " 00:00:00", strtotime("-1 days"));
        if (intval($page) < 1 || intval($page_size) < 1) {
            $result['msg'] = '分页参数错误';
            return $result;
        }
        $this->db->select('
            report.id,
            report.order_commodity_id,
            report.number,
            report.name,
            report.phone,
            report.project,
            report.download_amount,
            report.report_status,
            report.erp_collect_status,
            report.erp_back_up_inspection_status,
            report.erp_report_back_status,
            report.create_time,
            report.update_time,
            report.enter_information_time,
            report.erp_collect_time,
            report.erp_inspection_time,
            report.erp_back_up_inspection_time,
            report.report_attachment_upload_time,
            report.erp_back_up_cancel_time,
            online_commodity_specification.name as online_commodity_name,
            not_online_commodity_specification.name as not_online_commodity_name
        ');
        $this->db->join('order_commodity', 'order_commodity.id = report.order_commodity_id', 'left');
        $this->db->join('commodity_specification as online_commodity_specification', 'order_commodity.commodity_specification_id = online_commodity_specification.id', 'left');
        $this->db->join('commodity_specification as not_online_commodity_specification', 'order_commodity.erp_commodity_id = not_online_commodity_specification.erp_commodity_id', 'left');
        // $this->db->join('system_code as report_status', 'report_status.value = report.report_status and report_status.type = "' . jys_system_code::REPORT_STATUS . '"', 'left');
        //已登记
        if (intval($status_id) == jys_system_code::REPORT_STATUS_COMMITTED && !empty($status_id)) {
            $this->db->where('report.report_status', $status_id);
        }
        //未登记
        if (intval($status_id) == jys_system_code::REPORT_STATUS_UNCOMMITTED && !empty($status_id)) {
            $this->db->where('report.report_status', $status_id);
        }
        //已收样
        if (intval($status_id) == jys_system_code::ERP_COLLECT_STATUS_SUBMIT && !empty($status_id)) {
            $this->db->where('report.erp_collect_status', $status_id);
        }
        //已送检
        if (intval($status_id) == jys_system_code::ERP_COLLECT_STATUS_FINISH && !empty($status_id)) {
            $this->db->where('report.report_status', $status_id);
        }
        //重检中
        if (intval($status_id) == jys_system_code::ERP_BACK_UP_INSPECTION_STATUS_IN && !empty($status_id)) {
            $this->db->where('report.erp_back_up_inspection_status', $status_id);
        }
        //已出报告
        if (intval($status_id) == jys_system_code::ERP_REPORT_BACK_STATUS_IN && !empty($status_id)) {
            $this->db->where('report.erp_report_back_status', $status_id);
        }
        //已作废
        if (intval($status_id) == jys_system_code::ERP_COLLECT_STATUS_INVALID && !empty($status_id)) {
            $this->db->where('report.erp_collect_status', $status_id);
        }
        $this->db->order_by('report.create_time', 'DESC');
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $report_result = $this->db->get('report');

        if ($report_result && $report_result->num_rows() > 0) {
            $report = $report_result->result_array();
            $data = $this->del_report_data($report);
            $result['success'] = TRUE;
            $result['msg'] = '查询报告成功';
            $result['data'] = $data;
            // aa($data);
            $this->db->select('report.id');
            //已登记
            if (intval($status_id) == jys_system_code::REPORT_STATUS_COMMITTED && !empty($status_id)) {
                $this->db->where('report.report_status', $status_id);
            }
            //未登记
            if (intval($status_id) == jys_system_code::REPORT_STATUS_UNCOMMITTED && !empty($status_id)) {
                $this->db->where('report.report_status', $status_id);
            }
            //已收样
            if (intval($status_id) == jys_system_code::ERP_COLLECT_STATUS_SUBMIT && !empty($status_id)) {
                $this->db->where('report.erp_collect_status', $status_id);
            }
            //已送检
            if (intval($status_id) == jys_system_code::ERP_COLLECT_STATUS_FINISH && !empty($status_id)) {
                $this->db->where('report.report_status', $status_id);
            }
            //重检中
            if (intval($status_id) == jys_system_code::ERP_BACK_UP_INSPECTION_STATUS_IN && !empty($status_id)) {
                $this->db->where('report.erp_back_up_inspection_status', $status_id);
            }
            //已出报告
            if (intval($status_id) == jys_system_code::ERP_REPORT_BACK_STATUS_IN && !empty($status_id)) {
                $this->db->where('report.erp_report_back_status', $status_id);
            }
            //已作废
            if (intval($status_id) == jys_system_code::ERP_COLLECT_STATUS_INVALID && !empty($status_id)) {
                $this->db->where('report.erp_collect_status', $status_id);
            }
            $page_data = $this->db->get('report');
            if ($page_data && $page_data->num_rows() > 0) {
                $total_num = $page_data->num_rows();
                $result['total_page'] = ceil($total_num / $page_size * 1.0);
            } else {
                $result['total_page'] = 1;
            }
        } else {
            $result['msg'] = '未查询到相关报告';
        }

        return $result;
    }

    //处理报告数据
    public function del_report_data($report = []){
        $today =  date("Y-m-d" . " 00:00:00");
        $yesterday = date("Y-m-d" . " 00:00:00", strtotime("-1 days"));
        if (empty($report) || !is_array($report)) {
            return ;
        }
        //组装最新时间 最新状态 项目名称
        foreach ($report as $key => $value) {
            //组装项目名称
            if (!empty($value['project'])) {
                $project_ids = explode(',', $value['project']);
                //查找项目
                $report[$key]['project'] = $this->get_project_name_by_id($project_ids);
            }
            //已作废
            if (!empty($value['erp_back_up_cancel_time']) && $value['erp_collect_status'] == jys_system_code::ERP_COLLECT_STATUS_INVALID) {
                $report[$key]['current_time'] = $value['erp_back_up_cancel_time'];
                $report[$key]['current_status'] = '检测失败';
                continue;
            }
            //已出报告
            if (!empty($value['report_attachment_upload_time']) && $value['erp_report_back_status'] == jys_system_code::ERP_REPORT_BACK_STATUS_IN) {
                $report[$key]['current_time'] = $value['report_attachment_upload_time'];
                $report[$key]['current_status'] = '已出报告';
                continue;
            }
            //已备管
            if (!empty($value['erp_back_up_inspection_time']) && $value['erp_back_up_inspection_status'] == jys_system_code::ERP_BACK_UP_INSPECTION_STATUS_IN) {
                $report[$key]['current_time'] = $value['erp_back_up_inspection_time'];
                $report[$key]['current_status'] = '重检中';
                continue;
            }
            //已送检
            if (!empty($value['erp_inspection_time']) && $value['erp_collect_status'] == jys_system_code::ERP_COLLECT_STATUS_FINISH) {
                $report[$key]['current_time'] = $value['erp_inspection_time'];
                $report[$key]['current_status'] = '已送检';
                continue;
            }
            //已收样
            if (!empty($value['erp_collect_time']) && $value['erp_report_back_status'] == jys_system_code::ERP_COLLECT_STATUS_SUBMIT) {
                $report[$key]['current_time'] = $value['erp_collect_time'];
                $report[$key]['current_status'] = '已收样';
                continue;
            }
            //已登记
            if (!empty($value['update_time']) && $value['report_status'] == jys_system_code::REPORT_STATUS_COMMITTED) {
                $report[$key]['current_time'] = $value['update_time'];
                $report[$key]['current_status'] = '已登记';
                continue;
            }
            //未登记
            if (!empty($value['create_time']) && $value['report_status'] == jys_system_code::REPORT_STATUS_UNCOMMITTED) {
                $report[$key]['current_time'] = $value['create_time'];
                $report[$key]['current_status'] = '未登记';
                continue;
            }
        }
        // 按日期组装报告数据
        $data = [
            '0' => ['time' => '今日'],
            '1' => ['time' => '昨日'],
            '2' => ['time' => '以前']
        ];
        $i = 0;
        $j = 0;
        $k = 0;
        foreach ($report as $key => $value) {
            if ($value['create_time'] > $today) {
                $data['0']['sub_order'][$i] = $value;
                $i++;
            }elseif ($value['create_time'] > $yesterday) {
                $data['1']['sub_order'][$j] = $value;
                $j++;
            }else{
                $data['2']['sub_order'][$k] = $value;
                $k++;
            }
        }

        return $data;
    }

    /**
     * 根据模板id获取模板名称
     */
    public function get_project_name_by_id($ids)
    {
        $this->db->select('name');
        $this->db->where_in('id', $ids);
        $report_result = $this->db->get('detection_project');
        if ($report_result && $report_result->num_rows() > 0) {
            $project = $report_result->result_array();
            foreach ($project as $key => $value) {
                $data[] = $value['name'];
            }
            $data = implode('，', $data);
        } else {
            $data = '';
        }

        return $data;
    }

}