@extends('admin.layout')

@section('title', '选项配置')

@section('head-extend')
    <link href="{{cdn('plugins/datatables-plugins/dataTables.bootstrap.css')}}" rel="stylesheet">
@endsection

@section('page-wrapper')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">选项配置</h1>
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
                    <th>ID</th>
                    <th>选项名称</th>
                    <th>备注</th>
                    <th>选项值</th>
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
                            <label class="col-xs-3 control-label">选项名称</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="option_name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-3 control-label">备注</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="remark">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-3 control-label">选项值</label>
                            <div class="col-sm-9">
                                <textarea name="option_value" rows="5" class="form-control"></textarea>
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
        $modal.find('[name=option_name]').val(defObj.option_name || '');
        $modal.find('[name=remark]').val(defObj.remark || '');
        $modal.find('[name=option_value]').val(defObj.option_value || '');

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
            restful.post('/option', $form.serialize());
        });
    }

    // 编辑
    function edit(obj) {
        var $modal = showModal(obj, '编辑'),
            $form = $modal.find('form');

        $modal.find('.btn-submit').click(function() {
            restful.put('/option/' + obj.id, $form.serialize());
        });
    }

    // 加载数据
    var data = JSON.parse($('#paginate').html()).data,
        $tableBody = $('#table-body');
    $tableBody.html('');
    $.each(data, function(i, obj) {
        var $tr = $('<tr></tr>');
        var $option = $('<td>\
                <a href="javascript:void(0);" class="btn-edit">编辑</a>\
                </td>');

        $tr.append('<td>' + obj.id + '</td>');
        $tr.append('<td>' + obj.option_name + '</td>');
        $tr.append('<td>' + obj.remark + '</td>');
        $tr.append('<td>' + obj.option_value + '</td>');
        $tr.append('<td>' + obj.created_at + '</td>');
        $tr.append($option);

        $option.find('.btn-edit').click(function() {edit(obj)});
        $tableBody.append($tr);
    });
});
</script>
@endsection