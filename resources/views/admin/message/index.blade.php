@extends('admin.layout')

@section('title', '留言板')

@section('head-extend')
    <link href="{{cdn('plugins/datatables-plugins/dataTables.bootstrap.css')}}" rel="stylesheet">
@endsection

@section('page-wrapper')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">留言板</h1>
    </div>
</div>

<div class="row">
    <div class="col-xs-2 col-xs-offset-10">
        <div class="btn-group pull-right">
        </div>

    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <table class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline">
            <thead>
            <tr>
                <th>ID</th>
                <th>昵称</th>
                <th>邮箱</th>
                <th>IP</th>
                <th>内容</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>

            <tbody id="table-body">
            <tr><td colspan="50">暂无数据。</td></tr>
            </tbody>

        </table>
    </div>
</div>

<div class="row">
    <div class="col-xs-6 col-xs-offset-6 text-right">
        {!! $paginate->render() !!}
    </div>
</div>
@endsection

@section('body-extend')
<script id="paginate" type="text/html">{!! $paginate->toJson() !!}</script>
<script>
require(['jquery', 'restful'], function($, restful) {
    // 加载数据
    var data = JSON.parse($('#paginate').html()).data,
        $tableBody = $('#table-body');
    $tableBody.html('');
    $.each(data, function(i, obj) {
        var $tr = $('<tr></tr>'),
            $option = $('<td>\
                <a href="javascript:void(0);" class="btn-delete">删除</a>\
                <a href="javascript:void(0);" class="btn-restore">恢复</a>\
                </td>');

        if (obj.deleted_at) {
            $option.find('.btn-delete').hide();
            $option.find('.btn-restore').show();
        } else {
            $option.find('.btn-delete').show();
            $option.find('.btn-restore').hide();
        }

        $tr.append('<td>' + obj.id + '</td>');
        $tr.append('<td>' + obj.user.nickname + '</td>');
        $tr.append('<td>' + obj.user.username + '</td>');
        $tr.append('<td>' + obj.ip + '</td>');
        $tr.append('<td>' + obj.content + '</td>');
        $tr.append('<td>' + obj.created_at + '</td>');
        $tr.append($option);

        $option.find('.btn-delete').click(function() {restful.del('/message/' + obj.id)});
        $option.find('.btn-restore').click(function() {restful.restore('/message/restore/' + obj.id)});
        $tableBody.append($tr);
    });
});
</script>
@endsection