<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <title>借贷专家</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript">
        window.zhuge = window.zhuge || [];window.zhuge.methods = "_init debug identify track trackLink trackForm page".split(" ");
        window.zhuge.factory = function(b) {return function() {var a = Array.prototype.slice.call(arguments);a.unshift(b);
            window.zhuge.push(a);return window.zhuge;}};for (var i = 0; i < window.zhuge.methods.length; i++) {
            var key = window.zhuge.methods[i];window.zhuge[key] = window.zhuge.factory(key);}window.zhuge.load = function(b, x) {
            if (!document.getElementById("zhuge-js")) {var a = document.createElement("script");var verDate = new Date();
                var verStr = verDate.getFullYear().toString()+ verDate.getMonth().toString() + verDate.getDate().toString();
                a.type = "text/javascript";a.id = "zhuge-js";a.async = !0;a.src = (location.protocol == 'http:' ? "http://sdk.zhugeio.com/zhuge.min.js?v=" : 'https://zgsdk.zhugeio.com/zhuge.min.js?v=') + verStr;
                a.onerror = function(){window.zhuge.identify = window.zhuge.track = function(ename, props, callback){if(callback && Object.prototype.toString.call(callback) === '[object Function]')callback();};};
                var c = document.getElementsByTagName("script")[0];c.parentNode.insertBefore(a, c);window.zhuge._init(b, x)}};
        window.zhuge.load('93c18b079c7a4731b7b7097c64808491');//配置应用的AppKey
    </script>
</head>
<body>
<div id="app" style="display: none;">
    <router-view></router-view>

    <login :show.sync="showLoginDialog"></login>

    <mt-tabbar v-model="tabbarSelected" v-show="showTabBar" :fixed="true" ref="tabbar">
        <mt-tab-item v-for="tabbar in tabbars" :id="tabbar.id" :key="tabbar.id">
            <img slot="icon" :src="tabbar.icon">
            @{{tabbar.name}}
        </mt-tab-item>
    </mt-tabbar>
</div>

</body>
<script id="global" type="application/json">{!! json_encode($global) !!}</script>
<script id="products" type="application/json">{!! json_encode($products) !!}</script>
<script src="http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip={{$ip}}"></script>
<script src="{{elixir('js/app.js')}}"></script>
<script src="https://s19.cnzz.com/z_stat.php?id=1263536550&web_id=1263536550" language="JavaScript"></script>
<script>document.querySelector('[title="站长统计"]').style.display = 'none';</script>
</html>