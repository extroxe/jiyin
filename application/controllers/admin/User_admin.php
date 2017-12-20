<?php
if (!defined('BASEPATH'))
 exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: User_admin.php
 *
 *   Description: 会员管理
 *
 *       Created: 2016-11-23 16:07:55
 *
 *        Author: zourui
 *
 * =========================================================
 */

class User_admin extends CI_Controller {
     /**
     * 构造函数
     */
	public function __construct()
    {
		parent::__construct();
        $this->load->helper('form');
        $this->load->library(['Jys_excel', 'Jys_db_helper']);
		$this->load->model(['Level_model','User_model']);
	}

	/**
     * 根据ID获取数据
     */
    public function get_info_by_id(){
        $id      = $this->input->post('id');
        $result  = $this->jys_db_helper->get('user', $id);

        if(!$result){
            $data['success'] = FALSE;
        }else{
            $data['data'] = $result;

            //用户头像
            $attachment = $this->Common_model->get_attachment($result['avatar']);
            $data['data']['user_path'] = $attachment['path'];

            //会员等级
            $temp = $this->Level_model->get_level_by_id($result['level']);
            $data['data']['user_level'] = $temp['name'];

            $data['success'] = TRUE;

        }

        echo json_encode($data);
    }

     /**
     * 添加数据
     */
    public function add_info(){
        $this->form_validation->set_rules('password', '密码', 'trim|required|min_length[6]|max_length[30]',['min_length'=>'密码最小为6位','max_length'=>'密码最大为16位']);
        $this->form_validation->set_rules('openid', '用户微信openid', 'trim|numeric');
        $this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('name', '用户姓名', 'trim|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('gender', '用户性别', 'trim');
        $this->form_validation->set_rules('phone', '手机号', 'required|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['required'=>'手机号为必填',
            'regex_match'=>'手机号不符合规则']);
        $this->form_validation->set_rules('email', '用户邮箱', 'trim|regex_match[/^\w+@\w+\..+$/]',['regex_match'=>'邮件格式不符合规则']);
        $this->form_validation->set_rules('birthday', '出生日期', 'trim|regex_match[/^[1|2]\d{3}-[0|1]\d-[0-3][0-9]$/]', ['regex_match'=>'出生日期格式不符合规则']);
        $this->form_validation->set_rules('nickname', '用户论坛昵称', 'trim|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('attachment_id', '用户头像', 'trim|integer');
        $this->form_validation->set_rules('current_point', '当前积分', 'trim|required');
        $this->form_validation->set_rules('total_point', '总积分', 'trim|required');
        $this->form_validation->set_rules('role_id', '角色id', 'trim|required');
        $this->form_validation->set_rules('level', '会员等级', 'trim|numeric');
        $this->form_validation->set_rules('is_show', '是否显示', 'trim|numeric');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $array['current_point']         = $this->input->post('current_point', TRUE);
            $array['total_point']           = $this->input->post('total_point', TRUE);
            $pwd                            = $this->input->post('password', TRUE);
            $array['password']              = password_hash($pwd, PASSWORD_DEFAULT);
            $array['openid']                = $this->input->post('openid', TRUE);
            $array['username']              = $this->input->post('username', TRUE);
            $array['name']                  = $this->input->post('name', TRUE);
            $array['gender']                = $this->input->post('gender', TRUE);
            $array['phone']                 = $this->input->post('phone', TRUE);
            $array['email']                 = $this->input->post('email', TRUE);
            $array['birthday']              = $this->input->post('birthday', TRUE);
            $array['nickname']              = $this->input->post('nickname', TRUE);
            $array['avatar']                = $this->input->post('attachment_id', TRUE);
            $array['role_id']               = $this->input->post('role_id', TRUE);
            $array['level']                 = $this->input->post('level', TRUE);
            $array['is_show']               = $this->input->post('is_show', TRUE);
            $array['color']                 = $this->input->post('color', TRUE);
            $array['create_time']           = date('Y-m-d H:i:s');
            $array['update_time']           = date('Y-m-d H:i:s');
            $res_status = $this->jys_db_helper->set('user',$array);
            if($res_status){
                $data['success'] = TRUE;
                $data['msg'] = '添加成功';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '数据输入错误,请检查手机号 邮箱是否符合规则';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }


    /**
     * 更新数据
     */
    public function update_info(){
        $this->form_validation->set_rules('id', '用户ID', 'trim|required|integer');
        $this->form_validation->set_rules('username', '用户名', 'trim|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('name', '用户姓名', 'trim|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('gender', '用户性别', 'trim|integer');
        $this->form_validation->set_rules('phone', '手机号', 'trim|regex_match[/^1(3|4|5|7|8)\d{9}$/]', ['regex_match'=>'手机号不符合规则']);
        $this->form_validation->set_rules('email', '用户邮箱', 'trim|regex_match[/^\w+@\w+\..+$/]',['regex_match'=>'邮件格式不符合规则']);
        $this->form_validation->set_rules('birthday', '出生日期', 'trim|regex_match[/^[1|2]\d{3}-[0|1]\d-[0-3][0-9]$/]', ['regex_match'=>'出生日期格式不符合规则']);
        $this->form_validation->set_rules('nickname', '用户论坛昵称', 'trim|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('attachment_id', '用户头像', 'trim|integer');
        $this->form_validation->set_rules('current_point', '当前积分', 'trim|integer');
        $this->form_validation->set_rules('total_point', '总积分', 'trim|integer');
        $this->form_validation->set_rules('role_id', '角色id', 'trim|integer');
        $this->form_validation->set_rules('level', '会员等级', 'trim|integer');

        //var_dump($this->input->post('birthday'));exit;

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $id                             = intval($this->input->post('id', TRUE));
            $userinfo = array();
            // 用户名
            if (isset($_POST['username'])) {
                $userinfo['username'] = $this->input->post('username', TRUE);
            }
            // 姓名
            if (isset($_POST['name'])) {
                $userinfo['name'] = $this->input->post('name', TRUE);
            }
            // 性别
            if (intval($this->input->post('gender', TRUE)) == 1 || intval($this->input->post('gender', TRUE)) == 0) {
                $userinfo['gender'] = intval($this->input->post('gender', TRUE));
            }
            // 手机号
            if (!empty($_POST['phone'])) {
                $userinfo['phone'] = $this->input->post('phone', TRUE);
            }
            // 邮箱
            if (isset($_POST['email'])) {
                $userinfo['email'] = $this->input->post('email', TRUE) ? $this->input->post('email', TRUE) : NULL;
            }
            // 出生日期
            if (isset($_POST['birthday'])) {
                $userinfo['birthday'] = $this->input->post('birthday', TRUE) ? $this->input->post('birthday', TRUE) : NULL;
            }
            // 论坛昵称
            if (isset($_POST['nickname'])) {
                $userinfo['nickname'] = $this->input->post('nickname', TRUE);
            }
            // 用户头像
            if (intval($this->input->post('attachment_id', TRUE)) > 0) {
                $userinfo['avatar'] = intval($this->input->post('attachment_id', TRUE));
            }
            // 当前积分
            if (intval($_POST['current_point']) >= 0) {
                $userinfo['current_point'] = intval($this->input->post('current_point', TRUE));
            }
            // 总积分
            if (intval($_POST['total_point']) >= 0) {
                $userinfo['total_point'] = intval($this->input->post('total_point', TRUE));
            }
            // 角色ID
            if (intval($this->input->post('role_id', TRUE)) > 0) {
                $userinfo['role_id'] = intval($this->input->post('role_id', TRUE));
            }
            // 会员等级
            if (intval($this->input->post('level', TRUE)) > 0) {
                $userinfo['level'] = intval($this->input->post('level', TRUE));
            }
            // 代理商配色
            if ($this->input->post('color', TRUE)) {
                $userinfo['color'] = $this->input->post('color', TRUE);
            }

            if (!empty($userinfo)) {
                $userinfo['update_time']           = date('Y-m-d H:i:s');
                $res_status = $this->jys_db_helper->update('user', $id, $userinfo);
                if($res_status){
                    $data['success'] = TRUE;
                    $data['msg'] = '修改成功';
                }
            }else {
                $data['success'] = TRUE;
                $data['msg'] = '修改成功';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '数据输入错误,请检查手机号 邮箱是否符合规则';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

	/**
     * 分页获取数据
     */
    public function get_page_info(){
        $page          = $this->input->post('page');
        $page_size     = $this->input->post('page_size');
        $keyword       = $this->input->post('keyword', TRUE);
        $user_id = $_SESSION['user_id'];
        $role_id = $_SESSION['role_id'];

        if (empty($page_size) || intval($page_size) < 1) {
            $page_size = 10;
        }
        $result = $this->User_model->get_page_info($page, $page_size, $is_show = 1, $keyword, $user_id, $role_id);

        echo json_encode($result);
    }

    /**
     * 删除分类(软删除)
     * @return string 反馈信息
     */
    public function soft_delete()
    {
        $id['id'] = intval($this->input->post('id', TRUE));
        $condition['is_show'] = 0;

        $response = $this->jys_db_helper->soft_delete('user', $id, $condition);

        echo json_encode($response);
    }


    /**
     * 批量上传用户信息
     */
    public function batch_up_user_data(){
        $data['msg'] = "上传附件失败";
        $data['success'] = FALSE;
        $error = array();
        $all = TRUE;

        //上传附件
        $result = $this->jys_attachment->upload_excel_attachment();

        if ($result['success']){
            //解析Excel数据
            $user_data = $this->jys_excel->export_user_data_excel($result['path']);
            if(!empty($user_data) && is_array($user_data)){
                $i = 0;
                foreach ($user_data as $key=>$val){
                    $j = $key + 1;
                    //判断电话和性别 格式是否正确
                    $match = preg_match('/^1(3|4|5|8|9)\d{9}$/', $val['phone']);
                    $is_gender = ($val['gender'] == 1 || $val['gender'] == 0) ? TRUE : FALSE;
                    if ($match && $is_gender){
                        $res = $this->jys_db_helper->add('user', $val);
                        if (! $res['success']){
                            $error[$i] = '第'.$j.'条数据插入失败，可能原因：重复数据或空数据';
                            $i++;
                            $all = FALSE;
                        }
                    }else{
                        $error[$i] = '第'.$j.'条数据错误，可能原因：电话号码或性别格式';
                        $i++;
                        $all = FALSE;
                    }
                }
            }
            $success_result = count($user_data) - count($error);
            $fail_result = count($error);
        }
        if($result['success'] && $all){
            $data['msg'] = "插入数据完成：所有数据插入成功";
            $data['success'] = TRUE;
        }else if ($result['success']){
            $data['msg'] = "插入数据完成：".$success_result." 条数据插入成功，".$fail_result." 条数据插入失败！";
            $data['error'] = $error;
            $data['success'] = TRUE;
        }
        echo json_encode($data);
    }

    /**
     * 获取所有代理商信息
     */
    public function get_agents()
    {
        if ($_SESSION['role_id'] == jys_system_code::ROLE_ADMINISTRATOR) {
            $agents = $this->User_model->get_agents_detail_by_condition(['role_id' => jys_system_code::ROLE_AGENT]);
        } else {
            $agents = array();
        }

        if ($agents){
            $data = ['success' => TRUE, 'msg' => '获取代理商成功', 'data' => $agents];
        }else{
            $data = ['success' => FALSE, 'msg' => '获取代理商失败', 'data' => NULL];
        }

        echo json_encode($data);
    }

    /**
     * 分页获取代理商
     */
    public function get_agents_page(){
        $page          = $this->input->post('page');
        $page_size     = $this->input->post('page_size');
        $keyword       = $this->input->post('keyword', TRUE);
        $start_time   = $this->input->post('start_time',TRUE);
        $end_time   = $this->input->post('end_time',TRUE);
        $user_id = $_SESSION['user_id'];
        $role_id = $_SESSION['role_id'];

        $data = array('success'=>FALSE, 'msg'=>'获取失败', 'data'=>array(), 'total_page'=>0);
        if (intval($page) < 1) {
            $data['msg'] = '请输入要获取的页数';
            echo json_encode($data);
            exit;
        }

        if (empty($page_size) || intval($page_size) < 1) {
            $page_size = 10;
        }

        $data = $this->User_model->get_agents_page($page, $page_size, $is_show = 1, $keyword, $start_time, $end_time, $user_id, $role_id);

        echo json_encode($data);
    }

    /**
     * 更新代理商信息
     */
    public function update_agent_info(){
        $this->form_validation->set_rules('id', '用户ID', 'trim|required|integer');
        $this->form_validation->set_rules('gender', '用户性别', 'trim|integer');
        $this->form_validation->set_rules('email', '用户邮箱', 'trim|regex_match[/^\w+@\w+\..+$/]',['regex_match'=>'邮件格式不符合规则']);
        $this->form_validation->set_rules('level', '会员等级', 'trim|integer');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $id                             = intval($this->input->post('id', TRUE));
            $userinfo = array();
            // 性别
            if (intval($this->input->post('gender', TRUE)) == 1 || intval($this->input->post('gender', TRUE)) == 0) {
                $userinfo['gender'] = intval($this->input->post('gender', TRUE));
            }
            // 邮箱
            if (isset($_POST['email'])) {
                $userinfo['email'] = $this->input->post('email', TRUE) ? $this->input->post('email', TRUE) : NULL;
            }
            // 会员等级
            if (intval($this->input->post('level', TRUE)) > 0) {
                $userinfo['level'] = intval($this->input->post('level', TRUE));
            }
            // 配色
            if (isset($_POST['color'])) {
                $userinfo['color'] = $this->input->post('color', TRUE) ? $this->input->post('color', TRUE) : NULL;
            }
            // 性别
            if ($this->input->post('username', TRUE)) {
                $userinfo['username'] = $this->input->post('username', TRUE);
            }
            if (!empty($userinfo)) {
                $userinfo['update_time']           = date('Y-m-d H:i:s');
                $res_status = $this->jys_db_helper->update('user', $id, $userinfo);
                if($res_status){
                    $data['success'] = TRUE;
                    $data['msg'] = '修改成功';
                }
            }else {
                $data['success'] = TRUE;
                $data['msg'] = '修改成功';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '数据输入错误,请检查手机号 邮箱是否符合规则';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    public function update_agent_passwd() {
        $this->form_validation->set_rules('id', '用户ID', 'trim|required|integer');
        $this->form_validation->set_rules('newPasswd', '新密码', 'trim|min_length[6]|max_length[100]');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $id = intval($this->input->post('id', TRUE));
            $userinfo = array();

            // 密码
            if (isset($_POST['newPasswd'])) {
                $new_password= $this->input->post('newPasswd', TRUE) ? $this->input->post('newPasswd', TRUE) : NULL;
                $userinfo['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            }
            if (!empty($userinfo)) {
                $userinfo['update_time'] = date('Y-m-d H:i:s');
                $res_status = $this->jys_db_helper->update('user', $id, $userinfo);
                if($res_status) {
                    $data['success'] = TRUE;
                    $data['msg'] = '修改成功';
                }
            } else {
                $data['success'] = TRUE;
                $data['msg'] = '修改成功';
            }
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '数据输入错误, 请检查密码输入是否符合规范';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

}