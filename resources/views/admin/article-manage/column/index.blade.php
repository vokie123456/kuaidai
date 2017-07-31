@extends('admin.layout')

@section('title', '栏目列表')

@section('head-extend')
    <link href="{{cdn('plugins/datatables-plugins/dataTables.bootstrap.css')}}" rel="stylesheet">
@endsection

@section('page-wrapper')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">栏目列表</h1>
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
                    <th>父ID</th>
                    <th>别名</th>
                    <th>栏目名称</th>
                    <th>类型</th>
                    <th>视图</th>
                    <th>权重</th>
                    <th>是否是示</th>
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
                            <label class="col-xs-3 control-label">父级</label>
                            <div class="col-sm-9">
                                <select name="parent_id" class="form-control">
                                    <option value="0">0-顶级</option>
                                    <option value="{{Request::input('parent_id', 0)}}">{{Request::input('parent_id', 0)}}-当前</option>
                                    @foreach ($paginate as $column)
                                        <option value="{{$column->id}}">{{$column->id}}-{{$column->column_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-3 control-label">别名</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="alias">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-3 control-label">栏目名称</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="column_name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-3 control-label">类型</label>
                            <div class="col-sm-9">
                                <select name="type" class="form-control">
                                    <option value="1">列表</option>
                                    <option value="2">页面</option>
                                    <option value="3">自定义视图</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-3 control-label">视图名称</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="view" placeholder="类型为自定义视图时生效">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-3 control-label">权重</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="weight" value="50">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-3 control-label">是否显示</label>
                            <div class="col-sm-9">
                                <select name="is_show" class="form-control">
                                    <option value="1">是</option>
                                    <option value="2">否</option>
                                </select>
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
        $modal.find('[name=parent_id]').find('option[value=' + defObj.parent_id + ']').attr('selected', true);
        $modal.find('[name=column_name]').val(defObj.column_name || '');
        $modal.find('[name=type]').find('option[value=' + defObj.type + ']').attr('selected', true);
        $modal.find('[name=view]').val(defObj.view || '');
        $modal.find('[name=alias]').val(defObj.alias || '');
        $modal.find('[name=weight]').val(defObj.weight || '50');
        $modal.find('[name=is_show]').find('option[value=' + defObj.is_show + ']').attr('selected', true);

        $modal.find('[name=type]').change(function() {
            var $viewGroup = $modal.find('[name=view]').parents('.form-group');
            if ($(this).find('option:selected').val() == 3) {
                $viewGroup.show();
            } else {
                $viewGroup.hide();
            }
        }).change();

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
            restful.post('/article-manage/column', $form.serialize());
        });
    }

    // 编辑
    function edit(obj) {
        var $modal = showModal(obj, '编辑'),
            $form = $modal.find('form');

        $modal.find('.btn-submit').click(function() {
            restful.put('/article-manage/column/' + obj.id, $form.serialize());
        });
    }

    // 加载数据
    var data = JSON.parse($('#paginate').html()).data,
        $tableBody = $('#table-body');
    $tableBody.html('');
    $.each(data, function(i, obj) {
        var $tr = $('<tr></tr>');
        var $option = $('<td>\
                <a href="?parent_id=' + obj.id + '">子栏目</a>\
                <a href="javascript:void(0);" class="btn-edit">编辑</a>\
                <a href="javascript:void(0);" class="btn-delete">删除</a>\
                </td>');

        $tr.append('<td>' + obj.id + '</td>');
        $tr.append('<td>' + obj.parent_id + '</td>');
        $tr.append('<td>' + obj.alias + '</td>');
        $tr.append('<td>' + obj.column_name + '</td>');
        $tr.append('<td>' + obj.type_text + '</td>');
        $tr.append('<td>' + obj.view + '</td>');
        $tr.append('<td>' + obj.weight + '</td>');
        $tr.append('<td>' + obj.is_show_text + '</td>');
        $tr.append('<td>' + obj.created_at + '</td>');
        $tr.append($option);

        $option.find('.btn-edit').click(function() {edit(obj)});
        $option.find('.btn-delete').click(function() {restful.del('/article-manage/column/' + obj.id)});
        $tableBody.append($tr);
    });
});
</script>
@endsection