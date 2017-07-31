@extends('admin.layout')

@section('title', '修改密码')

@section('page-wrapper')
    <br>
    <div class="panel panel-default">
        <div class="panel-heading">修改密码</div>
        <div class="panel-body">
            <form class="form-horizontal" method="post">

                @if($errors->any())
                    <div class="form-group">
                        <div class="col-xs-8">
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                {{$errors->first()}}
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($success))
                    <div class="form-group">
                        <div class="col-xs-8">
                            <div class="alert alert-success alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                {{$success}}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label class="col-sm-2 control-label">旧密码</label>
                    <div class="col-sm-6">
                        <input type="password" class="form-control" name="old_password" disableautocomplete autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">新密码</label>
                    <div class="col-sm-6">
                        <input type="password" class="form-control" name="password" disableautocomplete autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">确认密码</label>
                    <div class="col-sm-6">
                        <input type="password" class="form-control" name="password_confirmation" disableautocomplete autocomplete="off">
                    </div>
                </div>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-6">
                        <button type="submit" class="btn btn-primary">提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection