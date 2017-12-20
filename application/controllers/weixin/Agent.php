<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =====================================================================================
 *
 *        Filename:  Agent.php
 *
 *     Description:  代理商控制器
 *
 *         Created:  2017-3-28 15:03:21
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */
Class Agent extends CI_Controller
{

    private $_default_password = 123456;


    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['User_model', 'Banner_model', 'Commodity_model', 'Agent_model']);
    }

    /**
     * 代理商主页
     * @param $category_id 代理商的分类
     */
    public function home($category_id = "")
    {
        $data['category_id'] = $category_id;
        if (!isset($_SESSION['agent_id']) || intval($_SESSION['agent_id']) < 1) {
            header('Location:' . site_url('/weixin/agent/hinter'));
            exit;
        } else {
            $data['banner'] = $this->Common_model->deal_banner_url($this->Banner_model->get_home_banner(5, jys_system_code::BANNER_POSITION_AGENT_HOME, $_SESSION['agent_id']));
            $color = $this->Agent_model->get_agent_index_color($_SESSION['agent_id'], $category_id);
            if ($color['success']) {
                $data['color'] = $color['data'];
            }
            $this->load->view('mobile/agent_index', $data);
        }
    }

    /**
     * 代理商商品详情
     * @param  integer $commodity_id 商品ID
     * @return None                
     */
    public function commodity_detail($commodity_id = 0)
    {
        redirect('/weixin/index/commodity_detail/'.$commodity_id);
    }

    public function agent_search()
    {
        $data['current_timestamp'] = time();
        $this->load->view('mobile/agent_search', $data);
    }

    /**
     * 未登录提示界面
     */

    public function hinter()
    {
        $this->load->view('mobile/sign_in_hinter');
    }

    /**
     * 外部接口登录入口
     */
    public function entrance($order_list = "")
    {
        $agent_id = $this->input->get('agent_id', TRUE);
        $uid = $this->input->get('uid', TRUE);
        $commodity_id = $this->input->get('coid', TRUE);
        $category_id = $this->input->get('cid', TRUE);
        $entrance_method = $this->input->get('entrance_method', TRUE);


        if (intval($agent_id) < 1) {
            header('Location:' . site_url('/weixin/agent/hinter'));
            exit;
        }
        $agent_info = $this->jys_db_helper->get_where('user', array('id'=>$agent_id));
        if (empty($agent_info)) {
            header('Location:' . site_url('/weixin/agent/hinter'));
            exit;
        }

        $res = $this->_auth_exist($agent_id, $uid, intval($entrance_method));
        if ($res) {
            $user = $this->User_model->get_user_detail_by_condition(['user.id' => $res], FALSE, TRUE);
//            file_put_contents(APPPATH.'/logs/agent_entrance_log_'.date('Ymd'), date('Y-m-d H:i:s')."，user:".json_encode($user)."\n", FILE_APPEND);

            unset(
                $_SESSION['user_id'],
                $_SESSION['username'],
                $_SESSION['name'],
                $_SESSION['nickname'],
                $_SESSION['role_id'],
                $_SESSION['avatar_path'],
                $_SESSION['uid'],
                $_SESSION['agent_id'],
                $_SESSION['internal']
            );
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['nickname'] = $user['nickname'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['avatar_path'] = $user['avatar_path'];
            $_SESSION['uid'] = $user['uid'];
            $_SESSION['agent_id'] = $user['agent_id'];
            $_SESSION['internal'] = intval($entrance_method);

            // 先清除session
            unset($_SESSION['commodity_id']);
            unset($_SESSION['category_id']);
            unset($_SESSION['order_list']);

            if (intval($commodity_id) > 0) {
                $_SESSION['commodity_id'] = intval($commodity_id);
            } else if (!empty($category_id)) {
                $_SESSION['category_id'] = $category_id;
            } else if ($order_list == "order_list") {
                $_SESSION['order_list'] = true;
            }

            $appid = $this->config->item('wx_appid');
            $redirect_uri = urlencode(site_url('/weixin/weixin/get_userinfo'));
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//            file_put_contents(APPPATH.'/logs/agent_entrance_log_'.date('Ymd'), date('Y-m-d H:i:s').":url={$url}，res=".var_export($_SESSION, TRUE)."\n\n", FILE_APPEND);

            $location = "Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$url}#wechat_redirect";
            header($location);
        } else {
            show_404();
            exit;
        }
    }

    /**
     * 根据代理商ID返回商品列表
     */
    public function get_commodity_list_by_agent_id()
    {
        $page = $this->input->post('page', TRUE);
        $page_size = $this->input->post('page_size', TRUE);
        $category_id = $this->input->post('category_id', TRUE);

        $data = array('success' => FALSE, 'msg' => '获取商品列表失败');
        if (!isset($_SESSION['agent_id']) || intval($_SESSION['agent_id']) < 1) {
            $data['msg'] = '您未在代理商处登记，无法获取代理商商品列表';
            echo json_encode($data);
            exit;
        } else if (intval($page) < 1 || intval($page_size) < 1) {
            $data['msg'] = '分页信息错误，获取商品列表失败';
            echo json_encode($data);
            exit;
        }
        $user_info = $this->User_model->get_user_detail_by_condition(['user.id' => $_SESSION['user_id']], FALSE, TRUE);
        $condition = array();
        if (!empty($category_id)) {
            $condition['agent_index.name'] = $category_id;
        }
        $data = $this->Commodity_model->paginate_by_agent_id($_SESSION['agent_id'], $page, $page_size, $condition, '', $user_info);

        echo json_encode($data);
    }

    /**
     * 用户认证
     * @param $agent_id 代理商ID
     * @param null $uid 用户唯一标识（一串不重复的字符串）
     * @param int $entrance_method 用户进入系统方式（外部接口或内部验证）
     */
    private function _auth_exist($agent_id, $uid = NULL, $entrance_method = jys_system_code::AGENT_ENTRANCE_METHOD_EXTERNAL)
    {
        if (empty($uid)) {
            return FALSE;
        }

        $agent_user = $this->jys_db_helper->get_where('user_agent', ['uid' => $uid, 'agent_id' => $agent_id]);

        if ($agent_user) {
            return $agent_user['user_id'];
        } else {
            //添加用户数据
            $add_user['username'] = $this->_generate_username($agent_id);

            //用户名生成失败
            if (!$add_user['username']) {
                return FALSE;
            }

            $add_user['password'] = password_hash($this->_default_password, PASSWORD_DEFAULT);
            $add_user['name'] = $add_user['username'];
            $add_user['nickname'] = $add_user['username'];
            $add_user['role_id'] = Jys_system_code::ROLE_AGENT_USER;
            $add_user['create_time'] = date('Y-m-d H:i:s');
            $add_user['update_time'] = date('Y-m-d H:i:s');

            $this->db->trans_start();
            $user = $this->jys_db_helper->add('user', $add_user, TRUE);

            if ($user['success']) {
                //添加代理用户数据
                $add_agent_user['uid'] = $uid;
                $add_agent_user['user_id'] = $user['insert_id'];
                $add_agent_user['internal'] = $entrance_method;
                $add_agent_user['agent_id'] = $agent_id;
                $add_agent_user['create_time'] = date('Y-m-d H:i:s');

                $agent_user = $this->jys_db_helper->add('user_agent', $add_agent_user);

                if ($agent_user['success']) {
                    $this->db->trans_complete();
                    return $user['insert_id'];
                } else {
                    $this->db->trans_rollback();
                }
            }
        }

        return FALSE;
    }

    /**
     * 生成用户名
     *
     * @return bool|string
     */
    private function _generate_username($agent_id)
    {
        // 获取代理商用户名
        $agent_info = $this->jys_db_helper->get_where('user', array('id'=>$agent_id));
        if (empty($agent_info) || !isset($agent_info['username']) || empty($agent_info['username'])) {
            $agent_name = "agent_{$agent_id}_";
        }else {
            $agent_name = $agent_info['username']."_";
        }

        // 获取代理商下最后一个用户的编号
        $this->db->select('user_agent.id, user.username');
        $this->db->join('user', 'user.id = user_agent.user_id', 'left');
        $this->db->where('user_agent.agent_id', $agent_id);
        $result = $this->db->get('user_agent');

        if ($result && $result->num_rows() > 0) {
            $last_agent_user = $result->last_row();
            if (preg_match('/[0-9]+$/', $last_agent_user->username, $result)) {
                $length = strlen($result[0]);
                $number = intval($result[0]) + 1;
                $num_length = strlen('' . $number);

                for ($i = 0; $i < $length - $num_length; $i++) {
                    $number = '0' . $number;
                }

                $username = substr($last_agent_user->username, 0, strlen($last_agent_user->username) - $length) . $number;
            } else {
                $username = $agent_name.'0000001';
            }
        } else {
            $username = $agent_name.'0000001';
        }

        return $username;
    }

    /**
     * 直接通过微信公众号获取用户信息登录接口
     */
    public function weixin_entrance($order_list = "") {
        $agent_id = $this->input->get('agent_id', TRUE);
        $commodity_id = $this->input->get('coid', TRUE);
        $category_id = $this->input->get('cid', TRUE);

        if (intval($agent_id) < 1) {
            header('Location:' . site_url('/weixin/agent/hinter'));
            exit;
        }
        $agent_info = $this->jys_db_helper->get_where('user', array('id'=>$agent_id));
        if (empty($agent_info)) {
            header('Location:' . site_url('/weixin/agent/hinter'));
            exit;
        }

        // 先清除session
        unset($_SESSION['internal']);
        unset($_SESSION['commodity_id']);
        unset($_SESSION['category_id']);
        unset($_SESSION['order_list']);
        unset($_SESSION['agent_id']);

        $_SESSION['internal'] = jys_system_code::AGENT_ENTRANCE_METHOD_INTERNAL;
        if (intval($agent_id) > 0) {
            $_SESSION['agent_id'] = intval($agent_id);
        }
        if (intval($commodity_id) > 0) {
            $_SESSION['commodity_id'] = intval($commodity_id);
        } else if (!empty($category_id)) {
            $_SESSION['category_id'] = $category_id;
        } else if ($order_list == "order_list") {
            $_SESSION['order_list'] = true;
        }

        $appid = $this->config->item('wx_appid');
        $redirect_uri = urlencode(site_url('/weixin/weixin/get_userinfo'));
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $location = "Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$url}#wechat_redirect";
        header($location);
    }
}