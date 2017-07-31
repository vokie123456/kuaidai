@extends('admin.layout')

@section('title', 'Markdown文章列表')

@section('head-extend')
    <link href="{{cdn('plugins/datatables-plugins/dataTables.bootstrap.css')}}" rel="stylesheet">
@endsection

@section('page-wrapper')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Markdown文章列表</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-2 col-xs-offset-10">
            <div class="btn-group pull-right">
                <a class="btn btn-success" href="{{url('/article-manage/markdown/create')}}">写文章</a>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <table class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>用户</th>
                    <th>标题</th>
                    <th>作者</th>
                    <th>类型</th>
                    <th>状态</th>
                    <th>创作时间</th>
                    <th>创建时间</th>
                    <th>置顶</th>
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
    function detail(id) {
        window.open('//{{config('domain.blog')}}/blog/' + id);
    }

    // 加载数据
    var data = JSON.parse($('#paginate').html()).data,
        $tableBody = $('#table-body');
    $tableBody.html('');
    $.each(data, function(i, obj) {
        var $tr = $('<tr></tr>'),
            $option = $('<td>\
                <a href="javascript:void(0);" class="btn-detail">详情</a>\
                <a href="javascript:void(0);" class="btn-up">发布</a>\
                <a href="javascript:void(0);" class="btn-down">下线</a>\
                <a href="/article-manage/markdown/' + obj.id + '" class="btn-edit">编辑</a>\
                <a href="javascript:void(0);" class="btn-delete">删除</a>\
                </td>'),
            $statusLabel = $('<label class="label"></label>');

        // 状态按钮只显示一个
        if (obj.article.status == 1) {
            $option.find('.btn-up').hide();
            $statusLabel.addClass('label-info');
        } else {
            $option.find('.btn-down').hide();
            $statusLabel.addClass('label-default');
        }

        // 置顶操作
        var $isTop = '<a href="javascript:void(0);" class="btn-top">置顶</a>';
        var $isTopLabel = '';
        if (obj.article.is_top == 1) {
            $isTopLabel = $('<label class="label label-danger">置顶</label>')
            $isTop = '<a href="javascript:void(0);" class="btn-untop">取消置顶</a>';
        }

        $statusLabel.html(obj.article.status_text);

        $tr.append('<td>' + obj.id + '</td>');
        $tr.append('<td>' + obj.article.user_id + '</td>');
        $tr.append('<td>' + obj.article.title + '</td>');
        $tr.append('<td>' + obj.article.author + '</td>');
        $tr.append('<td>' + obj.article.type_text + '</td>');
        $tr.append($('<td></td>').append($statusLabel, ' ', $isTopLabel));
        $tr.append('<td>' + obj.article.write_time.toString().substr(0, 10) + '</td>');
        $tr.append('<td>' + obj.created_at + '</td>');
        $tr.append($('<td></td>').append($isTop));
        $tr.append($option);

        $tr.find('.btn-detail').click(function() {detail(obj.article.id)});
        $tr.find('.btn-up').click(function() {restful.patch('/article-manage/article/up/' + obj.article.id)});
        $tr.find('.btn-down').click(function() {restful.patch('/article-manage/article/down/' + obj.article.id)});
        $tr.find('.btn-delete').click(function() {restful.del('/article-manage/markdown/' + obj.id)});
        $tr.find('.btn-top').click(function() {restful.patch('/article-manage/article/top/' + obj.article.id)});
        $tr.find('.btn-untop').click(function() {restful.patch('/article-manage/article/untop/' + obj.article.id)});
        $tableBody.append($tr);
    });
});
</script>
@endsection