<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <title>借贷专家</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
<div id="app">
    <transition>
        <router-view></router-view>
    </transition>

    <bottom-bar :show="showBottomBar"></bottom-bar>
    <login :show.sync="showLoginDialog"></login>
</div>
</body>
<script src="{{elixir('js/app.js')}}"></script>
</html>