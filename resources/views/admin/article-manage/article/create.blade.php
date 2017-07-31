@extends('admin.layout')

@section('title', '写文章')

@section('head-extend')
    <link href="{{cdn('plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{cdn('css/admin/article.css')}}">
@endsection

@section('page-wrapper')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">写文章</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <form class="form-horizontal" id="dataForm">
                <div class="row">

                    <div class="col-xs-8">
                        <div class="form-group">
                            <div class="col-xs-8">
                                <input type="text" class="form-control" name="title" placeholder="标题">
                            </div>
                            <div class="col-xs-4">
                                <input type="text" class="form-control" name="author" placeholder="作者" value="{{Auth::guard()->user()->username}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <script id="editor" name="content" type="text/plain" style="width:100%;height:500px;"></script>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-4" style="min-width: 272px;">
                        <div class="panel panel-default">
                            <div class="panel-heading"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> 选项</div>
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <div class="pull-left"><label class="control-label">状态</label></div>
                                        <div class="col-xs-8">
                                            <label class="radio-inline">
                                                <input type="radio" name="status" value="2" checked>下线
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="status" value="1">发布
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="pull-left"><label class="control-label">类型</label></div>
                                        <div class="col-xs-8">
                                            <label class="radio-inline">
                                                <input type="radio" name="type" value="1" checked>文章
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="type" value="2">页面
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="pull-left"><label class="control-label">写作时间</label></div>
                                        <div class="col-xs-8">
                                            <input class="form_datetime form-control datetimepicker" name="write_time" type="text" value="{{date('Y-m-d')}}" readonly style="width: 100px;">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="pull-left"><label class="control-label">封面</label></div>
                                        <div class="col-xs-8">
                                            <label class="radio-inline">
                                                <input type="radio" name="cover_type" value="1" checked>无
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="cover_type" value="2">小图
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="cover_type" value="3">大图
                                            </label>
                                        </div>
                                    </div>

                                    <div class="hide" id="cover-box">
                                        <div class="form-group">
                                            <div class="pull-left"><label class="control-label">&nbsp;</label></div>
                                            <div class="col-xs-10">
                                                <button class="btn btn-default" id="btn-cover-choose" type="button">从正文中选择</button>
                                                <button class="btn btn-default" id="btn-cover-upload" type="button">重新上传</button>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="pull-left"><label class="control-label">&nbsp;</label></div>
                                            <div class="col-xs-10">
                                                <img src="" class="img-responsive img-rounded" id="img-cover-url">
                                                <input type="hidden" name="cover_url">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> 栏目</div>
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        @foreach($columns as $column)
                                            <div class="checkbox"><label><input type="checkbox" name="column[]" value="{{$column['id']}}">{{$column['column_name']}}</label></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading"><span class="glyphicon glyphicon-tag" aria-hidden="true"></span> 标签</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-xs-8">
                                        <input type="text" class="form-control" id="iptTags">
                                    </div>
                                    <div class="col-xs-4">
                                        <button class="btn btn-default btn-block" id="btnAddTag" type="button">添加</button>
                                    </div>

                                    <br>
                                    <div class="col-xs-12">
                                        <span id="helpBlock" class="help-block">多个标签请用英文逗号（,）分开</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-12" id="tagBox">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <a href="javascript:void(0);" id="btnAllTagBox">从常用标签中选择</a>
                                    </div>
                                </div>

                                <div class="form-group hide" id="allTagBox">
                                    <div class="col-xs-12">
                                        @foreach($tags as $tag)
                                            <a href="javascript:void(0);"><u class="tag">{{$tag['tag']}}</u></a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <button class="btn btn-primary" id="btnSubmit" type="button">提交</button>
                                <button class="btn btn-default" id="btnPreview" type="button">预览</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('body-extend')
<script type="text/plain" id="uploadToken">{{$uploadToken}}</script>
<script>
    require(['jquery', 'restful', 'article'], function($, restful, article) {
        // 提交
        $('#btnSubmit').click(function() {
            restful.post('/article-manage/article', $('#dataForm').serialize());
        });
    });
</script>
@endsection