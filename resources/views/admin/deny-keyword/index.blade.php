@extends('admin.layout')

@section('title', '禁用词管理')

@section('head-extend')
    <link href="{{cdn('plugins/datatables-plugins/dataTables.bootstrap.css')}}" rel="stylesheet">
@endsection

@section('page-wrapper')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">禁用词管理</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-2 col-xs-offset-10">
            <div class="btn-group pull-right">
                <button class="btn btn-success" id="btn-add">新增</button>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <table class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline">
                <thead>
                <tr>
                    <th>关键词</th>
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

    <div class="modal fade" id="modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-xs-3 control-label">关键词</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="keyword" cols="30" rows="10" placeholder="一行一个输入一个关键词，每个词不得超过16个字。空格、空行将被自动过滤。"></textarea>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-submit">提交</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('body-extend')
<script id="paginate" type="text/html">{!! $paginate->toJson() !!}</script>
<script>
require(['jquery', 'restful'], function($, restful) {
    // 显示modal
    function showModal(defObj, title) {
        defObj = defObj || {};
        var $modal = $('#modal').clone();

        $modal.on('hidden.bs.modal', function() {
            $modal.remove();
        });

        $modal.find('.modal-title').html(title);
        $modal.find('[name=keyword]').val(defObj.keyword || '');

        $modal.modal();

        return $modal;
    }

    // 新增事件
    $('#btn-add').click(function() {
        add();
    });

    // 新增
    function add(defObj) {
        var $modal = showModal(defObj, '新增'),
            $form = $modal.find('form');

        $modal.find('.btn-submit').click(function() {
            restful.post('/deny-keyword', $form.serialize());
        });
    }

    // 加载数据
    var data = JSON.parse($('#paginate').html()).data,
        $tableBody = $('#table-body');
    $tableBody.html('');
    $.each(data, function(i, obj) {
        var $tr = $('<tr></tr>');
        var $option = $('<td>\
                <a href="javascript:void(0);" class="btn-delete">删除</a>\
                </td>');

        $tr.append('<td>' + obj.keyword + '</td>');
        $tr.append($option);

        $tr.find('.btn-delete').click(function() {restful.del('/deny-keyword/' + obj.keyword)});
        $tableBody.append($tr);
    });
});
</script>
@endsection