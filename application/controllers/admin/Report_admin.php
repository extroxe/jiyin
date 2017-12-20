 <?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =========================================================
 *
 *      Filename: Report_admin.php
 *
 *   Description: 报告管理
 *
 *       Created: 2017-1-12 14:37:01
 *
 *        Author: wuhaohua
 *
 * =========================================================
 */
class Report_admin extends CI_Controller
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library(['form_validation', 'Jys_kdniao', 'Jys_barcode', 'Zip', 'Jys_excel', 'Jys_soap']);
        $this->load->model(['Report_model']);
    }

    /**
     * 根据子订单ID获取其下所有报告
     */
    public function get_report_list_by_order_commodity_id()
    {
        $order_commodity_id = $this->input->post('order_commodity_id');

        $result = array('success' => FALSE, 'msg' => '获取报告列表失败', 'data' => array());
        if (intval($order_commodity_id) < 1) {
            $data['msg'] = '子订单ID不正确';
            echo json_encode($data);
            exit;
        }

        $data = $this->Report_model->get_report_list_by_order_commodity_id($order_commodity_id);
        if (!empty($data) && is_array($data)) {
            $result['success'] = TRUE;
            $result['msg'] = '查询成功';
            $result['data'] = $data;
        } else {
            $result['msg'] = '未查询到相关报告';
        }

        echo json_encode($result);
    }

    /**
     * 添加报告
     */
    public function add()
    {
        $result = array('success' => FALSE, 'msg' => '添加报告失败');
        //验证表单信息
        $this->form_validation->set_rules('order_commodity_id', '订单编号', 'trim|required');
        $this->form_validation->set_rules('number', '报告编号', 'trim|required');
        $this->form_validation->set_rules('name', '姓名', 'trim');
        $this->form_validation->set_rules('birth', '出生年月', 'trim');
        $this->form_validation->set_rules('gender', '性别', 'trim|is_natural');
        $this->form_validation->set_rules('phone', '手机号码', 'trim|regex_match[/^1(3|4|5|7|8)\d{9}$/]');
        $this->form_validation->set_rules('identity_card', '身份证号', 'trim|regex_match[/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)$/]');
        $this->form_validation->set_rules('province', '省份名称', 'trim|max_length[100]');
        $this->form_validation->set_rules('province_code', '省份代码', 'trim|max_length[100]');
        $this->form_validation->set_rules('city', '城市名称', 'trim|max_length[100]');
        $this->form_validation->set_rules('city_code', '城市代码', 'trim|max_length[100]');
        $this->form_validation->set_rules('district', '区县名称', 'trim|max_length[100]');
        $this->form_validation->set_rules('district_code', '区县代码', 'trim|max_length[100]');
        $this->form_validation->set_rules('address', '地址', 'trim|max_length[200]');
        $this->form_validation->set_rules('smoking', '是否吸烟', 'trim|is_natural');
        $this->form_validation->set_rules('height', '身高', 'trim|numeric');
        $this->form_validation->set_rules('weight', '体重', 'trim|numeric');
        $this->form_validation->set_rules('personal_history', '个人病史', 'trim|max_length[200]');
        $this->form_validation->set_rules('family_history', '家族病史', 'trim|max_length[200]');
//        $this->form_validation->set_rules('blood_relationship', '血缘关系', 'trim|numeric');
        $this->form_validation->set_rules('attachment_id', '报告ID', 'trim|is_natural_no_zero');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            $post['order_commodity_id'] = $this->input->post('order_commodity_id', TRUE);
            $post['number'] = $this->input->post('number', TRUE);
            $post['name'] = $this->input->post('name', TRUE);
            $post['birth'] = $this->input->post('birth', TRUE);
            $post['gender'] = $this->input->post('gender', TRUE);
            $post['phone'] = $this->input->post('phone', TRUE);
            $post['smoking'] = $this->input->post('smoking', TRUE);
            $post['identity_card'] = $this->input->post('identity_card', TRUE);
            $post['province'] = $this->input->post('province', TRUE);
            $post['province_code'] = $this->input->post('province_code', TRUE);
            $post['city'] = $this->input->post('city', TRUE);
            $post['city_code'] = $this->input->post('city_code', TRUE);
            $post['district'] = $this->input->post('district', TRUE);
            $post['district_code'] = $this->input->post('district_code', TRUE);
            $post['address'] = $this->input->post('address', TRUE);
            $post['height'] = $this->input->post('height', TRUE);
            $post['weight'] = $this->input->post('weight', TRUE);
            $post['personal_history'] = $this->input->post('personal_history', TRUE);
            $post['family_history'] = $this->input->post('family_history', TRUE);
//            $post['blood_relationship'] = $this->input->post('blood_relationship', TRUE);
            $post['attachment_id'] = $this->input->post('attachment_id', TRUE);
            $add_data = array();
            $number[] = $post['number'];
            $add_data[0]['number'] = $post['number'];
            $add_data[0]['order_commodity_id'] = $post['order_commodity_id'];
            //添加报告
            $result = $this->Report_model->add($post);
            if ($result['success']) {
                //将报告编号回传到erp
                $erp_number_result = $this->Report_model->insert_report_number_to_erp($add_data);
                //将报告信息回传到erp
                $erp_info_result = $this->Report_model->insert_report_to_erp($post);
                //将上传人信息回传到erp
                if (!empty($post['attachment_id'])) {
                    $erp_info_result = $this->Report_model->insert_report_user_info_to_erp($number);   
                }
                //将报告信息回传的海云服务器
                // $rest_info_result = $this->Report_model->insert_report_to_rest($post);
            }
        } else {
            $result['error'] = $res['msg'];
            $result['msg'] = '参数错误，'.$res['msg'];
        }
        echo json_encode($result);
    }


    public function update()
    {
        $result = array('success' => FALSE, 'msg' => '更新报告失败');
        //验证表单信息
        $this->form_validation->set_rules('id', '报告ID', 'trim|required');
        $this->form_validation->set_rules('name', '姓名', 'trim');
        $this->form_validation->set_rules('birth', '出生年月', 'trim');
        $this->form_validation->set_rules('gender', '性别', 'trim');
        $this->form_validation->set_rules('phone', '手机号码', 'trim|regex_match[/^1(3|4|5|7|8)\d{9}$/]');
        $this->form_validation->set_rules('identity_card', '身份证号', 'trim|regex_match[/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)$/]');
        $this->form_validation->set_rules('province', '省份名称', 'trim|max_length[100]');
        $this->form_validation->set_rules('province_code', '省份代码', 'trim|max_length[100]');
        $this->form_validation->set_rules('city', '城市名称', 'trim|max_length[100]');
        $this->form_validation->set_rules('city_code', '城市代码', 'trim|max_length[100]');
        $this->form_validation->set_rules('district', '区县名称', 'trim|max_length[100]');
        $this->form_validation->set_rules('district_code', '区县代码', 'trim|max_length[100]');
        $this->form_validation->set_rules('address', '地址', 'trim|max_length[200]');
        $this->form_validation->set_rules('smoking', '是否吸烟', 'trim|is_natural');
        $this->form_validation->set_rules('height', '身高', 'trim|numeric');
        $this->form_validation->set_rules('weight', '体重', 'trim|numeric');
        $this->form_validation->set_rules('personal_history', '个人病史', 'trim|max_length[200]');
        $this->form_validation->set_rules('family_history', '家族病史', 'trim|max_length[200]');
        $this->form_validation->set_rules('blood_relationship', '血缘关系', 'trim|numeric');
//        $this->form_validation->set_rules('project', '检测项目', 'trim|max_length[200]');
        $this->form_validation->set_rules('attachment_id', '报告文件ID', 'trim|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']) {
            $post['id'] = $this->input->post('id', TRUE);
            $post['number'] = $this->input->post('number', TRUE);
            $post['name'] = $this->input->post('name', TRUE);
            $post['birth'] = $this->input->post('birth', TRUE);
            $post['gender'] = $this->input->post('gender', TRUE);
            $post['phone'] = $this->input->post('phone', TRUE);
            $post['smoking'] = $this->input->post('smoking', TRUE);
            $post['identity_card'] = $this->input->post('identity_card', TRUE);
            $post['province'] = $this->input->post('province', TRUE);
            $post['province_code'] = $this->input->post('province_code', TRUE);
            $post['city'] = $this->input->post('city', TRUE);
            $post['city_code'] = $this->input->post('city_code', TRUE);
            $post['district'] = $this->input->post('district', TRUE);
            $post['district_code'] = $this->input->post('district_code', TRUE);
            $post['address'] = $this->input->post('address', TRUE);
            $post['height'] = $this->input->post('height', TRUE);
            $post['weight'] = $this->input->post('weight', TRUE);
            $post['personal_history'] = $this->input->post('personal_history', TRUE);
            $post['family_history'] = $this->input->post('family_history', TRUE);
            $post['blood_relationship'] = $this->input->post('blood_relationship', TRUE);
//            $post['project']            = $this->input->post('project', TRUE);
            $post['attachment_id'] = $this->input->post('attachment_id', TRUE);
            $post['order_commodity_id'] = $this->input->post('order_commodity_id', TRUE);
            $number[] = $post['number'];
            //更新报告
            $result = $this->Report_model->update($post);
            if ($result['success']) {
                //将报告信息回传到erp
                $erp_info_result = $this->Report_model->insert_report_to_erp($post);
                //将上传人信息回传到erp
                if (!empty($post['attachment_id'])) {
                    $erp_info_result = $this->Report_model->insert_report_user_info_to_erp($number); 
                }
            }
        } else {
            $result['msg'] = '参数错误'.$res['msg'];
        }

        echo json_encode($result);
    }

    //删除报告
    public function delete()
    {
        $report_id = $this->input->post('id', TRUE);
        $number = $this->input->post('number', TRUE);
        $is_online = $this->input->post('is_online', TRUE);
        if (empty($report_id) || intval($report_id) < 1) {
            $data['success'] = FALSE;
            $data['msg'] = '参数错误';
        }

        $result = $this->Report_model->delete_report_by_id($report_id);
        if ($result['success']) {
            //将删除状态回传到erp
            if ($is_online == 2) {
                $numbers[]['test_code'] = $number;
                $this->Report_model->delete_report_to_erp($numbers);
                $this->Report_model->delete_report_number_to_erp($numbers);

            }
            $data['success'] = TRUE;
            $data['msg'] = '删除报告成功';
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '删除报告失败';
        }

        echo json_encode($data);
    }

    /**
     * 依据子订单获取报告信息
     * @param int $page
     * @param int $page_size
     */
    public function get_report_by_suborder($page = 1, $page_size = 10)
    {
        $start_time = $this->input->post('start_time') ?  $this->input->post('start_time', TRUE) : NULL;
        $end_time = $this->input->post('end_time') ?  $this->input->post('end_time', TRUE) : NULL;
        $keywords = $this->input->post('keywords') ?  $this->input->post('keywords', TRUE) : NULL;
        $condition = array();
        if ($_SESSION['role_id'] == Jys_system_code::ROLE_ADMINISTRATOR) {
            $agent_id = $this->input->post('agent_id') ?  $this->input->post('agent_id', TRUE) : NULL;
        }elseif ($_SESSION['role_id'] == Jys_system_code::ROLE_AGENT) {
            $agent_id = $_SESSION['user_id'];
        }
        if (!empty($start_time)) {
            $condition['order_commodity.create_time >='] = $start_time;
        }
        if (!empty($end_time)) {
            $condition['order_commodity.create_time <='] = $end_time;
        }
        if (!empty($agent_id) && intval($agent_id) > 0) {
            $condition['user_agent.agent_id'] = $agent_id;
        }

        $result = $this->Report_model->get_report_by_suborder($page, $page_size, $condition, $keywords);

        echo json_encode($result);
    }
    /*
     * 分页获取所有检测报告
     */
    public function get_report_by_page()
    {
        $keyword = $this->input->post('keyword', TRUE);
        $start_create_time = $this->input->post('start_create_time', TRUE);
        $end_create_time = $this->input->post('end_create_time', TRUE);
        $attachment = intval($this->input->post('attachment', TRUE));
        $page = intval($this->input->post('page', TRUE));
        $page_size = $this->input->post('page_size', TRUE) ? intval($this->input->post('page_size', TRUE)) : 10;
        $has_written = intval($this->input->post('has_written', TRUE));
        $is_online = intval($this->input->post('is_online', TRUE));

        $agent_id = FALSE;
        if ($_SESSION['role_id'] == Jys_system_code::ROLE_AGENT)
        {
            $agent_id = $_SESSION['user_id'];
        }

        $data = $this->Report_model->get_all_report($page, $page_size, $start_create_time, $end_create_time, $attachment, $keyword, $has_written, $is_online, $agent_id);

        echo json_encode($data);
    }

    /*
     * 分页获取所有检测报告（样本管理）
     */
    public function get_report_by_page_for_sample()
    {
        $keyword = $this->input->post('keyword', TRUE);
        $start_create_time = $this->input->post('start_create_time', TRUE);
        $end_create_time = $this->input->post('end_create_time', TRUE);
        $attachment = intval($this->input->post('attachment', TRUE));
        $page = intval($this->input->post('page', TRUE));
        $page_size = $this->input->post('page_size', TRUE) ? intval($this->input->post('page_size', TRUE)) : 10;
        $has_written = intval($this->input->post('has_written', TRUE));
        $is_online = intval($this->input->post('is_online', TRUE));

        $agent_id = FALSE;
        if ($_SESSION['role_id'] == Jys_system_code::ROLE_AGENT)
        {
            $agent_id = $_SESSION['user_id'];
        }

        $data = $this->Report_model->get_all_report_for_sample($page, $page_size, $start_create_time, $end_create_time, $attachment, $keyword, $has_written, $is_online, $agent_id);

        echo json_encode($data);
    }

    /*
     * 根据报告编号生成条形码并下载
     */
    public function download_barcode()
    {
        $report_numbers = $_GET['data'];

        $numbers = explode(',', $report_numbers);
        if (empty($numbers) || !is_array($numbers)) {
            echo '参数错误';
            return;
        }

        $result = $this->Report_model->create_barcode_by_number($numbers);
        if ($result) {
            $file_path = FCPATH . 'source/download/report_barcode-' . date('YmdHis') . '.zip';
            $file_dir = FCPATH . 'source/download/';
            $this->zip->read_dir($result, FALSE);
            //删除保存条形码文件夹
            $this->deldir($result);
            $this->zip->archive($file_path);
            if (!file_exists($file_path)) {
                echo "没有该文件";
                return;
            }
            header("Content-type:text/html;charset=utf-8");
            $file_name = basename($file_path);
            $fp = fopen($file_path, "r");
            $file_size = filesize($file_path);
            //下载文件需要用到的头
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length:" . $file_size);
            Header("Content-Disposition: attachment; filename=" . $file_name);
            $buffer = 1024;
            $file_count = 0;
            //向浏览器返回数据
            while (!feof($fp) && $file_count < $file_size) {
                $file_con = fread($fp, $buffer);
                $file_count += $buffer;
                echo $file_con;
            }

            fclose($fp);
            //下载完成后删除压缩包，临时文件夹
            if ($file_count >= $file_size) {
                unlink($file_path);
                exec("rm -rf " . $file_dir);
            }
        } else {
            echo '下载失败';
            return;
        }
    }

    /*
     * 删除文件夹中的文件保留目录
     */
    public function deldir($dir)
    {
        //删除目录下的文件：
        $dh = opendir($dir);

        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;

                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 导出报告样本（Excel）
     */
    public function download_report()
    {
        $start_create_time = $this->input->get('start_create_time', TRUE);
        $end_create_time = $this->input->get('end_create_time', TRUE);
        $has_written = $this->input->get('has_written', TRUE);
        $keyword = $this->input->get('keyword', TRUE);
        $report_id = $this->input->get('report_id', TRUE);
        $report_id_array = explode('_', $report_id);

        $result = $this->Report_model->get_report_info($start_create_time, $end_create_time, $has_written, $keyword, $report_id_array);

        $header = ['检测码', '检测人', '身份证号', '性别', '出生日期', '是否吸烟', '身高(cm)', '体重(kg)', '联系方式', '联系地址', '商品名称', '检测项目', '下单时间', '报告状态', '报告类型', '订单编号', '子订单编号', '收件人姓名', '收件人手机号', '快递公司', '快递单号'];
        $file_name = 'report_table_' . date('Y-m-d_His');
        if (!$result['success']) {
            $result['data'] = array();
        }
        $this->jys_excel->export_reports_info_list($header, $result['data'], $file_name);
    }

    /**
     * 导出报告样本（CSV）
     */
    public function download_report_for_csv()
    {
        $start_create_time = $this->input->get('start_create_time', TRUE);
        $end_create_time = $this->input->get('end_create_time', TRUE);
        $has_written = $this->input->get('has_written', TRUE);
        $keyword = $this->input->get('keyword', TRUE);
        $report_id = $this->input->get('report_id', TRUE);
        $report_id_array = explode('_', $report_id);

        $result = $this->Report_model->get_report_info($start_create_time, $end_create_time, $has_written, $keyword, $report_id_array);

        $header = ['检测码', '检测人', '身份证号', '性别', '出生日期', '是否吸烟', '身高(cm)', '体重(kg)', '联系方式', '联系地址', '商品名称', '检测项目', '下单时间', '报告状态', '报告类型', '订单编号', '子订单编号', '收件人姓名', '收件人手机号', '快递公司', '快递单号'];
        $file_name = 'report_table_' . date('Y-m-d_His');
        if (!$result['success']) {
            $result['data'] = array();
        }
        $this->jys_excel->export_report_list_to_csv($header, $result['data'], $file_name);
    }

    /**
     * 根据检测码导出报告样本
     */
    public function download_report_from_input()
    {
        $is_excel = $this->input->post('is_excel', TRUE);
        $start_create_time = $this->input->post('start_create_time', TRUE);
        $end_create_time = $this->input->post('end_create_time', TRUE);
        $has_written = $this->input->post('has_written', TRUE);
        $keyword = $this->input->post('keyword', TRUE);
        $report_id = $this->input->post('report_id', TRUE);
        $report_id_array = explode('_', $report_id);
        $number = $this->input->post('real_number');

        $number = json_decode($number, TRUE);

        $number_array = array();
        foreach ($number as $value) {
            $number_array[] = $value['number'];
        }

        $result = $this->Report_model->get_report_info($start_create_time, $end_create_time, $has_written, $keyword, $report_id_array, $number_array);

        $header = ['检测码', '检测人', '身份证号', '性别', '出生日期', '联系方式', '联系地址', '备注', '商品名称', '检查项目', '检测模版名称'];
        $file_name = 'report_table_' . date('Y-m-d_His');
        if (!$result['success']) {
            $result['data'] = array();
        }

        foreach ($result['data'] as $key => $value) {
            foreach ($number as $item) {
                if ($item['number'] == $value['number']) {
                    $result['data'][$key]['remark'] = $item['remark'];
                }
            }
        }
        if ($is_excel == 1) { //导出到Excel
            $this->jys_excel->export_report_list_by_report_number_and_remark_to_excel($header, $result['data'], $file_name);
        } elseif ($is_excel == 0) {
            //导出到CSV
            $this->jys_excel->export_report_list_by_report_number_and_remark_to_csv($header, $result['data'], $file_name);
        }

    }

    /**
     * 下载报告模板
     */
    public function download_report_template()
    {
        $post['order_id'] = $this->input->get('order_id', TRUE);
        $post['order_commodity_id'] = $this->input->get('order_commodity_id', TRUE);
        $post['is_online'] = $this->input->get('is_online', TRUE);

        $order_commodity_number = $this->jys_db_helper->get('order_commodity',$post['order_commodity_id'])['amount'];
        $report_number = $this->jys_db_helper->get_where_multi('report',['order_commodity_id' => $post['order_commodity_id']]);
        if(!empty($report_number) && $report_number != FALSE){
            $order_commodity_number = $order_commodity_number - count($report_number);
        }
        $header = ['报告编号（最多填写'.$order_commodity_number.'项）'];
        $title = implode('_', $post);
        $this->jys_excel->export_report_template($header, [], $title);
    }

    /**
     * 批量上传报告编号信息
     */
    public function batch_up_report_info_backup()
    {
        $data['msg'] = "上传附件失败";
        $data['success'] = FALSE;
        $error = array();
        $all = TRUE;

        //上传附件
        $result = $this->jys_attachment->upload_excel_attachment();
        $template_id_and_project_num = '';
        if ($result['success']) {
            //解析Excel数据
            $report_data = $this->jys_excel->import_template($result['path'], $template_id_and_project_num);
            if (!empty($report_data) && is_array($report_data)) {
                $template = explode('_', $template_id_and_project_num[0]);
                $i = 0;
                foreach ($report_data as $key => $val) {
                    $val['template_id'] = $template[0];    //第一个数为模板Id
                    $val['project_num'] = $template[1];    //第二个数为项目数量
                    $val['create_time'] = date('Y-m-d H:i:s');
                    $val['update_time'] = date('Y-m-d H:i:s');
                    $j = $key + 1;
                    $res = $this->jys_db_helper->add('report', $val, TRUE);
                    if (!$res['success']) {
                        $error[$i] = '报告编号为' . $val['number'] . '的第' . $j . '条数据插入失败，可能原因：重复数据或空数据';
                        $i++;
                        $all = FALSE;
                    } else {
                        $temp_number[] = $res['insert_id'];
                    }
                }
                //将报告编号回传到erp
                $erp_number_result = $this->Report_model->insert_report_number_to_erp($temp_number);
            }
            $success_result = count($report_data) - count($error);
            $fail_result = count($error);
        }
        if ($result['success'] && $all) {
            $data['msg'] = "插入数据完成：所有数据插入成功";
            $data['success'] = TRUE;
            $data['all'] = TRUE;
        } else if ($result['success']) {
            $data['msg'] = "插入数据完成：" . $success_result . " 条数据插入成功，" . $fail_result . " 条数据插入失败！";
            $data['error'] = $error;
            $data['success'] = TRUE;
            $data['all'] = FALSE;
        }
        echo json_encode($data);
    }

    /**
     * 批量上传报告编号信息
     */
    public function batch_up_report_info()
    {
        $data['msg'] = "上传附件失败";
        $data['success'] = FALSE;
        $error = array();
        $all = TRUE;
        $m = 0;

        //上传附件
        $result = $this->jys_attachment->upload_excel_attachment();
        $template_id_and_project_num = '';
        if ($result['success']) {
            //解析Excel数据
            $report_data = $this->jys_excel->import_template($result['path'], $template_id_and_project_num);
            if (!empty($report_data) && is_array($report_data)) {
                //先调用删除erp检测码接口 删掉222的检测码
//                $numbers = array();
//                foreach ($report_data as $key => $value) {
//                    $numbers[$key]['test_code'] = $value['number'];
//                }
//                $erp_result = $this->jys_soap->delete_report_number_to_erp($numbers);

                $template = explode('_', $template_id_and_project_num[0]);
                $i = 0;
                $order_commodity_number = $this->jys_db_helper->get('order_commodity',$template[1])['amount'];
                $report_number = $this->jys_db_helper->get_where_multi('report',['order_commodity_id' => $template[1]]);
                if(!empty($report_number) && $report_number != FALSE){
                    $order_commodity_number = $order_commodity_number - count($report_number);
                }
//                if(count($report_data) == $order_commodity_number){   //上传的个数与剩下的数目相同，允许上传
                    foreach ($report_data as $key => $val) {
//                    $val['template_id'] = $template[0];    //第一个数为总订单Id
                        $val['order_commodity_id'] = $template[1];    //第二个数为子订单Id
                        $val['create_time'] = date('Y-m-d H:i:s');
                        $val['update_time'] = date('Y-m-d H:i:s');
                        $j = $key + 1;
                        $res = $this->jys_db_helper->add('report', $val, TRUE);
                        if (!$res['success']) {
                            $error[$i] = '报告编号为' . $val['number'] . '的第' . $j . '条数据插入失败，可能原因：重复数据或空数据';
                            $i++;
                            $all = FALSE;
                        } else {
                            $temp_number[] = $res['insert_id'];
                            $temp_data[$m]['order_commodity_id'] =  $template[1];
                            $temp_data[$m++]['number'] = $val['number'];
                        }
                    }
                    if(!empty($temp_data)){
                        //将报告编号回传到erp
                        $erp_number_result = $this->Report_model->insert_report_number_to_erp($temp_data);
                    }
                    $success_result = count($report_data) - count($error);
                    $fail_result = count($error);
                    if ($result['success'] && $all) {
                        $data['msg'] = "插入数据完成：所有数据插入成功";
                        $data['success'] = TRUE;
                        $data['all'] = TRUE;
                    } else if ($result['success']) {
                        $data['msg'] = "插入数据完成：" . $success_result . " 条数据插入成功，" . $fail_result . " 条数据插入失败！";
                        $data['error'] = $error;
                        $data['success'] = TRUE;
                        $data['all'] = FALSE;
                    }
//                } else{
//                    $data['msg'] = "上传失败！上传条码数量与规定数量不符合";
//                    $data['success'] = FALSE;
//                    $data['all'] = FALSE;
//                }
            }
        }

        echo json_encode($data);
    }

    /**
     * 根据检测码获取检测信息
     */
    public function get_report_by_number()
    {
        $number = $this->input->post('number', TRUE);
        $result = array('success' => FALSE, 'msg' => '获取检测码信息失败', 'data' => array());

        if (empty($number)) {
            $result['msg'] = '请输入要查询的检测码';
            echo json_encode($result);
            exit;
        }

        $result = $this->Report_model->get_report_by_number($number);

        echo json_encode($result);
    }

    /**
     * 批量上传压缩报告文件
     */
    public function batch_upload_report_info($dir = 'source/uploads/')
    {
        $data['msg'] = "上传附件失败";
        $data['success'] = FALSE;

        //上传附件
        $this->db->trans_start();
        $result = $this->jys_attachment->upload_batch();
        if ($result['success']) {
            //解压zip
            $zip = new ZipArchive;
            $res = $zip->open($result['path']);
            if ($res === TRUE) {
                //解压缩到报告文件夹
                $zip_path = FCPATH . $result['path'];// 压缩包当前在服务器上的完整路径
                $messages = array();
                $reports = array();
                $m = 0;
                $n = 0;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $real_filename = $zip->getNameIndex($i);// 压缩包中报告文件的文件名（带后缀）
                    $unzip_filepath = "zip://" . $zip_path . '#' . $real_filename;// 待解压文件的完成拷贝路径
                    $report_path = 'source/uploads/report/';// 报告存放目录的完整路径
                    $insert_db_filepath = $report_path . md5_file($unzip_filepath) . '.pdf';
                    if (!is_dir($real_filename)) {
                        copy($unzip_filepath, FCPATH . $report_path . md5_file($unzip_filepath) . '.pdf');
                        //根据文件md5判断是否重复
                        $data_info = $this->jys_db_helper->get_where('attachment', ['md5' => md5_file($unzip_filepath)]);
                        if (empty($data_info)) {
                            // 1、将解压所得的文件路径插入到attachment表中，并取得对应的id
                            $add_info = [
                                'md5' => md5_file($unzip_filepath),
                                'path' => $insert_db_filepath,
                                'create_time' => date("Y-m-d H:i:s")
                            ];
                            $attachment_info = $this->jys_db_helper->add('attachment', $add_info, TRUE);
                        } else {
                            $attachment_info['insert_id'] = $data_info['id'];
                        }
                        // 2、根据$real_filename中的报告编号，查找到对应的报告，并将附件ID更新到其中
                        $report_number = array();
                        if (preg_match("/[a-zA-Z0-9]*/", $real_filename, $report_number)) {
                            if (isset($report_number[0]) && !empty($report_number[0])) {
                                //获取报告编号
                                $number[$i] = $report_number[0];
                                //更新报告编号查找对应的报告
                                $report_result = $this->jys_db_helper->get_where('report', ['number' => $report_number[0]]);
                                if (empty($report_result['attachment_id'])) {
                                    // 查找对应的报告，将附件ID更新进去
                                    $update_info = [
                                        'attachment_id' => $attachment_info['insert_id'],
                                        'report_attachment_upload_time' => date('Y-m-d H:i:s')
                                    ];
                                    $report_info = $this->jys_db_helper->update_by_condition('report', ['number' => $report_number[0]], $update_info);
                                    //第一次上次的报告需发短信
                                    if (!empty($report_result['phone']) && $report_info) {
                                        $messages[$m]['phone'] = $report_result['phone'];
                                        $messages[$m]['number'] = $report_result['number'];
                                        $m++;
                                    }
                                } else {
                                    //已有记录的报告
                                    $reports[$n]['report_id'] = $report_result['id'];
                                    $reports[$n]['pre_attachment_id'] = $report_result['attachment_id'];
                                    $reports[$n]['now_attachment_id'] = $attachment_info['insert_id'];
                                    $reports[$n]['number'] = $report_result['number'];
                                    $n++;
                                    //覆盖已有的报告
                                    // $update_data = [
                                    //     'attachment_id' => $attachment_info['insert_id']
                                    // ];
                                    // $report_info = $this->jys_db_helper->update_by_condition('report', ['number' => $report_number[0]] ,$update_data);
                                }
                            }
                        }
                    }
                }
                //将上传信息回传到erp
                $erp_result = $this->Report_model->insert_report_user_info_to_erp($number);
                //给第一次上次报告记录的人发送短信处理
                $this->load->library('Jys_message');
                $flag = TRUE;
                if (!empty($messages)) {
                    foreach ($messages as $key => $value) {
                        if ($flag == TRUE) {
                            $message = $value['phone'] . ',' . $value['number'];
                            $flag = FALSE;
                        } else {
                            $message .= ';' . $value['phone'] . ',' . $value['number'];
                        }
                    }
                    $msg = '【赛安基因城】感谢您选择我们的服务，您的基因检测报告已经出具（{$var}）。快速查询地址http://suo.im/2QNrW4';
                    if (!empty($message)) {
                        $this->jys_message->send_variable_message($msg, $message);
                    }
                }
            }
            $zip->close();
            if (file_exists($zip_path)) {
                //删除压缩文件
                unlink($zip_path);
            }
            if ($this->db->trans_status() === FALSE) {
                $data['msg'] = '上传附件失败';
                $data['success'] = FALSE;
                $data['data'] = '';
                $this->db->trans_rollback();
            } else {
                $data['success'] = TRUE;
                $data['msg'] = '上传附件成功';
                $data['data'] = $reports;
                $this->db->trans_commit();
            }
        } else {
            $data['msg'] = '批量上传附件失败';
            $data['success'] = FALSE;
        }

        echo json_encode($data);
    }

    //批量覆盖上传报告
    public function batch_update_report()
    {
        $data = ['success' => FALSE, 'msg' => '更新失败'];
        $report_info = $this->input->post('val');
        $report_info = json_decode($report_info, 'TRUE');
        if (is_array($report_info) && !empty($report_info)) {
            foreach ($report_info as $key => $value) {
                $update[$key] = [
                    'id' => $value['report_id'],
                    'attachment_id' => $value['now_attachment_id'],
                    'report_attachment_upload_time' => date('Y-m-d H:i:s')
                ];
            }
            //批量更新
            $result = $this->jys_db_helper->update_batch('report', $update, 'id');
            if ($result) {
                $data = ['success' => TRUE, 'msg' => '更新成功'];
            }
        }
        echo json_encode($data);
    }

    /**
     * 删除报告附件
     */
    public function delete_report_attachment() {
        $result = array('success'=>FALSE, 'msg'=>'删除报告附件失败');
        $report_id = $this->input->post('report_id', TRUE);

        if (intval($report_id) < 1) {
            $result['msg'] = '请选择要删除附件的报告';
            echo json_encode($result);
            exit;
        }

        if ($this->jys_db_helper->update('report', intval($report_id), array('attachment_id' => NULL, 'report_attachment_upload_time' => NULL, 'report_status' => 0))) {
            $result['success'] = TRUE;
            $result['msg'] = '删除报告附件成功';
        }

        echo json_encode($result);
    }

    /**
     * 批量下载报告附件
     */
    public function download_report_attachment() {
        $post = file_get_contents("php://input");
        $attachment_ids = array();
        // $post = [
        //     ['attachment_id' => 241],
        //     ['attachment_id' => 242],
        //     ['attachment_id' => 243]
        // ];
        foreach ($post as $key => $value) {
            $attachment_ids[] = $value['attachment_id'];
        }
        //查找附件地址
        if (!empty($attachment_ids)) {
            $attachment = $this->Report_model->get_attachment_path_by_id($attachment_ids);
            //获取压缩文件名
            $zip_file_name = md5(time()).'.zip';
            $filename = FCPATH . 'source/uploads/' . $zip_file_name;
            //生成压缩文件
            $zip = new ZipArchive();
            $zip->open($filename, ZipArchive::CREATE);
            foreach($attachment as $value){
                $path = FCPATH . $value['path'];
                if (file_exists($path)) {
                    //向压缩包中添加文件
                    $zip->addFile($value['path'],basename($value['path']));   
                }
            }
            //关闭压缩包
            $zip->close(); 
            //下载压缩包
            $this->report_attachment_download($filename);
            //删除压缩包
            unlink($filename);
        }
    }

    /**
     * 这是实现下载报告附件的函数
     */
    public function report_attachment_download($file_path = ''){
        header("Content-type:text/html;charset=utf-8");
        //首先要判断给定的文件存在与否
        if(!file_exists($file_path) || empty($file_path)){
            echo "没有该文件";
            return ;
        }
        $fp=fopen($file_path,"r");
        $file_size=filesize($file_path);
        //下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:".$file_size);
        header('Content-Transfer-Encoding:binary');
        header("Content-Type:application/vnd.ms-execl");
        Header("Content-Disposition: attachment; filename=".'报告附件.zip');
        $buffer=1024;
        $file_count=0;
        //向浏览器返回数据
        while(!feof($fp) && $file_count<$file_size){
            $file_con=fread($fp,$buffer);
            $file_count+=$buffer;
            echo $file_con;
        }
        fclose($fp);
    }

    /**
     * 读取excel表格中的报告编号批量删除报告，回传到海云
     */
    public function batch_delete_report() {
        $array = array('success' =>FALSE, 'msg' => '删除失败');

        //上传表格文件
        $data = $this->jys_attachment->upload_excel_attachment();
        if (!empty($data)) {
            $path = FCPATH . $data['path'];
            //读取表格中的数据
            $res = $this->jys_excel->export_excel_for_report($path);
            //删除表格
            unlink($path);
            //删除数据库报告
            $number = array();
            $list = array();
            foreach ($res as $key => $value) {
                $number[] = $value['number'];
                $list['list'][] = $value['number'];
            }
            $this->Report_model->batch_delete_report($number);
            //将删除报告回传到海云
            $result = $this->delete_report_to_hy($list);
            //将删除报告回传到erp
            if ($result['httpCode'] == 200) {
                $array = ['success' => TRUE, 'msg' => '删除成功'];
            }
        }

        echo json_encode($array);
    }

    //将删除报告回传到海云
    public function delete_report_to_hy($data){
        $data = json_encode($data);
        $url = 'http://106.15.52.253:8080/medical/saian/sample';

        $ch = curl_init();
        curl_setopt ($ch,CURLOPT_URL,$url);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");   
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

        $result = curl_exec($ch);
        if ($error = curl_error($ch)) {
            die($error);
        }
        curl_close($ch);

        return json_decode($result, TRUE);
    }

    /**
     * 根据报告状态获取其下所有报告
     */
    public function get_report_list_by_status($page, $page_size)
    {
        $status_id = $this->input->post('status_id');
        // $page = $this->input->post('page');
        // $page_size = $this->input->post('page_size');
        // $page = 1;
        // $page_size = 10;

        $data = $this->Report_model->get_report_list_by_status($page, $page_size, $status_id);

        echo json_encode($data);
    }

    //获取报告所以状态
    public function get_all_report_status(){
        $status = array();
        $status = [
            ['name' => '已登记', 'value' => '1'],
            ['name' => '未登记', 'value' => '2'],
            ['name' => '已收样', 'value' => '3'],
            ['name' => '已送检', 'value' => '4'],
            ['name' => '重检中', 'value' => '5'],
            ['name' => '已出报告', 'value' => '6'],
            ['name' => '检测失败', 'value' => '7']
        ];
        
        echo json_encode($status);
    }

    
}