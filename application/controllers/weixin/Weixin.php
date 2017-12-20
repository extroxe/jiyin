<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =====================================================================================
 *
 *        Filename: Weixin.php
 *
 *     Description: 微信控制器
 *
 *         Created: 2016-11-23 19:19:08
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
class Weixin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library(['Jys_weixin', 'Jys_weixin_pay']);
        $this->load->model(['User_model', 'Order_model', 'Commodity_model']);
    }

    /**
     * 接受微信主动消息的接口
     */
    public function index()
    {
        $signature = $this->input->get("signature");
        $timestamp = $this->input->get("timestamp");
        $nonce = $this->input->get("nonce");
        $echoStr = $this->input->get("echostr");

        file_put_contents(APPPATH . "/logs/weixin" . date('Y-m-d') . ".log", date('Y-m-d H:i:s') . "  日志信息：{$signature}---{$timestamp}---{$nonce}---{$echoStr}\n", FILE_APPEND);
        if (!empty($echoStr)) {
            if ($this->jys_weixin->check_signature($signature, $timestamp, $nonce)) {
                // 设置服务器配置时需要返回echoStr参数的内容
                echo $echoStr;
                exit;
            } else {
                echo "false";
            }
        } else {
            // 正常的数据
            $this->message_router(file_get_contents("php://input"));
        }
    }

    /**
     * 接收消息路由器
     */
    private function message_router($message)
    {
        if (empty($message)) {
            echo "";
            exit;
        }
        //file_put_contents(APPPATH."/logs/weixin".date('Y-m-d').".log", date('Y-m-d H:i:s')."  日志信息：{$message}\n", FILE_APPEND);
        // 以下代码来自微信官方demo
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($message, 'SimpleXMLElement', LIBXML_NOCDATA);
        $fromUsername = $this->jys_weixin->object_item_to_string($postObj->FromUserName);
        $toUsername = $this->jys_weixin->object_item_to_string($postObj->ToUserName);
        $msgType = $this->jys_weixin->object_item_to_string($postObj->MsgType);

        switch ($msgType) {
            case "event" :
                // 事件推送消息
                $this->event_router($message);
                break;
            case "text" :
                // 文本消息
                $content = "您好，为了能及时回复您的消息，您有任何需要联系线上客服，请点击下面链接：\nhttp://suo.im/2LR9Yv\n或者您可拨打免费400电话：400-100-3908\n客服服务时间—工作日（9:00-17:00）";
                $this->jys_weixin->reply_text_message($fromUsername, $toUsername, $content);
                break;
            case "image" :
                // 图片消息
                break;
            case "voice" :
                // 语音消息
                break;
            case "video" :
                // 视频消息
                break;
            case "shortvideo" :
                // 视频消息
                break;
            case "location" :
                // 地理位置消息
                break;
            case "link" :
                // 链接消息
                break;
            default :
                echo "";
                break;
        }
    }

    /**
     * 接收事件推送消息路由器
     */
    private function event_router($message)
    {
        if (empty($message)) {
            echo "";
            exit;
        }

        // 以下代码来自微信官方demo
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($message, 'SimpleXMLElement', LIBXML_NOCDATA);
        $fromUsername = $this->jys_weixin->object_item_to_string($postObj->FromUserName);
        $toUsername = $this->jys_weixin->object_item_to_string($postObj->ToUserName);
        $msgType = $this->jys_weixin->object_item_to_string($postObj->MsgType);
        $event = $this->jys_weixin->object_item_to_string($postObj->Event);

        if ($msgType != "event") {
            echo "";
            exit;
        }

        switch ($event) {
            case "subscribe" :
                // 关注微信号
                $content = "Hi，欢迎关注“基因城”服务号！我们竭诚为您服务！\n如有任何疑问，请拨打客服热线电话：400-100-3908\n或者点击链接（http://suo.im/2LR9Yv）直接与在线客服进行联系。
（客服电话时间为工作日 9:00~17:00）\n基因城，引领全新生活方式";
                $this->jys_weixin->reply_text_message($fromUsername, $toUsername, $content);
                break;
            case "unsubscribe" :
                // 取消关注微信号
                break;
            case "subscribe" :
                // 扫描带参数二维码事件，用户未关注时，进行关注后的事件推送
                // 如果用户还未关注公众号，则用户可以关注公众号，关注后微信会将带场景值关注事件推送给开发者。
                break;
            case "SCAN" :
                // 扫描带参数二维码事件，用户已关注时的事件推送
                // 如果用户已经关注公众号，则微信会将带场景值扫描事件推送给开发者。
                break;
            case "LOCATION" :
                // 上报地理位置事件
                break;
            case "CLICK" :
                $key = $this->jys_weixin->object_item_to_string($postObj->EventKey);
                switch ($key) {
                    case 'contact_us':
                        $content = "您有任何服务需求、项目疑难或者投诉建议都可拨打400免费电话，我们热诚期待您的来电。\n服务电话：400-100-3908\n您也可以联系线上客服，点击链接联系我们：http://suo.im/2LR9Yv\n服务时间：工作日（9：00-17:00）";
                        $this->jys_weixin->reply_text_message($fromUsername, $toUsername, $content);
                        break;
                    default:
                        echo "";
                        break;
                }
                // 自定义菜单事件，点击菜单拉取消息时的事件推送
                break;
            case "VIEW" :
                // 自定义菜单事件，点击菜单跳转链接时的事件推送
                break;
            default :
                echo "";
                break;
        }
    }

    /**
     * 从微信的网页授权接口中接收用户信息并加入session
     */
    public function get_userinfo()
    {
        $code = $this->input->get('code');
        $state = $this->input->get('state');
        $server_info = $this->jys_weixin->get_oauth_access($code);
        if (!empty($server_info)) {
            $user_info = $this->jys_weixin->get_user_info($server_info['access_token'], $server_info['openid']);
            file_put_contents(APPPATH.'/logs/agent_entrance_log_'.date('Ymd'), date('Y-m-d H:i:s')."，user_info:".json_encode($user_info)."\n\n", FILE_APPEND);
            if (!empty($user_info)) {
                if (isset($_SESSION['agent_id']) && intval($_SESSION['agent_id']) > 0 && isset($_SESSION['user_id']) && intval($_SESSION['user_id']) > 0) {
                    // 代理商用户通过接口从外部系统跳入本系统进行注册
                    // 代理商用户跳转到代理商首页
                    $_SESSION['openid'] = $user_info['openid'];
                    if (!empty($_SESSION['commodity_id'])) {
                        header("Location:" . site_url() . 'weixin/index/commodity_detail/' . intval($_SESSION['commodity_id']));
                    } else if (!empty($_SESSION['category_id'])) {
                        header("Location:" . site_url() . 'weixin/agent/home/' . $_SESSION['category_id']);
                    } else if (!empty($_SESSION['order_list'])) {
                        header("Location:" . site_url('weixin/user/order_list'));
                    } else {
                        header("Location:" . site_url('weixin/agent/home'));
                    }
                } else {
                    // 普通微信端用户 或者 代理商用户通过系统自身的微信接口进入注册
                    $result = $this->User_model->get_user_from_weixin($user_info);
                    if ($result) {
                        // 登录成功
                    } else {
                        // 当前系统没有该用户
                    }
                    if (isset($_SESSION['agent_id']) && intval($_SESSION['agent_id']) > 0 && isset($_SESSION['internal']) && intval($_SESSION['internal']) == jys_system_code::AGENT_ENTRANCE_METHOD_INTERNAL) {
                        // 代理商用户通过系统自身的微信接口进入注册
//                        file_put_contents(APPPATH.'/logs/agent_entrance_log_'.date('Ymd'), date('Y-m-d H:i:s').":".json_encode($_SESSION)."\n", FILE_APPEND);
                        $params_str = "?entrance_method=" . intval($_SESSION['internal']) . "&uid={$server_info['openid']}";
                        unset($_SESSION['internal']);
                        if (isset($_SESSION['agent_id'])) {
                            $params_str .= "&agent_id={$_SESSION['agent_id']}";
                            unset($_SESSION['agent_id']);
                        }
                        if (isset($_SESSION['commodity_id'])) {
                            $params_str .= "&coid={$_SESSION['commodity_id']}";
                            unset($_SESSION['commodity_id']);
                        }
                        if (isset($_SESSION['category_id'])) {
                            $params_str .= "&cid={$_SESSION['category_id']}";
                            unset($_SESSION['category_id']);
                        }

                        if (isset($_SESSION['order_list']) && $_SESSION['order_list']) {
                            // 进入订单列表页面
                            header("Location:" . site_url('weixin/agent/entrance/order_list') . $params_str);
                        } else {
                            // 进入其他页面
                            header("Location:" . site_url('weixin/agent/entrance') . $params_str);
                        }
                    } else {
                        // 普通用户
                        header("Location:" . $state);
                    }
                }
            } else {
                // 未能通过微信接口获取到用户信息
                header("Location:" . $state);
            }
        } else {
            // 未能通过微信接口获取access_token
            header("Location:" . $state);
        }
    }

    /**
     * 微信支付后接收微信支付异步通知回调接口
     */
    public function pay_notify()
    {
        $xml = file_get_contents("php://input");
        $result = $this->jys_weixin_pay->FromXml($xml);
        $log = "";
        if ($result['return_code'] == 'SUCCESS') {
            // 支付成功
            if ($this->jys_weixin_pay->checkNotify($result)) {
                // 验证成功
                $total_fee = intval($result['total_fee']) / 100.0;
                $payment_time = $this->jys_weixin_pay->formatDatetime($result['time_end']);
                if (!$payment_time) {
                    // 获取支付时间失败
                    $log = "支付完成时间不正确:{$result['time_end']}";
                    $this->jys_weixin_pay->ReplyNotify("FAIL", "支付完成时间不正确");
                } else {
                    if ($this->Order_model->get_pay_result_set_status($result['out_trade_no'], $result['transaction_id'], $total_fee, jys_system_code::PAYMENT_WXPAY, $payment_time)) {
                        // 订单修改成功
                        $this->Order_model->notify_inform_order_info(0, $result['out_trade_no']);
                        $log = "订单修改成功";
                        // 根据商品类型，做相关判断
                        $chage_order_info = $this->change_order_by_commodity_type($result['out_trade_no']);
                        if (empty($chage_order_info) || !isset($chage_order_info['success']) || !$chage_order_info['success']) {
                            if (isset($chage_order_info['msg'])) {
                                $log = "订单修改成功，" . $chage_order_info['msg'];
                            } else {
                                $log = "订单修改成功，但根据订单中的商品信息修改订单失败";
                            }
                            $this->jys_weixin_pay->ReplyNotify("FAIL", $log);
                        } else {
                            //支付成功后将订单信息传回erp
                            $erp_order = $this->Order_model->insert_order_to_erp($result['out_trade_no']);
                            $this->jys_weixin_pay->ReplyNotify("SUCCESS", "OK");
                        }
                    } else {
                        // 订单修改失败
                        $log = "订单修改失败";
                        $this->jys_weixin_pay->ReplyNotify("FAIL", "订单修改失败");
                    }
                }
            } else {
                // 验证失败
                $log = "签名验证失败";
                $this->jys_weixin_pay->ReplyNotify("FAIL", "签名失败");
            }
        } else {
            // 支付失败
            $log = "支付失败";
            $this->jys_weixin_pay->ReplyNotify("FAIL", "参数格式校验错误");
        }
        if (!empty($log)) {
            $data = date("Y-m-d H:i:s") . "\n" . json_encode($result) . "\n" . $log . "\n\n";
            file_put_contents(APPPATH . "/logs/wxpay_pay_notify_" . date("Ymd"), $data, FILE_APPEND);
        }
    }

    /**
     * 根据订单中的商品类型修改订单信息
     * @param null $order_number 主订单编号
     */
    private function change_order_by_commodity_type($order_number = NULL)
    {
        $result = array('success' => FALSE, 'msg' => '修改订单信息失败');
        if (empty($order_number)) {
            $result['msg'] = '订单编号不能为空';
            return $result;
        }

        $order = $this->Order_model->get_order_by_condition(array('order.number' => $order_number));
        if ($order['success'] && !empty($order['data']) && isset($order['data']['sub_orders']) && is_array($order['data']['sub_orders']) && count($order['data']['sub_orders']) > 0) {
            $sub_orders = $order['data']['sub_orders'];
            if ($sub_orders[0]['type_id'] == jys_system_code::COMMODITY_TYPE_MEMBER) {
                // 会员商品
                // 修改会员等级，修改订单状态
                $commodity = $this->Commodity_model->get_commodity_by_condition(array('commodity.id' => $sub_orders[0]['commodity_id']));
                if (is_array($commodity['data']) && isset($commodity['data']['level_id']) && intval($commodity['data']['level_id']) > 0) {
                    if ($this->jys_db_helper->update('user', $order['data']['user_id'], array('level' => $commodity['data']['level_id']))) {
                        // 修改用户等级成功，更新订单状态
                        $change_result = $this->Order_model->finish_order($order_number);
                        if ($change_result['success']) {
                            // 订单状态修改成功
                            $result['success'] = TRUE;
                            $result['msg'] = '订单状态修改成功';
                        } else {
                            // 订单状态修改失败
                            $result['msg'] = "会员商品订单状态修改失败";
                        }
                    } else {
                        // 修改用户等级失败
                        $result['msg'] = "会员商品修改用户等级失败";
                    }
                } else {
                    // 会员商品但未制定对应会员等级
                    $result['msg'] = "会员商品但未指定对应会员等级";
                }
            } else {
                // 非会员商品
                $result['success'] = TRUE;
                $result['msg'] = "非会员商品，无需对订单状态进行修改";
            }
        } else {
            // 未找到相关订单信息
            $result['msg'] = "未找到相关订单信息";
        }

        return $result;
    }

    /**
     * 用户通过PC端微信扫码后，获取用户信息的接口
     */
    public function get_userinfo_by_unionid() {
        $code = $this->input->get('code');
        $state = $this->input->get('state');
        file_put_contents(APPPATH.'/logs/open_weixin_'.date('Ymd'), date('Y-m-d H:i:s')."，code:".$code."\n", FILE_APPEND);

        $server_info = $this->jys_weixin->get_oauth_access_by_code($code);
        if (!empty($server_info)) {
            $user_info = $this->jys_weixin->get_user_info_by_access_token($server_info['access_token'], $server_info['openid']);
            if (!empty($user_info)) {
                    $result = $this->User_model->get_user_from_weixin($user_info);
                    if ($result) {
                        //保存个人信息
                        $update = ['weixin_nickname' => $user_info['nickname'], 'weixin_avator' => $user_info['headimgurl'], 'openid' => $user_info['openid'], 'unionid' => $user_info['unionid']];
                        $condition = ['openid' => $update['openid']];
                        $user_result = $this->User_model->udpate_user_by_condition($condition, $update);
                        // 登录成功
                        header("Location:" . $state);
                    } else {
                        $_SESSION['openid'] = $user_info['openid'];
                        $_SESSION['avatar_path'] = $user_info['headimgurl'];
                        $_SESSION['weixin_nickname'] = $user_info['nickname'];
                        $_SESSION['unionid'] = $user_info['unionid'];
                        // 当前系统没有该用户，跳转到注册页面
                        $a = TRUE;
                        header("Location:" . base_url() . 'index/index/1');
                    }
            } else {
                // 未能通过微信接口获取到用户信息
                header("Location:" . $state);
            }
        } else {
            // 未能通过微信接口获取access_token
            header("Location:" . $state);
        }

    }

    /**
     * 用户个人中心，绑定微信用户信息
     */
    public function bind_userinfo_by_unionid() {
        $code = $this->input->get('code');
        $state = $this->input->get('state');
        file_put_contents(APPPATH.'/logs/open_weixin_'.date('Ymd'), date('Y-m-d H:i:s')."，code:".$code."\n", FILE_APPEND);

        $server_info = $this->jys_weixin->get_oauth_access_by_code($code);
        if (!empty($server_info)) {
            $user_info = $this->jys_weixin->get_user_info_by_access_token($server_info['access_token'], $server_info['openid']);
            if (!empty($user_info)) {
                //保存个人信息
                $id = $_SESSION['user_id'];
                $update = ['weixin_nickname' => $user_info['nickname'], 'weixin_avator' => $user_info['headimgurl'], 'openid' => $user_info['openid'], 'unionid' => $user_info['unionid']];
                $condition = ['id' => $id];
                $user_result = $this->User_model->udpate_user_by_condition($condition, $update);
                // 绑定成功
                header("Location:" . $state);
            } else {
                // 未能通过微信接口获取到用户信息
                header("Location:" . $state);
            }
        } else {
            // 未能通过微信接口获取access_token
            header("Location:" . $state);
        }

    }

    /**
     * 设置菜单
     */
//    public function test()
//    {
//	    $order = $this->Order_model->finish_order("7068231486200693");
//        var_dump($order);
//        $postarr = array(
//                "button"=>array(
//                                array(
//                                    "name"=>"",
//                                    "type"=>"view",
//                                    "url"=>"http://"
//                                ),
//                                array(
//                                    "name"=>"",
//                                    "type"=>"view",
//                                    "url"=>"http://"
//                                )
//                            )
//
//                );
		//var_dump($this -> jys_weixin -> create_menu($postarr));
//        $jibian = array('button' => array(
//            array('name' => '最新活动', "sub_button" => array(
//                array('type' => 'view', 'name' => "赛安小调查", 'url' => 'http://www.weixunyunduan.com/yunduanwx/index.php?g=Wap&m=Research&a=index&reid=434&token=405151'),
//                )
//            ),
//            array('type' => 'view', 'name' => '检测流程', "url" => 'http://b.eqxiu.com/s/cJdewVpT'),
//            array('name' => '检测服务', 'sub_button' => array(
//                array('type' => 'view', 'name' => '样本登记', 'url' => 'http://shop.c-genecity.net/weixin/user/add_report/1'),
//                array('type' => 'view', 'name' => '报告查询', 'url' => 'http://shop.c-genecity.net/weixin/user/search_report/1'),
//                array('type' => 'view', 'name' => '解读预约', 'url' => 'http://cn.mikecrm.com/6xTswjA'),
//                array('type' => 'view', 'name' => '在线客服', 'url' => 'http://p.qiao.baidu.com/cps2/mobileChat?siteId=10489379&userId=23339078&type=1&reqParam='),
//                array('type' => 'click', 'name' => '联系我们', 'key' => 'contact_us'),
//                )
//            )
//        )
//        );
//        var_dump($this->jys_weixin->create_menu($jibian));
//		echo json_encode($this -> jys_weixin -> get_menu());
		//var_dump($this->my_weixin->get_material_list());
//    }
}