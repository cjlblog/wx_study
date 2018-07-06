<!doctype html>
<html>
<head>
    <title>微信分享接口</title>
    <meta name="viewpoint" content="initial-scale=1.0;width=device-width"/>
    <meta http-equiv="content" content="text/html;charset=utf-8"/>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
</head>
<body>
<script>
    wx.config({
        debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '{{ $app_id }}', // 必填，公众号的唯一标识
        timestamp: {{ $time }}, // 必填，生成签名的时间戳
        nonceStr: '{{ $nonceStr }}', // 必填，生成签名的随机串
        signature: '{{ $signature }}',// 必填，签名
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage'
        ] // 必填，需要使用的JS接口列表
    });

    wx.ready(function(){

        wx.onMenuShareTimeline({
            title: 'test1', // 分享标题
            link: 'http://www.baidu.com', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'https://www.baidu.com/img/bd_logo1.png', // 分享图标
            success: function () {
                //todo 用户点击了分享后执行的回调函数
            },
            cancel: function () {
                //todo 用户点击了取消后执行的回调函数
            }
        });

        wx.onMenuShareAppMessage({
            title: 'test1', // 分享标题
            desc: 'test baidu', // 分享描述
            link: 'http://www.hao123.com', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'https://www.baidu.com/img/bd_logo1.png', // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                //todo 用户点击了分享后执行的回调函数
            },
            cancel: function () {
                //todo 用户点击了取消后执行的回调函数
            }
        });
    });

    wx.error(function(res){

    });
</script>
</body>
</html>