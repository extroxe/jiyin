<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =====================================================================================
 *
 *        Filename: Jys_alipay.php
 *
 *     Description: 支付宝支付类库
 *
 *         Created: 2017-11-10 10:17:50
 *
 *          Author: TangYu
 *
 * =====================================================================================
 */

require_once FCPATH."application/third_party/alipay/pagepay/service/AlipayTradeService.php";
require_once FCPATH."application/third_party/alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php";
require_once FCPATH."application/third_party/alipay/pagepay/buildermodel/AlipayTradeQueryContentBuilder.php";
require_once FCPATH."application/third_party/alipay/pagepay/buildermodel/AlipayTradeCloseContentBuilder.php";
require_once FCPATH."application/third_party/alipay/pagepay/buildermodel/AlipayTradeRefundContentBuilder.php";
require_once FCPATH."application/third_party/alipay/pagepay/buildermodel/AlipayTradeFastpayRefundQueryContentBuilder.php";

class Jys_alipay {
    private $_CI;

    // 阿里支付配置
    private $config = array();

    // 编码格式
    private $charset = "UTF-8";

    // 签名方式
    private $sign_type = "RSA2";

    // 支付宝网关
    private $gatewayUrl = "https://openapi.alipay.com/gateway.do";

    /**
     * 构造函数
     * Jys_alipay constructor.
     */
    public function __construct()
    {
        $this->_CI = & get_instance();
        $this->config['app_id'] = $this->_CI->config->item('ali_app_id');
        $this->config['merchant_private_key'] = $this->_CI->config->item('ali_merchant_private_key');
        $this->config['notify_url'] = site_url('alipay/pay_notify');
        $this->config['return_url'] = site_url('order/order_list');
        $this->config['charset'] = $this->charset;
        $this->config['sign_type'] = $this->sign_type;
        $this->config['gatewayUrl'] = $this->gatewayUrl;
        $this->config['alipay_public_key'] = $this->_CI->config->item('ali_alipay_public_key');
    }


    /**
     * 支付接口
     * @param string $out_trade_no 商户订单号，商户网站订单系统中唯一订单号，必填
     * @param string $subject 订单名称，必填
     * @param string $total_amount 付款金额，必填
     * @param int $order_id 订单ID
     * @param string $body 商品描述，可空
     */
    public function alipay_pagepay($out_trade_no = '', $subject = '', $total_amount = '', $order_id = 0, $body = '')
    {
        // 阿里支付构造参数
        $payRequestBuilder = new AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        if (!empty($order_id)) {
            $this->config['return_url'] = site_url("order/detail/".$order_id);
        }

        // 阿里支付服务
        $aop = new AlipayTradeService($this->config);
        
        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用AlipayTradePagePayContentBuilder中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $aop->pagePay($payRequestBuilder, $this->config['return_url'], $this->config['notify_url']);
    }

    /**
     * 订单查询接口
     * @param string $out_trade_no 商户订单号，商户网站订单系统中唯一订单号
     * @param string $trade_no 支付宝交易号
     * @return array
     */
    public function alipay_query($out_trade_no = '', $trade_no = '')
    {
        //构造参数
        $RequestBuilder = new AlipayTradeQueryContentBuilder();
        $RequestBuilder->setOutTradeNo($out_trade_no);
        $RequestBuilder->setTradeNo($trade_no);

        $aop = new AlipayTradeService($this->config);

        /**
         * alipay.trade.query (统一收单线下交易查询)
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @return $response 支付宝返回的信息
         */
        $response = $aop->Query($RequestBuilder);

        return $response;
    }

    /**
     * 订单关闭
     * @param string $out_trade_no 商户订单号，商户网站订单系统中唯一订单号
     * @param string $trade_no 支付宝交易号
     * @return array
     */
    public function alipay_close($out_trade_no = '', $trade_no = '')
    {
        // 构造参数
        $RequestBuilder = new AlipayTradeCloseContentBuilder();
        $RequestBuilder->setOutTradeNo($out_trade_no);
        $RequestBuilder->setTradeNo($trade_no);

        $aop = new AlipayTradeService($this->config);

        /**
         * alipay.trade.close (统一收单交易关闭接口)
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @return $response 支付宝返回的信息
         */
        $response = $aop->Close($RequestBuilder);

        return $response;
    }

    /**
     * @param string $out_trade_no 商户订单号，商户网站订单系统中唯一订单号
     * @param string $trade_no 支付宝交易号
     * @param string $refund_amount 需要退款的金额，该金额不能大于订单金额，必填
     * @param string $refund_reason 退款的原因说明
     * @param string $out_request_no 标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传
     * @return array
     */
    public function alipay_refund($out_trade_no = '', $trade_no = '', $refund_amount = '', $refund_reason = '', $out_request_no = '')
    {
        //构造参数
        $RequestBuilder = new AlipayTradeRefundContentBuilder();
        $RequestBuilder->setOutTradeNo($out_trade_no);
        $RequestBuilder->setTradeNo($trade_no);
        $RequestBuilder->setRefundAmount($refund_amount);
        $RequestBuilder->setRefundReason($refund_reason);
        if (!empty($out_request_no)) {
            $RequestBuilder->setOutRequestNo($out_request_no);
        }

        $aop = new AlipayTradeService($this->config);

        /**
         * alipay.trade.refund (统一收单交易退款接口)
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @return $response 支付宝返回的信息
         */
        $response = $aop->Refund($RequestBuilder);

        return $response;
    }

    /**
     * @param string $out_trade_no 商户订单号，商户网站订单系统中唯一订单号
     * @param string $trade_no 支付宝交易号
     * @param string $out_request_no 请求退款接口时，传入的退款请求号，如果在退款请求时未传入，则该值为创建交易时的外部交易号，必填
     * @return array
     */
    public function alipay_refund_query($out_trade_no = '', $trade_no = '', $out_request_no = '')
    {
        //构造参数
        $RequestBuilder = new AlipayTradeFastpayRefundQueryContentBuilder();
        $RequestBuilder->setOutTradeNo($out_trade_no);
        $RequestBuilder->setTradeNo($trade_no);
        $RequestBuilder->setOutRequestNo($out_request_no);

        $aop = new AlipayTradeService($this->config);

        /**
         * 退款查询   alipay.trade.fastpay.refund.query (统一收单交易退款查询)
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @return $response 支付宝返回的信息
         */
        $response = $aop->refundQuery($RequestBuilder);

        return $response;
    }

    /**
     * alipay异步通知验证
     * @param array $arr
     * @return bool
     */
    public function alipay_notify($arr = array())
    {
        $alipaySevice = new AlipayTradeService($this->config);
        $result = $alipaySevice->check($arr);

        return $result;
    }
}