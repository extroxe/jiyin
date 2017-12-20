<div ng-controller="collectionCtrl">
    <header>
        <div class="titlebar">
            <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
            <h1 class="text-center"><?php echo $title; ?></h1>
        </div>
    </header>
    <article>
        <div class="card">
            <div class="inputbox" style="display: flex;" ng-repeat="collection in collection_list">
                <img ng-if=" collection.path !== null " ng-src="{{ SITE_URL + collection.path }}" alt="" ng-click="GoCommodityDetail(collection.id, collection.commodity_specification_id)">
                <img ng-if=" collection.path === null " ng-src="{{ SITE_URL + 'source/mobile/img/photo.jpg' }}" alt="" ng-click="GoCommodityDetail(collection.id, collection.commodity_specification_id)">
                <div class="info-box">
                    <div class="info-title" ng-click="GoCommodityDetail(collection.id, collection.commodity_specification_id)">{{collection.commodity_name}}</div>
                    <div class="info-group">
                        <div class="info-price">
                            <span ng-if="collection.is_point == '0'">¥ {{collection.price}}</span>
                            <span ng-if="collection.is_point == '1'">{{collection.price}} 积分</span>
                        </div>
                    </div>
                    <div class="info-button">
                        <a class="button lrpadding8 info outline" ng-click="delete(collection)">删 除</a>
                    </div>
                </div>
            </div>
            <hr>
        </div>
        <div class="content">
            <div class="lists"></div>
        </div>
    </article>
</div>
