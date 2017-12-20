<header style="z-index: -99">
    <div class="titlebar add-report <?php echo $is_agent == 1 ? 'another_titlebar' : '';?>" >
        <a class="titlebar-button" ng-click="back()">
            <i class="icon size16 icon-arrowleft"></i>
        </a>
        <h1 class="text-center"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="addReportCtrl">
    <div class="group" style=" padding: 0 8px;">
        <img style="width: 100%" src="<?php echo site_url('source/mobile/img/add_report_banner.png');?>">
    </div>
    <div class="group index-info">
        <div class="inputbox underline no-after">
            <label class="inputbox-left" style="padding: 10px 0 10px 10px; width: 75px;">样本编号：</label>
            <div class="inputbox-right inputbox">
                <input type="text" class="input-text code-result" ng-model="sampleNumber" placeholder="请输入样本编码" style="padding:15px 12px;"/>
                <img id="scan" ng-click = "scan()" ng-src="{{ SITE_URL + 'source/mobile/img/scan.png' }}">
            </div>
        </div>
        <div class="inputbox underline no-after" ng-click="add_report_info('#selected_project')">
            <label class="inputbox-left" style="padding: 10px 0 10px 10px; width: 75px;">检测套餐：</label>
            <div class="inputbox-right inputbox">
                <input type="text" class="input-text" disabled readonly placeholder="请选择基因检测套餐及项目" ng-model="templateName" style="padding:15px 12px;"/>
                <i class="icon list-icon icon-arrowright"></i>
            </div>
        </div>
        <div class="inputbox underline no-after" ng-click="fill_report_info()">
            <label class="inputbox-left" style="padding: 10px 0 10px 10px; width: 75px;">检测人：</label>
            <div class="inputbox-right inputbox">
                <input type="text" class="input-text" disabled readonly placeholder="请输入检测人信息" ng-model="report_info.name" style="padding:15px 12px;"/>
                <i class="icon list-icon icon-arrowright"></i>
            </div>
        </div>

        <div class="inputbox underline no-after">
            <label class="inputbox-left" style="padding: 10px 0 10px 10px; width: 75px;">采集日期：</label>
            <div class="inputbox-right inputbox">
                <input type="text" class="input-text" disabled readonly ng-model="current_date" placeholder="采集日期" style="padding:15px 12px;"/>
            </div>
        </div>
<!--        <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=1057554779&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2::53" alt="点击这里给我发消息" title="点击这里给我发消息"/></a>-->
    </div>
    <div class="group" style="    background: transparent;">
        <div style="line-height: 50px;padding: 4px 12px 8px 12px; text-align: center;">
            <!--<a class="radius4 button block submit-btn margin8 <?php /*echo $is_agent == 0 ? '' : 'another_button';*/?>" ng-if="project_num != null" ng-click="add_report_info('#selected_project')">
                <label>下一步</label>
            </a>-->
            <button id="confirm_submit" class="radius4 button block submit-btn margin8 <?php echo $is_agent == 1 ? 'another_button' : '';?>" ng-click="submit_report_info()">
                提交
            </button>
        </div>

        <span class="aggrement" style="color: #999;">点击“提交”，即表示您已阅读并同意<br>
  <a href="{{SITE_URL + 'weixin/index/aggrement/'}}<?php echo $is_agent == 1 ? '1' : '0';?>">《知情同意书》</a> </span>
    </div>
    
    <section id="page_modify" data-animation="slideRight" class="page" style="background-color:#F9F9F9;position: fixed;">
        <header>
            <div class="titlebar add-report-info-form <?php echo $is_agent == 1 ? 'another_titlebar' : '';?>">
                <a class="titlebar-button" ng-click="closePage('#page_modify')">
                    <i class="icon size16 icon-arrowleft"></i>
                </a>
                <h1 class="text-center">检测人信息录入</h1>
            </div>
        </header>
        <article style="padding-bottom: 100px;    z-index: -1;">
            <div >
                <div class="group">
                    <div class="inputbox underline">
                        <label class="inputbox-left" style="padding: 10px 0 10px 12px;"><span style="color: red">*</span>姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名：</label>
                        <div class="inputbox-right inputbox">
                            <input type="text" class="input-text" ng-disabled="has_report_info" placeholder="请填写真实姓名" ng-model="report_info.name" name="nickname" style="padding:15px 12px;"/>
                        </div>
                    </div>
                    <div class="inputbox underline">
                        <label class="inputbox-left" style="padding: 10px 0 10px 12px;"><span style="color: red">*</span>性&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;别：</label>
                        <div class="inputbox-right inputbox">
                            <div ng-show="!report_info.identity_card" class="input-text" style="padding:15px 12px;">
                                <label>
                                    <input type="radio" class="input-radio <?php echo $is_agent == 1 ? '' : 'isnt_agent';?>" ng-model="report_info.gender" value="1" name="sex" ng-checked="true"/>
                                    <span>男</span>
                                </label>
                                <label style="margin-left:8px;">
                                    <input type="radio" class="input-radio <?php echo $is_agent == 1 ? '' : 'isnt_agent';?>" ng-model="report_info.gender" value="0" name="sex"/>
                                    <span>女</span>
                                </label>
                            </div>
<!--                            <input type="text" class="input-text text-right" ng-show="!report_info.identity_card" ng-disabled="has_report_info" id="SEX" placeholder="请选择性别" readonly="readonly" value="男" style="color: #666666;margin-right: -10px;"/>-->
                            <input type="text" class="input-text text-right" disabled  placeholder="无需填写，根据身份证号自动匹配" ng-if="report_info.identity_card && report_info.gender == 1" value="男" readonly="readonly" style="padding:15px 12px;"/>
                            <input type="text" class="input-text text-right" disabled  placeholder="无需填写，根据身份证号自动匹配" value="" ng-show="report_info.identity_card && (report_info.gender == 3 || report_info.gender == undefined)" style="padding:15px 12px;"/>
                            <input type="text" class="input-text text-right" disabled  placeholder="无需填写，根据身份证号自动匹配" ng-if="report_info.identity_card && report_info.gender == 0" value="女" readonly="readonly" style="padding:15px 12px;"/>
                        </div>
                    </div>
                    <div class="inputbox underline">
                        <label class="inputbox-left" style="padding: 10px 0 10px 12px;"><span style="color: red">*</span>出生日期：</label>
                        <div class="inputbox-right inputbox">
                            <input type="text" ng-show="!report_info.identity_card" class="input-text text-right SID-Date" ng-disabled="{{has_report_info}}" placeholder="请选择出生日期" readonly="readonly" ng-model="report_info.birth" value="{{ report_info.birth }}" style="padding:15px 12px;"/>
                            <input type="text" ng-show="report_info.identity_card" class="input-text " disabled placeholder="无需填写，根据身份证号自动匹配" readonly="readonly" ng-model="report_info.birth" value="{{ report_info.birth }}" style="padding:15px 12px;"/>
                        </div>
                    </div>
                    <div class="inputbox underline">
                        <label class="inputbox-left" style="padding: 10px 0 10px 12px;"><span style="color: red">*</span>联系方式：</label>
                        <div class="inputbox-right inputbox">
                            <input type="text" ng-disabled="has_report_info" class="input-text tel-phone" placeholder="请填写手机号" ng-model="report_info.phone" name="phone" style="padding:15px 12px;"/>
                        </div>
                    </div>
                    <div class="inputbox underline location">
                        <label class="inputbox-left" style="padding: 10px 0 10px 12px;"><span style="color: red">*</span>所在地区：</label>
                        <div class="inputbox-right inputbox">
                            <p class="address-parent" ng-disabled="{{has_report_info}}"><span class="address-detail"></span><span class="icon icon-rdoclose cancel-addr" ng-click="show_address_select()"></span></p>
                            <select id="province" class="input-text">
                                <option class="province" value="">-- 省 --</option>
                            </select>
                            <select id="city" class="input-text">
                                <option class="city" value="">-- 市 --</option>
                            </select>
                            <select id="district" class="input-text" style="padding-right: 0;">
                                <option class="district" value="">-- 区 --</option>
                            </select>
                            <?php echo $is_agent == 1 ? '<img class="location-img" ng-click="location()" ng-src="{{ SITE_URL + \'source/mobile/img/location-1.png\' }}">' : '<img class="location-img" ng-click="location()" ng-src="{{ SITE_URL + \'source/mobile/img/location-0.png\' }}">';?>

                        </div>
                    </div>
                    <div class="inputbox underline">
                        <label class="inputbox-left" style="padding: 10px 0 10px 12px;"><span style="color: red">*</span>详细地址：</label>
                        <div class="inputbox-right inputbox">
                            <input type="text" class="input-text" ng-disabled="has_report_info" name="identity-card" ng-model="report_info.address" placeholder="请填写邮寄地址" style="padding:15px 12px;"/>
                        </div>
                    </div>
                    <div class="group warning-group">
                        <p class="warning-info" style="color: <?php echo $is_agent == 1 ? '#44b9fc' : '#117d94';?>">
                            *以上为必填，请填写您真实有效的信息！
                        </p>
                    </div>

                    <div class="inputbox underline">
                        <label class="inputbox-left" style="padding: 10px 0 10px 12px;">身份证号：</label>
                        <div class="inputbox-right inputbox">
                            <input type="text" class="input-text" ng-disabled="has_report_info" placeholder="请填写真实身份证号码" ng-model="report_info.identity_card" name="nickname" style="padding:15px 12px;"/>
                        </div>
                    </div>
                   <!-- <div class="inputbox underline" >
                        <label class="inputbox-left" style="padding: 10px 0 10px 12px;">血缘关系<span style="color: red">*</span></label>
                        <div class="inputbox-right inputbox">
                            <input type="text" class="input-text text-right" ng-disabled="has_report_info" id="Relationship" placeholder="请选择真实关系" readonly="readonly" value="" style="padding: 0;color: #666666;margin-right: -10px;"/>
                        </div>
                        <i class="icon size20 icon-arrowright"></i>
                    </div>-->
<!--                    <div class="inputbox underline">-->
<!--                        <label class="inputbox-left" style="padding: 10px 0 10px 12px;">是否吸烟<span style="color: red">*</span></label>-->
<!--                        <div class="inputbox-right inputbox">-->
<!--                            <input type="text" ng-if="has_report_info && report_info.smoking == 0" value="否" disabled class="input-text text-right">-->
<!--                            <input type="text" ng-if="has_report_info && report_info.smoking == 1" value="是" disabled class="input-text text-right">-->
<!--                            <input type="text" class="input-text text-right" ng-disabled="has_report_info" id="Smoking" placeholder="是否吸烟" readonly="readonly" value="是" style="padding: 0;color: #666666;margin-right: -10px;"/>-->
<!--                        </div>-->
<!--                        <i class="icon size20 icon-arrowright"></i>-->
<!--                    </div>-->

                    <div class="inputbox underline">
                        <label class="inputbox-left" style="padding: 10px 0 10px 12px;">身高/体重：</label>
                        <div class="inputbox-right inputbox">
                            <input type="text" ng-disabled="has_report_info" class="input-text" id="Weight-Height" placeholder="请填写身高体重" readonly="readonly" value="" style="color: #666666;"/>
                        </div>
                    </div>
                </div>

                <div class="inputbox underline" ng-click="add_personal('#curative_personal_effect','target','focus_text_personal')">
                    <label class="inputbox-left" style="padding: 10px 0 10px 12px;">个人病史：</label>
                    <div class="inputbox-right inputbox">
                        <input type="text" class="input-text" ng-disabled="has_report_info" name="identity-card" ng-readonly="true" ng-model="report_info.personal_history" placeholder="选填，请概要在100字以内" style="padding:15px 12px;"/>
                    </div>
                </div>
                <div class="inputbox underline" ng-click="add_personal('#curative_family_effect','target','focus_text_family')">
                    <label class="inputbox-left" style="padding: 10px 0 10px 12px;">家族病史：</label>
                    <div class="inputbox-right inputbox">
                        <input type="text" class="input-text" ng-disabled="has_report_info" ng-readonly="true" name="identity-card" ng-model="report_info.family_history" placeholder="选填，请概要在100字以内" style="padding:15px 12px;"/>
                    </div>
                </div>
            </div>
            </div>


        </article>
        <div class="group save-userinfo footer bottom-btn" style="z-index: 1000">
            <div style="line-height: 50px;padding: 4px 12px 8px 12px; text-align: center;">
                <button class="radius4 button block submit-btn margin8 <?php echo $is_agent == 1 ? 'another_button' : '';?>" ng-click="save_report_info()">
                    <label>保存</label>
                </button>
            </div>
        </div>
    </section>
    <section id="curative_personal_effect" data-animation="slideRight" class="page" style="background:rgba(255,255,255,1) !important;">
        <header>
            <div class="titlebar <?php echo $is_agent == 1 ? 'another_titlebar' : '';?>">
                <a class="titlebar-button" ng-click="close_effect('#curative_personal_effect')"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center">个人病史</h1>
            </div>
        </header>
        <div style="width: 100%; text-align: center">
            <textarea style="width: 90%" ng-disabled="has_report_info" maxlength="100" id="focus_text_personal" placeholder="请概要在100字以内" class="clinical_history_detail <?php echo $is_agent == 1 ? 'another_border' : '';?>" ng-model="report_info.personal_history_temp"></textarea>
            <span ng-if="!has_report_info" ng-click="close_effect('#curative_personal_effect')" class="button cancel-btn">放弃</span>
            <span ng-if="!has_report_info" class="button <?php echo $is_agent == 1 ? 'another_button' : '';?>" ng-click="saveMedicationHistory_personal('#curative_personal_effect')">保存</span>
        </div>
    </section>
    <section id="curative_family_effect" data-animation="slideRight" class="page" style="background:rgba(255,255,255,1) !important;">
        <header>
            <div class="titlebar project-header <?php echo $is_agent == 1 ? 'another_titlebar' : '';?>">
                <a class="titlebar-button" ng-click="close_effect('#curative_family_effect')"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center">家族病史</h1>
            </div>
        </header>
        <div style="width: 100%; text-align: center">
            <textarea style="width: 90%" ng-disabled="has_report_info" maxlength="100" id="focus_text_family" placeholder="请概要在100字以内" class="clinical_history_detail <?php echo $is_agent == 1 ? 'another_border' : 'another_border';?>" ng-model="report_info.family_history_temp"></textarea>
            <span ng-if="!has_report_info" ng-click="close_effect('#curative_family_effect')" class="button cancel-btn">放弃</span>
            <span ng-if="!has_report_info" class="button <?php echo $is_agent == 1 ? 'another_button' : '';?>" ng-click="saveMedicationHistory_family('#curative_family_effect')">保存</span>
        </div>
    </section>
    <section id="selected_project" data-animation="slideRight" class="page" style="background-color:#F9F9F9;position: fixed;">
        <header>
            <div class="titlebar project-name <?php echo $is_agent == 1 ? 'another_titlebar' : '';?>">
                <a class="titlebar-button" ng-click="close_project('#selected_project')"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center">检测套餐选择</h1>
            </div>
        </header>
        <article style="padding-bottom: 100px; z-index: -1">
            <div ng-repeat="project_list in project_lists">
                <div class="group" style="margin: 0;">
                    <div class="inputbox underline project-list">
                        <label class="inputbox underline project-num" style="padding: 5px 12px; border: none; color:  <?php echo $is_agent == 1 ? '#44b9fc' : '#117d94';?>">
                            {{project_list[0].template_name}} ({{project_list.length}}选{{project_list[0].project_num}})
                        </label>
                    </div>
                </div>

<!--                <div ng-if="project_num != null" class="project-list" style="background: #fff">-->
                <div class="project-list template-list" style="background: #fff">
                    <button ng-repeat="project in project_list" class="button" ng-class="project.checked" ng-style="{opacity: project.opacity}" ng-click="check_project_num($event, project.id, project.template_id, project.project_num)" ng-disabled="{{project.disabled}}"> {{project.name}} </button>
                    <div style="clear: both;"></div>
                </div>
            </div>
        </article>
        <div class="group save-project-btn footer bottom-btn" style="z-index: 1000">
            <div style="line-height: 50px;padding: 4px 12px 8px 12px; text-align: center;">
                <button class="radius4 button block submit-btn margin8 <?php echo $is_agent == 1 ? 'another_button' : '';?>" style="width: 95%" ng-click="save_project('#selected_project')">
                    <label>保存</label>
                </button>
            </div>
        </div>
    </section>
    <section id="project_description" data-animation="slideRight" class="page" style="background-color:#fff;position: fixed; padding-top: 60px">
        <header>
            <div class="titlebar description-header <?php echo $is_agent == 1 ? 'another_titlebar' : '';?>">
                <a class="titlebar-button" ng-click="close_description('#project_description')"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center">{{project_detail.name}}</h1>
            </div>
        </header>
        <p class="project-description">{{project_detail.description}}</p>
    </section>
    <section id="submit_success" data-animation="slideRight" class="page" style="background-color:#58c1fd;position: fixed; padding-top: 60px">
       <div>
           <img src="<?php echo site_url('source/mobile/img/success.png');?>" alt="">
       </div>
        <p class="first-child">提交成功</p>
        <p>您已提交完成</p>
        <p>约 <span>8-20</span>个工作日可查看报告</p>
    </section>
</article>

<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript">
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: "<?php echo $jssdk_config['appId'];?>", // 必填，公众号的唯一标识
        timestamp: <?php echo $jssdk_config['timestamp'];?>, // 必填，生成签名的时间戳
        nonceStr: "<?php echo $jssdk_config['nonceStr'];?>", // 必填，生成签名的随机串
        signature: "<?php echo $jssdk_config['signature'];?>",// 必填，签名，见附录1
        jsApiList: [
            "scanQRCode",
            'translateVoice'
        ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
    
    wx.error(function (res) {//错误时调用
        console.log('微信出错'+res.errMsg);
    });
</script>