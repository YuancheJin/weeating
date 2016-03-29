<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,target-densitydpi=medium-dpi">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-touch-fullscreen" content="YES">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="Cache-Control" content="no-store">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>calculate web</title>
    <style type="text/css">
        #calculators {
            margin: 10% auto;
            width:430px;
            border:1px solid #000;
        }
    </style>
    <script src="<?=base_url().'/static/scripts/jquery-2.2.0.min.js'?>"></script>
    <script type="text/javascript">
        /*
        var xmlhttp = null;
        function $(id) {
            return document.getElementById(id);
        }
        //创建ajax引擎
        function getXMLHttpRequest() {
            var xmlhttp;
            try {
                //Firefox,Opera 8.0+, Safari
                xmlhttp = new XMLHttpRequest();
            }catch (e) {
                //Internet Explorer
                try {
                    xmlhttp = new ActiveXObject("Msxml12.XMLHTTP");
                }catch (e) {
                    try {
                        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                    }catch (e) {
                        alert("您的浏览器不支持AJAX！");
                        return false;
                    }
                }
            }
            return xmlhttp;
        }
        function isubmit() {
            xmlhttp = getXMLHttpRequest();
            //怎么判断创建是否成功
            if (xmlhttp) {
                //以post方式发送
                var url = "index.php/calculate/count/";
                var data = "num1="+$("num1").value+"&operate="+$("operate").value+"&num2="+$("num2").value;
                //打开请求
                xmlhttp.open("post", url, true);
                //下面这句话是post方式发送时必须要
                xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                //指定回调函数，指定的函数名一定不要带括号
                xmlhttp.onreadystatechange = deal;
                //发送请求
                xmlhttp.send(data);
            }
        }
        function deal() {
            //取出从服务器返回的数据
            if (xmlhttp.readyState == 4) {
                //取出值，根据返回信息的格式而定
                $("result").value = xmlhttp.responseText;

            }
        }
        */
    </script>
    <script>
        $(function(){
            /* //标准模板
            $.ajax({
                url: '/ci3.0/calculate/count',
                dataType: "json",
                //async: false, //非异步
                data:{
                    num1: 3,
                    num2: 5,
                    operate: 'x'
                },
                success:function(data) {
                    console.log('jq success:');
                    console.log(data);
                },
                error: function(data) {
                    console.log('jq error:');
                    console.log(data);
                }
            });
            */
            $('#cal').click(function(){
                var num1 = $('#num1').val();
                var num2 = $('#num2').val();
                var operate = $('#operate').val();
                $.ajax({
                    url: '/ci3.0/calculate/count',
                    dataType: "json",
                    async: false, //非异步
                    data:{
                        num1: num1,
                        num2: num2,
                        operate: operate
                    },
                    success:function(data) {
                        console.log('jq success:');
                        console.log(data);
                        if (data) {
                            $('#result').val(data.result);
                        }
                    },
                    error: function(data) {
                        console.log('jq error:');
                        console.log(data);
                    }
                });
            });
        });
    </script>
</head>
<body>
<h3>eating view</h3>
</body>
</html>