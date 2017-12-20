<?php

/**
 * =====================================================================================
 *
 *        Filename: Erp_admin.php
 *
 *     Description: ERP接口对接（包含代理商，商品，订单，报告等接口）
 *
 *         Created: 2017-09-14 15:28:28
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
class Erp_admin extends CI_Controller
{
    private $_pwd_string = '';

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_soap', 'Jys_db_helper', 'Jys_tool', 'Jys_kdniao']);
        $this->load->model(['Common_model', 'User_model', 'Commodity_model', 'Order_model', 'Report_model']);
    }

    /**
     * 从ERP同步代理商
     */
    public function synchronize_agent_from_erp()
    {
        //验证用户
        $this->form_validation->set_rules('start_time', '开始时间', 'trim|required', ['required' => '开始时间必填']);
        $this->form_validation->set_rules('end_time', '结束时间', 'trim|required', ['required' => '结束时间必填']);

        //表单验证是否通过
        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']) {
            $insert_array = [];
            $update_array = [];
            $temp_array = [];
            $insert = [];
            $update = [];
            //处理数据
            $start_time = date('Y-m-d', strtotime($this->input->post('start_time', TRUE)));
            $end_time = date('Y-m-d', strtotime($this->input->post('end_time', TRUE)));
            $result = $this->jys_soap->agent($start_time, $end_time);
            if ($result['returnCode'] == 1) {
                foreach ($result['dataList'] as $key => $value) {
                    if (empty($value['sendtime'])) {
                        $insert_array[] = $value;
                    } elseif ($value['sendtime'] < $value['updatetime']) {
                        $update_array[] = $value;
                    } else {
                        $temp_array[] = $value;
                    }
                }
                if (!empty($insert_array)) {
                    //要插入的数组循环赋值给一个新的数组
                    foreach ($insert_array as $insert_key => $insert_value) {
                        $insert[$insert_key]['erp_user_id'] = intval($insert_value['customid']);
                        $insert[$insert_key]['username'] = $insert_value['customopcode'].intval($insert_value['customid']);
                        $insert[$insert_key]['name'] = $insert_value['customname'];
                        $insert[$insert_key]['custom_no'] = $insert_value['customno'];
                        $insert[$insert_key]['create_time'] = $insert_value['credate'];
                        $insert[$insert_key]['update_time'] = $insert_value['updatetime'];
                        $insert[$insert_key]['current_point'] = 0;
                        $insert[$insert_key]['total_point'] = 0;
                        $insert[$insert_key]['role_id'] = Jys_system_code::ROLE_AGENT;
                        $insert[$insert_key]['password'] = password_hash('123456' . $this->_pwd_string, PASSWORD_DEFAULT);;  //默认密码123456
                    }
                }
                if (!empty($update_array)) {
                    //要更新的数组循环赋值给一个新的数组
                    foreach ($update_array as $update_key => $update_value) {
                        $update[$update_key]['erp_user_id'] = intval($update_value['customid']);
                        $update[$update_key]['username'] = $update_value['customopcode'];
                        $update[$update_key]['name'] = $update_value['customname'];
                        $update[$update_key]['custom_no'] = $update_value['customno'];
                        $update[$update_key]['create_time'] = $update_value['credate'];
                        $update[$update_key]['update_time'] = $update_value['updatetime'];
                    }
                }
                if(!empty($insert_array) || !empty($update_array)){
                    $data = $this->User_model->add_update_user_from_erp($insert, $update);
                }else{
                    $data['success'] = FALSE;
                    $data['msg'] = '无数据需要同步';
                }
            } else {
                $data['success'] = FALSE;
                $data['msg'] = '获取失败';
            }
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }
        echo json_encode($data);
    }


    /**
     * 从ERP同步商品
     */
    public function synchronize_commodity_from_erp()
    {
        //验证用户
        $this->form_validation->set_rules('start_time', '开始时间', 'trim|required', ['required' => '开始时间必填']);
        $this->form_validation->set_rules('end_time', '结束时间', 'trim|required', ['required' => '结束时间必填']);

        //表单验证是否通过
        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']) {
            $insert_array = [];
            $update_array = [];
            $temp_array = [];
            $insert = [];
            $update = [];
            $i = 0;   //临时循环变量
            $j = 0;   //临时循环变量
            //处理数据
            $start_time = date('Y-m-d', strtotime($this->input->post('start_time', TRUE)));
            $end_time = date('Y-m-d', strtotime($this->input->post('end_time', TRUE)));
            $result = $this->jys_soap->commodity($start_time, $end_time);
            if ($result['returnCode'] == 1) {
                foreach ($result['dataList'] as $key => $value) {
                    if (empty($value['sendtime'])) {
                        $insert_array[] = $value;
                    } elseif ($value['sendtime'] < $value['updatetime']) {
                        $update_array[] = $value;
                    } else {
                        $temp_array[] = $value;
                    }
                }
//                $new_temp_ids = ['123','124','125','126','127'];
//                $new_temp = ['name' => '15选N'];
//                $data = $this->Commodity_model->compare_commodity_name($new_temp['name'], $new_temp_ids);
                if (!empty($update_array)) {
                    //根据分类不同，合并成一个新的数组
                    foreach ($update_array as $update_key => $update_value) {
                        $update_new_array[$update_value['classname']][] = $update_value;
                    }
                    foreach ($update_new_array as $temp_key => $temp_value) {
                        $need_to_compare_name['name'] = $temp_key;
                        $data = $this->Commodity_model->compare_commodity_name($need_to_compare_name['name'], $temp_value);
                    }
                }
                if (!empty($insert_array)) {
                    //根据分类不同，合并成一个新的数组
                    foreach ($insert_array as $insert_key => $insert_value) {
                        $insert_new_array[$insert_value['classname']][] = $insert_value;
                    }
                    //要插入的数组循环赋值给一个新的数组
                    foreach ($insert_new_array as $insert_key => $insert_value) {
                        //这里是把商品的分类独立出来，也就是商城商品一级
                        $insert_commodity['name'] = trim($insert_key);
                        $insert_commodity['number'] = md5(time() . $insert_key);
                        $insert_commodity['is_point'] = 0;   //默认为现金商品
                        $insert_commodity['introduce'] = $insert_key;   //默认为商品名字
                        $insert_commodity['detail'] = $insert_key;   //默认为商品名字
//                        $insert_commodity['status_id'] = Jys_system_code::COMMODITY_STATUS_SOLDOUT;   //默认为下架
                        $insert_commodity['category_id'] = 76;     //默认分类为ERP商品
                        $insert_commodity['type_id'] = 1;     //默认类型为ERP商品
                        $insert_commodity['create_time'] = date("Y-m-d H:i:s", time());
                        $insert_commodity['update_time'] = date("Y-m-d H:i:s", time());

                        $temp = $this->jys_db_helper->is_exist('commodity', ['name' => trim($insert_key)]);
                        $temp_result['success'] = FALSE;
                        //插入商品表
                        if ($temp) {  //如果已存在，则直接返回Id
                            $temp_result['success'] = TRUE;
                            $temp_result['insert_id'] = $temp;
                        } else {
                            $temp_result = $this->jys_db_helper->add('commodity', $insert_commodity, TRUE);
                        }
                        if ($temp_result['success']) {
                            foreach ($insert_value as $temp_insert_commodity_center_key => $temp_insert_commodity_center_value) {
                                //根据不同商品名字，分类
                                $insert_commodity_center_new_array[$temp_insert_commodity_center_value['goodsname']][] = $temp_insert_commodity_center_value;
                            }
                            foreach ($insert_commodity_center_new_array as $insert_commodity_center_new_key => $insert_commodity_center_new_value) {
                                //把上边插入商品表的Id返回给商品中间表插入用
                                $insert_commodity_center['commodity_id'] = $temp_result['insert_id'];
                                $insert_commodity_center['name'] = trim($insert_commodity_center_new_key);
                                $temp_center = $this->jys_db_helper->is_exist('commodity_center', ['name' => trim($insert_commodity_center_new_key)]);
                                $temp_insert_commodity_result['success'] = FALSE;
                                //插入商品中间表
                                if ($temp_center) {  //如果已存在，则直接返回Id
                                    $temp_insert_commodity_result['success'] = TRUE;
                                    $temp_insert_commodity_result['insert_id'] = $temp_center;
                                } else {
                                    //这里是插入商品中间表
                                    $temp_insert_commodity_result = $this->jys_db_helper->add('commodity_center', $insert_commodity_center, TRUE);
                                }
                                $insert_commodity_center_name = $insert_commodity_center['name'];
                                unset($insert_commodity_center);
                                if ($temp_insert_commodity_result['success']) {
                                    foreach ($insert_commodity_center_new_value as $insert_commodity_center_key => $insert_commodity_center_value) {
                                        //组装商品规格表数组
                                        $insert_commodity_specification[$i]['commodity_id'] = $temp_result['insert_id'];
                                        $insert_commodity_specification[$i]['commodity_center_id'] = $temp_insert_commodity_result['insert_id'];
                                        $insert_commodity_specification[$i]['packagetype'] = $insert_commodity_center_value['packagetype'];
                                        $insert_commodity_specification[$i]['name'] = $insert_commodity['name'].$insert_commodity_center_name.$insert_commodity_center_value['goodsunit'].'装';
                                        $insert_commodity_specification[$i]['erp_commodity_id'] = intval($insert_commodity_center_value['goodsid']);
                                        $insert_commodity_specification[$i]['market_price'] = floatval($insert_commodity_center_value['refpricre']);
                                        $insert_commodity_specification[$i]['selling_price'] = floatval($insert_commodity_center_value['refpricre']);
                                        $insert_commodity_specification[$i]['goodsunit'] = $insert_commodity_center_value['goodsunit'];
                                        $insert_commodity_specification[$i]['erp_user_id'] = $insert_commodity_center_value['customerid'];
                                        $insert_commodity_specification[$i]['erp_user_name'] = $insert_commodity_center_value['customname'];
                                        $insert_commodity_specification[$i]['status_id'] = Jys_system_code::COMMODITY_SPECIFICATION_STATUS_DISABLED;  //默认为下架状态
                                        $insert_commodity_specification[$i]['update_time'] = $insert_commodity_center_value['updatetime'];
                                        $insert_commodity_specification[$i]['create_time'] = $insert_commodity_center_value['credate'];
                                        $i++;
                                    }
                                    $data = $this->jys_db_helper->add_batch('commodity_specification', $insert_commodity_specification, TRUE);
                                }else{
                                    //添加日志
                                    $add = [
                                        'id' => $this->jys_tool->uuid(),
                                        'success' => Jys_system_code::ERP_STATUS_FAIL,
                                        'msg' => 'ERP系统商品名为'.$insert_commodity_center_new_key.'的商品新增到商城失败',
                                        'interface_name' => jys_system_code::ERP_NAME_GOODS_INCREASE_ERP_DS,
                                        'code' => jys_system_code::ERP_CODE_BASE02,
                                        'create_time' => date("Y-m-d H:i:s"),
                                        'level' => jys_system_code::ERP_RETURN_STATUS_FAIL
                                    ];
                                    $log_res = $this->jys_db_helper->add('log', $add);
                                }
                                $i = 0;   //临时循环变量
                            }
                            unset($insert_commodity_center_new_array);
                        }else{
                            //添加日志
                            $add = [
                                'id' => $this->jys_tool->uuid(),
                                'success' => Jys_system_code::ERP_STATUS_FAIL,
                                'msg' => 'ERP系统分类名为'.$insert_key.'的商品新增到商城失败',
                                'interface_name' => jys_system_code::ERP_NAME_GOODS_INCREASE_ERP_DS,
                                'code' => jys_system_code::ERP_CODE_BASE02,
                                'create_time' => date("Y-m-d H:i:s"),
                                'level' => jys_system_code::ERP_RETURN_STATUS_FAIL
                            ];
                            $log_res = $this->jys_db_helper->add('log', $add);
                        }
                    }
                }
                if(empty($insert_array) && empty($update_array)){
                    $data['success'] = FALSE;
                    $data['msg'] = '无数据需要同步';
                }
            } else {
                $data['success'] = FALSE;
                $data['msg'] = '获取失败';
            }
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }
        echo json_encode($data);
    }

    /**
     * 从ERP同步线下订单
     */
    public function synchronize_order_from_erp()
    {
        //验证用户
        $this->form_validation->set_rules('start_time', '开始时间', 'trim|required', ['required' => '开始时间必填']);
        $this->form_validation->set_rules('end_time', '结束时间', 'trim|required', ['required' => '结束时间必填']);

        //表单验证是否通过
        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']) {
            $insert_array = [];
            $insert_total_order = [];
            $update_total_order = [];
            $kdniao_success_insert_array = [];
            $update_array = [];
            $temp_array = [];
            $insert = [];
            $update = [];
            $i = 0;   //临时循环变量
            $j = 0;   //临时循环变量
            //处理数据
            $start_time = date('Y-m-d', strtotime($this->input->post('start_time', TRUE)));
            $end_time = date('Y-m-d', strtotime($this->input->post('end_time', TRUE)));
            $result = $this->jys_soap->order_increase_to_ds($start_time, $end_time);
            if ($result['returnCode'] == 1) {
                foreach ($result['dataList'] as $total_order_key => $total_order_value) {
                    if (empty($total_order_value['sendtime'])) {
                        $insert_array[] = $total_order_value;
                    } elseif ($total_order_value['sendtime'] < $total_order_value['updatetime']) {
                        $update_array[] = $total_order_value;
                    } else {
                        $temp_array[] = $total_order_value;
                    }
                }
                if (!empty($insert_array)) {
                    //要插入的数组循环赋值给一个新的数组
                    foreach ($insert_array as $insert_total_key => $insert_total_value) {
                        //判断省市区的代码
                        $addressInfo = array();
                        $erpAddressInfo['province'] = '';
                        $erpAddressInfo['province_code'] = '';
                        $erpAddressInfo['city'] = '';
                        $erpAddressInfo['city_code'] = '';
                        $erpAddressInfo['district'] = '';
                        $erpAddressInfo['district_code'] = '';
                        $erpAddressInfo['address'] = '';
                        if (!empty($insert_total_value['address'])) {
                            $addressInfo = $this->get_map(trim($insert_total_value['address']));
                        }
                        if (!empty($addressInfo) && is_array($addressInfo)) {
                            $erpAddressInfo['name'] = $insert_total_value['contact'];
                            $erpAddressInfo['phone'] = $insert_total_value['contact_tel'];
                            if (isset($addressInfo['province'])) {
                                $erpAddressInfo['province'] = $addressInfo['province'];
                                $erpAddressInfo['province_code'] = $addressInfo['province_code'];
                            }
                            if (isset($addressInfo['city'])) {
                                $erpAddressInfo['city'] = $addressInfo['city'];
                                $erpAddressInfo['city_code'] = $addressInfo['city_code'];
                            }
                            if (isset($addressInfo['district'])) {
                                $erpAddressInfo['district'] = $addressInfo['district'];
                                $erpAddressInfo['district_code'] = $addressInfo['district_code'];
                            }
                            if (isset($addressInfo['address'])) {
                                $erpAddressInfo['address'] = $addressInfo['address'];
                            }
                            $insert_total_order[$insert_total_key]['address'] = json_encode($erpAddressInfo);
                        }

                        //总订单需要的数据
                        $insert_total_order[$insert_total_key]['erp_docid'] = intval($insert_total_value['docid']);
                        $insert_total_order[$insert_total_key]['user_id'] = Jys_system_code::ERP_ORDER_USER_ID;    //默认线下订单用户
                        $insert_total_order[$insert_total_key]['number'] = md5(time() . intval($insert_total_value['docid']));
                        $insert_total_order[$insert_total_key]['total_price'] = $insert_total_value['total'];
                        $insert_total_order[$insert_total_key]['erp_user_id'] = intval($insert_total_value['customid']);
                        $insert_total_order[$insert_total_key]['payment_id'] = Jys_system_code::PAYMENT_INTEGRAL_LINE;  //默认线下支付
                        $insert_total_order[$insert_total_key]['predict_complete_time'] = $insert_total_value['predespdate'];
                        $insert_total_order[$insert_total_key]['terminal_type'] = Jys_system_code::TERMINAL_TYPE_LINE;   //默认终端为线下
                        $insert_total_order[$insert_total_key]['contact_person'] = $insert_total_value['contact'];
                        $insert_total_order[$insert_total_key]['phone'] = $insert_total_value['contact_tel'];
                        $insert_total_order[$insert_total_key]['detail_address'] = $insert_total_value['address'];
                        $insert_total_order[$insert_total_key]['create_time'] = $insert_total_value['credate'];
                        if(!empty($insert_total_value['expressno'])){ //如果有订单号，则状态为已发货
                            $insert_total_order[$insert_total_key]['express_number'] = $insert_total_value['expressno'];
                            $insert_total_order[$insert_total_key]['status_id'] = Jys_system_code::ORDER_STATUS_DELIVERED;   //状态已发货
                        } else{
                            $insert_total_order[$insert_total_key]['status_id'] = Jys_system_code::ORDER_STATUS_PAID;   //默认状态已付款
                        }
                        $insert_total_order[$insert_total_key]['express_company_name'] = $insert_total_value['expresscom'];
                        switch ($insert_total_value['expresscom'])
                        {
                            case '顺丰': $insert_total_order[$insert_total_key]['express_company_id'] = 1; //默认为顺丰快递
                                          break;
                            default :    break;
                        }
                        $insert_total_order[$insert_total_key]['update_time'] = date("Y-m-d H:i:s", time());
                        $temp_result = $this->jys_db_helper->add('order', $insert_total_order[$insert_total_key], TRUE);
                        if($temp_result['success']){
                            $kdniao_success_insert_array[] = $insert_total_order[$insert_total_key];
                            //总订单下的子订单
                            foreach ($insert_total_value['detailList'] as $insert_key => $insert_value) {
                                if ($temp_result['success']) {
                                    $insert[$i]['order_id'] = $temp_result['insert_id'];
                                    $insert[$i]['erp_docid'] = intval($insert_value['docid']);
                                    $insert[$i]['erp_dtlid'] = intval($insert_value['dtlid']);
                                    $insert[$i]['number'] = $insert_total_order[$insert_total_key]['number'] . $insert_key;
                                    $insert[$i]['erp_commodity_id'] = intval($insert_value['goodsid']);
                                    $insert[$i]['commodity_id'] = Jys_system_code::ERP_ORDER_COMMODITY_ID;  //默认线下商品
                                    $insert[$i]['price'] = $insert_value['unitprice'];
                                    $insert[$i]['amount'] = $insert_value['goodsqty'];
                                    $insert[$i]['total_price'] = floatval($insert_value['unitprice'] * $insert_value['goodsqty']);
                                    $insert[$i]['points'] = 0;
                                    $insert[$i]['zx_report'] = $insert_value['zx_report'];
                                    $insert[$i]['create_time'] = date("Y-m-d H:i:s", time());
                                }
                                $temp = $this->jys_db_helper->add('order_commodity', $insert[$i], TRUE);
                                if($temp['success']){
                                    $insert_template_status = $this->Order_model->get_template_by_order_commodity_id($temp['insert_id']);
                                }
                                ++$i;
                            }
                        }else{
                            //添加日志
                            $add = [
                                'id' => $this->jys_tool->uuid(),
                                'success' => Jys_system_code::ERP_STATUS_FAIL,
                                'msg' => 'ERP系统订单编号为'.$insert_value['docid'].'的订单新增到商城失败',
                                'interface_name' => jys_system_code::ERP_NAME_ORDER_INCREASE_ERP_DS,
                                'code' => jys_system_code::ERP_CODE_SA01,
                                'create_time' => date("Y-m-d H:i:s"),
                                'level' => jys_system_code::ERP_RETURN_STATUS_FAIL
                            ];
                            $log_res = $this->jys_db_helper->add('log', $add);
                        }

                    }
                    if(!empty(!$kdniao_success_insert_array)){
                        foreach ($kdniao_success_insert_array as $for_kdniao_num_key => $for_kdniao_num_value){
                            switch ($for_kdniao_num_value['expresscom'])
                            {
                                case '顺丰': $this->jys_kdniao->dist('SF',$for_kdniao_num_value['express_number']); //默认为顺丰快递
                                              break;
                                default :    break;
                            }
                        }
                    }
                }
                if (!empty($update_array)) {
                    //要更新的数组循环赋值给一个新的数组
                    foreach ($update_array as $update_total_key => $update_total_value) {
                        //判断省市区的代码
                        $addressInfo = array();
                        $erpAddressInfo['province'] = '';
                        $erpAddressInfo['province_code'] = '';
                        $erpAddressInfo['city'] = '';
                        $erpAddressInfo['city_code'] = '';
                        $erpAddressInfo['district'] = '';
                        $erpAddressInfo['district_code'] = '';
                        $erpAddressInfo['address'] = '';
                        if (!empty($update_total_value['address'])) {
                            $addressInfo = $this->get_map(trim($update_total_value['address']));
                        }
                        if (!empty($addressInfo) && is_array($addressInfo)) {
                            $erpAddressInfo['name'] = $update_total_value['contact'];
                            $erpAddressInfo['phone'] = $update_total_value['contact_tel'];
                            if (isset($addressInfo['province'])) {
                                $erpAddressInfo['province'] = $addressInfo['province'];
                                $erpAddressInfo['province_code'] = $addressInfo['province_code'];
                            }
                            if (isset($addressInfo['city'])) {
                                $erpAddressInfo['city'] = $addressInfo['city'];
                                $erpAddressInfo['city_code'] = $addressInfo['city_code'];
                            }
                            if (isset($addressInfo['district'])) {
                                $erpAddressInfo['district'] = $addressInfo['district'];
                                $erpAddressInfo['district_code'] = $addressInfo['district_code'];
                            }
                            if (isset($addressInfo['address'])) {
                                $erpAddressInfo['address'] = $addressInfo['address'];
                            }
                            $update_total_order[$update_total_key]['address'] = json_encode($erpAddressInfo);
                        }

                        $update_total_order[$update_total_key]['erp_docid'] = intval($update_total_value['docid']);
                        $update_total_order[$update_total_key]['user_id'] = Jys_system_code::ERP_ORDER_USER_ID;    //默认线下订单用户
                        $update_total_order[$update_total_key]['total_price'] = $update_total_value['total'];
                        $update_total_order[$update_total_key]['erp_user_id'] = intval($update_total_value['customid']);
                        $update_total_order[$update_total_key]['predict_complete_time'] = $update_total_value['predespdate'];
                        $update_total_order[$update_total_key]['contact_person'] = $update_total_value['contact'];
                        $update_total_order[$update_total_key]['phone'] = $update_total_value['contact_tel'];
                        $update_total_order[$update_total_key]['detail_address'] = $update_total_value['address'];
                        $update_total_order[$update_total_key]['create_time'] = $update_total_value['credate'];
                        if(!empty($insert_total_value['expressno'])){ //如果有订单号，则状态为已发货
                            $update_total_order[$update_total_key]['express_number'] = $update_total_value['expressno'];
                            $update_total_order[$update_total_key]['status_id'] = Jys_system_code::ORDER_STATUS_DELIVERED;   //状态已发货
                        }

                        $update_total_order[$update_total_key]['express_company_name'] = $update_total_value['expresscom'];
                        switch ($update_total_value['expresscom'])
                        {
                            case '顺丰': $update_total_order[$update_total_key]['express_company_id'] = 1; //默认为顺丰快递
                                          break;
                            default :    break;
                        }
                        $update_total_order[$update_total_key]['update_time'] = $update_total_value['updatetime'];

                        //总订单下的子订单
                        foreach ($update_total_value['detailList'] as $update_key => $update_value) {
                            $update[$j]['erp_docid'] = intval($update_value['docid']);
                            $update[$j]['erp_dtlid'] = intval($update_value['dtlid']);
                            $update[$j]['erp_commodity_id'] = intval($update_value['goodsid']);
//                            $update[$j]['name'] = $update_value['goodsname'];
                            $update[$j]['price'] = $update_value['unitprice'];
                            $update[$j]['amount'] = $update_value['goodsqty'];
                            $update[$j]['total_price'] = floatval($update_value['unitprice'] * $update_value['goodsqty']);
                            $update[$j]['zx_report'] = $update_value['zx_report'];
                            ++$j;
                        }
                    }
                }
                if(!empty($update_array)){
                    $data = $this->Order_model->add_update_orders_from_erp([], $update_total_order, $update);
                }else{
                    $data['success'] = FALSE;
                    $data['msg'] = '无数据需要同步';
                }
            } else {
                $data['success'] = FALSE;
                $data['msg'] = '获取失败';
            }
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }
        echo json_encode($data);
    }

    /**
     * 从ERP同步报废线下订单
     */
    public function synchronize_invalid_order_from_erp()
    {
        //验证用户
        $this->form_validation->set_rules('start_time', '开始时间', 'trim|required', ['required' => '开始时间必填']);
        $this->form_validation->set_rules('end_time', '结束时间', 'trim|required', ['required' => '结束时间必填']);

        //表单验证是否通过
        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']) {
            $update_total_order = [];
            $update = [];
            $i = 0;
            $j = 0;
            //处理数据
            $start_time = date('Y-m-d', strtotime($this->input->post('start_time', TRUE)));
            $end_time = date('Y-m-d', strtotime($this->input->post('end_time', TRUE)));
            $result = $this->jys_soap->order_cancel_to_ds($start_time, $end_time);
            if ($result['returnCode'] == 1) {
                if(!empty($result['dataList'])){
                    foreach ($result['dataList'] as $total_order_key => $total_order_value) {
                        //要更新的数组循环赋值给一个新的数组
                        $update_total_order[$i]['erp_docid'] = intval($total_order_value['docid']);
                        $update_total_order[$i]['user_id'] = Jys_system_code::ERP_ORDER_USER_ID;    //默认线下订单用户
                        $update_total_order[$i]['total_price'] = $total_order_value['total'];
                        $update_total_order[$i]['erp_user_id'] = intval($total_order_value['customid']);
                        $update_total_order[$i]['predict_complete_time'] = $total_order_value['predespdate'];
                        $update_total_order[$i]['create_time'] = $total_order_value['credate'];
                        $update_total_order[$i++]['update_time'] = $total_order_value['updatetime'];

                        //总订单下的子订单
                        foreach ($total_order_value['detailList'] as $update_key => $update_value) {
                            $update[$j]['erp_docid'] = intval($update_value['docid']);
                            $update[$j]['erp_dtlid'] = intval($update_value['dtlid']);
                            $update[$j]['erp_commodity_id'] = intval($update_value['goodsid']);
//                            $update[$j]['name'] = $update_value['goodsname'];
                            $update[$j]['price'] = $update_value['unitprice'];
                            $update[$j]['amount'] = $update_value['goodsqty'];
                            $update[$j]['total_price'] = floatval($update_value['unitprice'] * $update_value['goodsqty']);
                            $update[$j]['zx_report'] = $update_value['zx_report'];
                            if (intval($update_value['invalidflag']) == 1) {
                                $update[$j]['status_id'] = Jys_system_code::ORDER_COMMODITY_STATUS_CANCEL;
                                $update[$j]['update_time'] = $update_value['invalidtime'];   //作废时间作为更新时间
                            }
                            ++$j;
                        }
                    }
                    $data = $this->Order_model->add_update_orders_from_erp([], $update_total_order, $update, FALSE);
                } else{
                    $data['success'] = FALSE;
                    $data['msg'] = '无数据需要同步';
                }
            } else {
                $data['success'] = FALSE;
                $data['msg'] = '获取失败';
            }
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }
        echo json_encode($data);
    }


    public function test()
    {
        $arr['dataList'][0]['test_code'] = '170949608';
        $arr['dataList'][1]['test_code'] = 'rewgvaer';
        $arr['dataList'][2]['test_code'] = 'strdhst';
        aa($arr);
        $this->synchronize_sales_return_from_erp($arr);
    }
    /**
     * 从ERP同步退货订单
     */
    public function synchronize_sales_return_from_erp()
    {
        //验证用户
        $this->form_validation->set_rules('start_time', '开始时间', 'trim|required', ['required' => '开始时间必填']);
        $this->form_validation->set_rules('end_time', '结束时间', 'trim|required', ['required' => '结束时间必填']);

        //表单验证是否通过
        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']) {
            $update_total_order = [];
            $update_test_codes = [];
            $update = [];
            $j = 0;
            //处理数据
            $start_time = date('Y-m-d', strtotime($this->input->post('start_time', TRUE)));
            $end_time = date('Y-m-d', strtotime($this->input->post('end_time', TRUE)));
            $result = $this->jys_soap->order_refund_to_ds($start_time, $end_time);
            if ($result['returnCode'] == 1) {
                if(!empty($result['dataList'])){
                    foreach ($result['dataList'] as $total_order_key => $total_order_value) {
                        //要更新的数组循环赋值给一个新的数组
                        $update_test_codes[$total_order_key] = $total_order_value['test_code'];
                        $update_total_order[$total_order_key]['status_id'] = Jys_system_code::ORDER_COMMODITY_STATUS_CANCEL;   //标记订单为作废
                        $update_total_order[$total_order_key]['number'] = $total_order_value['test_code'];
//                        $update_total_order[$total_order_key]['update_time'] = $total_order_value['credate'];  //退货时间
                    }
                    $data = $this->Order_model->update_report_refund($update_total_order, $update_test_codes);
                } else {
                    $data['success'] = FALSE;
                    $data['msg'] = '无数据需要同步';
                }
            } else {
                $data['success'] = FALSE;
                $data['msg'] = '获取失败';
            }
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }
        echo json_encode($data);
    }

    /**
     * 获取地址接口
     * @param $address
     * @return mixed
     */
    public function get_map($address)
    {
        //GET请求
        $url = 'http://restapi.amap.com/v3/geocode/geo?key=d699aa7ed9692cdc7735e2d3337fbcfb&output=JSON&address=' . $address;

        $ch = curl_init();
        //设置请求URL
        curl_setopt($ch, CURLOPT_URL, $url);
        // 将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //设置不显示头部消息
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        $result = curl_exec($ch);
        if ($error = curl_error($ch)) {
            die($error);
        }
        curl_close($ch);

        $decode_address = array();
        $manCode = json_decode($result, TRUE);
        if ($manCode['status'] == 1 && !empty($manCode['geocodes'])) {
            if (isset($manCode['geocodes'][0]['level']) && !empty($manCode['geocodes'][0]['level'])) {
                if (!empty($manCode['geocodes'][0]['province'])) {
                    $decode_address['province'] = $manCode['geocodes'][0]['province'];
                    $decode_address['province_code'] = mb_substr($manCode['geocodes'][0]['adcode'], 0, 2) . "0000";
                }
                if (!empty($manCode['geocodes'][0]['city'])) {
                    $decode_address['city'] = $manCode['geocodes'][0]['city'];
                    $decode_address['city_code'] = mb_substr($manCode['geocodes'][0]['adcode'], 0, 4) . "00";
                }
                if (!empty($manCode['geocodes'][0]['district'])) {
                    $decode_address['district'] = $manCode['geocodes'][0]['district'];
                    $decode_address['district_code'] = $manCode['geocodes'][0]['adcode'];
                }
                $decode_address['address'] = $address;
            }
        }

        return $decode_address;
    }

    /**
     * 从ERP同步收样信息查询
     */
    public function search_report_from_erp()
    {
        $data = array('success' => FALSE, 'msg' => '操作失败');
        //验证用户
        $this->form_validation->set_rules('start_time', '开始时间', 'trim|required', ['required' => '开始时间必填']);
        $this->form_validation->set_rules('end_time', '结束时间', 'trim|required', ['required' => '结束时间必填']);

        //表单验证是否通过
        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']) {
            //处理数据
            $start_time = date('Y-m-d', strtotime($this->input->post('start_time', TRUE)));
            $end_time = date('Y-m-d', strtotime($this->input->post('end_time', TRUE)));
            $result = $this->jys_soap->search_report_from_erp($start_time, $end_time);
            if ($result['returnCode'] == 1) {
                $number_list = array();
                foreach ($result['dataList'] as $key => $value) {
                    $update[$key]['number'] = $value['test_code'];
                    $update[$key]['erp_collect_status'] = $value['systatus'];
                    $update[$key]['erp_back_up_inspection_status'] = $value['bgteststatus'];
                    $update[$key]['erp_back_up_inspection_time'] = $value['bgtesttime'];
                    $update[$key]['erp_report_back_status'] = $value['reportstatus'];
                    $update[$key]['report_attachment_upload_time'] = $value['reportuploadtiem'];
                    // $update[$key]['update_time'] = $value['sys_modifydate'];
                    $update[$key]['erp_collect_time'] = $value['sytjtime'];
                    $update[$key]['erp_inspection_time'] = $value['sywctime'];
                    $update[$key]['erp_back_up_cancel_time'] = $value['syzftime'];
                    $number_list[] = $value['test_code'];
                }
                //更新报告状态
                $result = $this->jys_db_helper->update_batch('report', $update, 'number');
                if ($result) {
                    //添加日志
                    foreach ($update as $key => $value) {
                        $add[$key] = [
                            'id' => $this->jys_tool->uuid(),
                            'success' => Jys_system_code::ERP_STATUS_SUCCESS,
                            'msg' => 'ERP系统报告编号为'.$value['number'].'的报告收样信息查询成功',
                            'interface_name' => jys_system_code::ERP_NAME_DETECTION_COLLECT_INFO_ERP_DS,
                            'code' => jys_system_code::ERP_CODE_SY01,
                            'create_time' => date("Y-m-d H:i:s"),
                            'level' => jys_system_code::ERP_RETURN_STATUS_SUCCESS
                        ];   
                    }
                    $log_res = $this->jys_db_helper->add_batch('log', $add);
                    $data['success'] = TRUE;
                    $data['msg'] = '操作成功';
                }
                if (!empty($number_list)) {
                    $this->Report_model->send_report_status_information_to_user($number_list);   
                }
            }else{
                //添加日志
                foreach ($result['dataList'] as $key => $value) {
                    $add[$key] = [
                        'id' => $this->jys_tool->uuid(),
                        'success' => Jys_system_code::ERP_STATUS_FAIL,
                        'msg' => 'ERP系统报告编号为'.$value['test_code'].'的报告收样信息查询失败',
                        'interface_name' => jys_system_code::ERP_NAME_DETECTION_COLLECT_INFO_ERP_DS,
                        'code' => jys_system_code::ERP_CODE_SY01,
                        'create_time' => date("Y-m-d H:i:s"),
                        'level' => jys_system_code::ERP_RETURN_STATUS_FAIL
                    ];   
                }
                $log_res = $this->jys_db_helper->add_batch('log', $add);
            }
        } else {
            $data['msg'] = '输入有错误' . $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 从海云接口获取pdf文件
     */
    public function upload_excel_attachment_report_info()
    {
        $data['msg'] = "接收文件失败";
        $data['success'] = FALSE;

        $this->db->trans_start();
        //上传附件
        $result = $this->jys_attachment->upload_report();
        if ($result['success']) {
            //根据文件md5判断是否重复
            $data_info = $this->jys_db_helper->get_where('attachment', ['md5' => $result['md5']]);
            if (empty($data_info)) {
                $add_info = [
                    'md5' => $result['md5'],
                    'path' => $result['path'],
                    'create_time' => date("Y-m-d H:i:s")
                ];
                $attachment_info = $this->jys_db_helper->add('attachment', $add_info, TRUE);
            } else {
                $attachment_info['insert_id'] = $data_info['id'];
            }
            // 2、根据$real_filename中的报告编号，查找到对应的报告，并将附件ID更新到其中
            $report_number = array();
            $message = array();
            if (preg_match("/[a-zA-Z0-9]*/", $result['real_filename'], $report_number)) {
                if (isset($report_number[0]) && !empty($report_number[0])) {
                    //获取报告编号
                    $number['0'] = $report_number[0];
                    //根据报告编号查找对应的报告
                    $report_result = $this->jys_db_helper->get_where('report', ['number' => $report_number[0]]);
                    if (empty($report_result['attachment_id'])) {
                        //第一次上次的报告需发短信
                        if (!empty($report_result['phone'])) {
                            $message['phone'] = $report_result['phone'];
                            $message['number'] = $report_result['number'];
                        }
                    }
                    //更新报告
                    $update_info = [
                        'attachment_id' => $attachment_info['insert_id'],
                        'report_attachment_upload_time' => date('Y-m-d H:i:s'),
                        'erp_report_back_status' => jys_system_code::ERP_REPORT_BACK_STATUS_IN
                    ];
                    $report_info = $this->jys_db_helper->update_by_condition('report', ['number' => $report_number[0]], $update_info);
                }
            }
            //将上传信息回传到erp
            $erp_result = $this->Report_model->insert_report_user_info_to_erp($number);
            //给第一次上次报告记录的人发送短信处理
            $this->load->library('Jys_message');
            $flag = TRUE;
            if (!empty($message)) {
                $this->Report_model->send_report_status_information_to_user(array($number['0']));
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                 $data['msg'] = '上传附件失败';
            } else {
                $this->db->trans_commit();
                $data['success'] = TRUE;
                $data['msg'] = '上传附件成功';
            }
        }

        echo json_encode($data);
    }

}