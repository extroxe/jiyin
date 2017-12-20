<header>
    <div class="personal-title titlebar">
        <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
        <h1 class="text-center"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="familyInfoCtrl" style="padding-bottom: 70px;">
    <div class="inputbox">
        <label class="inputbox-left" style="line-height: 21px;">姓名</label>
        <div class="inputbox-right inputbox" style="margin-right: 21px">
            <input type="text" class="input-text text-right" placeholder="请输入亲属姓名" ng-model="family_info.name" style="padding: 0;color: #666666;"/>
        </div>
    </div>
    <hr>
    <div >
        <span>性别</span>
        <div style="float: right;margin-right: 21px;">
            <label>
                <input type="radio" class="input-radio" name="sex" value="1" ng-model="family_info.gender"/>
                <span>男</span>
            </label>
            <label>
                <input type="radio" class="input-radio" name="sex" value="0" ng-model="family_info.gender"/>
                <span>女</span>
            </label>
        </div>
    </div>
    <hr>
    <div class="inputbox"  >
        <label class="inputbox-left" style="line-height: 21px;">联系电话</label>
        <div class="inputbox-right inputbox"  style="margin-right: 21px">
            <input type="text" class="input-text text-right" placeholder="请输入电话号码" ng-model="family_info.phone" style="padding: 0;color: #666666;"/>
        </div>
    </div>
    <hr>
    <div class="inputbox">
        <label class="inputbox-left" style="line-height: 21px;">出生年月日</label>
        <div class="inputbox-right inputbox">
            <input type="text" class="input-text text-right SID-Date" placeholder="请选择日期" readonly="readonly" value="{{ family_info.birth }}" style="padding: 0;color: #666666;margin-right: -10px;"/>
        </div>
        <i class="icon size20 icon-arrowright"></i>
    </div>
    <hr>

    <div class="inputbox">
        <label class="inputbox-left" style="line-height: 21px;">身份证号码</label>
        <div class="inputbox-right inputbox" style="margin-right: 21px">
            <input type="text" class="input-text text-right" placeholder="请输入身份证号码" ng-model="family_info.identity_card" style="padding: 0;color: #666666;"/>
        </div>
    </div>
    <hr>
    <div class="inputbox" ng-click="show_mid_frame('#curative_effect')">
        <label class="inputbox-left" style="line-height: 21px;">具体用药史和疗效</label>
        <i class="icon size20 icon-arrowright " style="right: 15px;    position: absolute;"></i>
    </div>
    <hr>
    <div class="inputbox">
        <label class="inputbox-left" style="line-height: 21px;">血缘关系</label>
        <div class="inputbox-right inputbox">
            <input type="text" class="input-text text-right" id="ID-Sp" placeholder="请选择关系" readonly="readonly" value="{{ family_info.relation }}" style="padding: 0;color: #666666;margin-right: -10px;"/>
        </div>
        <i class="icon size20 icon-arrowright"></i>
    </div>
    <hr>
    <div class="inputbox">
        <label class="inputbox-left" style="line-height: 21px;">健康状态</label>
        <div class="inputbox-right inputbox">
            <input type="text" class="input-text text-right" id="ID-Health" placeholder="请选择健康状态" readonly="readonly" value="{{ family_info.health_status }}" style="padding: 0;color: #666666;margin-right: -10px;"/>
        </div>
        <i class="icon size20 icon-arrowright"></i>
    </div>
    <hr>
    <div class="inputbox" ng-click="goToUrl('#page_modify')">
        <label class="inputbox-left" style="line-height: 21px;">查看他的基因检测报告</label>
        <i class="icon size20 icon-arrowright" style="right: 15px;    position: absolute;"></i>
    </div>
    <hr>
    <div class="clinical_history">
        <label style="margin: 0px 0 5px; font-size: 16px">曾接受治疗</label>
        <div class="inputbox underline">
            <div class="inputbox-right inputbox">
                <div class="input-text" style="">
                    <label>
                        <input type="checkbox" class="input-radio" value="10" ng-checked="treat"/>
                        <span>手术</span>
                    </label>
                    <label>
                        <input type="checkbox" class="input-radio" value="20" ng-checked="radiotherapy"/>
                        <span>化疗</span>
                    </label>
                    <label>
                        <input type="checkbox" class="input-radio" value="30" ng-checked="chemotherapy"/>
                        <span>放疗</span>
                    </label>
                    <label>
                        <input type="checkbox" class="input-radio" value="40" ng-checked="targeted_therapies"/>
                        <span>药物靶向治疗</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div ng-if="family_id != 'family_info'" class="sendcode" style="text-align: center">
        <a class="button block radius4" ng-click="update_family_member()">更 新</a>
        <a class="button block radius4" ng-click="del_family_member()">删 除</a>
    </div>
    <div ng-if="family_id == 'family_info'" class="sendcode" style="text-align: center">
        <a class="button block radius4 add_family_member"  ng-click="add_family_member()">保存信息</a>
    </div>

    <section id="curative_effect" data-animation="zoom" class="page" >
        <div style="width: 240px; text-align: center">
            <textarea placeholder="请输入用药史和疗效" class="clinical_history_detail" ng-model="medication_history_info"></textarea>
            <span class="button" ng-if="family_id != 'family_info'" ng-click="saveMedicationHistory('#curative_effect')">保存</span>
            <span class="button" ng-if="family_id == 'family_info'" ng-click="addMedicationHistory('#curative_effect')">保存</span>
            <span ng-click="backTab('#curative_effect')" class="cancel-btn">取消</span>
        </div>
    </section>
    <section id="page_modify" data-animation="slideRight" class="page" style="background:rgba(255,255,255,1) !important;position: fixed;">
        <header>
            <div class="titlebar">
                <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center">亲属报告信息</h1>
            </div>
        </header>
        <article style="    text-align: center;padding-top: 103px;margin-top: 20px;">
            没有该亲属报告信息！！
        </article>
    </section>
</article>

