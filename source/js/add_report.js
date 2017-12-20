/**
 * Created by sailwish001 on 2017/5/24.
 */
/**
 * Created by sailwish009 on 2016/12/7.
 */
$(function () {
    /**
     * 添加报告信息
     */
    var report_info = {};
    var checked_project_ids = [];
    /**
     * DOM元素
     */
    var dom_report_list = $("#report-list");
    var dom_order_number = $("#order_number");
    var dom_search_btn = $("#search_btn");
    var dom_name = $("#name");
    var dom_gender = $("input[name='gender']");
    var dom_birth = $("#birth");
    var dom_smoking = $("input[name='smoking']");
    var dom_phone = $("#phone");
    var dom_identity_card = $("#identity_card");
    var dom_height = $("#height");
    var dom_weight = $("#weight");
    var dom_province = $("#province");
    var dom_city = $("#city");
    var dom_district = $("#district");
    var dom_address = $("#address");
    var dom_personal_history = $("#personal_history");
    var dom_family_history = $("#family_history");
    // var dom_relationship = $("#relationship");
    var dom_checkproject = $("#checkproject");
    var dom_submit_report_info = $("#submit-report-info");

    dom_report_list.validate({
        focusInvalid: true,
        errorElement: 'span',
        submitHandler: function (form) {   //表单提交句柄,为一回调函数，带一个参数：form
            alert("提交表单");
        },
        rules: {
            name: {
                required: true,
                minlength: 2
            },
            gender: {
                required: true,
                range: [0, 1]
            },
            birth: {
                required: true,
                dateISO: true
            },
            smoking: {
                required: true,
                range: [0, 1]
            },
            phone: {
                required: true,
                minlength: 11,
                maxlength: 11
            },
            identity_card: {
                required: true,
                minlength: 18,
                maxlength: 18
            },
            height: {
                required: true,
                number: true,
                min: 10
            },
            weight: {
                required: true,
                number: true,
                min: 10
            },
            province: {
                required: true,
                number: true,
                min: 110000
            },
            city: {
                required: true,
                number: true,
                min: 110000
            },
            district: {
                number: true,
                min: 110000
            },
            address: {
                required: true,
                minlength: 2
            },
            // relationship: {
            //     required: true,
            //     number: true,
            //     min: 1
            // }
        },
        messages: {
            name: {
                required: "请填写被检测人的真实姓名",
                minlength: "请填写被检测人的真实姓名"
            },
            gender: {
                required: "请选择被检测人的性别",
                range: "请选择被检测人的性别"
            },
            birth: {
                required: "请填写被检测人的生日",
                dateISO: "请正确填写被检测人的生日"
            },
            smoking: {
                required: "请选择被检测人是否吸烟",
                range: "请选择被检测人是否吸烟"
            },
            phone: {
                required: "请填写被检测人的11位电话号码",
                minlength: "请填写被检测人的11位电话号码",
                maxlength: "请填写被检测人的11位电话号码"
            },
            identity_card: {
                required: "请填写被检测人的18位身份证号码",
                minlength: "请填写被检测人的18位身份证号码",
                maxlength: "请填写被检测人的18位身份证号码"
            },
            height: {
                required: "请填写被检测人的身高",
                number: "请正确填写被检测人的身高",
                min: "身高不得小于10cm"
            },
            weight: {
                required: "请填写被检测人的体重",
                number: "请正确填写被检测人的体重",
                min: "身高不得小于10cm"
            },
            province: {
                required: "请选择被检测人所在省份",
                number: "请选择被检测人所在省份"
            },
            city: {
                required: "请选择被检测人所在城市",
                number: "请选择被检测人所在城市"
            },
            district: {
                number: "请选择被检测人所在区县"
            },
            address: {
                required: "请填写被检测人详细住址",
                minlength: "详细住址至少3位"
            },
            // relationship: {
            //     required: "请选择被检测人血缘关系",
            //     number: "请选择被检测人血缘关系"
            // }
        },
        errorPlacement: function (error, element) {
            error.addClass("help-inline");
        },
        success: function (label, element) {
            $(element).parent().parent().addClass('success').removeClass('error');
        },
        highlight: function (element, errorClass, validClass) {
            $(element).parent().parent().addClass('error').removeClass('success');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).parent().parent().addClass('success').removeClass('error');
        },
    });
    $('#birth').val('无需填写，根据身份证号自动匹配')

    $(document).on('keyup', '#identity_card', function () {
        // setTimeout(function () {
            if($(this).val().length == 18){
                var identity_card = $(this).val();
                if(parseInt(identity_card[16]) % 2 == 0){
                    report_info.gender = 0;
                }else{
                    report_info.gender = 1;
                }
                report_info.birth = identity_card.substr(6,4) + '-' + identity_card.substr(10,2) + '-' + identity_card.substr(12,2);
                $('input[name="gender"][value="'+report_info.gender+'"]').prop("checked", true);
                $('#birth').val(report_info.birth);
            }else{
                 $('input[name="gender"]').prop('checked',false);
                $('#birth').val('无需填写，根据身份证号自动匹配').css('font-size','13px');
            }
        // },500)

    });

    $('#submit-report-info').click(function (e) {
        var verification = dom_report_list.valid();
        e.preventDefault();
        report_info.number = $('#order_number').val();
        report_info.name = $('#name').val();
        report_info.birth = $('#birth').val();
        report_info.phone = $('#phone').val();
        report_info.gender = $('input[name="gender"]:checked').val();
        report_info.identity_card = $('#identity_card').val();
        report_info.province = $('#province').find('option:selected').text();
        report_info.province_code = $('#province').find('option:selected').val();
        report_info.city = $('#city').find('option:selected').text();
        report_info.city_code = $('#city').find('option:selected').val();
        if (report_info.city_code == "120200" || report_info.city_code == "310200" || report_info.city_code == "429004" || report_info.city_code == "429005" || report_info.city_code == "429006" || report_info.city_code == "429021" || report_info.city_code == "500200") {
            // 没有县级行政区信息
            report_info.district = "--区--";
            report_info.district_code = "0";
            $('#district').prop('disabled', true);
        } else {
            $('#district').prop('disabled', false);
            report_info.district = $('#district').find('option:selected').text();
            report_info.district_code = $('#district').find('option:selected').val();
        }

        report_info.address = $('#address').val();
        // report_info.smoking = $('input[name="smoking"]:checked').val();
        report_info.height = $('#height').val();
        report_info.weight = $('#weight').val();
        // report_info.blood_relationship = $('#relationship').find('option:selected').val();
        $.each(report_info, function (index, data) {
            if (data == undefined || data == '--请选择--' || data == '') {
                verification = false;
            }
        });
        report_info.personal_history = $('#personal_history').val();
        report_info.family_history = $('#family_history').val();
        report_info.project = '';
        var template_flag = false;
        $.each(projects, function (index, data) {
            if(!template_flag){
                var template_id = data[0].template_id;
                var template_num = data[0].project_num;
                if(!checked_project_ids[template_id] || checked_project_ids[template_id].length < template_num){

                    template_flag = true;
                    return;
                }
            }
        });

        report_info.family_history = $('#family_history').val();
        if(!report_info.family_history || report_info.family_history== ''){
            delete report_info.family_history;
        }

        if(!report_info.personal_history || report_info.personal_history== ''){
            delete report_info.personal_history;
        }

        if (verification == false) {
            alert('请按照要求完善被检测人信息！');
        }else if(template_flag){
            alert('请选择对应数量的模板');
        }else {
            $.each(checked_project_ids, function (index, data) {
                $.each(data, function (eq, item) {
                    report_info.project += ',' + item;
                })
            });

            report_info.project = report_info.project.substring(1, report_info.project.length);

            report_info.order_commodity_id = order_commodity_id;
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: SITE_URL + 'user/add_report_userInfo',
                data: report_info,
                success: function (response) {
                    if (response.success) {
                        alert('添加成功');
                        empty_report_info();
                    } else {
                        alert(response.msg);
                        report_info = {};
                    }
                }
            });
        }
    });

    //清空报告信息列表
    function empty_report_info() {
        dom_name.val('');
        dom_birth.val('');
        dom_phone.val('');
        initAddress();
        dom_identity_card.val('');
        dom_district.empty();
        dom_address.val('');
        dom_height.val('');
        dom_weight.val('');
        dom_report_list.css("display", "none");

        report_info = {};
        checked_project_ids = {};
        projects = [];
        // order_commodity_id  ='';
    }

    function padding_form_data(report_info) {
        dom_name.val(report_info.name);
        $(":radio[name='gender'][value='" + report_info.gender + "']").prop("checked", "checked");
        dom_birth.val(report_info.birth);
        $(":radio[name='smoking'][value='" + report_info.smoking + "']").prop("checked", "checked");
        dom_phone.val(report_info.phone);
        dom_identity_card.val(report_info.identity_card);
        dom_height.val(report_info.height);
        dom_weight.val(report_info.weight);
        dom_province.empty().append("<option value='" + report_info.province_code + "' selected>" + report_info.province + "</option>");
        dom_city.empty().append("<option value='" + report_info.city_code + "' selected>" + report_info.city + "</option>");
        if (report_info.district && report_info.district_code) {
            dom_district.empty().append("<option value='" + report_info.district_code + "' selected>" + report_info.district + "</option>");
        }
        dom_address.val(report_info.address);
        dom_personal_history.val(report_info.personal_history);
        dom_family_history.val(report_info.family_history);
        // dom_relationship.val(report_info.blood_relationship);
        console.log(dom_checkproject.children("input"));
    }

    function disbaled_report_info(flag) {
        if (flag) {
            dom_name.attr('disabled', 'disabled');
            dom_gender.attr('disabled', 'disabled');
            dom_birth.attr('disabled', 'disabled');
            dom_smoking.attr('disabled', 'disabled');
            dom_phone.attr('disabled', 'disabled');
            dom_province.attr('disabled', 'disabled');
            dom_city.attr('disabled', 'disabled');
            dom_identity_card.attr('disabled', 'disabled');
            dom_district.attr('disabled', 'disabled');
            dom_address.attr('disabled', 'disabled');
            dom_height.attr('disabled', 'disabled');
            dom_weight.attr('disabled', 'disabled');
            dom_personal_history.attr('disabled', 'disabled');
            dom_family_history.attr('disabled', 'disabled');
            // dom_relationship.attr('disabled', 'disabled');
            dom_submit_report_info.attr('disabled', 'disabled');
            $(".project_option").attr('disabled', 'disabled');
            $('.project-name').prop('disabled', true);
        } else {
            dom_name.removeAttr('disabled');
            dom_gender.removeAttr('disabled').prop('checked',false);
            dom_birth.removeAttr('disabled');
            dom_smoking.removeAttr('disabled').prop('checked',false);
            dom_phone.removeAttr('disabled');
            dom_province.removeAttr('disabled');
            dom_city.removeAttr('disabled');
            dom_identity_card.removeAttr('disabled');
            dom_district.removeAttr('disabled');
            dom_address.removeAttr('disabled');
            dom_height.removeAttr('disabled');
            dom_weight.removeAttr('disabled');
            dom_personal_history.removeAttr('disabled');
            dom_family_history.removeAttr('disabled');
            // dom_relationship.removeAttr('disabled');
            dom_submit_report_info.removeAttr('disabled');
            $(".project_option").removeAttr('disabled');
            $('.project-name').prop('disabled', false);
        }

    }

    /**
     * 项目复选框点击事件处理函数
     * @param event
     */
    var selected_project_option_num = 0;
    var selected_project_option_list = [];

    function project_option_click(event) {
        if ($(this).is(':checked')) {
            selected_project_option_num++;
            selected_project_option_list.push($(this).val());
        } else {
            var index = $.inArray($(this).val(), selected_project_option_list);
            if (index >= 0) {
                selected_project_option_list.splice(index, 1);
            }
            if (selected_project_option_num > 0) {
                selected_project_option_num--;
            } else {
                selected_project_option_num = 0;
            }
        }

        if (selected_project_option_num >= parseInt($("#project_num").data("num"))) {
            // 已经选够了规定的项目数量
            $(".project_option").attr("disabled", "disabled");
            $("input[class='project_option']:checked").removeAttr("disabled");
        }else {
            $(".project_option").removeAttr("disabled");
        }
    }

    laydate({
        elem: '#birth'
    });

    /**
     * 编辑地址
     * 收获信息页面
     */
    if ($('#receiving_info').html() != undefined) {
        district = new AMap.DistrictSearch({
            level: 'country',
            showbiz: false,
            subdistrict: 1
        });
        initAddress();
    }
    //根据报告编号查询是否存在
    var projects = [];
    var order_commodity_id = '';
    $('#search_btn').click(function (e) {
        var number = $('#order_number').val();
        var x = document.getElementById("report-list");
        if (number == '') {
            alert('请填写报告编号!');
        }
        else {
            $.ajax({
                type: 'post',
                dataType: "json",
                url: SITE_URL + 'user/check_report',
                data: {
                    number: number
                },
                success: function (response) {
                    if (response.success) {
                        order_commodity_id = response.data.report.order_commodity_id;

                        // $('#birth').attr('disabled','disabled');
                        $('.removetemplate').remove();
                        var tpl = document.getElementById('project_tpl').innerHTML;
                        $.each(response.data.project, function (index, data) {
                            $.each(data, function (child_index, childe_data) {
                                
                                childe_data.checked = false;
                                childe_data.disabled = false;
                            })
                        });
                        console.log();
                        projects = response.data.project;
                        $(".project-lists").html(template(tpl, {list: response.data.project}));
                        if(response.data.project && response.data.project.length > 0 && response.data.project[0] && response.data.project[0].length > 0 && response.data.project[0][0].id){
                            if(response.data.report.project && response.data.report.project != '' && response.data.report.project.length > 0 && response.data.report.report_status == 1){
                                $.each($('.project-name'), function (index, ele) {
                                    var template_id = $(ele).data('project-id').toString();
                                    var eq = $.inArray(template_id, response.data.report.project);
                                    if(eq >=0 ){
                                        $(ele).prop('checked', true);
                                        return;
                                    }
                                })
                            }
                            $('.project-select').show();
                        }else{
                            $('.project-select').hide();
                        }

                        // 为有描述的检测项目添加气泡
                        // $(".project_description").each(function (index, item) {
                        //     $(item).popover({
                        //         animation: true,
                        //         placement: 'top',
                        //         title: '项目说明',
                        //         trigger: 'hover',
                        //         content: $(item).data('description')
                        //     });
                        // });
                        selected_project_option_num = 0;
                        $(".project_option").click(project_option_click);
                        if (response.data.report.project) {
                            $(".project_option").each(function (index, item) {
                                var item_index = $.inArray($(item).val(), response.data.report.project);
                                if (item_index >= 0) {
                                    $(item).prop("checked", "checked");
                                    selected_project_option_list.push(response.data.report.project[item_index]);
                                    selected_project_option_num++;
                                }
                            });
                        }
                        if (response.data.report.report_status != undefined && response.data.report.report_status != "" && response.data.report.report_status == "1") {
                            padding_form_data(response.data.report);
                            disbaled_report_info(true);
                        } else {
                            empty_report_info();
                            disbaled_report_info(false);
                        }
                        x.style.display = "block";
                        dom_gender.prop('disabled', 'disabled');
                        dom_birth.prop('disabled', 'disabled');
                    } else {
                        alert(response.msg);
                        empty_report_info();
                        x.style.display = "none";
                    }
                },
                error: function (error) {
                    alert('服务器繁忙，请稍后重试');
                }
            });
        }
    });
    
//    选择项目
    var checked_project_ids = {};
    $(document).on('click', '.project-name', function () {
        var id = $(this).data('project-id');
        var template_id = $(this).data('template-id');
        var project_num = $(this).data('project-num');
        if(!checked_project_ids[template_id] || checked_project_ids[template_id] == ''){
            checked_project_ids[template_id] = [];
        }

        var id_index = checked_project_ids[template_id].indexOf(id);
        if($(this).prop('checked') && id_index < 0){
            checked_project_ids[template_id].push(id);
        }else if(!$(this).prop('checked') && id_index >= 0){
            checked_project_ids[template_id].splice(id_index, 1);
        }

        if(checked_project_ids[template_id].length == project_num){
            $(this).parents('.parent-span').find('input').prop('disabled', true);
            $(this).parents('.parent-span').find('input:checked').prop('disabled', false);
        }else{
            $(this).parents('.parent-span').find('input').prop('disabled', false);
        }

        console.log(checked_project_ids);
    })
});
/**
 * 初始化省市区选择控件
 */
function initAddress() {
    $("#city").innerHTML = '';
    $("#city").empty();
    $("#city").val("");
    $("#city").removeAttr("disabled");

    $('#district').innerHTML = '';
    $('#district').empty();
    $('#district').val("");
    $('#district').removeAttr("disabled");

    district.search('中国', function (status, result) {
        if (status == 'complete') {
            if (result.districtList.length > 0) {
                getAdministrativeRegion(result.districtList[0]);
            } else {
                console.log('获取省级行政区失败');
            }
        }
    });
}
/**
 * 解析省市区信息
 * @param data
 */
function getAdministrativeRegion(data, city_code, district_code) {
    var subList = data.districtList;
    var level = data.level;
    //清空下一级别的下拉列表
    if (level === 'province') {
        nextLevel = 'city';
        $("#city").innerHTML = '';
        $('#district').innerHTML = '';
        $("#city").empty();
        $("#city").val("");
        $('#district').empty();
        $('#district').val("");
    } else if (level === 'city') {
        nextLevel = 'district';
        $('#district').innerHTML = '';
        $('#district').empty();
        $('#district').val("");
    }
    if (subList) {
        if (subList.length > 0) {
            $('#' + subList[0].level).empty();
        }

        var contentSub = new Option('--请选择--');
        contentSub.setAttribute("value", "");
        for (var i = 0, l = subList.length; i < l; i++) {
            var name = subList[i].name;
            var value = subList[i].adcode;
            var levelSub = subList[i].level;
            var cityCode = subList[i].citycode;

            if (i == 0) {
                document.querySelector('#' + levelSub).add(contentSub);
                document.querySelector('#' + levelSub).removeAttribute('disabled');
            }
            contentSub = new Option(name);
            contentSub.setAttribute("value", value);
            contentSub.center = subList[i].center;
            contentSub.adcode = subList[i].adcode;

            document.querySelector('#' + levelSub).add(contentSub);
        }
        if (typeof(city_code) != 'undefined' && city_code != "" && levelSub == "city") {
            $('#' + levelSub).val(city_code);
            searchNextLevel($('#' + levelSub)[0], city_code, district_code);
        } else if (typeof(district_code) != 'undefined' && district_code != "" && levelSub == "district") {
            $('#' + levelSub).val(district_code);
        }
    } else {
        if (level == "province") {
            // 将市级、县级下拉列表置为不可用
            $("#city").attr('disabled', 'disabled');
            $("#district").attr('disabled', 'disabled');
        } else if (level == "city") {
            // 将县级下拉列表置为不可用
            $("#district").attr('disabled', 'disabled');
        }
    }

}
/**
 * 根据当前所选省市搜索下级行政区域列表
 * @param obj
 * @param city_code 城市代码，编辑地址时初始化控件使用
 * @param district_code 区县代码，编辑地址时初始化控件使用
 */
function searchNextLevel(obj, city_code, district_code) {
    city_code = city_code || '';
    district_code = district_code || '';
    var option = obj[obj.options.selectedIndex];
    var keyword = option.text; //关键字
    var adcode = option.adcode;
    district.setLevel(option.value); //行政区级别
    //行政区查询
    //按照adcode进行查询可以保证数据返回的唯一性
    district.search(adcode, function (status, result) {
        if (status === 'complete') {
            getAdministrativeRegion(result.districtList[0], city_code, district_code);
        }
    });
}



