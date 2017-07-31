<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录 - {{config('app.name')}}</title>
    <link href="{{cdn('plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{cdn('css/sb-admin-2.css')}}" rel="stylesheet">
    <link href="{{cdn('plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">

    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">请登录</h3>
                </div>
                <div class="panel-body">
                    <form role="form" method="post">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="用户名" name="username" type="text" value="{{old('username')}}" autofocus>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="密码" name="password" type="password">
                            </div>

                            @if($errors->any())
                                <div class="form-group">
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        {{$errors->first()}}
                                    </div>
                                </div>
                            @endif

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button class="btn btn-lg btn-success btn-block">提交</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>