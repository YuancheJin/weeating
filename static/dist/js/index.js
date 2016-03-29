/**
 * Created by jyc on 2016/3/14.
 */

//配置信息
var GET_TOTAL_URL='http://localhost/weeating/index.php/eating/get_total';
var EAT_ORDER_URL='http://localhost/weeating/index.php/eating/order/1/1';
var UN_ORDER_URL='http://localhost/weeating/index.php/eating/order/1/0';
$(function () {

    //页面管理
    var pageManager = {
        $container: $('.js_container'),
        _pageStack: [],
        _configs: [],
        _defaultPage: null,
        _pageIndex: 1,
        setDefault: function (defaultPage) {
            this._defaultPage = this._find('name', defaultPage);
            return this;
        },
        init: function () {
            var self = this;

            $(window).on('hashchange', function () {
                var state = history.state || {};
                var url = location.hash.indexOf('#') === 0 ? location.hash : '#';
                var page = self._find('url', url) || self._defaultPage;
                if (state._pageIndex <= self._pageIndex || self._findInStack(url)) {
                    self._back(page);
                } else {
                    self._go(page);
                }
            });

            if (history.state && history.state._pageIndex) {
                this._pageIndex = history.state._pageIndex;
            }

            this._pageIndex--;

            var url = location.hash.indexOf('#') === 0 ? location.hash : '#';
            var page = self._find('url', url) || self._defaultPage;
            this._go(page);
            return this;
        },
        push: function (config) {
            this._configs.push(config);
            return this;
        },
        go: function (to) {
            var config = this._find('name', to);
            if (!config) {
                return;
            }
            location.hash = config.url;
        },
        _go: function (config) {
            this._pageIndex ++;

            history.replaceState && history.replaceState({_pageIndex: this._pageIndex}, '', location.href);

            var html = $(config.template).html();
            var $html = $(html).addClass('slideIn').addClass(config.name);
            this.$container.append($html);
            this._pageStack.push({
                config: config,
                dom: $html
            });

            if (!config.isBind) {
                this._bind(config);
            }

            return this;
        },
        back: function () {
            history.back();
        },
        _back: function (config) {
            this._pageIndex --;

            var stack = this._pageStack.pop();
            if (!stack) {
                return;
            }

            var url = location.hash.indexOf('#') === 0 ? location.hash : '#';
            var found = this._findInStack(url);
            if (!found) {
                var html = $(config.template).html();
                var $html = $(html).css('opacity', 1).addClass(config.name);
                $html.insertBefore(stack.dom);

                if (!config.isBind) {
                    this._bind(config);
                }

                this._pageStack.push({
                    config: config,
                    dom: $html
                });
            }

            stack.dom.addClass('slideOut').on('animationend', function () {
                stack.dom.remove();
            }).on('webkitAnimationEnd', function () {
                stack.dom.remove();
            });

            return this;
        },
        _findInStack: function (url) {
            var found = null;
            for(var i = 0, len = this._pageStack.length; i < len; i++){
                var stack = this._pageStack[i];
                if (stack.config.url === url) {
                    found = stack;
                    break;
                }
            }
            return found;
        },
        _find: function (key, value) {
            var page = null;
            for (var i = 0, len = this._configs.length; i < len; i++) {
                if (this._configs[i][key] === value) {
                    page = this._configs[i];
                    break;
                }
            }
            return page;
        },
        _bind: function (page) {
            var events = page.events || {};
            for (var t in events) {
                for (var type in events[t]) {
                    this.$container.on(type, t, events[t][type]);
                }
            }
            page.isBind = true;
        }
    };

    var home = {
        name: 'home',
        url: '#',
        template: '#tpl_home',
        events: {
            '.js_grid': {
                click: function (e) {
                    var id = $(this).data('id');
                    pageManager.go(id);
                }
            },
            '#click_sure':{
                click:function (e) {
                    clickReserveButton();
                }
            }
        }
    };
    var article = {
        name: 'article',
        url: '#article',
        template: '#tpl_article',
        events: {}
    };
    pageManager.push(home)
        .push(article)
        .setDefault('home')
        .init();

    //serviceInit
    serviceInit();
});
var serviceInit=function(){
    showLoadingToast();
    //禁止触摸滑动
    $(document).on('touchmove', function(e) {
        e.preventDefault();
    });
    //初始化请求
    getEatData(GET_TOTAL_URL,'',function(data){
        hideLoadingToast();
        var code=data.code;
        if(code==0){
            var time=data.date;
            //php bug 临时解决 现在返回的是秒数，而非毫秒数
            time=time*1000;
            var ts=new Date(time);
            //时间倒计时
            var isWork=isWorkTime(ts);
            if(isWork){
                showWorkPage();
                //点餐时间
                //初始化日期控件
                initDatePlugin(ts);
                if($('.shake-slow').html()!=data.total){
                    clock(data.total);
                }
                //初始化函数-刷新订餐数据
                var interval=setInterval(function(){
                    getEatData(GET_TOTAL_URL,'',function(data){
                        var code=data.code;
                        if(code==0){
                            var time=data.date;
                            //php bug 临时解决 现在返回的是秒数，而非毫秒数
                            time=time*1000;
                            var ts=new Date(time);
                            //时间倒计时
                            var isWork=isWorkTime(ts);
                            if(isWork){
                                showWorkPage();
                                if($('.shake-slow').html()!=data.total){
                                    clock(data.total);
                                }
                            }else{
                                //清除定时器 && 显示非工作时间页面
                                window.clearInterval(interval);
                                showNoWorkPage();
                                //再显示一次最终的定参数

                                getEatData(GET_TOTAL_URL,'',function(data){
                                    var code=data.code;
                                    if(code==0){
                                        clock(data.total);
                                    }
                                });
                            }
                        }
                    });
                },5000);
            }else{
                showNoWorkPage();
                clock(data.total);
            }
        }else{
            //失败页面（ERROR）
            errorPage();
        }
    });
}

/**
 * loading效果
 */
var showLoadingToast=function(){
    var $loadingToast = $('#loadingToast');
    if ($loadingToast.css('display') != 'none') {
        return;
    }

    $loadingToast.show();
}
var hideLoadingToast=function(){
    var $loadingToast = $('#loadingToast');
    $loadingToast.hide();
}
/**
 * 微信提示框
 */
var showToast=function(type){
    if(type){
        $('.weui_toast_content').html('已提交');
    }else{
        $('.weui_toast_content').html('提交失败');
    }
    var $toast = $('#toast');
    if ($toast.css('display') != 'none') {
        return;
    }

    $toast.show();
    setTimeout(function () {
        $toast.hide();
    }, 2000);
}
/**
 * 点击订餐按钮
 */
var clickReserveButton=function(){
    showLoadingToast();
    //首先判断按钮是否显示（只有在5）
    if($('#click_sure').html()=='确认'){
        getEatData(EAT_ORDER_URL,'',function(data){
            hideLoadingToast();
            var code=data.code;
            if(code==0){
                showToast(true);
                $('#click_sure').removeClass('weui_btn_plain_primary').addClass('weui_btn_plain_default').html('撤销');
                //订餐成功动画
                countDown();
                getEatData(GET_TOTAL_URL,'',function(data){
                    var code=data.code;
                    if(code==0){
                        //切换按钮图标
                        var time=data.date;
                        //php bug 临时解决 现在返回的是秒数，而非毫秒数
                        time=time*1000;
                        var ts=new Date(time);
                        //时间倒计时
                        var isWork=isWorkTime(ts);
                        if(isWork){
                            if($('.shake-slow').html()!=data.total){
                                clock(data.total);
                            }
                        }else{
                            //显示非工作时间页面
                            showNoWorkPage();
                        }
                    }else{
                        //查询失败-不做提示
                    }
                });
            }else{
                //订餐失败提示
                showToast(false);
            }
        });
    }else{
        getEatData(UN_ORDER_URL,'',function(data){
            hideLoadingToast();
            var code=data.code;
            if(code==0){
                showToast(true);
                $('#click_sure').removeClass('weui_btn_plain_default').addClass('weui_btn_plain_primary').html('确认');
                getEatData(GET_TOTAL_URL,'',function(data){
                    var code=data.code;
                    if(code==0){
                        var time=data.date;
                        //php bug 临时解决 现在返回的是秒数，而非毫秒数
                        time=time*1000;
                        var ts=new Date(time);
                        //时间倒计时
                        var isWork=isWorkTime(ts);
                        if(isWork){
                            if($('.shake-slow').html()!=data.total){
                                clock(data.total);
                            }
                        }else{
                            //显示非工作时间页面
                            showNoWorkPage();
                        }
                    }else{
                        //查询失败-不做提示
                    }
                });
            }else{
                //订餐失败提示
                showToast(false);
            }
        });
    }
}
/**
 * 非工作时间页面展示||错误页面
 */
var showNoWorkPage=function(){

    $('#click_sure').hide();
    $('#orderImg').attr('src','dist/img/34958PICXun_1024.png');
    $('.shake-slow').css('font-size','40px');

}
/**
 * 工作时间页面展示
 */
var showWorkPage=function(){

    $('#click_sure').show();
    $('#orderImg').attr('src','dist/img/plate.png');
    $('.shake-slow').css('font-size','30px');

}
/**
 * 错误页面
 */
var errorPage=function(){
    $('#orderImg').attr('src','dist/img/broken.png');
    $('.weui_text_area').hide();
    $('#countdown').hide();
    $('.weui_opr_area').hide();
}

/**
 *  时间处理函数
 */
var isWorkTime=function(ts){
    //时间倒计时
    var week=ts.getDay();
    if(week==6||week==0){
        //周末-不提供服务 （非工作时页面）
        return false;
    }else{
        var hours=ts.getHours();       //获取当前小时数(0-23)
        if(hours>=13&&hours<16){
            return true;
        }else{
            //非点餐时间=不提供服务
            return false;
        }
    }
}
/**
 * 订餐成功动画
 */
var countDown=function(){
    var num=0;
    setInterval(function() {
        num++;
        if(num<20){
            var left = Math.random() * window.innerWidth;
            var height = Math.random() * window.innerHeight;
            var src = "dist/img/s" + Math.ceil(Math.random()*5) + ".png"; //两张图片分别为"s1.png"、"s2.png"
            snow(left, height, src);
        }
    }, 500);
}
/**
 * 初始化日期控件
 */
var initDatePlugin=function(ts){
    //点餐时间
    var countDate = new Date(ts.getFullYear()+'/'+(ts.getMonth()+1)+'/'+ts.getDate()+' 16:00:00');
    $('#countdown').countdown({
        timestamp	: countDate,
        callback	: function(days, hours, minutes, seconds){
        }
    });
}

/**
 * 发送数据请求
 */
var getEatData=function(url,param,callback){
    $.ajax({
        type:'post',
        url:url,
        contentType : "application/x-www-form-urlencoded;", //避免乱码
        data:param,
        dataType: "text",
        timeout:100000,  //依照 app ms 配置超时信息
        success:function(data){
            data=JSON.parse(data.toString())
            callback(data);
        },
        beforeSend:function(xhr){
            //console.log("ajax beforeSend inner,when I return false the request will be cancel"+"\r\n");
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            //请求出错时调用。 (超时，解析错误，或者状态码不在HTTP 2xx)。
            console.log("error");
            callback('');
        },
        complete:function(xhr,status){
            console.log("complete");
        }
    });
}