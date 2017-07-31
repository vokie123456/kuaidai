@extends('admin.layout')

@section('title', '角色列表')

@section('head-extend')
    <link href="{{cdn('plugins/datatables-plugins/dataTables.bootstrap.css')}}" rel="stylesheet">
    <link href="{{cdn('plugins/icheck/skins/square/blue.css')}}" rel="stylesheet">
@endsection

@section('page-wrapper')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">角色列表</h1>
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
                    <th>角色</th>
                    <th>名称</th>
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
                            <label class="col-xs-3 control-label">角色</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="role">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-3 control-label">名称</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name">
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

    <div class="modal fade" id="modal-permission" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">权限</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        {{--<div class="form-group">
                            <label class="col-xs-3 control-label"></label>
                            <div class="col-sm-9">
                            </div>
                        </div>--}}
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
require(['jquery', 'restful', 'icheck'], function($, restful) {
    // 显示modal
    function showModal(defObj, title) {
        defObj = defObj || {};
        var $modal = $('#modal').clone();

        $modal.on('hidden.bs.modal', function() {
            $modal.remove();
        });

        $modal.find('.modal-title').html(title);
        $modal.find('[name=role]').val(defObj.role || '');
        $modal.find('[name=name]').val(defObj.name || '');

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
            restful.post('/permission/role', $form.serialize());
        });
    }

    // 编辑
    function edit(obj) {
        var $modal = showModal(obj, '编辑'),
            $form = $modal.find('form');

        $modal.find('.btn-submit').click(function() {
            restful.put('/permission/role/' + obj.id, $form.serialize());
        });
    }

    // 获取权限
    function permission(id) {
        restful.get('/permission/role/' + id + '/permission').done(function(data) {
            var $modal = $('#modal-permission').clone(),
                $form = $modal.find('form'),
                permissions = data.all,
                checkeds = data.checked;

            $.each(permissions, function(ctl, ctlObj) {
                var $formGroup = $('<div class="form-group"></div>'),
                    $label = $('<label class="col-xs-3 control-label" style="word-break:break-all;"></label>'),
                    $container = $('<div class="col-xs-9"></div>');

                $label.html(ctlObj.title);

                $.each(ctlObj.actions, function(i, obj) {
                    var checked = '';

                    if ($.inArray(obj.id, checkeds) >= 0) {
                        checked = 'checked';
                    }

                    var $cbox = $('<label class="checkbox-inline">\
                                <input type="checkbox" name="node_id[]" value="' + obj.id + '" ' + checked + '> ' + obj.node + '\
                            </label>');

                    $container.append($cbox);
                });

                $label.click(function() {
                    var self = $(this);
                    var checked = false;
                    var parent = self.parents('.form-group');

                    if (typeof(self.data('checked')) == 'undefined') {
                        checked = !!parent.find(':checkbox').attr('checked');
                    } else {
                        checked = !!self.data('checked');
                    }


                    if (checked) {
                        parent.find(':checkbox').iCheck('uncheck');
                    } else {
                        parent.find(':checkbox').iCheck('check');
                    }
                    self.data('checked', !checked);
                });

                $formGroup.append($label, $container);
                $form.append($formGroup);
            });

            $modal.on('hidden.bs.modal', function() {
                $modal.remove();
            });

            $modal.find('.btn-submit').click(function() {
                restful.post('/permission/role/' + id + '/permission', $form.serialize());
            });

            $form.find('input:checkbox').iCheck({
                checkboxClass: 'icheckbox_square-blue'
            });

            $modal.modal();
        })
    }

    // 加载数据
    var data = JSON.parse($('#paginate').html()).data,
        $tableBody = $('#table-body');
    $tableBody.html('');
    $.each(data, function(i, obj) {
        var $tr = $('<tr></tr>');
        var $option = $('<td>\
                <a href="javascript:void(0);" class="btn-permission">权限</a>\
                <a href="javascript:void(0);" class="btn-edit">编辑</a>\
                <a href="javascript:void(0);" class="btn-delete">删除</a>\
                </td>');

        $tr.append('<td>' + obj.id + '</td>');
        $tr.append('<td>' + obj.role + '</td>');
        $tr.append('<td>' + obj.name + '</td>');
        $tr.append('<td>' + obj.created_at + '</td>');
        $tr.append($option);

        $option.find('.btn-edit').click(function() {edit(obj)});
        $option.find('.btn-permission').click(function() {permission(obj.id)});
        $option.find('.btn-delete').click(function() {restful.del('/permission/role/' + obj.id)});
        $tableBody.append($tr);
    });
});
</script>
@endsection