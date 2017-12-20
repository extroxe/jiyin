'use strict';

/**
 * Config for the router
 */
angular.module('app')
  .run(
    [          '$rootScope', '$state', '$stateParams',
      function ($rootScope,   $state,   $stateParams) {
          $rootScope.$state = $state;
          $rootScope.$stateParams = $stateParams;
          $rootScope.SITE_URL = window.location.origin + '/';
      }
    ]
  )
  .config(
    [          '$stateProvider', '$urlRouterProvider',
      function ($stateProvider,   $urlRouterProvider) {
          
          $urlRouterProvider
              .otherwise('/app/user');
          $stateProvider
              //主页
              .state('app', {
                  abstract: true,
                  url: '/app',
                  templateUrl: '/source/admin/tpl/app.html'
              })
              //个人中心
              .state('app.user', {
                  url: '/user',
                  templateUrl: '/source/admin/tpl/user/user.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/user/user.js'
                              ]);
                          }]
                  }
              })
              //首页管理
              .state('app.banner', {
                  url: '/banner',
                  templateUrl: '/source/admin/tpl/index/banner.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load('ngGrid').then(
                                  function(){
                                      return $ocLazyLoad.load([
                                          '/source/admin/tpl/index/banner.js',
                                          '/source/admin/tpl/modal/modal-banner.js',
                                          '/source/admin/tpl/modal/modal-agentAddCommodity.js'
                                      ]);
                                  }
                              );
                          }]
                  }
              })
              //代理商商品
              .state('app.agentIndex', {
                  url: '/agentIndex',
                  templateUrl: '/source/admin/tpl/index/agentIndex.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load('ngGrid').then(
                                  function(){
                                      return $ocLazyLoad.load([
                                          '/source/admin/tpl/index/agentIndex.js',
                                          '/source/admin/tpl/modal/modal-agentIndex.js',
                                          '/source/admin/tpl/modal/modal-agentAddCommodity.js',
                                          '/source/admin/tpl/modal/modal-agentCommodity.js'
                                      ]);
                                  }
                              );
                          }]
                  }
              })
              //商品管理
              .state('app.commodityClass', {
                  url: '/commodityClass',
                  templateUrl: '/source/admin/tpl/commodity/commodityClass.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/commodityClass.js',
                                  '/source/admin/tpl/modal/modal-commoClass.js'
                              ]);
                          }]
                  }
              })
              .state('app.commodityCategory', {
                  url: '/commodityCategory',
                  templateUrl: '/source/admin/tpl/commodity/commodityCategory.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/commodityCategory.js',
                                  '/source/admin/tpl/modal/modal-category.js'
                              ]);
                          }]
                  }
              })
              .state('app.discount', {
                  url: '/discount',
                  templateUrl: '/source/admin/tpl/commodity/discount.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/discount.js',
                                  '/source/admin/tpl/modal/modal-discount.js',
                                  '/source/admin/tpl/modal/modal-agentAddCommodity.js'
                              ]);
                          }]
                  }
              })
              .state('app.integralCommodity', {
                  url: '/integralCommodity',
                  templateUrl: '/source/admin/tpl/commodity/integralCommodity.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/integralCommodity.js',
                                  '/source/admin/tpl/modal/modal-commoClass.js'
                              ]);
                          }]
                  }
              })
              .state('app.hotCommodity', {
                  url: '/hotCommodity',
                  templateUrl: '/source/admin/tpl/index/hotCommodity.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/index/hotCommodity.js',
                                  '/source/admin/tpl/modal/modal-recommond.js',
                                  '/source/admin/tpl/modal/modal-agentAddCommodity.js'
                              ]);
                          }]
                  }
              })
              .state('app.recommend', {
                  url: '/recommend',
                  templateUrl: '/source/admin/tpl/commodity/recommend.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/recommend.js',
                                  '/source/admin/tpl/modal/modal-recommond.js',
                                  '/source/admin/tpl/modal/modal-agentAddCommodity.js'
                              ]);
                          }]
                  }
              })
              .state('app.evaluate', {
                  url: '/evaluate/:commodity_id/:type/:specification_id',
                  templateUrl: '/source/admin/tpl/commodity/evaluate.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/evaluate.js',
                                  '/source/admin/tpl/modal/modal-replyEvaluation.js'
                              ]);
                          }]
                  }
              })
              //积分抽奖
              .state('app.integralDraw', {
                  url: '/integralDraw',
                  templateUrl: '/source/admin/tpl/commodity/integralDraw.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load(['/source/admin/tpl/commodity/integralDraw.js']);
                          }]
                  }
              })
              .state('app.drawUser', {
                  url: '/drawUser/:id',
                  templateUrl: '/source/admin/tpl/commodity/drawUser.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/drawUser.js'
                              ]);
                          }]
                  }
              })
              .state('app.prize', {
                  url: '/prize',
                  templateUrl: '/source/admin/tpl/commodity/prize.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/prize.js',
                                  '/source/admin/tpl/modal/modal-agentAddCommodity.js'
                              ]);
                          }]
                  }
              })
              //积分夺宝
              .state('app.integralIndiana', {
                  url: '/integralIndiana',
                  templateUrl: '/source/admin/tpl/commodity/integralIndiana.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/integralIndiana.js',
                                  '/source/admin/tpl/modal/modal-agentAddCommodity.js'
                              ]);
                          }]
                  }
              })
              .state('app.indianaUser', {
                  url: '/indianaUser/:id',
                  templateUrl: '/source/admin/tpl/commodity/indianaUser.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/indianaUser.js'
                              ]);
                          }]
                  }
              })
              //优惠券管理
              .state('app.coupon', {
                  url: '/coupon',
                  templateUrl: '/source/admin/tpl/coupon/coupon.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/coupon/coupon.js',
                                  '/source/admin/tpl/modal/modal-coupon.js'
                              ]);
                          }]
                  }
              })
              .state('app.couponPayment', {
                  url: '/couponPayment',
                  templateUrl: '/source/admin/tpl/coupon/couponPayment.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load(['/source/admin/tpl/coupon/couponPayment.js']);
                          }]
                  }
              })
              .state('app.couponUser', {
                  url: '/couponUser/:id',
                  templateUrl: '/source/admin/tpl/coupon/couponUser.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/coupon/couponUser.js'
                              ]);
                          }]
                  }
              })

              //会员管理
              .state('app.memberInformation', {
                  url: '/memberInformation',
                  templateUrl: '/source/admin/tpl/member/memberInformation.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/member/memberInformation.js',
                                  '/source/admin/tpl/modal/modal-userInfo.js'
                              ]);
                          }]
                  }
              })
              .state('app.memberFamily', {
                  url: '/memberFamily',
                  templateUrl: '/source/admin/tpl/member/memberFamily.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load(['/source/admin/tpl/member/memberFamily.js']);
                          }]
                  }
              })
              .state('app.memberLevel', {
                  url: '/memberLevel',
                  templateUrl: '/source/admin/tpl/member/memberLevel.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/member/memberLevel.js',
                                  '/source/admin/tpl/modal/modal-level.js'
                              ]);
                          }]
                  }
              })

              //订单管理
              .state('app.ordinaryList', {
                  url: '/ordinaryList',
                  templateUrl: '/source/admin/tpl/list/ordinaryList.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/list/ordinaryList.js',
                                  '/source/admin/tpl/modal/modal-orderList.js'
                              ]);
                          }]
                  }
              })
              .state('app.ordinaryEditor', {
                  url: '/ordinaryEditor',
                  templateUrl: '/source/admin/tpl/list/ordinaryEditor.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load(['/source/admin/tpl/list/ordinaryEditor.js']);
                          }]
                  }
              })
              .state('app.integrationList', {
                  url: '/integrationList',
                  templateUrl: '/source/admin/tpl/list/integrationList.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/list/integrationList.js',
                                  '/source/admin/tpl/modal/modal-orderList.js'
                              ]);
                          }]
                  }
              })
              .state('app.integrationEditor', {
                  url: '/integrationEditor',
                  templateUrl: '/source/admin/tpl/list/integrationEditor.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load(['/source/admin/tpl/list/integrationEditor.js']);
                          }]
                  }
              })
              .state('app.reportList', {
                  url: '/reportList/:reportIsOnline',
                  templateUrl: '/source/admin/tpl/list/reportList.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/list/reportList.js'
                              ]);
                          }]
                  }
              })
              .state('app.allReportList', {
                  url: '/allReportList',
                  templateUrl: '/source/admin/tpl/list/allReportList.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/list/allReportList.js'
                              ]);
                          }]
                  }
              })
              .state('app.codeManagement', {
                  url: '/codeManagement',
                  templateUrl: '/source/admin/tpl/report/codeManagement.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/report/codeManagement.js'
                              ]);
                          }]
                  }
              })
              .state('app.sampleManagement', {
                  url: '/sampleManagement',
                  templateUrl: '/source/admin/tpl/report/sampleManagement.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/report/sampleManagement.js'
                              ]);
                          }]
                  }
              })
              .state('app.sampleMagnet2', {
                  url: '/sampleMagnet2',
                  templateUrl: '/source/admin/tpl/report/sampleMagnet2.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/report/sampleMagnet2.js'
                              ]);
                          }]
                  }
              })

              .state('app.subOrderList', {
                  url: '/subOrderList/:id/:type',
                  templateUrl: '/source/admin/tpl/list/subOrderList.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/list/subOrderList.js',
                                  '/source/admin/tpl/modal/modal-subOrderList.js'
                              ]);
                          }]
                  }
              })
              //检测模板管理
              .state('app.detectionTemplate',{
                  url: '/detectionTemplate',
                  templateUrl: '/source/admin/tpl/template/detectionTemplate.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/template/detectionTemplate.js',
                                  '/source/admin/tpl/modal/modal-detectionTemplate.js'
                              ]);
                          }]
                  }
              })
              //检测项目管理
              .state('app.detectionProject',{
                  url: '/detectionProject',
                  templateUrl: '/source/admin/tpl/template/detectionProject.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/template/detectionProject.js',
                                  '/source/admin/tpl/modal/modal-detectionProject.js'
                              ]);
                          }]
                  }
              })
              //报告管理
              .state('app.report', {
                  url: '/report/:id/:order_id',
                  templateUrl: '/source/admin/tpl/list/report.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/list/report.js'
                              ]);
                          }]
                  }
              })
              //退款管理
              .state('app.weChatRefund', {
                  url: '/weChatRefund',
                  templateUrl: '/source/admin/tpl/refund/weChatRefund.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/refund/weChatRefund.js',
                                  '/source/admin/tpl/modal/modal-refund.js'
                              ]);
                          }]
                  }
              })
              .state('app.alipayRefund', {
                  url: '/alipayRefund',
                  templateUrl: '/source/admin/tpl/refund/alipayRefund.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/refund/alipayRefund.js',
                                  '/source/admin/tpl/modal/modal-refund.js'
                              ]);
                          }]
                  }
              })
              .state('app.unionPayRefund', {
                  url: '/unionPayRefund',
                  templateUrl: '/source/admin/tpl/refund/unionPayRefund.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/refund/unionPayRefund.js',
                                  '/source/admin/tpl/modal/modal-refund.js'
                              ]);
                          }]
                  }
              })

              //贴吧管理
              .state('app.postBar', {
                  url: '/postBar',
                  templateUrl: '/source/admin/tpl/postBar/postBar.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load(['/source/admin/tpl/postBar/postBar.js']);
                          }]
                  }
              })
              .state('app.post', {
                  url: '/post',
                  templateUrl: '/source/admin/tpl/postBar/post.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load(['/source/admin/tpl/postBar/post.js']);
                          }]
                  }
              })
              .state('app.comment', {
                  url: '/comment/:id',
                  templateUrl: '/source/admin/tpl/postBar/comment.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load(['/source/admin/tpl/postBar/comment.js']);
                          }]
                  }
              })
              .state('app.recommended', {
                  url: '/recommended',
                  templateUrl: '/source/admin/tpl/postBar/recommended.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load(['/source/admin/tpl/postBar/recommended.js']);
                          }]
                  }
              })

              //健康论坛管理
              .state('app.article', {
                  url: '/article',
                  templateUrl: '/source/admin/tpl/health/article.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/health/article.js',
                                  '/source/admin/tpl/modal/modal-article.js'
                              ]);
                          }]
                  }
              })
              //系统设置
              .state('app.sysSetting', {
                  url: '/sysSetting',
                  templateUrl: '/source/admin/tpl/sys/sysSetting.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/sys/sysSetting.js'
                              ]);
                          }]
                  }
              })
              .state('app.sweepstakesRules', {
                  url: '/sweepstakesRules',
                  templateUrl: '/source/admin/tpl/sys/sweepstakesRules.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/sys/sweepstakesRules.js'
                              ]);
                          }]
                  }
              })
              .state('app.indianaRules', {
                  url: '/indianaRules',
                  templateUrl: '/source/admin/tpl/sys/indianaRules.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/sys/indianaRules.js'
                              ]);
                          }]
                  }
              })
              .state('app.hotsale', {
                  url: '/hotsale',
                  templateUrl: '/source/admin/tpl/sys/hotsale.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/sys/hotsale.js'
                              ]);
                          }]
                  }
              })
              .state('app.hotexchange', {
                  url: '/hotexchange',
                  templateUrl: '/source/admin/tpl/sys/hotexchange.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/sys/hotexchange.js'
                              ]);
                          }]
                  }
              })
              //代理商管理
              .state('app.agentInformation', {
                  url: '/agentInformation',
                  templateUrl: '/source/admin/tpl/member/agentInformation.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/member/agentInformation.js',
                                  '/source/admin/tpl/modal/modal-agentInfo.js',
                                  '/source/admin/tpl/modal/modal-agentChangePasswd.js'
                              ]);
                          }]
                  }
              })
              //代理商商品分类管理
              .state('app.agentCommodityCategory', {
                  url: '/agentCommodityCategory',
                  templateUrl: '/source/admin/tpl/commodity/agentCommodityCategory.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/agentCommodityCategory.js',
                                  '/source/admin/tpl/modal/modal-agentAddCommodity.js'
                              ]);
                          }]
                  }
              })
              //代理商商品分类添加商品
              .state('app.editCommodityCategory', {
                  url: '/editCommodityCategory/:operate/:category/:id/:agent_id/:color/:agent_name',
                  templateUrl: '/source/admin/tpl/commodity/editCommodityCategory.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/editCommodityCategory.js',
                                  '/source/admin/tpl/modal/modal-editCommodityCategory.js',
                                  '/source/admin/tpl/modal/modal-agentAddCommodity.js'
                              ]);
                          }]
                  }
              })
              //代理商商品分类模态框选择商品
              .state('app.editCommodityCategoryModal', {
                  url: '/editCommodityCategoryModal',
                  templateUrl: '/source/admin/tpl/modal/modal-editCommodityCategory.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function ( $ocLazyLoad ) {
                              return $ocLazyLoad.load([
                              ])
                          }
                      ]
                  }
              })
              //商品进入规格页面
              .state('app.specification', {
                  url: '/specification/:commodity_id/:type/:commodity_type',
                  templateUrl: '/source/admin/tpl/commodity/specification.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function ( $ocLazyLoad ) {
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/commodity/specification.js',
                                  '/source/admin/tpl/modal/modal-agentAddCommodity.js',
                                  '/source/admin/tpl/modal/modal-uploadCommodityPic.js'
                              ])
                          }
                      ]
                  }
              })
              //邮费管理
              .state('app.freight', {
                  url: '/freight',
                  templateUrl: '/source/admin/tpl/index/freight.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function ( $ocLazyLoad ) {
                              return $ocLazyLoad.load([
                                '/source/admin/tpl/index/freight.js'
                              ])
                          }
                      ]
                  }
              })
              // 免邮规则管理
              .state('app.postage', {
                  url: '/postage',
                  templateUrl: '/source/admin/tpl/postage/postage.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function ( $ocLazyLoad ) {
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/postage/postage.js',
                                  '/source/admin/tpl/modal/modal-postage.js',
                                  '/source/admin/tpl/modal/modal-agentAddCommodity.js'
                              ])
                          }
                      ]
                  }
              })
              //日志管理
              .state('app.log', {
                  url: '/log',
                  templateUrl: '/source/admin/tpl/health/log.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/health/log.js'
                              ]);
                          }]
                  }
              })
              //线下订单管理
              .state('app.offline', {
                  url: '/offline',
                  templateUrl: '/source/admin/tpl/list/offLineOrderList.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/list/offLineOrderList.js'
                              ]);
                          }]
                  }
              })
              //报告状态管理
              .state('app.reportStatus', {
                  url: '/reportStatus',
                  templateUrl: '/source/admin/tpl/list/reportStatus.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              return $ocLazyLoad.load([
                                  '/source/admin/tpl/list/reportStatus.js'
                              ]);
                          }]
                  }
              })
      }
    ]
  );