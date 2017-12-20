<article class="my_family" ng-controller="myFamilyCtrl" style="padding-bottom: 60px;">
    <div class="titlebar" style="background-color: #117d94">
        <a class="titlebar-button" ng-click="back()">
            <i style="color: #eee" class="icon icon-arrowleft back_btn"></i>
        </a>
        <h1 class="title"><?php echo $title; ?></h1>
    </div>
    <div class="group info_card" ng-repeat="family_member in my_family_list" ng-click="family_info(family_member.id)">
        <div class="member_name">
            <label>{{family_member.name}}</label>
            <label ng-if="family_member.health_status == '10'" style="border-color: #8fc31f; color: #8fc31f;">健康</label>
            <label ng-if="family_member.health_status == '20'" style="border-color: #20aeff; color: #20aeff">亚健康</label>
            <label ng-if="family_member.health_status == '30'" style="border-color: #d9534f; color: #d9534f">疾病</label>
        </div>
        <div class="detail_info">
            <span>{{family_member.relation}}</span>
            <span>40岁</span>
            <ul class="treat">
                <li ng-repeat="clinical_history in family_member.clinical_history_arr">
                    <label ng-if="$index < 3 && family_member.health_status == '10'" class="clinical_history_fitness">{{clinical_history}}</label>
                    <label ng-if="$index < 3 && family_member.health_status == '20'" class="clinical_history_sub_health">{{clinical_history}}</label>
                    <label ng-if="$index < 3 && family_member.health_status == '30'" class="clinical_history_illness">{{clinical_history}}</label>
                </li>
            </ul>
        </div>
        <i class="icon list-icon icon-arrowright"></i>
    </div>
   
    <div class="sendcode" style="background-color: #f9f9f9">
        <a class="button block radius4" ng-click="add_family_member()">添加新家属</a>
    </div>
</article>
