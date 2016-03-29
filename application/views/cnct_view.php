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
    <title>connect client</title>
    <style type="text/css">
        #calculators {
            margin: 10% auto;
            width:430px;
            border:1px solid #000;
        }
    </style>
    <script src="<?=base_url().'/static/scripts/jquery-2.2.0.min.js'?>"></script>
    <script type="text/javascript">

    </script>
    <script>
        $(function(){
            console.log('start websocket connect');
            var ws = new WebSocket("ws://localhost:4000");
            ws.onopen = function(e) {
                console.log("on open succeed.");
            };
            ws.onerror = function(e) {
                console.log("in on error.");
                console.log(e);
            };
        });
    </script>
</head>
<body>


</body>
</html>