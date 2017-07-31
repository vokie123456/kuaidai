@extends('admin.layout')

@section('title', '缓存管理')

@section('head-extend')
    <link href="{{cdn('/plugins/datatables-plugins/dataTables.bootstrap.css')}}" rel="stylesheet">
@endsection

@section('page-wrapper')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">缓存管理</h1>
        </div>
    </div>

    <form class="form-horizontal" id="data-form">
        <div class="form-group">
            @foreach($pages as $page)
                <div class="col-xs-4">
                    @foreach($page as $key => $value)
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="chk-key" name="key[]" value="{{$key}}"> {{$value}}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="form-group">
            <div class="col-xs-12">
                <button class="btn btn-primary" id="btnSubmit" type="button">提交</button>
                <button class="btn btn-default" id="btnSelectAll" type="button">全选</button>
                <button class="btn btn-default" id="btnClearAll" type="button">全不选</button>
            </div>
        </div>
    </form>

@endsection

@section('body-extend')
<script>
require(['jquery', 'restful'], function($, restful) {
    $('#btnSelectAll').click(function() {
        $('.chk-key').attr('checked', true);
    });

    $('#btnClearAll').click(function() {
        $('.chk-key').attr('checked', false);
    });

    $('#btnSubmit').click(function() {
        restful.del('/cache', $('#data-form').serialize());
    });
});
</script>
@endsection