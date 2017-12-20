<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: User.php
 *
 *     Description: 用户控制器
 *
 *         Created: 2016-11-15 20:20:06
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
class User extends CI_Controller{

    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->load->library(['Jys_weixin']);
        $this->load->model(['User_model', 'Verification_code_model','Address_model', 'Discount_coupon_model', 'Level_model', 'Message_model', 'Report_model']);
    }
    
    /**
     * 个人中心页面
     */
    public function user_center($plate = NULL){
        $data['js'] = [];
        if(empty($plate) || $plate == 'personal_info'){
            $data['js'] = array('YMDClass.mini', 'iscroll-zoom', 'lrz.all.bundle', 'jquery.photoClip');
        }

        $data['title'] = "赛安生物-登个人中心";
        array_push($data['js'], 'personal_info', 'template');
        $data['css'] = array('personal_info');
        $data['user_right_side'] = $plate ? $plate : 'personal_info';
        $data['main_content'] = 'user_center';
        $data['need_gaode_api'] = TRUE;
        $data['user'] = $this->User_model->get_user_detail_by_condition(['user.id'=>$_SESSION['user_id']]);
        $data['user']['phone'] = $this->User_model->filter_phone($data['user']['phone']);
        $data['isset_nav'] = FALSE;
        $data['isset_footer'] = FALSE;
        $this->load->view('includes/template_view', $data);
    }

    /**
     * 用户注册
     */
    public  function  register(){
        $result['success'] = FALSE;
        $result['msg'] = '用户注册失败';

        //验证表单信息
        $this->form_validation->set_rules('username', '用户名', 'trim|required',['required'=>'用户名为必填']);
        $this->form_validation->set_rules('password', '密码', 'trim|required',['required'=>'密码为必填']);
        $this->form_validation->set_rules('password_confirm', '确认密码', 'trim|required|matches[password]', ['required'=>'确认密码为必填', 'matches'=>'两次密码输入不一致']);
        $this->form_validation->set_rules('phone', '手机号', 'required|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['required'=>'手机号为必填',
            'regex_match'=>'手机号不符合规则']);
        $this->form_validation->set_rules('verification_code', '验证码', 'trim|required', ['required'=>'手机验证码为必填']);

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if($res['success']){
            $verification_code = $this->input->post('verification_code');
            $phone = $this->input->post('phone', TRUE);
            if ($this->Verification_code_model->check_code($phone, $verification_code, Jys_system_code::VERIFICATION_CODE_PURPOSE_REGISTER)) {
                $username = $this->input->post('username', TRUE);
                $password = $this->input->post('password', TRUE);
                $role_id = Jys_system_code::ROLE_USER;

                $res = $this->User_model->register($username, $password, $phone, $role_id);
                if($res['success']){
                    $result['success'] = TRUE;
                    $result['msg'] = '用户注册成功';
                }else {
                    $result['success'] = FALSE;
                    $result['msg'] = $res['msg'];
                }
            }else {
                $result['success'] = FALSE;
                $result['msg'] = '验证码不正确';
            }
        }else{
            $result['success'] = FALSE;
            $result['error'] = $res['msg'];
        }
        echo json_encode($result);

    }

    /**
     * 检测用户名是否重复
     */
    public function check_username() {
        //验证表单信息
        $this->form_validation->set_rules('username', '用户名', 'trim|required',['required'=>'用户名为必填']);

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if($res['success']) {
            $username = $this->input->post('username');
            if ($this->User_model->username_is_available($username)) {
                echo 'true';
            }else {
                echo 'false';
            }
        }else {
            echo 'false';
        }
    }

    /**
     * 检测手机号是否重复，表单验证插件专用！！！
     */
    public function check_phone() {
        //验证表单信息
        $this->form_validation->set_rules('phone', '手机号码', 'trim|required',['required'=>'手机号码为必填']);

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if($res['success']) {
            $phone = $this->input->post('phone');
            if ($this->User_model->phone_is_available($phone)) {
                echo 'true';
            }else {
                echo 'false';
            }
        }else {
            echo 'false';
        }
    }

    /**
     * 用户更新信息
     */
    public  function  update_user(){
        $result['success'] = FALSE;
        $result['msg'] = '用户更新信息失败';

        //验证表单信息
        $this->form_validation->set_rules('username', '用户名', 'trim|max_length[50]');
        $this->form_validation->set_rules('password', '密码', 'trim|max_length[100]');
        $this->form_validation->set_rules('name', '用户姓名', 'trim|max_length[255]');
        $this->form_validation->set_rules('nickname', '昵称', 'trim|max_length[200]');
        $this->form_validation->set_rules('gender', '性别', 'trim');
        $this->form_validation->set_rules('birthday', '出生日期', 'trim');
        $this->form_validation->set_rules('phone', '手机号', 'regex_match[/^1(3|4|5|7|8)\d{9}$/]', ['regex_match'=>'手机号不符合规则']);
        $this->form_validation->set_rules('email', '邮件', 'trim|regex_match[/^[a-z]([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/]',['regex_match'=>'邮件格式不符合规则']);

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if($res['success']){
            $data = $this->input->post();
            $data['update_time'] = date('Y-m-d H:i:s');
            if (isset($data['phone']) && $data['phone'] == ''){
                $data['phone'] = NULL;
            }

            $res_status = $this->jys_db_helper->set_update('user', $_SESSION['user_id'], $data);
            if($res_status){
                $result['success'] = true;
                $result['msg'] = '用户更新信息成功';
            }
        }else{
            $result['success'] = FALSE;
            $result['error'] = $res['msg'];
        }
        echo json_encode($result);

    }

    /**
     * 保存用户信息
     */
    public function save_user_info(){
        $this->form_validation->set_rules('nickname', '昵称', 'trim|max_length[200]');
        $this->form_validation->set_rules('gender', '性别', 'trim');
        $this->form_validation->set_rules('birthday', '出生日期', 'trim');
        $this->form_validation->set_rules('avatar', '头像附件ID', 'trim');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if($res['success']){
            $post['nickname']   = $this->input->post('nickname', TRUE);
            $post['gender']     = $this->input->post('gender', TRUE);
            $post['birthday']   = $this->input->post('birthday', TRUE);
            $avatar             = $this->input->post('avatar', TRUE);
            if (isset($avatar) && !empty($avatar) && is_numeric($avatar)){
                $post['avatar'] = $avatar;
            }
            $post['update_time'] = date('Y-m-d H:i:s');

            $result = $this->jys_db_helper->update('user', $_SESSION['user_id'], $post);
            if($result){
                $data['success'] = true;
                $data['msg'] = '修改成功';
            }
        }else{
            $data['success'] = FALSE;
            $data['error'] = $res['msg'];
        }
        echo json_encode($data);
    }

    /**
     *  获取个人用户信息
     */
    public function get_personal_info(){
        $result['success'] = FALSE;
        $result['msg'] = '获取用户个人信息失败';

        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }else{
            $user_id = NULL;
        }
        $id  = $this->input->post('id', TRUE) ? $this->input->post('id', TRUE) : $user_id;
        if (!empty($id)) {
            $result['data'] = $this->User_model->get_user_detail_by_condition(['user.id'=>$id], FALSE, TRUE);
            if($result['data']){
                $result['success'] = true;
                $result['msg'] = '获取用户个人信息成功';
            }        
        }
    
        echo json_encode($result);
    }

    /**
     * 修改密码
     */
    public function modify_psd(){
        //验证表单信息
        $this->form_validation->set_rules('old_psd', '原密码', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('new_psd', '新密码', 'trim|required|max_length[100]');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $result = $this->Common_model->deal_validation_errors();

        if ($result['success']){
            //处理数据
            $old_psd = $this->input->post('old_psd', TRUE);
            $new_psd = $this->input->post('new_psd', TRUE);

            if ($this->User_model->check_psd($old_psd)){
                $data['success'] = $this->jys_db_helper->update('user', $_SESSION['user_id'], ['password'=>password_hash($new_psd, PASSWORD_DEFAULT)]);
                $data['msg'] = '修改成功';
            }else{
                $data['success'] = FALSE;
                $data['msg'] = '原密码错误';
            }
            
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '修改密码输入错误';
        }

        echo json_encode($data);
    }

    /**
     * 根据验证码修改密码
     */
    public function modify_psd_by_verification(){
        $this->form_validation->set_rules('phone', '手机号码', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['regex_match'=>'请填写正确的手机号']);
        $this->form_validation->set_rules('code', '验证码', 'trim|required');
        $this->form_validation->set_rules('new_password', '密码', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('confirm_password', '密码', 'trim|required|matches[new_password]');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $result = $this->Common_model->deal_validation_errors();

        if ($result['success']){
            $user_id    = $_SESSION['user_id'];
            $phone      = $this->input->post('phone', TRUE);
            $code       = $this->input->post('code', TRUE);
            $password   = $this->input->post('new_password', TRUE);

            if ($this->Verification_code_model->check_code($phone, $code, Jys_system_code::VERIFICATION_CODE_PURPOSE_PHONE, $user_id)){
                $data['success'] = $this->jys_db_helper->update('user', $user_id, ['password'=>password_hash($password, PASSWORD_DEFAULT)]);
                $data['msg'] = '修改成功';
            }else{
                $data['success'] = FALSE;
                $data['msg'] = '修改失败';
            }
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '修改密码验证不通过';
        }

        echo json_encode($data);
    }

    /**
     * 验证手机号与用户是否绑定
     */
    public function check_user_phone_valid(){
        $this->form_validation->set_rules('phone', '手机号码', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]',['regex_match'=>'请填写正确的手机号']);

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $result = $this->Common_model->deal_validation_errors();

        if ($result['success']){
            $user_id    = $_SESSION['user_id'];
            $phone      = $this->input->post('phone', TRUE);

            $user = $this->jys_db_helper->get('user', $user_id);
            if (!empty($user['phone'])) {
                if ($user['phone'] == $phone) {
                    $data = array('success' => TRUE, 'msg' => '手机号与用户已绑定手机号一致');
                } else {
                    $data = array('success' => FALSE, 'msg' => '手机号与用户绑定的手机号不一致');
                }
            } else {
                $data = array('success' => FALSE, 'msg' => '您还未绑定手机号，请前往PC端官网登陆并绑定手机号');
            }
        } else {
            $data['success'] = FALSE;
            $data['msg'] = '输入手机号不正确';
        }

        echo json_encode($data);
    }

    /**
     * 显示地址信息
     */
    public function show_address($limit = FALSE){
        $data = $this->Address_model->show_address($limit);
        if (isset($_SESSION['agent_id']) && intval($_SESSION['agent_id']) > 0) {
            $data['is_agent'] = TRUE;
        }else {
            $data['is_agent'] = FALSE;
        }

        echo json_encode($data);
    }

    /**
     * 添加地址信息
     */
    public function add_address(){
        //验证表单信息
        $this->form_validation->set_rules('name', '收货人姓名', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('phone', '收货人电话', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]');
        $this->form_validation->set_rules('province', '省份名称', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('province_code', '省份代码', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('city', '城市名称', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('city_code', '城市代码', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('district', '区县名称', 'trim|max_length[100]');
        $this->form_validation->set_rules('district_code', '区县代码', 'trim|max_length[100]');
        $this->form_validation->set_rules('address', '收货地址', 'trim|required|max_length[200]');
        $this->form_validation->set_rules('default', '是否默认地址', 'trim');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $post['name']           = $this->input->post('name', TRUE);
            $post['phone']          = $this->input->post('phone', TRUE);
            $post['province']       = $this->input->post('province', TRUE);
            $post['province_code']  = $this->input->post('province_code', TRUE);
            $post['city']           = $this->input->post('city', TRUE);
            $post['city_code']      = $this->input->post('city_code', TRUE);
            $post['district']       = $this->input->post('district', TRUE);
            $post['district_code']  = $this->input->post('district_code', TRUE);
            $post['address']        = $this->input->post('address', TRUE);
            $post['default']        = ($this->input->post('default', TRUE) == "true" || $this->input->post('default', TRUE) == 1)  ? 1 : 0;
            $post['user_id']        = $_SESSION['user_id'];
            $post['create_time']    = date('Y-m-d H:i:s');
            $post['update_time']    = $post['create_time'];

            $data = $this->Address_model->add_address($post);
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 修改地址信息
     */
    public function update_address(){
        //验证表单信息
        $this->form_validation->set_rules('id', '地址ID', 'trim|required|numeric');
        $this->form_validation->set_rules('name', '收货人姓名', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('phone', '收货人电话', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]');
        $this->form_validation->set_rules('province_code', '省份代码', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('city', '城市名称', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('city_code', '城市代码', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('district', '区县名称', 'trim|max_length[100]');
        $this->form_validation->set_rules('district_code', '区县代码', 'trim|max_length[100]');
        $this->form_validation->set_rules('address', '收货地址', 'trim|required|max_length[200]');
        $this->form_validation->set_rules('default', '是否默认地址', 'trim');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            //处理数据
            $user_id                = $_SESSION['user_id'];
            $id                     = intval($this->input->post('id', TRUE));
            $post['name']           = $this->input->post('name', TRUE);
            $post['phone']          = $this->input->post('phone', TRUE);
            $post['province']       = $this->input->post('province', TRUE);
            $post['province_code']  = $this->input->post('province_code', TRUE);
            $post['city']           = $this->input->post('city', TRUE);
            $post['city_code']      = $this->input->post('city_code', TRUE);
            $post['district']       = $this->input->post('district', TRUE);
            $post['district_code']  = $this->input->post('district_code', TRUE);
            $post['address']        = $this->input->post('address', TRUE);
            $post['default']        = ($this->input->post('default', TRUE) == "true" || $this->input->post('default', TRUE) == 1) ? 1 : 0;
            $post['user_id']        = $user_id;
            $post['update_time']    = date('Y-m-d H:i:s');

            $data = $this->Address_model->update_address($id, $user_id, $post);

        }else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误';
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 删除地址信息
     */
    public function delete_address(){
        $result = array('success'=>FALSE, 'msg'=>'删除地址失败');
        $id = intval($this->input->post('id', TRUE));
        if (intval($id) < 1) {
            $result['msg'] = '地址ID不正确，删除失败';
            echo json_encode($result);
            exit;
        }

        if ($this->jys_db_helper->delete('address', $id)) {
            $result['success'] = TRUE;
            $result['msg'] = '删除地址成功';
        }
        echo json_encode($result);
    }

    /**
     * 获取当前用户的优惠券
     */
    public function get_discount_coupon_by_user_id()
    {
        $data = ['success' => FALSE, 'msg' => '获取优惠券失败', 'data' => NULL];
        $user_id = $_SESSION['user_id'];
        if (empty($user_id)){
            show_404();
            exit;
        }

        $discount_coupon = $this->Discount_coupon_model->get_user_discount_coupon_list_by_user_id($user_id, jys_system_code::USER_DISCOUNT_COUPON_STATUS_UNUSED)['data'];
        if (!empty($discount_coupon)){
            $data = ['success' => TRUE, 'msg' => '获取优惠券成功', 'data' => $discount_coupon];
        }

        echo json_encode($data);
    }

    /**
     * 获取已发布的优惠券
     */
    public function get_pulished_discount_coupon()
    {
        $user_id = $_SESSION['user_id'];
        $pulished_discount_coupon = $this->Discount_coupon_model->get_discount_coupon_by_status_id($user_id)['data'];
        if (!empty($pulished_discount_coupon)){
            $data = ['success' => TRUE, 'msg' => '获取已发布优惠券成功', 'data' => $pulished_discount_coupon];
        }else{
            $data = ['success' => FALSE, 'msg' => '获取已发布优惠券失败', 'data' => NULL];
        }

        echo json_encode($data);
    }

    /**
     * 用户点击获取优惠券，将数据加入数据库
     */
    public function add_discount_coupon_to_user()
    {
        $discount_coupon_id = $this->input->POST('id', TRUE);
        if (empty($discount_coupon_id) || intval($discount_coupon_id) < 1){
            $data = ['success' => FALSE, 'msg' => '领取优惠券失败，参数错误'];
            echo json_encode($data);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $result = $this->Discount_coupon_model->check_user_discount_coupon_by_id($discount_coupon_id, $user_id);
        if (!$result['success']){
            $discount_coupon_info = $this->Discount_coupon_model->get_discount_coupon_info_by_id($discount_coupon_id);
            $info['user_id'] = $user_id;
            $info['discount_coupon_id'] = $discount_coupon_id;
            $info['start_time'] = $discount_coupon_info['start_time'];
            $info['end_time'] = $discount_coupon_info['end_time'];
            $info['status_id'] = $discount_coupon_info['status_id'];
            $info['create_time'] = date('Y-m-d H:i:s');
            $result = $this->jys_db_helper->add('user_discount_coupon', $info);
            if ($result['success']){
                $data = ['success' => TRUE, 'msg' => '获取优惠券成功，请查看'];
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '您已获取过此优惠券，领取失败'];
        }

        echo json_encode($data);
    }

    /**
     * 获取系统中所有的会员等级
     */
    public function get_all_level() {
        $result = $this->Level_model->get_all_level();

        echo json_encode($result);
    }

    /**
     * 根据亲属id获取亲属信息
     */
    public function get_family_info_by_id($id = NULL)
    {
        if (empty($id) || intval($id) < 1){
            $data['success'] = FALSE;
            $data['msg'] = '参数错误';
            return $data;
        }

        $family_info = $this->jys_db_helper->get('family', $id);
        if ($family_info){
            $data['success'] = TRUE;
            $data['msg'] = '获取亲属信息成功';
            $data['data'] = $family_info;
        }else{
            $data['success'] = FALSE;
            $data['msg'] = '获取亲属信息失败';
            $data['data'] = NULL;
        }

        echo json_encode($data);
    }

    /**
     *获取该用户所有亲属信息
     */
    public function get_all_family_info()
    {
        $user_id = $_SESSION['user_id'];

        $family_infos = $this->jys_db_helper->get_where_multi('family', array('user_id' => $user_id));
        if ($family_infos){
            $data['success'] = TRUE;
            $data['msg'] = '获取所有亲属信息成功';
            $data['data'] = $family_infos;
        }else {
            $data['success'] = FALSE;
            $data['msg'] = '获取所有亲属信息失败';
            $data['data'] = NULL;
        }

        echo json_encode($data);
    }
    /**
     * 增加亲属关系
     */
    public function add_family_relation()
    {
        $this->form_validation->set_rules('name', '姓名', 'trim|required', ['required' => '姓名必填']);
        $this->form_validation->set_rules('gender', '性别', 'trim|required', ['required' => '性别必填']);
        $this->form_validation->set_rules('phone', '联系电话', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]', ['required' => '电话号码必填', 'regex_match' => '电话号码格式不正确']);
        $this->form_validation->set_rules('birth', '出生年月日', 'trim|required', ['required' => '出生年月日必填']);
        $this->form_validation->set_rules('identity_card', '亲属身份证号', 'trim|required', ['required' => '身份证号必填']);
        $this->form_validation->set_rules('medication_history', '用药史', 'trim');
        $this->form_validation->set_rules('clinical_history', '亲属病史', 'trim');
        $this->form_validation->set_rules('relation', '血缘关系', 'trim|required', ['required' => '姓名必填']);
        $this->form_validation->set_rules('health_status', '健康状态', 'trim');
        $result = $this->Common_model->deal_validation_errors();

        if ($result['success']){
            $post['user_id'] = $_SESSION['user_id'];
            $post['name'] = $this->input->POST('name' ,TRUE);
            $post['gender'] = $this->input->POST('gender' ,TRUE);
            $post['phone'] = $this->input->POST('phone' ,TRUE);
            $post['identity_card'] = $this->input->POST('identity_card' ,TRUE);
            $post['clinical_history'] = $this->input->POST('clinical_history' ,TRUE);
            $post['relation'] = $this->input->POST('relation' ,TRUE);
            $post['birth'] = $this->input->POST('birth' ,TRUE);
            $post['medication_history'] = $this->input->POST('medication_history' ,TRUE);
            $post['health_status'] = $this->input->POST('health_status' ,TRUE);

            $result = $this->jys_db_helper->add('family', $post);
            if ($result['success']){
                $data = ['success' => TRUE, 'msg' => '添加成功'];
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '添加失败', 'error' => $result['msg']];
        }

        echo json_encode($data);
    }

    /**
     * 修改亲属关系信息
     */
    public function update_family_relation_by_id($id = NULL)
    {
        if (empty($id) || intval($id) < 1){
            show_404();
            exit;
        }
        $this->form_validation->set_rules('name', '姓名', 'trim|required', ['required' => '姓名必填']);
        $this->form_validation->set_rules('gender', '性别', 'trim|required', ['required' => '性别必填']);
        $this->form_validation->set_rules('phone', '联系电话', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]', ['required' => '电话号码必填', 'regex_match' => '电话号码格式不正确']);
        $this->form_validation->set_rules('birth', '出生年月日', 'trim|required', ['required' => '出生年月日必填']);
        $this->form_validation->set_rules('identity_card', '亲属身份证号', 'trim|required', ['required' => '身份证号必填']);
        $this->form_validation->set_rules('medication_history', '用药史', 'trim');
        $this->form_validation->set_rules('clinical_history', '亲属病史', 'trim');
        $this->form_validation->set_rules('relation', '血缘关系', 'trim|required', ['required' => '姓名必填']);
        $this->form_validation->set_rules('health_status', '健康状态', 'trim');
        $result = $this->Common_model->deal_validation_errors();

        if ($result['success']){
            $post['user_id'] = $_SESSION['user_id'];
            $post['name'] = $this->input->POST('name' ,TRUE);
            $post['gender'] = $this->input->POST('gender' ,TRUE);
            $post['phone'] = $this->input->POST('phone' ,TRUE);
            $post['identity_card'] = $this->input->POST('identity_card' ,TRUE);
            $post['clinical_history'] = $this->input->POST('clinical_history' ,TRUE);
            $post['relation'] = $this->input->POST('relation' ,TRUE);
            $post['birth'] = $this->input->POST('birth' ,TRUE);
            $post['medication_history'] = $this->input->POST('medication_history' ,TRUE);
            $post['health_status'] = $this->input->POST('health_status' ,TRUE);

            $result = $this->jys_db_helper->update('family', $id, $post);

            if ($result){
                $data = ['success' => TRUE, 'msg' => '更新成功'];
            }
        }else{
            $data = ['success' => FALSE, 'msg' => '更新失败', 'error' => $result['msg']];
        }

        echo json_encode($data);
    }

    /**
     * 删除亲属关系
     */
    public function delete_family_relation_by_id($id = NULL)
    {
        if (empty($id) || intval($id) < 1){
            show_404();
            exit;
        }
        $result = $this->jys_db_helper->delete('family', $id);
        if ($result){
            $data = ['success' => TRUE, 'msg' => '删除成功'];
        }else{
            $data = ['success' => FALSE, 'msg' => '删除失败'];
        }

        echo json_encode($data);
    }

    /**
     * 关注某个贴吧用户接口
     */
    public function focus_user() {
        $result = array('success'=>TRUE, 'msg'=>'关注失败');
        $focus_id = $this->input->post('focus_id', TRUE);

        if (intval($focus_id) < 1) {
            $result['msg'] = '请选择要关注的用户';
            echo json_encode($result);
            exit;
        }

        $result = $this->User_model->focus_user($_SESSION['user_id'], $focus_id);
        echo json_encode($result);
    }

    /**
     * 取消关注某个贴吧用户接口
     */
    public function cancel_focus_user() {
        $result = array('success'=>TRUE, 'msg'=>'取消关注失败');
        $focus_id = $this->input->post('focus_id', TRUE);

        if (intval($focus_id) < 1) {
            $result['msg'] = '请选择要取消关注的用户';
            echo json_encode($result);
            exit;
        }

        if ($this->jys_db_helper->delete_by_condition('focus_user', ['user_id'=>$_SESSION['user_id'], 'focus_id'=>$focus_id])) {
            $result['success'] = TRUE;
            $result['msg'] = '取消关注成功';
        }
        echo json_encode($result);
    }

    /**
     * 分页获取当前用户在贴吧关注的用户列表接口
     *
     * @param int $page 页数
     * @param int $page_size 页大小
     */
    public function paginate_focus_user($page = 1, $page_size = 10) {
        $result = $this->User_model->paginate_focus_user($page, $page_size, $_SESSION['user_id']);

        echo json_encode($result);
    }

    /**
     * 分页获取当前用户在贴吧的粉丝列表接口
     *
     * @param int $page 页数
     * @param int $page_size 页面大小
     */
    public function paginate_fans($page = 1, $page_size = 10) {
        $result = $this->User_model->paginate_fans($page, $page_size, $_SESSION['user_id']);

        echo json_encode($result);
    }

    /**
     * 分页获取当前用户的站内信发送数据
     *
     * @param int $page
     * @param int $page_size
     */
    public function paginate_send_message($page = 1, $page_size = 10){
        $result = $this->Message_model->paginate($page, $page_size, ['send_user.id'=>$_SESSION['user_id']]);

        echo json_encode($result);
    }

    /**
     * 分页获取当前用户的站内信接收数据
     *
     * @param int $page
     * @param int $page_size
     */
    public function paginate_receive_message($page = 1, $page_size = 10){
        $result = $this->Message_model->paginate($page, $page_size, ['receive_user.id'=>$_SESSION['user_id']]);

        echo json_encode($result);
    }

    /**
     * 添加发送信息
     */
    public function add_message(){
        $result = array('success'=>FALSE, 'msg'=>'添加失败');
        //验证表单信息
        $this->form_validation->set_rules('receive_user_id', '接收用户ID', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('content', '内容', 'trim|min_length[1]|max_length[500]|required');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if ($res['success']){
            $data['user_id']            = $_SESSION['user_id'];
            $data['receive_user_id']    = intval($this->input->post('receive_user_id', TRUE));
            $data['content']            = $this->input->post('content', FALSE);
            $data['create_time']        = date('Y-m-d H:i:s');

            $result = $this->jys_db_helper->add('message', $data);
        }else {
            $result['msg'] = '参数错误';
            $result['error'] = $res['msg'];
        }

        echo json_encode($result);
    }

    /**
     * 根据ID获取站内信详情
     */
    public function get_message_by_id(){
        $id = $this->input->post('message_id', TRUE);
        $result = $this->Message_model->get_message_by_id($id);
        if ($result['data']['send_user_id'] == $_SESSION['user_id']){
            $result['data']['status_type'] = 1;
        }else{
            $result['data']['status_type'] = 2;
        }

        echo json_encode($result);
    }

    /**
     * 修改信息状态为已读
     */
    public function read_message(){
        $id = $this->input->post('message_id', TRUE);
        $res = $this->jys_db_helper->update('message', $id, ['status_id'=>Jys_system_code::MESSAGE_STATUS_READ]);
        if ($res){
            $result['success'] = TRUE;
            $result['msg'] = '修改成功';
        }else{
            $result['success'] = FALSE;
            $result['msg'] = '修改失败';
        }

        echo json_encode($result);
    }

    /**
     * 获取接收信息者的数据
     */
    public function get_receive_msg_user_info(){
        $user_id = intval($this->input->post('user_id', TRUE));
        $res = $this->User_model->get_user_detail_by_condition(['user.id'=>$user_id]);

        if($res){
            $result['success'] = TRUE;
            $result['msg'] = '获取成功';
            $result['data']['id'] = $res['id'];
            $result['data']['username'] = $res['username'];
        }else{
            $result['success'] = FALSE;
            $result['msg'] = '获取失败';
        }

        echo json_encode($result);
    }

    /**
     * 获取我的报告
     */
    public function get_my_report(){
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        
        $result = $this->Report_model->get_report_by_user_id($user_id);

        echo json_encode($result);
    }

    /**
     * 根据订单编号获取报告
     */
    public function get_report_by_order_number() {
        $data = [
            'success' => FALSE,
            'msg' => '没有报告',
            'data' => NULL
        ];
        $order_number = '9358461493003706';
//        $order_number = intval($this->input->post('order_number', TRUE));
        $report_info = $this->Report_model->get_report_by_order_number($order_number);
        if (!empty($report_info)) {
            $data = [
                'success' => TRUE,
                'msg' => '获取报告成功',
                'data' => $report_info
            ];
        }

        echo json_encode($data);
    }

    /**
     * 用户填写检测报告的个人信息
     */
    public function add_report_userInfo() {
        //验证表单信息
        $this->form_validation->set_rules('number', '报告编号', 'trim|required');
        $this->form_validation->set_rules('name', '姓名', 'trim|required');
        $this->form_validation->set_rules('birth', '出生年月', 'trim|required');
        $this->form_validation->set_rules('gender', '性别', 'trim|required|is_natural');
        $this->form_validation->set_rules('phone', '手机号码', 'trim|required|regex_match[/^1(3|4|5|7|8)\d{9}$/]');
        $this->form_validation->set_rules('identity_card', '身份证号', 'trim|regex_match[/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)$/]');
        $this->form_validation->set_rules('province', '省份名称', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('province_code', '省份代码', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('city', '城市名称', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('city_code', '城市代码', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('district', '区县名称', 'trim|max_length[100]');
        $this->form_validation->set_rules('district_code', '区县代码', 'trim|max_length[100]');
        $this->form_validation->set_rules('address', '地址', 'trim|required|max_length[200]');
//        $this->form_validation->set_rules('smoking', '是否吸烟', 'trim|required|is_natural');
        $this->form_validation->set_rules('height', '身高', 'trim|numeric');
        $this->form_validation->set_rules('weight', '体重', 'trim|numeric');
        $this->form_validation->set_rules('personal_history', '个人病史', 'trim|max_length[200]');
        $this->form_validation->set_rules('family_history', '家族病史', 'trim|max_length[200]');
//        $this->form_validation->set_rules('blood_relationship', '血缘关系', 'trim|required|numeric');
        $this->form_validation->set_rules('project', '检测项目', 'trim|required|max_length[200]');
        
        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']) {
            $number             = $this->input->post('number', TRUE);
            $name               = $this->input->post('name', TRUE);
            $birth              = $this->input->post('birth', TRUE);
            $gender             = $this->input->post('gender', TRUE);
            $phone              = $this->input->post('phone', TRUE);
//            $smoking            = $this->input->post('smoking', TRUE);
            $smoking            = 0;
            $identity_card      = $this->input->post('identity_card', TRUE);
            $province           = $this->input->post('province', TRUE);
            $province_code      = $this->input->post('province_code', TRUE);
            $city               = $this->input->post('city', TRUE);
            $city_code          = $this->input->post('city_code', TRUE);
            $district           = $this->input->post('district', TRUE);
            $district_code      = $this->input->post('district_code', TRUE);
            $address            = $this->input->post('address', TRUE);
            $height             = $this->input->post('height', TRUE);
            $weight             = $this->input->post('weight', TRUE);
            $personal_history   = $this->input->post('personal_history', TRUE);
            $family_history     = $this->input->post('family_history', TRUE);
//            $blood_relationship = $this->input->post('blood_relationship', TRUE);
            $project            = $this->input->post('project', TRUE);
            $order_commodity_id = $this->input->post('order_commodity_id', TRUE);

            $data = $this->Report_model->add_report_userInfo($number, $name, $birth, $gender, $phone, $smoking, $identity_card, $province, $province_code, $city, $city_code, $district, $district_code, $address, $height, $weight, $personal_history, $family_history, $project, $order_commodity_id);
        } else{
            $data['success'] = FALSE;
            $data['msg'] = '输入有错误，'.$res['msg'];
            $data['error'] = $res['msg'];
        }
        echo json_encode($data);
    }
    
    /**
     * 获取用户的手机号码
     */
    public function get_phone_by_user_id() {
        $result['success'] = FALSE;
        $result['msg'] = '获取用户电话号码失败';

        $id = $_SESSION['user_id'];
        $res['data'] = $this->User_model->get_user_detail_by_condition(['user.id' => $id]);
        if($res['data']['phone']){
            $result['success'] = true;
            $result['msg'] = '获取用户电话号码成功';
            $result['data'] = substr_replace($res['data']['phone'],'****','3','4');
        }else {
            $result['success'] = TRUE;
            $result['data'] = '未绑定';
        }
        echo json_encode($result);
    }

    /**
     * 根据报告编号查询报告是否存在,存在则获取可选择的检测项目
     */
    public function check_report()
    {
        $data = ['success' => FALSE,
                 'msg'     => '查询失败',
                 'data'    => array()
        ];

        //验证表单信息
        $this->form_validation->set_rules('number', '报告编号', 'trim|required');

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();
        if ($res['success']) {
            $number = $this->input->post('number', TRUE);
            $result = $this->Report_model->check_report($number);

            if ($result['success']) {
                $data['success']     = TRUE;
                $data['msg']         = '查询成功';
                $data['data']        = $result['data'];
            }else {
                $data['msg'] = '报告编号不存在,请输入正确的报告编号';
            }
        } else{
            $data['msg']   = $res['msg'];
            $data['error'] = $res['msg'];
        }

        echo json_encode($data);
    }

    /**
     * 用户微信扫码登录
     */
    public function login_from_weixin(){
        $appid = $this->config->item('wx_open_appid');
        $redirect_uri = urlencode(site_url('/weixin/weixin/get_userinfo_by_unionid'));
        $url = base_url();

        $this->jys_weixin->login_from_weixin($appid, $redirect_uri, $url);
    }

    /**
     * 用户个人中心微信账号绑定
     */
    public function  bind_from_weixin(){
        $appid = $this->config->item('wx_open_appid');
        $redirect_uri = urlencode(site_url('/weixin/weixin/bind_userinfo_by_unionid'));
        $url = base_url() . '/user/user_center/account_contact';

        $this->jys_weixin->login_from_weixin($appid, $redirect_uri, $url);
    }

    /**
     * 用户个人中心微信账号解除绑定
     */
    public function  unbind_from_weixin(){
        $id = $_SESSION['user_id'];
        $update = ['openid' => NULL];
        $condition = ['id' => $id];

        $result = $this->User_model->udpate_user_by_condition($condition, $update);

        echo json_encode($result);
    }

    /**
     * 用户填写信息
     */
    public  function  fill_in_userinfo(){
        $result['success'] = FALSE;
        $result['msg'] = '操作失败';

        //验证表单信息
        $this->form_validation->set_rules('username', '用户名', 'trim|required',['required'=>'用户名为必填']);
        $this->form_validation->set_rules('password', '密码', 'trim|required',['required'=>'密码为必填']);
        $this->form_validation->set_rules('password_confirm', '确认密码', 'trim|required|matches[password]', ['required'=>'确认密码为必填', 'matches'=>'两次密码输入不一致']);

        //表单验证是否通过, 若不通过 返回表单错误信息，停止执行
        $res = $this->Common_model->deal_validation_errors();

        if($res['success']){
            $username = $this->input->post('username', TRUE);
            $password = $this->input->post('password', TRUE);
            $phone    = $this->input->post('phone', TRUE);
            $role_id = Jys_system_code::ROLE_USER;

            $result = $this->User_model->weixin_register($username, $password, $phone, $role_id);
        }else{
            $result['success'] = FALSE;
            $result['msg'] = '操作失败'.$res['msg'];
        }
        echo json_encode($result);

    }
    
}