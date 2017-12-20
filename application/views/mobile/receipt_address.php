<div ng-controller="receiptAddressCtrl">
    <header>
        <div class="titlebar receipt-info">
            <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
            <h1 class="text-center"><?php echo $title;?></h1>
        </div>
    </header>
    <article style="margin-bottom: 70px;">
        <div class="address-item" ng-repeat="row in address">
            <div class="item" ng-click="set_default(row, $index)">
                <div class="item-title">
                    <span ng-cloak>{{ row.name }}</span>
                    <span>{{ row.phone }}</span>
                </div>
                <div class="item-info">
                    <span>{{ (row.province || '')+(row.city || '')+(row.district || '')+' '+row.address }}</span>
                </div>
            </div>
            <hr>
            <div class="item">
                <div class="item-operation">
                    <span>
                        <input type="radio" class="input-radio" checked="true" name="address" ng-if="row.default == 1">
                        <input type="radio" class="input-radio" name="address" ng-if="row.default != 1" ng-click="set_default(row, $index)">
                    </span>
                    <span style="color: #117D94;" ng-if="row.default == 1">默认地址</span>
                    <span ng-if="row.default != 1">设为默认地址</span>
                    <span ng-click="delete($index)"><img src="<?php echo site_url('source/mobile/img/icon/delete.png'); ?>" alt="">删除</span>
                    <span ng-click="edit('#page_modify', row)"><img src="<?php echo site_url('source/mobile/img/icon/edit.png'); ?>" alt="">编辑</span>
                </div>
            </div>
        </div>
        <div class="item-footer">
            <button class="button" ng-click="add('#page_modify')">添加新地址</button>
        </div>
    </article>
    <section id="page_modify" data-animation="slideRight" class="page" style="background-color:#F9F9F9;position: fixed;">
        <header>
            <div class="titlebar">
                <a class="titlebar-button" ng-click="closePage('#page_modify')"><i class="icon size16 icon-arrowleft"></i></a>
                <h1 class="text-center">{{ type == 'edit' ? '编辑地址' : '新增地址' }}</h1>
            </div>
        </header>
        <article>
            <div class="inputbox underline">
                <label class="inputbox-left">姓名</label>
                <div class="inputbox-right inputbox">
                    <input type="text" class="input-text" placeholder="建议填写真实姓名" name="name" ng-model="address_row.name"/>
                </div>
            </div>
            <div class="inputbox">
                <label class="inputbox-left">联系电话</label>
                <div class="inputbox-right inputbox">
                    <input type="text" class="input-text" placeholder="请输入您的手机号码" name="username" ng-model="address_row.phone"/>
                </div>
            </div>
            <div class="inputbox underline" style="margin-top: 12px;">
                <label class="inputbox-left" style="width: 22%;">所在地区</label>
                <div class="inputbox-right inputbox">
                    <select id="province" class="input-text">
                        <option>-- 省 --</option>
                    </select>
                    <select id="city" class="input-text">
                        <option class="city">-- 市 --</option>
                    </select>
                    <select id="district" class="input-text" style="padding-right: 0;">
                        <option class="district">-- 区 --</option>
                    </select>
                </div>
            </div>
            <div class="inputbox">
                <label class="inputbox-left">详细地址</label>
                <div class="inputbox-right inputbox">
                    <input type="text" class="input-text" placeholder="建议填写详细收货地址" name="username" ng-model="address_row.address"/>
                </div>
            </div>
            <div class="inputbox" style="margin-top: 12px;padding: 5px 15px;">
                <div class="box-flex-1">设置默认地址</div>
                <div class="switch notext {{ address_row.default == 1 ? 'active' : '' }}" data-name="switch_default" data-on-value="ok" data-off-value="off" ng-click="select_default($event)">
                    <div class="switch-handle"></div>
                </div>
            </div>
            <div class="sub-btn">
                <button class="button" ng-click="complete_add()" ng-if="type == 'add'">添&nbsp;&nbsp;&nbsp;&nbsp;加</button>
                <button class="button" ng-click="complete_edit()" ng-if="type == 'edit'">保&nbsp;&nbsp;&nbsp;&nbsp;存</button>
            </div>
        </article>
    </section>
</div>