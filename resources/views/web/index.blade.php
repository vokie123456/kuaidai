<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <title>借贷专家</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
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
<script src="{{elixir('js/app.js')}}"></script>
</html>