@extends('admin.layout')

@section('title', '用户列表')

@section('head-extend')
    <link href="{{cdn('plugins/datatables-plugins/dataTables.bootstrap.css')}}" rel="stylesheet">
@endsection

@section('page-wrapper')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">用户列表</h1>
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
                    <th>用户名</th>
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
                            <label class="col-xs-3 control-label">用户名</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="username">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-3 control-label">名称</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-3 control-label">密码</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="password" autocomplete="new-password">
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

    <div class="modal fade" id="modal-role" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">角色</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
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
        $modal.find('[name=username]').val(defObj.username || '');
        $modal.find('[name=name]').val(defObj.name || '');
        $modal.find('[name=password]').val('');

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
            restful.post('/permission/user', $form.serialize());
        });
    }

    // 编辑
    function edit(obj) {
        var $modal = showModal(obj, '编辑'),
            $form = $modal.find('form');

        $modal.find('[name=password]').attr('placeholder', '留空表示不修改');

        $modal.find('.btn-submit').click(function() {
            restful.put('/permission/user/' + obj.id, $form.serialize());
        });
    }

    function role(id) {
        restful.get('/permission/user/' + id + '/role').done(function(data) {

            var $modal = $('#modal-role').clone(),
                $form = $modal.find('form'),
                roles = data.all,
                checkeds = data.checked;

            $.each(roles, function(i, role) {
                var $formGroup;

                if (i % 2 == 0) {
                    $formGroup = $('<div class="form-group"></div>');
                    $form.append($formGroup);
                } else {
                    $formGroup = $form.find('div.form-group:last');
                }

                var checked = '';
                if ($.inArray(role.id, checkeds) >= 0) {
                    checked = 'checked';
                }

                var $cbox = $('<div class="col-xs-6"><label>\
                        <input type="checkbox" name="role_id[]" value="' + role.id + '" ' + checked + '> ' + role.role + ' - ' + role.name + '\
                        </label></div>');

                $formGroup.append($cbox);
            });

            $modal.on('hidden.bs.modal', function() {
                $modal.remove();
            });

            $modal.find('.btn-submit').click(function() {
                restful.post('/permission/user/' + id + '/role', $form.serialize());
            });

            $modal.modal();
        });
    }

    // 加载数据
    var data = JSON.parse($('#paginate').html()).data,
        $tableBody = $('#table-body');
    $tableBody.html('');
    $.each(data, function(i, obj) {
        var $tr = $('<tr></tr>');
        var $option = $('<td>\
                <a href="javascript:void(0);" class="btn-role">角色</a>\
                <a href="javascript:void(0);" class="btn-edit">编辑</a>\
                <a href="javascript:void(0);" class="btn-delete">删除</a>\
                </td>');

        $tr.append('<td>' + obj.id + '</td>');
        $tr.append('<td>' + obj.username + '</td>');
        $tr.append('<td>' + obj.name + '</td>');
        $tr.append('<td>' + obj.created_at + '</td>');
        $tr.append($option);

        $option.find('.btn-edit').click(function() {edit(obj)});
        $option.find('.btn-role').click(function() {role(obj.id)});
        $option.find('.btn-delete').click(function() {restful.del('/permission/user/' + obj.id)});
        $tableBody.append($tr);
    });
});
</script>
@endsection