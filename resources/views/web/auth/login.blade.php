<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <title>登录</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
<div id="app"></div>
</body>
<script src="{{elixir('js/login.js')}}"></script>
</html>